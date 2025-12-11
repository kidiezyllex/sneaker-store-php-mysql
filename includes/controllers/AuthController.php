<?php

class AuthController extends Controller
{
    public function login(string $email, string $password): array
    {
        $userModel = new UserModel($this->db);
        $user = $userModel->findByEmail($email);

        if (!$user || !password_verify($password, $user['password'])) {
            return ['success' => false, 'message' => 'Email hoặc mật khẩu không đúng'];
        }

        if ($user['status'] === 'locked') {
            return ['success' => false, 'message' => 'Tài khoản của bạn đã bị khóa'];
        }

        $_SESSION['user_id'] = $user['id'];
        $_SESSION['email'] = $user['email'];
        $_SESSION['full_name'] = $user['full_name'];
        $_SESSION['role'] = $user['role'];

        return ['success' => true, 'role' => $user['role']];
    }

    public function register(array $data): array
    {
        $userModel = new UserModel($this->db);

        if ($userModel->findByEmail($data['email'])) {
            return ['success' => false, 'message' => 'Email đã được sử dụng'];
        }

        $hashed = password_hash($data['password'], PASSWORD_DEFAULT);
        $ok = $userModel->create([
            'email' => $data['email'],
            'password' => $hashed,
            'full_name' => $data['full_name'],
            'phone' => $data['phone'] ?? null,
            'address' => $data['address'] ?? null,
        ]);

        return ['success' => $ok, 'message' => $ok ? 'Đăng ký thành công! Vui lòng đăng nhập.' : 'Có lỗi xảy ra, vui lòng thử lại'];
    }
}

