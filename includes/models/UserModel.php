<?php

class UserModel extends Model
{
    public function findByEmail(string $email): ?array
    {
        $stmt = $this->db->prepare("SELECT * FROM users WHERE email = :email");
        $stmt->execute([':email' => $email]);
        $user = $stmt->fetch();
        return $user ?: null;
    }

    public function findById(int $id): ?array
    {
        $stmt = $this->db->prepare("SELECT * FROM users WHERE id = :id");
        $stmt->execute([':id' => $id]);
        $user = $stmt->fetch();
        return $user ?: null;
    }

    public function create(array $data): bool
    {
        $stmt = $this->db->prepare("
            INSERT INTO users (email, password, full_name, phone, address, role)
            VALUES (:email, :password, :full_name, :phone, :address, 'customer')
        ");
        return $stmt->execute([
            ':email' => $data['email'],
            ':password' => $data['password'],
            ':full_name' => $data['full_name'],
            ':phone' => $data['phone'] ?? null,
            ':address' => $data['address'] ?? null,
        ]);
    }

    public function updateProfile(int $id, array $data): bool
    {
        $stmt = $this->db->prepare("
            UPDATE users SET full_name = :full_name, phone = :phone, address = :address
            WHERE id = :id
        ");
        return $stmt->execute([
            ':full_name' => $data['full_name'],
            ':phone' => $data['phone'] ?? null,
            ':address' => $data['address'] ?? null,
            ':id' => $id,
        ]);
    }

    public function updatePassword(int $id, string $hashed): bool
    {
        $stmt = $this->db->prepare("UPDATE users SET password = :password WHERE id = :id");
        return $stmt->execute([':password' => $hashed, ':id' => $id]);
    }
}

