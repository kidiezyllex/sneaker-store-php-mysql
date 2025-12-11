<?php

class UserController extends Controller
{
    public function profile(int $userId): ?array
    {
        $model = new UserModel($this->db);
        return $model->findById($userId);
    }

    public function updateProfile(int $userId, array $data): array
    {
        if (empty($data['full_name'])) {
            return ['success' => false, 'message' => 'Họ tên không được để trống'];
        }

        $model = new UserModel($this->db);
        $ok = $model->updateProfile($userId, $data);
        if ($ok) {
            $_SESSION['full_name'] = $data['full_name'];
        }
        return ['success' => $ok, 'message' => $ok ? 'Cập nhật thông tin thành công!' : 'Có lỗi xảy ra'];
    }

    public function changePassword(array $user, string $current, string $new, string $confirm): array
    {
        if (!password_verify($current, $user['password'])) {
            return ['success' => false, 'message' => 'Mật khẩu hiện tại không đúng'];
        }
        if (strlen($new) < 6) {
            return ['success' => false, 'message' => 'Mật khẩu mới phải có ít nhất 6 ký tự'];
        }
        if ($new !== $confirm) {
            return ['success' => false, 'message' => 'Mật khẩu xác nhận không khớp'];
        }

        $model = new UserModel($this->db);
        $hashed = password_hash($new, PASSWORD_DEFAULT);
        $ok = $model->updatePassword((int) $user['id'], $hashed);
        return ['success' => $ok, 'message' => $ok ? 'Đổi mật khẩu thành công!' : 'Có lỗi xảy ra'];
    }
}

