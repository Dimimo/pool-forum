<x-layout>
    <div class="container">
        <h1> Settings Show </h1>

        <div class="form-group">
            <label for="key">Key</label>
            <p>{{$setting->key}}</p>
        </div>
        <div class="form-group">
            <label for="value">Value</label>
            <p>{{$setting->value}}</p>
        </div>
        <a href="{{route('forum.settings.index')}}">Back</a>
    </div>
</x-layout>
