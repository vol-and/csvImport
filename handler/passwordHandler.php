<?php
require_once('../db.php');
if(isset($_GET['code'])) {
    $temp_hash = $_GET['code'];
    $verifyQuery = $mysqli->prepare("SELECT id FROM " . TABLE_NAME . " WHERE temp_hash = ?");
    $verifyQuery->bind_param("s", $temp_hash);
    $verifyQuery->execute();
    $verifyQuery->store_result();
    if($verifyQuery->num_rows == 0) {
        header("Location: https://www.google.com"); // TODO change path to start page if not in DB
        exit();
    }

    if(isset($_POST['change'])) {
        $email = strtolower(trim($_POST['email']));
        $new_password = trim($_POST['new_password']);
        $options = [
            'cost' => 14,
        ];
        $encoded_password = password_hash($new_password, PASSWORD_BCRYPT, $options);
        $changeQuery = $mysqli->prepare("UPDATE " . TABLE_NAME . " SET password_hash = ?, first_login = NOW()
                                               WHERE email = ?");
        $changeQuery->bind_param("ss", $encoded_password, $email);

        if($changeQuery->execute()) {
            header("Location: ../content/success.html");
            exit();
        }
    }
}
else {
    header("Location: ../index.php"); // TODO change path to start page if no code
    exit();
}