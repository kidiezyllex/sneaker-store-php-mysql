<?php
require_once 'includes/init.php';

$page_title = 'Đăng nhập';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = sanitize($_POST['email']);
    $password = $_POST['password'];
    
    if (empty($email) || empty($password)) {
        $error = 'Vui lòng điền đầy đủ thông tin';
    } else {
        $auth = new AuthController($db_conn);
        $result = $auth->login($email, $password);
        if ($result['success']) {
            if (($result['role'] ?? '') === 'admin') {
                redirect('/admin/index.php');
            }
            redirect('/index.php');
        } else {
            $error = $result['message'];
        }
    }
}

include 'includes/header.php';
include 'includes/views/login.view.php';
include 'includes/footer.php';
