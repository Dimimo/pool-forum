<x-layout>
    <div class="container mx-auto mt-8">
        @if (session('pool-forum-status'))
            <div class="alert alert-success">
                {{ session('pool-forum-status') }}
            </div>
        @endif
        <div class="grid grid-cols-4 gap-4">
            <div class="col-span-1">
                <div class="pb-3">
                    <a
                        class="inline-block text-center w-full py-2 px-4 rounded bg-primary-500 text-white"
                        href="{{route('forum.discussions.create')}}"
                    >
                        New Discussion
                    </a>
                </div>

                @if($currentTag)
                    <h6 class="text-gray-400">
                        Showing:
                        <a class="py-1 px-2 rounded" style="color: {{$currentTag->color}}; background: {{$currentTag->background_color}}"
                           href="javascript:void(0)" onclick="preventDefault();tag(null)">
                            {{ $currentTag->name }}
                            <svg class="h-2 w-2" fill="currentColor" viewBox="0 0 20 20">
                                <path
                                    d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z"
                                    clip-rule="evenodd" fill-rule="evenodd"></path>
                            </svg>
                        </a>
                    </h6>
                @else
                    <h6 class="text-gray-400">Showing all discussions</h6>
                @endif

                <h6 class="pt-3 text-gray-400">Tags</h6>

                <ul class="flex flew-wrap">
                    @foreach ($tags as $tag)
                        @if(!$currentTag || $currentTag->id !== $tag->id)
                            <li>
                                <a class="badge" style="color: {{$tag->color}}; background: {{$tag->background_color}}" href="javascript:void(0)"
                                   onclick="preventDefault();tag('{{$tag->slug}}')">
                                    {{$tag->name}}
                                </a>
                            </li>
                        @endif
                    @endforeach

                </ul>

            </div>

            <div class="col-span-3">
                <div class="mb-3">
                    <label for="discussion-search-input"></label>
                    <input
                        type="search"
                        id="discussion-search-input"
                        class="border border-gray-300 rounded leading-none py-3 px-4 w-full "
                        placeholder="Search"
                    >
                    <div class="input-group-prepend">
                        <button
                            class="btn btn-outline-primary"
                            type="button" onclick="search(document.getElementById('discussion-search-input').value)"
                        >
                            <i class="fa fa-search"></i>
                        </button>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6">
                        <label for="discussion-sort-selector"></label>
                        <select
                            id="discussion-sort-selector"
                            class="border border-gray-300 h-10 w-1/2 rounded bg-white"
                            onchange="str_sort(this.value)"
                        >
                            <option value="created_at,DESC">Latest</option>
                            <option value="comment_count,DESC">Hot</option>
                            <option value="created_at,ASC">First</option>
                        </select>
                    </div>
                    <div class="col-md-6">
                        <a
                            href="{{route('forum.discussions.status.all')}}?key=read&value={{$allRead?0:1}}&ids={{implode(',',$discussionIds)}}"
                            class="btn btn-primary btn-block"
                        >
                            @if(count($stickies) > 1 && count($discussions) > 1)
                                @if($allRead)
                                    Unread All
                                @else
                                    Read All
                                @endif
                            @endif
                        </a>
                    </div>
                </div>


                <table class="mt-4 bg-gray-100 rounded-lg border border-gray-200">
                    <tbody>
                    <!--Sticky-->
                    @foreach($stickies as $discussion)
                        <tr class="bg-dark">
                            <td style="width:60px;" class="text-center">
                                <div class="bg-primary-500" avatar="{{$discussion->user->name}}">
                                    JS
                                </div>
                            </td>
                            <td>
                                <div>
                                    <a href="{{route('forum.discussions.show',['discussion'=>$discussion->slug] )}}">
                                        {{$discussion->title}}
                                    </a>
                                </div>
                                <small class="text-muted">
                                    @if(!$discussion->lastPost)
                                        Started by <b>{{ $discussion->user->name }}</b>
                                        {{$discussion->created_at->diffForHumans()}}
                                    @else
                                        Replied by <b>{{$discussion->lastPost->user->name}}</b>
                                        {{$discussion->lastPost->created_at->diffForHumans()}}
                                    @endif
                                </small>
                            </td>
                            <td class="text-right">
                                @foreach($discussion->tags as $tag)
                                    @if(!$currentTag || $tag->id !== $currentTag->id)
                                        <a class="badge" style="color: {{$tag->color}}; background: {{$tag->background_color}}" href="javascript:void(0)"
                                           onclick="preventDefault();tag('{{$tag->slug}}')">
                                            {{$tag->name}}
                                        </a>
                                    @else
                                        <span class="badge badge" style="color:{{$tag->color}};background-color:{{$tag->background_color}};">
                                {{$tag->name}}
                            </span>
                                    @endif
                                @endforeach
                            </td>
                            <td class="text-right text-muted">
                                <i class="far fa-comment"></i>
                                <small>{{$discussion->comment_count}}</small>
                                @if(Auth::user()->id === $discussion->user_id)

                                    <span class="dropdown show ml-5">
                                <i class="fas fa-ellipsis-v" id="discussion-options-{{$discussion->id}}" data-toggle="dropdown" aria-haspopup="true"
                                   aria-expanded="false" style="cursor: pointer;"></i>
                                <div class="dropdown-menu  dropdown-menu-right" aria-labelledby="discussion-options-{{$discussion->id}}">
                                    @if($discussion->isRead())
                                        <a class="dropdown-item" href="{{route('forum.discussions.status',['discussion'=>$discussion] )}}?key=read&value=0">
                                        <span class="d-inline-block text-muted text-center" style="width:30px;">
                                            <i class="far fa-eye-slash"></i>
                                        </span>
                                        Set as unread
                                    </a>
                                    @else
                                        <a class="dropdown-item" href="{{route('forum.discussions.status',['discussion'=>$discussion] )}}?key=read&value=1">
                                        <span class="d-inline-block text-muted text-center" style="width:30px;">
                                            <i class="far fa-eye"></i>
                                        </span>
                                        Set as read
                                    </a>
                                    @endif

                                    @if($discussion->canEdit())
                                        @if($discussion->is_locked)
                                            <a class="dropdown-item" href="{{route('forum.discussions.status',['discussion'=>$discussion] )}}?key=lock&value=0">
                                        <span class="d-inline-block text-muted text-center" style="width:30px;">
                                            <i class="fas fa-lock-open"></i>
                                        </span>
                                        Unlock
                                    </a>
                                        @else
                                            <a class="dropdown-item" href="{{route('forum.discussions.status',['discussion'=>$discussion] )}}?key=lock&value=1">
                                        <span class="d-inline-block text-muted text-center" style="width:30px;">
                                            <i class="fas fa-lock"></i>
                                        </span>
                                        Lock
                                    </a>
                                        @endif
                                        @if($discussion->is_private)
                                            <a class="dropdown-item" href="{{route('forum.discussions.status',['discussion'=>$discussion] )}}?key=private&value=0">
                                        <span class="d-inline-block text-muted text-center" style="width:30px;">
                                            <i class="fas fa-user-check"></i>
                                        </span>
                                        Set public
                                    </a>
                                        @else
                                            <a class="dropdown-item" href="{{route('forum.discussions.status',['discussion'=>$discussion] )}}?key=private&value=1">
                                        <span class="d-inline-block text-muted text-center" style="width:30px;">
                                            <i class="fas fa-user-slash"></i>
                                        </span>
                                        Set private
                                    </a>
                                        @endif
                                        <a class="dropdown-item" href="{{route('forum.discussions.edit',['discussion'=>$discussion] )}}">
                                        <span class="d-inline-block text-muted text-center" style="width:30px;">
                                            <i class="far fa-edit"></i>
                                        </span>
                                        Edit
                                    </a>

                                        <a class="dropdown-item" href="javascript:void(0)" onclick="preventDefault();
                                        document.getElementById('delete-discussion-{{$discussion->id}}').submit();">
                                        <span class="d-inline-block text-muted text-center" style="width:30px;">
                                            <i class="fas fa-trash"></i>
                                        </span>
                                        Delete
                                    </a>
                                        <form id="delete-discussion-{{$discussion->id}}" action="{{ route('forum.discussions.destroy',['discussion'=>$discussion]) }}"
                                              method="POST" style="display: none;">
                                        @csrf
                                            @method('DELETE')
                                    </form>
                                    @endif
                                </div>
                            </span>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                    <!--Discussions-->
                    @foreach($discussions->filter(function($d){return !$d->is_private;})->all() as $discussion)
                        <tr>
                            <td class="p-3 text-center">
                                <div class="py-4 px-4 rounded-full bg-primary-300" avatar="{{$discussion->user->name}}">
                                    JS
                                </div>
                            </td>
                            <td>
                                <div>
                                    <a href="{{route('forum.discussions.show',['discussion'=>$discussion->slug] )}}">
                                        {{$discussion->title}}
                                    </a>
                                </div>
                                <small class="text-muted">
                                    @if(!$discussion->lastPost)
                                        Started by <b>{{ $discussion->user->name }}</b>
                                        {{$discussion->created_at->diffForHumans()}}
                                    @else
                                        Replied by <b>{{$discussion->lastPost->user->name}}</b>
                                        {{$discussion->lastPost->created_at->diffForHumans()}}
                                    @endif
                                </small>
                            </td>
                            <td class="text-right">
                                @foreach($discussion->tags as $tag)
                                    @if(!$currentTag || $tag->id !== $currentTag->id)
                                        <a class="badge" style="color: {{$tag->color}}; background: {{$tag->background_color}}" href="javascript:void(0)"
                                           onclick="preventDefault();tag('{{$tag->slug}}')">
                                            {{$tag->name}}
                                        </a>
                                    @else
                                        <span class="badge badge" style="color:{{$tag->color}};background-color:{{$tag->background_color}};">
                                {{$tag->name}}
                            </span>
                                    @endif
                                @endforeach
                            </td>
                            <td class="text-right text-muted">
                                <i class="far fa-comment"></i>
                                <small>{{$discussion->comment_count}}</small>
                                <span class="dropdown show ml-5">
                                <i class="fas fa-ellipsis-v" id="discussion-options-{{$discussion->id}}" data-toggle="dropdown" aria-haspopup="true"
                                   aria-expanded="false" style="cursor: pointer;"></i>
                                <div class="dropdown-menu  dropdown-menu-right" aria-labelledby="discussion-options-{{$discussion->id}}">
                                    @if($discussion->isRead())
                                        <a class="dropdown-item" href="{{route('forum.discussions.status',['discussion'=>$discussion] )}}?key=read&value=0">
                                        <span class="d-inline-block text-muted text-center" style="width:30px;">
                                            <i class="far fa-eye-slash"></i>
                                        </span>
                                        Set as unread
                                    </a>
                                    @else
                                        <a class="dropdown-item" href="{{route('forum.discussions.status',['discussion'=>$discussion] )}}?key=read&value=1">
                                        <span class="d-inline-block text-muted text-center" style="width:30px;">
                                            <i class="far fa-eye"></i>
                                        </span>
                                        Set as read
                                    </a>
                                    @endif

                                    @if($discussion->canEdit())
                                        @if($discussion->is_locked)
                                            <a class="dropdown-item" href="{{route('forum.discussions.status',['discussion'=>$discussion] )}}?key=lock&value=0">
                                        <span class="d-inline-block text-muted text-center" style="width:30px;">
                                            <i class="fas fa-lock-open"></i>
                                        </span>
                                        Unlock
                                    </a>
                                        @else
                                            <a class="dropdown-item" href="{{route('forum.discussions.status',['discussion'=>$discussion] )}}?key=lock&value=1">
                                        <span class="d-inline-block text-muted text-center" style="width:30px;">
                                            <i class="fas fa-lock"></i>
                                        </span>
                                        Lock
                                    </a>
                                        @endif
                                        @if($discussion->is_private)
                                            <a class="dropdown-item" href="{{route('forum.discussions.status',['discussion'=>$discussion] )}}?key=private&value=0">
                                        <span class="d-inline-block text-muted text-center" style="width:30px;">
                                            <i class="fas fa-user-check"></i>
                                        </span>
                                        Set public
                                    </a>
                                        @else
                                            <a class="dropdown-item" href="{{route('forum.discussions.status',['discussion'=>$discussion] )}}?key=private&value=1">
                                        <span class="d-inline-block text-muted text-center" style="width:30px;">
                                            <i class="fas fa-user-slash"></i>
                                        </span>
                                        Set private
                                    </a>
                                        @endif
                                        <a class="dropdown-item" href="{{route('forum.discussions.edit',['discussion'=>$discussion] )}}">
                                        <span class="d-inline-block text-muted text-center" style="width:30px;">
                                            <i class="far fa-edit"></i>
                                        </span>
                                        Edit
                                    </a>

                                        <a class="dropdown-item" href="javascript:void(0)" onclick="preventDefault();
                                        document.getElementById('delete-discussion-{{$discussion->id}}').submit();">
                                        <span class="d-inline-block text-muted text-center" style="width:30px;">
                                            <i class="fas fa-trash"></i>
                                        </span>
                                        Delete
                                    </a>
                                        <form id="delete-discussion-{{$discussion->id}}" action="{{ route('forum.discussions.destroy',['discussion'=>$discussion]) }}"
                                              method="POST" style="display: none;">
                                        @csrf
                                            @method('DELETE')
                                    </form>
                                    @endif
                                </div>
                            </span>
                            </td>
                        </tr>
                    @endforeach
                    @if(count($stickies) < 1 && count($discussions) < 1)
                        <p>No discussions</p>
                    @endif
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <script type="text/javascript">
        const url = "{{route('forum.forum.index')}}?sort_by=__SORT_BY__&sort_dir=__SORT_DIR__&tag=__TAG__&search=__SEARCH__";
        let currentSort = {!! json_encode($currentSort, true)!!};
        let currentTag = "{{ ($currentTag) ? $currentTag->slug : '' }}";
        let currentSearch = "{!! $currentSearch !!}";

        function go() {
            window.location.href = url.replace('__SORT_BY__', currentSort[0])
                .replace('__SORT_DIR__', currentSort[1])
                .replace('__TAG__', currentTag)
                .replace('__SEARCH__', currentSearch);
        }

        function tag(value) {
            if (typeof value !== 'string' || value.trim().length < 1) {
                value = '';
            }
            currentTag = value;
            go();
        }

        function sort(by, dir) {
            currentSort = [by, dir === 'ASC' ? 'ASC' : 'DESC'];
            go();
        }

        function str_sort(str) {
            const tmp = str.split(',').map(function (value) {
                return value.trim();
            });
            sort(tmp[0], tmp[1]);
        }

        function search(value) {
            currentSearch = value.trim();
            console.log(currentSearch);
            go();
        }

        function sort_to_str() {
            return currentSort[0] + ',' + currentSort[1];
        }

        document.getElementById('discussion-sort-selector').value = currentSort[0] + ',' + currentSort[1];
        document.getElementById('discussion-search-input').value = currentSearch;

    </script>
</x-layout>
