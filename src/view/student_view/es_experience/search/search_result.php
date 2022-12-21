<?php

// セッション開始
session_start();
ob_start();

// 外部ファイルのインポート
require_once '../../../../../class/Session_calc.php';
require_once '../../../../../class/Register_calc.php';
require_once '../../../../../class/Validation_calc.php';
require_once '../../../../../function/functions.php';
require_once '../../../../../class/View_calc.php';
require_once '../../../../../class/Like_calc.php';
require_once '../../../../../class/Search_calc.php';


// インスタンス化
$ses_calc = new Session();
$val_calc = new ValidationCheck();
$rgs_calc = new Register();
$viw_calc = new View();
$lik_calc = new Like();
$srh_calc = new Search();

// ログインチェック
$student_login_data = $ses_calc->student_login_check();

// ユーザIDを抽出
foreach ($student_login_data as $row) {
    $user_id = $row['student_id'];
}

// ユーザ名を抽出
foreach ($student_login_data as $row) {
    $user_name = $row['name'];
}

// ログイン情報がない場合リダイレクト
if (!$student_login_data) {
    $uri = '../../../Exception/400_request.php';
    header('Location: ' . $uri);
}

// POSTリクエストを受け取る
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // 送信された値を受け取る
    $search_category = filter_input(INPUT_POST, 'category');
    $search_keyword = filter_input(INPUT_POST, 'keyword');

    // 検索結果を取得
    $search_result = $srh_calc->es_experience_search($search_category, $search_keyword);
}

// POSTリクエストがlikeだったら投稿にいいねをする
if (isset($_POST['like'])) {

    // プロパティに値をセット
    $lik_calc->set_post_id($_POST['post_id']);
    $lik_calc->set_student_id($_POST['student_id']);

    // csrfトークンの存在確認
    $csrf_check = $ses_calc->csrf_match_check($_POST['csrf_token']);

    // csrfトークンの正誤判定
    if (!$csrf_check) {
        $uri = '../../../Exception/400_request.php';
        header('Location:' . $uri);
    }

    // 投稿にいいねをする
    $lik_calc->intern_experience_like();

    // csrf_token削除　二重送信対策
    $ses_calc->csrf_token_unset();
    $uri = '../posts.php';
    header('Location: ' . $uri);
}

// POSTリクエストがlike_deleteだった場合投稿のいいねを解除する
if (isset($_POST['like_delete'])) {

    // プロパティに値をセット
    $lik_calc->set_post_id($_POST['post_id']);
    $lik_calc->set_student_id($_POST['student_id']);

    // csrfトークンの存在確認
    $csrf_check = $ses_calc->csrf_match_check($_POST['csrf_token']);

    // csrfトークンの正誤判定
    if (!$csrf_check) {
        $uri = '../../../Exception/400_request.php';
        header('Location:' . $uri);
    }

    // 投稿のいいねを解除
    $lik_calc->intern_experience_like_delete();

    // csrf_token削除　二重送信対策
    $ses_calc->csrf_token_unset();
    $uri = '../posts.php';
    header('Location: ' . $uri);
}

?>

