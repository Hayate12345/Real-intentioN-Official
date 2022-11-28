<?php

session_start();

require '/Applications/MAMP/htdocs/Deliverables4/class/Session_calc.php';
require '../../../function/functions.php';

$ses_calc = new Session();

$email_token_check = $ses_calc->check_email_token();

if (!$email_token_check) {
    $uri = '../../400_request.php';
    header('Location:' . $uri);
}

$email = filter_input(INPUT_GET, 'email');

// クッキーの存在チェック　なければ不正レクエスト
if (!$_COOKIE['auto_login']) {
    $uri = '../../400_request.php';
    header('Location:' . $uri);
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
    <header>
        <nav class="navbar navbar-expand-lg navbar-light py-4">
            <div class="container">
                <a class="navbar-brand" href="./index.html">
                    <img src="../../../public/img/logo.png" alt="" width="30" height="24" class="d-inline-block
                                align-text-top" style="object-fit: cover;"> Real intentioN
                </a>

                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                    <span class="navbar-toggler-icon"></span>
                </button>

                <div class="collapse navbar-collapse" id="navbarNav">
                    <ul class="navbar-nav ms-auto">
                        <li class="nav-item">
                            <a class="nav-link" href="./src/StaffView/login/login_form.php">職員の方はこちら</a>
                        </li>

                        <li class="nav-item">
                            <a class="login-btn btn px-3" href="./src/UserView/login/login_form.php">ログインはこちら</a>
                        </li>
                    </ul>
                </div>
            </div>
        </nav>
    </header>

    <div class="box d-flex vh-100 align-items-center">
        <div class="container bg-light py-5">
            <div class="row py-5">
                <div class="col-lg-5 mx-auto">
                    <form action="./auth_email.php" method="post">
                        <h1 class="text-center fs-2 mb-5">
                            メールアドレスを認証する
                        </h1>

                        <div class="mb-4">
                            <label class="form-label" for="name">認証トークン</label>
                            <input class="form-control" type="password" name="token">
                        </div>

                        <input type="hidden" name="email" value="<?php h($email); ?>">
                        <input type="hidden" name="csrf_token" value="<?php h($ses_calc->create_csrf_token()); ?>">
                        <input type="hidden" name="email_token" value="<?php h($email_token_check); ?>">

                        <button type="submit" class="login-btn btn px-4">認証する</button>
                    </form>

                    <form class="needs-validation" novalidate action="./auth_email.php" method="POST">
                        <h1 class="text-center fs-2 mb-5">
                            メールアドレスを認証する
                        </h1>

                        <div class="mt-4">
                            <label for="validationCustom02" class="form-label">名前</label>
                            <input type="text" class="form-control" id="validationCustom02" required name="email">

                            <div class="invalid-feedback">
                                <p>メールアドレスを入力してください。</p>
                            </div>
                        </div>

                        <input type="hidden" name="csrf_token" value="<?php h($ses_calc->create_csrf_token()); ?>">

                        <div class="mt-4">
                            <button type="submit" class="login-btn btn px-4">仮登録する</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>


    <footer class="text-center py-2">
        <div class="text-light text-center small">
            © 2020 Toge-company:
            <a class="text-white" target="_blank" href="https://hayate-takeda.xyz/">hayate-takeda.xyz</a>
        </div>
    </footer>

    <script>
        (() => {
            'use strict'
            const forms = document.querySelectorAll('.needs-validation')

            // ループして帰順を防ぐ
            Array.from(forms).forEach(form => {
                form.addEventListener('submit', event => {
                    if (!form.checkValidity()) {
                        event.preventDefault()
                        event.stopPropagation()
                    }

                    form.classList.add('was-validated')
                }, false)
            })
        })()
    </script>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0-beta1/dist/js/bootstrap.bundle.min.js" integrity="sha384-ygbV9kiqUc6oa4msXn9868pTtWMgiQaeYH7/t7LECLbyPA2x65Kgf80OJFdroafW" crossorigin="anonymous">
    </script>
</body>

</html>