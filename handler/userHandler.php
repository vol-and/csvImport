<?php
require_once '../db.php';
require_once '../classes/user.class.php';
require_once '../functions/functions.php';

$post = json_decode($_POST['data'], true);

function echoResult($res, $par = null)
{
    if ($res === true) {
        $o['result'] = 'success';
        if ($par == 'email') $o['email'] = 'Invite is sent.';
        if ($par == 'resend') $o['email'] = 'Invite is resented.';
    } else {
        $o['result'] = 'error';
    }
    echo json_encode($o, JSON_FORCE_OBJECT);
}

$userClass = new User($mysqli);
if (isset($post['activity'])) {
    switch ($post['activity']) {
        case 'update':
            echoResult($userClass->updateUserData($post));
            break;
        case 'sendinvite':
            if ($userClass->updateWelcomeMailDate($post['uid'])) {
                echoResult(include_once 'mailHandler.php', 'email');
            }
            break;
        case 'resendinvite':
            echoResult(include_once 'mailHandler.php', 'resend');
            break;
        case 'deactivate':
        case 'activate':
            echoResult($userClass->toggleUserActivity($post));
            break;
        case 'delete':
            echoResult($userClass->deleteUser($post));
            break;
        default:
            echoResult(false);
            break;
    }
}