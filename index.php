<?php
require_once 'db.php';
require_once 'functions/functions.php';

$main_content = null;
$headline = null;
$c = filter_input(INPUT_GET, 'c', FILTER_SANITIZE_STRING);
switch ($c) {
    case 'participants':
        $main_content = 'content/users.inc.php';
        $headline = 'Participants';
        break;
    case 'chairman':
        $main_content = null;
        $headline = 'Chairman';
        break;
    case 'speakers':
        $main_content = null;
        $headline = 'Speakers';
        break;
    case 'bootstaff':
        $main_content = null;
        $headline = 'Boot Staff';
        break;
    default :
        $main_content = '';
        $headline = null;
        break;
}
?>

<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Backend</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.5.0/font/bootstrap-icons.css" integrity="sha384-tKLJeE1ALTUwtXlaGjJYM3sejfssWdAaWR2s97axw4xkiAdMzQjtOjgcyw0Y50KU" crossorigin="anonymous">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/css/toastr.css" rel="stylesheet" type="text/css"/>
    <link href="css/dashboard.css" rel="stylesheet" type="text/css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js" integrity="sha256-/xUj+3OJU5yExlq6GSYGSHk7tPXikynS7ogEvDej/m4=" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js" type="text/javascript"></script>
</head>
<body>
<nav class="navbar navbar-dark sticky-top bg-dark flex-md-nowrap p-0">
    <div class="navbar-brand ms-4 me-0">
        <a href="<?php echo DOMAIN;?>/participants/index.php"><?php echo 'test' ?></a>
    </div>
    <div class="headline">
        --- <?php echo $headline; ?> ---
    </div>
    <div class="px-3">
        <span class="nav-item text-nowrap">
            <a class="nav-link logout" href="<?php echo URL_ADMIN_LOGIN; ?>?logout=1">Logout</a>
        </span>
    </div>
</nav>

<div class="container-fluid">
    <div class="row">
        <div class="col-1">
            <nav class="sidebar">
                <div class="sidebar-sticky mt-2">
                    <ul class="nav flex-column">
                        <li class="nav-item my-2 mx-2">
                            <a class="nav-link menu_button <?php if ($_GET['c'] == 'participants') {
                                echo 'active';
                            } ?>" href="<?php echo INDEX_LINK; ?>?c=participants"> Participants
                            </a>
                        </li>
                        <li class="nav-item my-2 mx-2">
                            <a class="nav-link menu_button <?php if ($_GET['c'] == 'chairman') {
                                echo 'active';
                            } ?>" href="<?php echo INDEX_LINK; ?>?c=chairman">Chairman
                            </a>
                        </li>
                        <li class="nav-item my-2 mx-2">
                            <a class="nav-link menu_button <?php if ($_GET['c'] == 'speakers') {
                                echo 'active';
                            } ?>" href="<?php echo INDEX_LINK; ?>?c=speakers">Speakers
                            </a>
                        </li>
                        <li class="nav-item my-2 mx-2">
                            <a class="nav-link menu_button <?php if ($_GET['c'] == 'bootstaff') {
                                echo 'active';
                            } ?>" href="<?php echo INDEX_LINK; ?>?c=bootstaff">Boot Staff
                            </a>
                        </li>
                    </ul>
                </div>
            </nav>
        </div>
        <main role="main" class="col-md ml-sm-auto col-lg pt-3 px-4">
            <?php
            include_once($main_content);
            ?>
        </main>
    </div>
</div>
</body>
</html>
