<?php

namespace Dimimo\PoolForum\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Dimimo\PoolForum\Models\Discussion;
use Dimimo\PoolForum\Models\Discussion\Tag as DiscussionTag;
use Dimimo\PoolForum\Models\Discussion\User as DiscussionUser;
use Dimimo\PoolForum\Models\Tag;

class DiscussionController
{
    public function index()
    {
        $currentSort = [
            request('sort_by', 'created_at'),
            request('sort_dir', 'DESC') === 'ASC' ? 'ASC' : 'DESC',
        ];
        $currentTag = Tag::where('slug', request('tag', ''))->first();
        $currentSearch = trim((string) request('search', ''));
        $discussions = $this->makeSearch($currentSearch, $currentSort, $currentTag);
        $stickies = $this->makeSearch($currentSearch, $currentSort, $currentTag, true);
        $tags = Tag::orderBy('name')->get();
        $user = Auth::user();

        $allRead = true;
        $discussionIds = [];

        foreach ([$discussions, $stickies] as $current) {
            foreach ($current as $discussion) {
                if (! in_array($discussion->id, $discussionIds)) {
                    $discussionIds[] = $discussion->id;
                }
                if (! $discussion->isRead()) {
                    $allRead = false;
                }
            }
        }

        return view(
            'pool-forum::'.config('pool-forum.views.folder').'discussions.index',
            compact('discussionIds', 'allRead', 'user', 'stickies', 'discussions', 'tags', 'currentSort', 'currentTag', 'currentSearch')
        );
    }

    /**
     * Manage "settings" of many discussions (or all).
     * Atm only to sets read/unread all.
     */
    public function statusAll(Request $request)
    {
        $key = $request->get('key');
        $value = intval($request->get('value', 0)) === 1;

        if ($key == 'read') {
            $ids = array_map(function ($value) {
                return (int) trim($value);
            }, explode(',', $request->get('ids')));
            foreach ($ids as $id) {
                $this->setRead($value, $id);
            }
        }

        return back()->with('pool-forum-status', __('pool-forum::words.status_changed'));
    }

    /**
     * Manage "settings" of some post (locked, sticky, read, etc).
     */
    public function status(Request $request, Discussion $discussion)
    {
        $key = $request->get('key');
        $value = intval($request->get('value', 0)) === 1;
        if ($key !== 'read' && ! $discussion->canEdit(Auth::user()->id)) {
            // Forbidden
            dd('No');
        }
        switch ($key) {
            case 'lock':
                $discussion->is_locked = $value === true ? 1 : 0;
                $discussion->save();

                break;
            case 'private':
                $discussion->is_private = $value === true ? 1 : 0;
                $discussion->save();

                break;
            case 'read':
                $this->setRead($value, $discussion);

                break;
        }

        return back()->with('pool-forum-status', __('pool-forum::words.status_changed'));
    }

    public function show(string $slug)
    {
        $discussion = Discussion::where('slug', $slug)->firstOrFail();

        $posts = (Auth::user()->id === $discussion->user_id)
            ? $discussion->posts()->orderBy('created_at', 'ASC')->get()
            : $discussion->posts()->where('is_approved', 1)
                ->orderBy('created_at', 'ASC')
                ->get();

        $discussionUser = DiscussionUser::where('user_id', Auth::user()->id)
            ->where('discussion_id', $discussion->id)
            ->first();
        if (! $discussionUser) {
            $discussionUser = new DiscussionUser();
            $discussionUser->fill([
                'discussion_id' => $discussion->id,
                'user_id' => Auth::user()->id,
            ]);
        }
        $discussionUser->last_read_at = Carbon::now()->toDateTimeString();
        $discussionUser->last_read_post_number = $discussion->post_number_index;
        $discussionUser->save();

        return view('pool-forum::'.config('pool-forum.views.folder').'discussions.show', compact('discussion', 'posts'));
    }

    /**
     * Create a new discussion.
     */
    public function create()
    {
        $tags = Tag::orderBy('name')->get();

        return view('pool-forum::'.config('pool-forum.views.folder').'discussions.create', compact('tags'));
    }

    /**
     * Store a new discussion.
     */
    public function store(Request $request)
    {
        $data = $request->only('tags', 'title', 'is_private', 'is_approved', 'is_locked', 'is_sticky');

        $validator = Validator::make($data, [
            'title' => ['required', 'string', 'max:200'],
            'is_private' => ['nullable', 'boolean'],
            'is_approved' => ['nullable', 'boolean'],
            'is_locked' => ['nullable', 'boolean'],
            'is_sticky' => ['nullable', 'boolean'],
            'tags' => ['nullable', 'array'],
            'tags.*' => ['numeric', 'exists:tags,id'],
        ]);

        $data['user_id'] = Auth::user()->id;
        if ($validator->fails()) {
            return redirect()->route(config('pool-forum.name_prefix').'discussions.create')
                ->withErrors($validator)
                ->withInput();
        }

        $data['slug'] = $this->makeSlug($data['title']);

        $discussion = Discussion::create($data);
        $this->setTags($discussion, $data['tags'] ?? []);

        return redirect()->route(config('pool-forum.name_prefix').'forum.index')->with('pool-forum-status', __('pool-forum::words.record_created'));
    }

