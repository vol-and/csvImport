<?php
require_once '../db.php';
require_once '../classes/user.class.php';
require_once '../functions/functions.php';

session_start();
$users = new User($mysqli);
$cols_with_code = 5;
$cols_without_code = 4;
$event = null;
$message = null;

if (isset($_POST['upload-file']) && isset($_FILES['file'])) {
    $f = fopen($_FILES['file']['tmp_name'], 'r+');
    if (detectCsvDelimiter($f) == ',') {
        $csvObject = createObjectFromCsv($f);
        $pass = checkColumnsQuantity($csvObject->numcols, COLS_QUANTITY);
//        $pass_with = checkColumnsQuantity($csvObject->numcols, $cols_with_code);
        if ($pass) {
            $result = $users->insertNewUsersCSV($csvObject->array);
            if ($result == 0) {
                $text = '';
            } else if ($result == 1) {
                $text = ' has been successfully added';
            } else {
                $text = ' have been successfully added';
            }
            $response = '<div class="alert alert-success alert-dismissible fade show" role="alert"> 
                        <i class="bi bi-check2-all"></i>
                        <span class="badge bg-light text-dark">' . $result . ' new users </span>' .
                $text
                . '<button type="button" class="btn-close float-end" aria-label="Close" data-bs-dismiss="alert">
                        </button></div>';
            $_SESSION['response'] = $response;
            unset($_POST['upload-file']);
            unset($_FILES['file']);
            header('Location: ../index.php?c=participants');
            exit();
        } else {
            $response = '<div class="alert alert-danger alert-dismissible fade show" role="alert"><i class="fa fa-warning"></i>
                        There are more or less than ' . COLS_QUANTITY . ' columns in your file.
                    <span type="button" class="close" data-dismiss="alert">&times;</span></div>';
            $_SESSION['response'] = $response;
            unset($_POST['upload-file']);
            unset($_FILES['file']);
            header('Location: ../index.php?c=participants');
            exit();
        }
    } else {
        $response = '<div class="alert alert-danger alert-dismissible fade show" role="alert"><i class="fa fa-warning"></i>
                        Use "," as delimiter in your csv file.
                    <span type="button" class="close" data-dismiss="alert">&times;</span></div>';
        $_SESSION['response'] = $response;
        unset($_POST['upload-file']);
        unset($_FILES['file']);
        header('Location: ../index.php?c=participants');
        exit();
    }
}