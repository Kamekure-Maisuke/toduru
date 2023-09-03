# laravel sailを利用した開発

## インストール

```shell
# example-appが名前
curl -s "https://laravel.build/example-app" | bash
```

## Dockerディレクトリ構築

```shell
cd example-app
sail artisan sail:publish
# ルートにDockerディレクトリが作成される。
```

- `docker-compose.yml`内のdockerバージョンにて、`docker`ディレクトリのバージョンのディレクトリに移動。
- `docker/x.x`内の`Dockerfile`の`ENV TZ=UTC`を`ENV TZ='Asia/Tokyo'`に変更。
- その後、以下でビルド

```shell
sail build --no-cache
```

## mysql日本語化
- `docker/x.x/my.cnf`を作成して以下のように編集

```conf
[mysqld]
character-set-server=utf8mb4
collation-server=utf8mb4_bin

[client]
default-character-set=utf8mb4
```

- `docker-compose.yml`のmysqlのvolumesに以下を追加

```yaml
'./docker/8.2/my.cnf:/etc/my.cnf'
```

## 起動

```shell
./vendor/bin/sail up
```

- `~/.zshrc`にコマンド記述

```vim:.vimrc
alias sail='[  -f sail ] && bash || bash vendor/bin/sail'
```

```shell
sail up -d
sail down
```

## コンテナログイン

```shell
sail shell
```

## データベースログイン

```shell
sail mysql
```

## artisanコマンド起動

```shell
# sail artisan コマンド名
sail artisan -V
```

## テスト実行

```shell
#　./vendor/bin/phpunit
sail test
```

## phpやcomposer実行

```shell
sail php -v
sail composer -V
```

## コントローラ作成

```shell
# 「sail artisan make:controller Sample/IndexController」でも良い。
# が、1つのコントローラに1つのエンドポイントしか設定できないシングルアクションを採用できる以下を使う。
sail artisan make:controller Sample/IndexController --invokable
```

## ビュー生成
- `resource/views/sample/index.blade.php`のように生成
- 呼び出しはコントローラー内のinvokeメソッドで以下のように呼び出し。

```php
return view('sample.index',['data'=>'Hello']);
```

- 表示は以下。

```php
Hello {{$data}}
```

## テーブル作成

```shell
# database/migrations/xxxx_xx_xx_xxxx_create_todurus_table.phpの作成
sail artisan make:migration create_samples_table
```

- `database/migrations/xxxx_xx_xx_xxxx_create_todurus_table.php`の中身を例として以下。

```php
public function up(): void
{
    Schema::create('samples', function (Blueprint $table) {
        $table->id(); // autoIncrement
        $table->string('content');
        $table->timestamps(); // created_atとupdated_atの2つが作成される。
    });
}
```

- 以下でマイグレーション実行

```shell
sail artisan migrate
```

- `sail mysql`でログインしてtableとかカラムを確認


```shell
show tables from samples;
show columns from todurus;
```

## 初期データ挿入
- シーダーを利用して初期データを一括挿入。
- 以下でシーダーファイルを作成

```shell
sail artisan make:seeder SampleSeeder
```

- 作成されたファイルに以下を追加

```php
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class SampleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('samples')->insert([
            'content' => Str::random(100),
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }
}
```

- 定義後、呼び出すため`database/seeders/DatabaseSeeder.php`のrunメソッドに以下を追加。

```php
$this->call([SampleSeeder::class]);
```

- シーダー実行

```shell
sail artisan db:seed
```

## モデル作成
- データベースとのやり取りをEloquentモデル作成して行う。

```shell
# sail artisan make:model テーブル名(単数系)
# 「-a」オプションでモデル・コントローラー・テストデータの作成を行う。
sail artisan make:model sample
```

## ファクトリー作成

```shell
sail artisan make:factory ToduruFactory --model=Toduru
```

- 作成されたファイルに例として以下のようなfakerデータを返す記述をする。

```php
public function definition(): array
{
    return [
        'content' => $this->faker->realText(100)
    ];
}
```

- fakerを日本語化するために`config/app.php`の以下の記述を変更。

```php
// 'faker_locale' => 'UTC',
'faker_locale' => 'ja_JP',
```

- それでシーディングを行う。`SampleSeeder.php`を以下に変更

```php
public function run(): void
{
    Toduru::factory()->count(10)->create();
}
```

- 実行

```shell
sail artisan db:seed
```

## 一覧表示
- Controllerのinvokeに書く。

```php
use App\Models\Sample;

    public function __invoke(Request $request)
    {
        $samples = Sample::all();
        return view('sample.index',[
            // viewファイルに渡すデータ
            'samples'=>$samples,
        ]);
    }
```

- bladeで表示。

```php
@foreach($samples as $sample)
    <p>{{ $sample->content }}</p>
@endforeach
```

## 作成画面(Create)

```shell
# コントローラ作成
sail artisan make:controller Toduru/CreateController --invokable
# リクエストファイル作成(POSTやPUTの時に使う。)
sail artisan make:request Toduru/CreateRequest
```

- `make:requesut`で作成される`authorize`と`rules`の概要は以下。
    - authorize : ユーザー認証。.trueで誰でもリクエスト可能。
    - rules : バリデーション
- 一旦`CreateRequesut`のファイルをを以下のようにする。

```php
public function authorize(): bool
{
    return true;
}

public function rules(): array
{
    return [
        // 必須かつ100文字以内
        // keyはリクエストBodyのkeyにする。
        'sample' => 'required|max:100'
    ];
}
```

