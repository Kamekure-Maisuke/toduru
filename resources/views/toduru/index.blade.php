<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Document</title>
</head>
<body>
    <h2>新規作成</h2>
    @if (session('feedback.success'))
    <p style="color: green">{{ session('feedback.success') }}</p>
    @endif
    <form action="{{ route('toduru.create') }}" method="post">
        @csrf
        <label for="toduru-content">TODURU</label>
        <textarea name="toduru" id="toduru-content"></textarea>
        @error('toduru')
        <p style="color: red">{{ $message }}</p>
        @enderror
        <button type="submit">作成</button>
    </form>
    @foreach ($todurus as $toduru)
        <div>
            {{ $toduru->content }}
            <a href="{{ route('toduru.update.index', ['toduruId' => $toduru->id]) }}">
                編集
            </a>
            <form
                action="{{ route('toduru.delete', ['toduruId' => $toduru->id]) }}"
                method="post"
            >
                @method('DELETE')
                @csrf
                <button type="submit">削除</button>
            </form>
        </div>
    @endforeach
</body>
</html>
