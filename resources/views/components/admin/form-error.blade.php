@props(['messages'])

@foreach ((array) $messages as $message)
    <p class="form-error">{{ $message }}</p>
@endforeach