<!DOCTYPE html>
<html lang="ja">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0-beta1/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-giJF6kkoqNQ00vy+HMDP7azOuL0xtbfIcaT9wjKHr8RbDVddVHyTfAAsrekwKmP1" crossorigin="anonymous" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.8.0/font/bootstrap-icons.css">
    <link rel="shortcut icon" href="../../../../../public/img/favicon.ico" type="image/x-icon">
    <title>ES体験記を検索 /「Real intentioN」</title>
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
    <header class="sticky-top">
        <nav class="navbar navbar-expand-lg navbar-light py-4">
            <div class="container">
                <a class="navbar-brand" href="">
                    <img src="../../../../../public/img/logo.png" alt="" width="30" height="24" class="d-inline-block
                            align-text-top" style="object-fit: cover;"> Real intentioN
                </a>
            </div>
        </nav>
    </header>

    <div class="container my-5">
        <div class="row">
            <div class="col-lg-8 col-md-12 col-12">
                <?php if (is_array($search_result) || is_object($search_result)) : ?>
                    <?php foreach ($search_result as $row) : ?>
                        <div class="intern-contents mb-5 px-4 py-4 bg-light">
                            <div class="row mt-2">
                                <div class="info-left col-lg-2 col-md-2 col-4">
                                    <div class="text-center">
                                        <div class="ratio ratio-1x1" style="background-color: #bbded6; border-radius: 5px;">
                                            <div class="fs-5 text-light fw-bold d-flex align-items-center justify-content-center">
                                                ES
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-lg-9 col-md-9 col-7">
                                    <p class="fs-5 fw-bold">
                                        <?php h($row['company']) ?><span style="margin: 0 10px;">/</span><?php h($row['field']) ?>
                                    </p>
                                </div>

                                <div class="info-right col-lg-1 col-md-1 col-1">
                                    <div class="text-end">
                                        <div class="btn-group">
                                            <?php if ($user_id == $row['student_id']) : ?>
                                                <div class="btn-group dropstart" role="group">
                                                    <button type="button" class="btn btn-secondary dropdown-toggle dropdown-toggle-split" data-bs-toggle="dropdown" aria-expanded="false">
                                                    </button>
                                                    <ul class="dropdown-menu dropdown-menu-dark">
                                                        <li><a href="./delete/delete.php?post_id=<?php h($row['post_id']) ?>" class="dropdown-item">削除</a></li>

                                                        <li><a class="dropdown-item" href="./update/update_form.php?post_id=<?php h($row['post_id']) ?>">編集</a>
                                                        </li>
                                                    </ul>
                                                </div>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="mt-4">
                                <div class="row">
                                    <div class="col-lg-1 col-md-1 col-1">
                                        <div class="text-end">
                                            <span style="color: blue;" class="fw-bold">Q.</span>
                                        </div>
                                    </div>

                                    <div class="col-lg-11 col-md-11 col-11 fw-bold">
                                        <div class="text-start">
                                            <span>
                                                <?php h($row['question']) ?>
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="mt-2">
                                <div class="row">
                                    <div class="col-lg-1 col-md-1 col-1">
                                        <div class="text-end">
                                            <span style="color: red; font-weight: bold;">A.</span>
                                        </div>
                                    </div>

                                    <div class="col-lg-11 col-md-11 col-11">
                                        <div class="text-start">
                                            <span>
                                                <?php echo preg_replace('/\n/', "<br>",  $row['answer']); ?>
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="row mt-3">
                                <div class="col-lg-1 col-md-1 col-2">
                                    <?php $lik_calc->set_post_id($row['post_id']); ?>
                                    <?php $lik_calc->set_student_id($row['student_id']); ?>
                                    <?php $like_check = $lik_calc->intern_experience_like_check(); ?>
                                    <?php $like_val = $lik_calc->intern_experience_like_count(); ?>

                                    <?php if ($like_check) : ?>
                                        <form action="./search_result.php" method="post">
                                            <input type="hidden" name="post_id" value="<?php h($row['post_id']) ?>">
                                            <input type="hidden" name="student_id" value="<?php h($row['student_id']) ?>">
                                            <input type="hidden" name="csrf_token" value="<?php h($ses_calc->create_csrf_token()); ?>">
                                            <button class="btn fs-5" name="like_delete">
                                                <i style="color: red;" class="bi bi-heart-fill"></i>
                                            </button>
                                        </form>
                                    <?php else : ?>
                                        <form action="./search_result.php" method="post">
                                            <input type="hidden" name="post_id" value="<?php h($row['post_id']) ?>">
                                            <input type="hidden" name="student_id" value="<?php h($row['student_id']) ?>">
                                            <input type="hidden" name="csrf_token" value="<?php h($ses_calc->create_csrf_token()); ?>">
                                            <button class="btn fs-5" name="like">
                                                <i style="color: red;" class="bi bi-heart"></i>
                                            </button>
                                        </form>
                                    <?php endif; ?>
                                </div>

                                <div class="col-lg-4 col-md-4 col-6 mt-2">
                                    <span class="fs-6">いいね数：<?php h($like_val) ?></span>
                                </div>

                                <div class="col-lg-7 col-md-7 col-12 text-end mt-2">
                                    <?php h($row['name']) ?> ｜ <?php h($row['course_of_study']) ?> ｜ <?php h($row['grade_in_school']) ?>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>

            <div class="side-bar col-md-12 col-12 col-lg-4 bg-light h-100">
                <div class="d-flex flex-column flex-shrink-0 p-3 bg-light">
                    <ul class="nav nav-pills flex-column mb-auto">
                        <li class="nav-item">
                            <a href="../staff_information/staff_information.php" class="nav-link link-dark">
                                インターンシップ情報
                            </a>
                        </li>

                        <li class="nav-item">
                            <a href="../staff_information/staff_information.php" class="nav-link link-dark">
                                会社説明会情報
                            </a>
                        </li>

                        <li class="nav-item">
                            <a href="../staff_information/staff_information.php" class="nav-link link-dark">
                                キャリアセンターからのお知らせ
                            </a>
                        </li>

                        <li class="nav-item">
                            <a href="../posts.php" class="nav-link link-dark">
                                インターンシップ体験記
                            </a>
                        </li>

                        <li>
                            <a href="./post/post_form.php" class="nav-link link-dark">
                                ES体験記
                            </a>
                        </li>

                        <li>
                            <a href="./post/post_form.php" class="nav-link link-dark">
                                インターンシップ体験記を投稿
                            </a>
                        </li>

                        <li>
                            <a href="./post/post_form.php" class="nav-link link-dark">
                                ES体験記を投稿
                            </a>
                        </li>
                    </ul>

                    <hr>

                    <div class="dropdown">
                        <div class="mb-4">
                            <form action="../search/search_result.php" method="post">
                                <div class="input-group">
                                    <input type="text" class="form-control" name="keyword" placeholder="フリーワード検索">
                                    <input type="hidden" name="category" value="answer">
                                    <button class="btn btn-outline-success" type="submit" id="button-addon2"><i class="bi bi-search"></i></button>
                                </div>
                            </form>
                        </div>

                        <div class="mb-4">
                            <form action="../search/search_result.php" method="post">
                                <div class="input-group">
                                    <input type="text" class="form-control" name="keyword" placeholder="企業名で検索">
                                    <input type="hidden" name="category" value="company">
                                    <button class="btn btn-outline-success" type="submit" id="button-addon2"><i class="bi bi-search"></i></button>
                                </div>
                            </form>
                        </div>

                        <div class="mb-4">
                            <form action="../search/search_result.php" method="post">
                                <div class="input-group">
                                    <select class="form-select" name="keyword" aria-label="Default select example">
                                        <option selected>開催形式で検索</option>
                                        <option value="対面開催">対面開催</option>
                                        <option value="オンライン開催">オンライン開催</option>
                                    </select>
                                    <input type="hidden" name="category" value="format">
                                    <button class="btn btn-outline-success" type="submit" id="button-addon2"><i class="bi bi-search"></i></button>
                                </div>
                            </form>
                        </div>

                        <div class="mb-4">
                            <form action="../search/search_result.php" method="post">
                                <div class="input-group">
                                    <select class="form-select" name="keyword" aria-label="Default select example">
                                        <option selected>職種分野で検索</option>
                                        <option value="IT分野">IT分野</option>
                                        <option value="ゲームソフト分野">ゲームソフト分野</option>
                                        <option value="ハード分野">ハード分野</option>
                                        <option value="ビジネス分野">ビジネス分野</option>
                                        <option value="CAD分野">CAD分野</option>
                                        <option value="グラフィックス分野">グラフィックス分野</option>
                                        <option value="サウンド分野">サウンド分野</option>
                                        <option value="日本語分野">日本語分野</option>
                                        <option value="国際コミュニケーション分野">国際コミュニケーション分野</option>
                                    </select>
                                    <input type="hidden" name="category" value="field">
                                    <button class="btn btn-outline-success" type="submit" id="button-addon2"><i class="bi bi-search"></i></button>
                                </div>
                            </form>
                        </div>
                    </div>

                    <hr>

                    <div class="dropdown">
                        <a href="#" class="d-flex align-items-center link-dark text-decoration-none dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">
                            <img src="https://github.com/mdo.png" alt="" width="32" height="32" class="rounded-circle me-2">
                            <strong><?php h($user_name) ?></strong>
                        </a>
                        <ul class="dropdown-menu text-small shadow">
                            <li><a class="dropdown-item" href="#">プロフィール</a></li>
                            <li>
                                <hr class="dropdown-divider">
                            </li>
                            <li><a class="dropdown-item" href="../logout.php">サインアウト</a></li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <footer class="text-center py-3">
        <div class="text-light text-center small">
            &copy; 2022 Toge-Company, Inc
            <a class="text-white" target="_blank" href="https://hayate-takeda.xyz/">hayate-takeda.xyz</a>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0-beta1/dist/js/bootstrap.bundle.min.js" integrity="sha384-ygbV9kiqUc6oa4msXn9868pTtWMgiQaeYH7/t7LECLbyPA2x65Kgf80OJFdroafW" crossorigin="anonymous">
    </script>
</body>

</html>