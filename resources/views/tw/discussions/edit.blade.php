<x-layout>
    <div class="container">
        <h1> Edit Discussion </h1>
        @if ($errors->any())
            <ul class="alert alert-danger">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        @endif


        <form action="{{route('forum.discussions.update',['discussion'=>$discussion])}}" method="POST" id="form">
            @method('PUT')
            @csrf
            <div class="form-group">
                <label for="title">Title</label>
                <input class="form-control" type="text" name="title" id="title" value="{{old('title', $discussion->title)}}" maxlength="200">
                @if($errors->has('title'))
                    <p class="text-danger">{{$errors->first('title')}}</p>
                @endif
            </div>
            <div class="form-group">
                <div class="form-check form-check-inline">
                    <input class="form-check-input" name="is_private" type="checkbox" id="is_private" boolean
                           value="{{old('is_private',  $discussion->is_private)}}">
                    <label class="form-check-label" for="is_private">private </label>
                </div>
                <div class="form-check form-check-inline">
                    <input class="form-check-input" name="is_approved" type="checkbox" id="is_approved" boolean
                           value="{{old('is_approved',  $discussion->is_approved)}}">
                    <label class="form-check-label" for="is_approved">approved </label>
                </div>
                <div class="form-check form-check-inline">
                    <input class="form-check-input" name="is_locked" type="checkbox" id="is_locked" boolean
                           value="{{old('is_locked', $discussion->is_locked)}}">
                    <label class="form-check-label" for="is_locked">locked </label>
                </div>
                <div class="form-check form-check-inline">
                    <input class="form-check-input" name="is_sticky" type="checkbox" id="is_sticky" boolean
                           value="{{old('is_sticky', $discussion->is_sticky)}}">
                    <label class="form-check-label" for="is_sticky">sticky </label>
                </div>
            </div>
            <div class="form-group">
                <h2>
                    Tags (
                    <span id="tag-counter">
                    {{ is_array(old('tags')) ? count(old('tags')) : 0 }}
                </span>
                    )
                </h2>
            </div>
            <div class="form-group">
                @foreach($tags as $tag)
                    <span class="badge" style="color:{{$tag->color}};background-color:{{$tag->background_color}};">
                <div class="form-check form-check-inline">
                    <input class="form-check-input" name="tags[{{$tag->id}}]" tag-checkbox type="checkbox" id="tags-{{$tag->id}}"
                           {{isset($discussionTags[$tag->id]) ? 'checked="checked"' : ''}} value="{{$tag->id}}" onclick="count_tags()">
                    <label class="form-check-label" for="tags-{{$tag->id}}">{{$tag->name}}</label>
                </div>
            </span>
                @endforeach
            </div>

            <div>
                <button class="btn btn-primary" type="submit">Save</button>
                <a href="{{route('forum.discussions.index')}}">Back</a>
            </div>

        </form>
    </div>

    <script type="text/javascript">

        function count_tags() {
            const el = document.querySelectorAll('#form input[type=checkbox][tag-checkbox]');
            let count = 0;

            for (let i = 0; i < el.length; i++) {
                if (el[i].checked === true) {
                    count++;
                }
            }
            document.getElementById('tag-counter').innerHTML = count.toString();
        }

        count_tags();
    </script>
</x-layout>
