<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>TODURU</title>
</head>
<body>
    <a href="{{ route('toduru.index') }}">戻る</a>
    <h2>編集</h2>
    @if (session('feedback.success'))
        <p style="color: green">{{ session('feedback.success') }}</p>
    @endif
    <form
        action="{{ route('toduru.update.put', ['toduruId' => $toduru->id]) }}"
        method="post"
    >
        @method('PUT')
        @csrf
        <label for="toduru-content">TODURU</label>
        <textarea name="toduru" id="toduru-content">
            {{ $toduru->content }}
        </textarea>
        @error('toduru')
        <p style="color: red">{{ $message }}</p>
        @enderror
        <button type="submit">編集</button>
    </form>
</body>
</html>
