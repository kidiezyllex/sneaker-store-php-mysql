<?php
require_once 'includes/init.php';

$home = new HomeController($db_conn);
$data = $home->index(is_logged_in() ? (int) $_SESSION['user_id'] : null);
extract($data);

include __DIR__ . '/includes/views/index.view.php';