    /**
     * Edits an existing discussion.
     */
    public function edit(Request $request, Discussion $discussion)
    {
        $tags = Tag::orderBy('name')->get();
        $discussionTags = [];
        if (Session::get('errors')) {
            if (is_array($request->old('tags'))) {
                $discussionTags = $request->old('tags');
            }
        } else {
            foreach (DiscussionTag::where('discussion_id', $discussion->id)->get() as $current) {
                $discussionTags[$current->tag_id] = $current->tag_id;
            }
        }

        return view('pool-forum::'.config('pool-forum.views.folder').'discussions.edit', compact('discussion', 'tags', 'discussionTags'));
    }

    /**
     * Updates an existing discussion.
     */
    public function update(Request $request, Discussion $discussion)
    {
        $data = $request->only('tags', 'title', 'is_private', 'is_approved', 'is_locked', 'is_sticky');

        $validator = Validator::make($data, [
            'title' => ['required', 'string', 'max:200'],
            'is_approved' => ['nullable', 'boolean'],
            'is_locked' => ['nullable', 'boolean'],
            'is_sticky' => ['nullable', 'boolean'],
            'tags' => ['nullable', 'array'],
            'tags.*' => ['numeric', 'exists:tags,id'],
        ]);
        if ($validator->fails()) {
            return redirect()->route(config('pool-forum.name_prefix').'discussions.edit', ['discussion' => $discussion])
                ->withErrors($validator)
                ->withInput();
        }
        if ($data['title'] !== $discussion->title) {
            $data['slug'] = $this->makeSlug($data['title']);
        }
        $discussion->fill($data);
        $discussion->save();
        $this->setTags($discussion, $data['tags'] ?? []);

        return redirect()->route(config('pool-forum.name_prefix').'forum.index')->with('pool-forum-status', __('pool-forum::words.record_updated'));
    }

    /**
     * Deletes an existing discussion.
     */
    public function destroy(Discussion $discussion)
    {
        $discussion->delete();

        return redirect()->route(config('pool-forum.name_prefix').'forum.index')->with('pool-forum-status', __('pool-forum::words.record_destroyed'));
    }

    protected function setRead(bool $status, int|Discussion $discussion, ?int $user = null)
    {
        if (! ($discussion instanceof Discussion)) {
            $discussion = Discussion::find($discussion);
        }
        if (! $user) {
            $user = Auth::user()->id;
        }
        $read = DiscussionUser::where('discussion_id', $discussion->id)
            ->where('user_id', $user)
            ->first();
        if ($status) {
            if (! $read) {
                $read = DiscussionUser::create([
                    'user_id' => $user,
                    'discussion_id' => $discussion->id,
                ]);
            }
            $read->fill([
                'last_read_at' => Carbon::now()->toDateTimeString(),
                'last_read_post_number' => $discussion->post_number_index,
            ]);
            $read->save();
        } else {
            $read?->delete();
        }
    }

    protected function makeSearch(?string $search, array $sort, ?Tag $tag = null, bool $sticky = false)
    {
        if ($tag) {
            $ids = DiscussionTag::where('tag_id', $tag->id)
                ->pluck('discussion_id')
                ->all();
            $query = Discussion::whereIn('id', $ids);
        } else {
            $query = Discussion::where('id', '>', 0);
        }
        if (! empty($search)) {
            $query->where('title', 'LIKE', '%'.$search.'%');
        }
        if ($sticky) {
            $query->where('is_sticky', 1);
        } else {
            $query->where('is_sticky', '!=', 1);
        }
        $query->orderBy($sort[0], $sort[1]);

        return $query->get();
    }

    protected function makeSlug(string $title)
    {
        $slug = Str::slug($title);

        $counter = 1;
        while (1) {
            $test = $slug.'-'.$counter;
            if (! Discussion::where('slug', $test)->first()) {
                return $test;
            }
            $counter++;
        }
    }

    protected function setTags(Discussion $discussion, ?array $tags)
    {
        if (! is_array($tags)) {
            $tags = [];
        }
        $tags = array_values(array_map('intval', $tags));
        DiscussionTag::where('discussion_id', $discussion->id)->whereNotIn('tag_id', $tags)->delete();
        foreach ($tags as $id) {
            $assoc = DiscussionTag::where('discussion_id', $discussion->id)
                ->where('tag_id', $id)->first();
            // discussion_tag assoc exists, continue
            if ($assoc) {
                continue;
            }
            $tag = Tag::find($id);
            if ($tag) {
                DiscussionTag::create([
                    'discussion_id' => $discussion->id,
                    'tag_id' => $id,
                ]);
                $tag->fill([
                    'discussion_count' => ($tag->discussion_count + 1),
                    'last_posted_at' => $discussion->updated_at,
                    'last_posted_discussion_id' => $discussion->id,
                    'last_posted_user_id' => $discussion->user_id,
                ]);
                $tag->save();
            }
        }
    }
}
