<?php
require_once 'includes/init.php';

require_login();

$page_title = 'Thông tin cá nhân';
$userController = new UserController($db_conn);
$user = $userController->profile((int) $_SESSION['user_id']);

$success = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['update_profile'])) {
        $full_name = sanitize($_POST['full_name']);
        $phone = sanitize($_POST['phone']);
        $address = sanitize($_POST['address']);
        
        $result = $userController->updateProfile((int) $_SESSION['user_id'], [
            'full_name' => $full_name,
            'phone' => $phone,
            'address' => $address
        ]);
        if ($result['success']) {
            $success = $result['message'];
            $user = $userController->profile((int) $_SESSION['user_id']);
        } else {
            $error = $result['message'];
        }
    }
    
    if (isset($_POST['change_password'])) {
        $current_password = $_POST['current_password'];
        $new_password = $_POST['new_password'];
        $confirm_password = $_POST['confirm_password'];
        
        $result = $userController->changePassword($user, $current_password, $new_password, $confirm_password);
        if ($result['success']) {
            $success = $result['message'];
        } else {
            $error = $result['message'];
        }
    }
}

include 'includes/header.php';
include 'includes/views/profile.view.php';
include 'includes/footer.php';
