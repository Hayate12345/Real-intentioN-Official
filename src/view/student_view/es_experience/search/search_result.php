<?php

// セッション開始
session_start();

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

// ログイン情報がない場合リダイレクト
if (!$student_login_data) {
    $uri = '../../../Exception/400_request.php';
    header('Location: ' . $uri);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $search_category = filter_input(INPUT_POST, 'category');
    $search_keyword = filter_input(INPUT_POST, 'keyword');

    // 検索結果を取得
    $search_result = $srh_calc->intern_experience_search($search_category, $search_keyword);
}

// 投稿にいいねする
if (isset($_POST['like'])) {
    $lik_calc->set_post_id($_POST['post_id']);
    $lik_calc->set_student_id($_POST['student_id']);

    // csrfトークンの存在確認と正誤判定
    $csrf_check = $ses_calc->csrf_match_check($_POST['csrf_token']);
    if (!$csrf_check) {
        $uri = '../../../Exception/400_request.php';
        header('Location:' . $uri);
    }

    // csrf_token削除　二重送信対策
    $ses_calc->csrf_token_unset();

    $lik_calc->intern_experience_like();
    $uri = './posts.php';
    header('Location: ' . $uri);
}

// 投稿のいいねを解除する
if (isset($_POST['like_delete'])) {
    $lik_calc->set_post_id($_POST['post_id']);
    $lik_calc->set_student_id($_POST['student_id']);

    // csrfトークンの存在確認と正誤判定
    $csrf_check = $ses_calc->csrf_match_check($_POST['csrf_token']);
    if (!$csrf_check) {
        $uri = '../../../Exception/400_request.php';
        header('Location:' . $uri);
    }

    $lik_calc->intern_experience_like_delete();

    // csrf_token削除　二重送信対策
    $ses_calc->csrf_token_unset();

    $uri = './posts.php';
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
    <link rel="shortcut icon" href="../../../public/img/favicon.ico" type="image/x-icon">
    <title>学生ログイン / 「Real intentioN」</title>
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

        .square_box {
            position: relative;
            max-width: 100px;
            background: #ffb6b9;
            border-radius: 5px;
        }

        .square_box::before {
            content: "";
            display: block;
            padding-bottom: 100%;
        }

        .square_box p {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            font-weight: bold;
        }
    </style>
</head>

<body>
    <header class="sticky-top">
        <nav class="navbar navbar-expand-lg navbar-light py-4">
            <div class="container">
                <a class="navbar-brand" href="./index.html">
                    <img src="../../../../public/img/logo.png" alt="" width="30" height="24" class="d-inline-block
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

                            <div class="row mt-3">
                                <div class="info-left col-lg-2 col-md-2 col-2">
                                    <div class="text-center">
                                        <div class="square_box">
                                            <p>INTERN</p>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-lg-9 col-md-9 col-9">
                                    <p class="fs-5">
                                        <?php h($row['company']) ?><span style="margin: 0 10px;">/</span><?php h($row['field']) ?><span style="margin: 0 10px;">/</span><?php h($row['format']) ?>
                                    </p>

                                    <span><?php h($row['content']) ?></span><br>

                                    <span class="student-review" style="color: #FCCA4D;">
                                        <?php if ($row['ster'] === '星1') : ?>
                                            <i class="bi bi-star-fill"></i>
                                            <i class="bi bi-star"></i>
                                            <i class="bi bi-star"></i>
                                            <i class="bi bi-star"></i>
                                            <i class="bi bi-star"></i>
                                        <?php elseif ($row['ster'] === '星2') : ?>
                                            <i class="bi bi-star-fill"></i>
                                            <i class="bi bi-star-fill"></i>
                                            <i class="bi bi-star"></i>
                                            <i class="bi bi-star"></i>
                                            <i class="bi bi-star"></i>
                                        <?php elseif ($row['ster'] === '星3') : ?>
                                            <i class="bi bi-star-fill"></i>
                                            <i class="bi bi-star-fill"></i>
                                            <i class="bi bi-star-fill"></i>
                                            <i class="bi bi-star"></i>
                                            <i class="bi bi-star"></i>
                                        <?php elseif ($row['ster'] === '星4') : ?>
                                            <i class="bi bi-star-fill"></i>
                                            <i class="bi bi-star-fill"></i>
                                            <i class="bi bi-star-fill"></i>
                                            <i class="bi bi-star-fill"></i>
                                            <i class="bi bi-star"></i>
                                        <?php elseif ($row['ster'] === '星5') : ?>
                                            <i class="bi bi-star-fill"></i>
                                            <i class="bi bi-star-fill"></i>
                                            <i class="bi bi-star-fill"></i>
                                            <i class="bi bi-star-fill"></i>
                                            <i class="bi bi-star-fill"></i>
                                        <?php endif; ?>
                                    </span>
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

                            <div class="mt-4">
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

                            <div class="row mt-4">
                                <div class="col-lg-1 col-md-1 col-1">
                                    <?php
                                    $lik_calc->set_post_id($row['post_id']);
                                    $lik_calc->set_student_id($row['student_id']);

                                    // 未いいねかいいね済みか判定
                                    $like_check = $lik_calc->intern_experience_like_check();

                                    // 投稿についているいいね数を取得
                                    $like_val = $lik_calc->intern_experience_like_count();
                                    ?>
                                    <?php if ($like_check) : ?>
                                        <form action="./posts.php" method="post">
                                            <input type="hidden" name="post_id" value="<?php h($row['post_id']) ?>">
                                            <input type="hidden" name="student_id" value="<?php h($row['student_id']) ?>">
                                            <input type="hidden" name="csrf_token" value="<?php h($ses_calc->create_csrf_token()); ?>">
                                            <button class="btn fs-5" name="like_delete">
                                                <i style="color: red;" class="bi bi-heart-fill"></i>
                                            </button>
                                        </form>
                                    <?php else : ?>
                                        <form action="./posts.php" method="post">
                                            <input type="hidden" name="post_id" value="<?php h($row['post_id']) ?>">
                                            <input type="hidden" name="student_id" value="<?php h($row['student_id']) ?>">
                                            <input type="hidden" name="csrf_token" value="<?php h($ses_calc->create_csrf_token()); ?>">
                                            <button class="btn fs-5" name="like">
                                                <i style="color: red;" class="bi bi-heart"></i>
                                            </button>
                                        </form>
                                    <?php endif; ?>
                                </div>

                                <div class="col-lg-4 col-md-4 col-5 mt-2">
                                    <span class="fs-6">いいね数：<?php h($like_val) ?></span>
                                </div>

                                <div class="col-lg-7 col-md-7 col-6 text-end mt-2">
                                    <?php h($row['name']) ?> ｜ <?php h($row['course_of_study']) ?> ｜ <?php h($row['grade_in_school']) ?>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>


            <div class="side-bar col-md-4 bg-light  h-100" style="margin-top: 100px;">
                <div class="d-flex flex-column flex-shrink-0 p-3 bg-light">
                    <ul class="nav nav-pills flex-column mb-auto">
                        <li class="nav-item">
                            <a href="../staff_information/staff_information.php" class="nav-link link-dark">
                                インターン情報　/ 説明会情報
                            </a>
                        </li>

                        <li class="nav-item">
                            <a href="./view.php" style="background-color: #EB6440;" class="nav-link active" aria-current="page">
                                インターン体験記
                            </a>
                        </li>

                        <li>
                            <a href="./post/post_form.php" class="nav-link link-dark">
                                インターン体験記を新規投稿
                            </a>
                        </li>
                    </ul>

                    <hr>
                    <div class="dropdown">
                        <form action="./search/free_word_search.php" method="post">
                            <div class="input-group">
                                <input type="text" class="form-control" name="keyword" placeholder="フリーワード検索">
                                <button class="btn btn-outline-success" type="submit" id="button-addon2"><i class="fas fa-search"></i>検索</button>
                            </div>
                        </form>

                        <form action="./search/company_search.php" method="post">
                            <div class="input-group mt-4">
                                <input type="text" class="form-control" name="keyword" placeholder="企業名で検索">
                                <button class="btn btn-outline-success" type="submit" id="button-addon2"><i class="fas fa-search"></i>検索</button>
                            </div>
                        </form>

                        <form action="./search/format_search.php" method="post">
                            <div class="input-group mt-4">
                                <select class="form-select" name="keyword" aria-label="Default select example">
                                    <option selected>開催形式で検索</option>
                                    <option value="対面開催">対面開催</option>
                                    <option value="オンライン開催">オンライン開催</option>
                                </select>
                                <button class="btn btn-outline-success" type="submit" id="button-addon2"><i class="fas fa-search"></i>検索</button>
                            </div>
                        </form>

                        <form action="./search/field_search.php" method="post">
                            <div class="input-group mt-4">
                                <select class="form-select" name="keyword" aria-label="Default select example">
                                    <option selected>開催分野で検索</option>
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
                                <button class="btn btn-outline-success" type="submit" id="button-addon2"><i class="fas fa-search"></i>検索</button>
                            </div>
                        </form>
                    </div>

                    <hr>
                    <!-- <div class="dropdown">
                        <a href="#" class="d-flex align-items-center link-dark text-decoration-none dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">
                            <img src="https://github.com/mdo.png" alt="" width="32" height="32" class="rounded-circle me-2">
                            <strong><?php h($userName) ?></strong>
                        </a>
                        <ul class="dropdown-menu text-small shadow">
                            <!-- <li><a class="dropdown-item" href="#">プロフィール</a></li> -->
                    <!-- <li>
                                <hr class="dropdown-divider">
                            </li> -->
                    <li><a class="dropdown-item" href="../logout.php">サインアウト</a></li>
                    </ul>
                </div> -->
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