- Controllerに以下を追加

```php
use App\Http\Controllers\Controller;
use App\Http\Requests\Sample\CreateRequest;

class CreateController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(CreateRequest $request)
    {
        //
    }
}
```

- routeに定義

```php
// nameを定義することで、route('sample.create')のようにviewとかで呼び出せる
Route::post('/sample/create',\App\Http\Controllers\Sample\CreateController::class)->name('sample.create');
```

- バリデーションメッセージの日本語化。`config/app.php`の以下の項目を修正。

```php
'locale' => 'ja',
'fallback_locale' => 'ja',
```

- そのほかに日本語化の詳細は後ほど確認。

- blade画面に以下のフォームを作成

```php
    <form action="{{ route('sample.create') }}" method="post">
        @csrf
        <label for="sample-content">TODURU</label>
        <textarea name="sample" id="sample-content"></textarea>
        @error('sample')
        <p style="color: red">{{ $message }}</p>
        @enderror
        <button type="submit">作成</button>
    </form>
```

- `app/Http/Requests/Sample/CreateRequest.php`に以下を追加して、データを取得できるようにする。

```php
public function sample(): string
{
    // formのnameに対応。
    return $this->input('sample');
}
```

## 編集画面作成(PUT)

```shell
# 編集画面(詳細画面)と編集リクエストコントローラ作成
sail artisan make:controller Toduru/Update/IndexController --invokable
sail artisan make:controller Toduru/Update/PutController --invokable

# 編集リクエストクラスフォーム作成
sail artisan make:request Toduru/UpdateRequest
```

- UpdateRequestのファイル内容はCreateRequestファイルと一緒にする。
- route/web.phpに以下のように追加。

```php
Route::get('/sample/update/{sampleId}',\App\Http\Controllers\Sample\Update\IndexController::class)->name('sample.update.index');
Route::put('/sample/update/{sampleId}',\App\Http\Controllers\Sample\Update\PutController::class)->name('sample.update.put');
```

- パスとしてsampleIdに数値以外が渡ってこないように、`app/Providers/RouteServiceProvider.php`のbootメソッドに以下を追加。

```php
Route::pattern('toduruId', '[0-9]+');
```

- UpdateのindexControllerにIDでDBから取得してくる処理を以下のように追加。

```php
public function __invoke(Request $request)
{
    $sampleId = (int) $request->route('sampleId');
    $sample = Toduru::where('id', $sampleId)->firstOrFail();
    return view('sample.update',[
        'sample'=>$sample,
    ]);
}
```

- viewは以下。
    - formタグ内はgetかpostのみ。なので@method('put')と定義する。
    - パスパラメータが必要なので、routeメソッドの第2引数に配列として渡す。例 : `update/{sampleId}`なら`['sampleId'=>$sample->id]`

```php
<a href="{{ route('toduru.index') }}">戻る</a>
<h2>編集</h2>
<form
    action="{{ route('sample.update.put', ['sampleId' => $sample->id]) }}"
    method="post"
>
    @method('put')
    @csrf
    <label for="sample-content">TODURU</label>
    <textarea name="sample" id="sample-content">
        {{ $sample->content }}
    </textarea>
    @error('sample')
    <p style="color: red">{{ $message }}</p>
    @enderror
    <button type="submit">編集</button>
</form>
```

- UpdateRequestファイルにIDを取得する以下を追加。

```php
public function id(): int
{
    // ID取得はrequestに書くと簡略化できる。
    return (int) $this->route('toduruId');
}
```

- putcontrollerに以下を追加。

```php
<?php

namespace App\Http\Controllers\Sample\Update;

use App\Http\Controllers\Controller;
use App\Http\Requests\Sample\UpdateRequest;
use App\Models\Sample;

class PutController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(UpdateRequest $request)
    {
        $toduru = Sample::where('id', $request->id())->firstOrFail();
        $sample->content = $request->sample();
        $sample->save();
        // return redirect()->route('sample.index');
        return redirect()
            ->route('sample.update.index',['sampleId' => $toduru->id])
            ->with('feedback.success', "編集しました。"); // セッションデータ
    }
}
```

- 上記のように編集後のredirectに`with('feedback.success', "編集しました。")`とかで渡すことで一時データ(例: 通知)をviewで使える。セッション的な。

- viewは以下。

```php
@if (session('feedback.success'))
    <p style="color: green">{{ session('feedback.success') }}</p>
@endif
```

## 削除処理

```shell
sail artisan make:controller Toduru/DeleteController --invokable
```

- `web.php`に以下を追加

```php
Route::delete('/toduru/delete/{sampleId}',\App\Http\Controllers\Sample\DeleteController::class)->name('sample.delete');
```

- DeleteControllerに以下を追加

```php
<?php

namespace App\Http\Controllers\Sample;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Sample;

class DeleteController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(Request $request)
    {
        $sampleId = (int) $request->route('sampleId');
        Sample::destroy($sampleId);
        return redirect()
        ->route('sample.index')
        ->with('feedback.success', "削除しました。");
    }
}
```

- viewに削除部分を追加して完了。
    - こちらも同様にformタグ内のmethodはpost。@methodはDELETE。

```php
<form
    action="{{ route('sample.delete', ['sampleId' => $sample->id]) }}"
    method="post"
>
    @method('DELETE')
    @csrf
    <button type="submit">削除</button>
</form>
```
