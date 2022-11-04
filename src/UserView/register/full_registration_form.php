<?php

// セッション有効期限
// ini_set('session.gc_maxlifetime', 60);
session_start();

// クラスファイルインポート
require __DIR__ . '../../../../class/Logic.php';

// functionファイルインポート
require __DIR__ . '../../../../function/functions.php';

// パラメータがない場合は、不正なリクエストとみなす。
$key = $_SESSION['email'];

// パラメータがない場合リダイレクト
if (!$access_key = filter_input(INPUT_GET, 'key')) {
    header('Location: ./provisional_registration_form.php');
}

?>

<!DOCTYPE html>
<html lang="ja">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>

<body>
    <div class="authContent">
        <h2>学生情報登録</h2>
        <form action="./full_registration.php" method="post">
            <p><span>必須</span><label style="margin-right: 30px;">ニックネーム</label><input type="text" name="name"></p>

            <p>
                <span>必須</span><label style="margin-right: 93px;">学科名</label>
                <select name="department">
                    <option hidden>選択してください</option>
                    <option value="ITエキスパート学科">ITエキスパート学科</option>
                    <option value="ITスペシャリスト学科">ITスペシャリスト学科</option>
                </select>
            </p>

            <p>
                <span>必須</span><label style="margin-right: 113px;">学年</label>
                <select name="school_year">
                    <option hidden>選択してください</option>
                    <option value="1年生">1年生</option>
                    <option value="2年生">2年生</option>
                </select>
            </p>

            <p><span>必須</span><label style="margin-right: 68px;">出席番号</label><input type="text" name="number"></p>

            <p><span>必須</span><label style="margin-right: 48px;">パスワード</label><input type="password" name="password"></p>

            <!-- email情報 -->
            <input type="hidden" name="email" value="<?php h($key); ?>">

            <button type="submit">登録する</button>
        </form>
    </div>
</body>

</html>