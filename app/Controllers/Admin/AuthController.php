<?php
// app/Controllers/Admin/AuthController.php

namespace App\Controllers\Admin;

class AuthController
{
    public static function redirect(): void
    {
        if (!empty($_SESSION['admin_logged_in']) && $_SESSION['admin_logged_in'] === true) {
            redirect('/admin/dashboard');
        }

        redirect('/admin/login');
    }

    public static function showLogin(): void
    {
        // Kalau sudah login, langsung ke dashboard
        if (!empty($_SESSION['admin_logged_in'])) {
            redirect('/admin/dashboard');
        }

        view('admin/login');
    }

    public static function login(): void
    {
        $email    = trim($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';

        // Validasi input kosong
        if ($email === '' || $password === '') {
            flash('admin_error', 'Email dan password wajib diisi.');
            redirect('/admin/login');
        }

        // Cari user berdasarkan email, exclude soft-deleted
        $stmt = db()->prepare("
             SELECT id, nama_lengkap, email, password, role, slug_uuid
            FROM users
            WHERE email = :email
              AND deleted_at IS NULL
            LIMIT 1
        ");
        $stmt->execute([':email' => strtolower($email)]);
        $user = $stmt->fetch();

        // Verifikasi user & password
        if (!$user || !password_verify($password, $user['password'])) {
            flash('admin_error', 'Email atau password salah.');
            redirect('/admin/login');
        }

        // Pastikan role admin
        if ($user['role'] !== 'admin') {
            flash('admin_error', 'Akses ditolak.');
            redirect('/admin/login');
        }

        // Regenerate session ID untuk mencegah session fixation attack
        session_regenerate_id(true);

        $_SESSION['admin_logged_in'] = true;
        $_SESSION['admin'] = [
            'id'    => $user['id'],
            'name'  => $user['nama_lengkap'],
            'email' => $user['email'],
            'role'  => $user['role'],
            'slug_uuid' => $user['slug_uuid'],
        ];

        redirect('/admin/dashboard');
    }

    public static function logout(): void
    {
        // Hapus semua session data
        $_SESSION = [];

        // Hapus session cookie
        if (ini_get('session.use_cookies')) {
            $params = session_get_cookie_params();
            setcookie(
                session_name(),
                '',
                time() - 42000,
                $params['path'],
                $params['domain'],
                $params['secure'],
                $params['httponly']
            );
        }

        session_destroy();

        redirect('/admin/login');
    }
}