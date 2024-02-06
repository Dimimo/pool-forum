<x-layout>
    <div class="container posts">
        <div class="row py-3 my-3 border-bottom border-color-secondary">
            <div class="col-auto">
                <a href="{{route('forum.discussions.index')}}" class="h1 text-secondary">
                    <i class="fas fa-chevron-left"></i>
                </a>
            </div>
            <div class="col  text-center">
                <h1 class="text-secondary">{{$discussion->title}}</h1>
                @foreach($discussion->tags as $tag)
                    <span class="badge badge" style="color:{{$tag->color}};background-color:{{$tag->background_color}};">
                {{$tag->name}}
            </span>
                @endforeach
            </div>
        </div>
        @if (session('pool-forum-status'))
            <div class="alert alert-success">

                {{ session('pool-forum-status') }}
            </div>
        @endif

        @livewire('forum.comments', ['discussion'=>$discussion,'posts' => $posts])

        <div class="row py-3 my-3">
            @if(!$discussion->is_locked)
                @livewire('forum.comment', ['discussion' => $discussion,'user'=>Auth::user()->name])
            @else
                <div class="col text-center text-muted">
                    Discussion locked by owner
                </div>
            @endif
        </div>
    </div>

    <script type="text/javascript">
        function toggleEdit(postId) {
            const content = document.getElementById('post-content-' + postId);
            const form = document.getElementById('post-form-' + postId);

            const addContent = document.getElementById('post-content');
            const addSubmit = document.querySelectorAll('#post-form [type=submit]')[0];

            if (form.classList.contains('d-none')) {
                content.classList.remove('d-block');
                content.classList.add('d-none');
                form.classList.remove('d-none');
                form.classList.add('d-block');
                addContent.disabled = true;
                addSubmit.disabled = true;
            } else {
                content.classList.remove('d-none');
                content.classList.add('d-block');
                form.classList.remove('d-block');
                form.classList.add('d-none');
                addContent.disabled = false;
                addSubmit.disabled = false;
            }
        }

        function canEdit(postId) {
            const submit = document.querySelectorAll('#post-form-' + postId + ' [type=submit]')[0];
            const textarea = document.querySelectorAll('#post-form-' + postId + ' [name=content]')[0];
            const data = textarea.value.trim();
            const old = textarea.getAttribute('old').trim();

            submit.disabled = !(data.length > 0 && data !== old);
        }
    </script>
</x-layout>
