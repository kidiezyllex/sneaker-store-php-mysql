<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/functions.php';

/**
 * Simple autoloader for controllers, models và core classes.
 */
spl_autoload_register(function ($class) {
    $paths = [
        __DIR__ . '/core/' . $class . '.php',
        __DIR__ . '/models/' . $class . '.php',
        __DIR__ . '/controllers/' . $class . '.php',
    ];

    foreach ($paths as $path) {
        if (file_exists($path)) {
            require_once $path;
            return;
        }
    }
});

// Khởi tạo/đảm bảo schema mới (wishlist, coupon, cột đơn hàng) khi cần
require_once __DIR__ . '/schema.php';

