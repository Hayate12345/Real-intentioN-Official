<?php

session_start();
ob_start();
define('PATH', '/Applications/MAMP/htdocs/Deliverables4');

// 外部ファイルのインポート
require_once PATH . '/class/Session_calc.php';
require_once PATH . '/class/Database_calc.php';
require_once PATH . '/class/Register_calc.php';
require_once PATH . '/class/Validation_calc.php';
require_once PATH . '/function/functions.php';

// インスタンス化
$ses_calc = new Session();
$val_calc = new ValidationCheck();
$rgs_calc = new Register();

$err_array = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = filter_input(INPUT_POST, 'email');
    $user_input_token = filter_input(INPUT_POST, 'token');
    $email_token = filter_input(INPUT_POST, 'email_token');
    $csrf_token = filter_input(INPUT_POST, 'csrf_token');

    // csrfトークンの存在確認と正誤判定
    $csrf_check = $ses_calc->csrf_match_check($csrf_token);
    if (!$csrf_check) {
        $uri = '/Deliverables4/src/' . basename('400_request.php');
        header('Location:' . $uri);
    }

    // バリデーションチェック
    $val_check_arr[] = strval($user_input_token);
    if (!$val_calc->not_yet_entered($val_check_arr)) {
        $err_array[] = $val_calc->getErrorMsg();
    }

    if ($email_token !== $user_input_token) {
        $err_array[] = '認証コードが間違っています。';
    }

    // 学生情報を入力できる時間を制限 20分間
    $cookieName = 'input_time_limit';
    $cookieValue = rand();
    $cookieExpire = time() + 1200;
    setcookie($cookieName, $cookieValue, $cookieExpire);

    // csrf_token削除　二重送信対策
    $ses_calc->csrf_token_unset();
} else {
}

?>

<!DOCTYPE html>
<html lang="ja">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0-beta1/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-giJF6kkoqNQ00vy+HMDP7azOuL0xtbfIcaT9wjKHr8RbDVddVHyTfAAsrekwKmP1" crossorigin="anonymous" />
    <link href="https://unpkg.com/boxicons@2.0.9/css/boxicons.min.css" rel="stylesheet" />
    <link rel="shortcut icon" href="../../../public/img/favicon.ico" type="image/x-icon">
    <title>学生情報登録 / 「Real intentioN」</title>
    <style>
        body {
            background-color: #EFF5F5;
        }

        header {
            background-color: #D6E4E5;
        }

        footer {
            background-color: #497174;
        }

        .nav-link {
            font-weight: bold;
        }

        .nav-link:hover {
            text-decoration: underline;
        }

        .login-btn {
            background-color: #EB6440;
            color: white;
        }

        .login-btn:hover {
            color: white;
            background-color: #eb6540c4;
        }
    </style>
</head>

<body>
    <?php include(PATH . '/src/template/header_template.php') ?>

    <div class="box d-flex vh-100 align-items-center">
        <div class="container bg-light py-5">
            <div class="row py-5">
                <div class="col-lg-5 mx-auto">
                    <?php if (count($err_array) > 0) : ?>
                        <?php foreach ($err_array as $err_msg) : ?>
                            <div class="alert alert-danger" role="alert"><strong>エラー</strong>　-<?php h($err_msg) ?></div>
                        <?php endforeach; ?>

                        <div class="mt-2">
                            <a class="btn btn-primary px-4" href="./auth_email_form.php?email=<?php h($email); ?>">戻る</a>
                        </div>
                    <?php endif; ?>

                    <?php if (count($err_array) === 0) : ?>
                        <div class="alert alert-dark" role="alert"><strong>チェック</strong>　-認証が完了しました。</div>
                        <?php $uri = './register_form.php?email=' . $email ?>
                        <?php header('refresh:3;url=' . $uri); ?>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <?php include(PATH . '/src/template/footer.php') ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0-beta1/dist/js/bootstrap.bundle.min.js" integrity="sha384-ygbV9kiqUc6oa4msXn9868pTtWMgiQaeYH7/t7LECLbyPA2x65Kgf80OJFdroafW" crossorigin="anonymous">
    </script>
</body>

</html>