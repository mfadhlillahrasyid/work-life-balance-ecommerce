<?php
// app/Controllers/Customer/AuthController.php

namespace App\Controllers\Customer;

use App\Middleware\CustomerAuth;

class AuthController
{
    public static function loginForm(): void
    {
        CustomerAuth::requireGuest();
        view('account/login');
    }

    public static function registerForm(): void
    {
        CustomerAuth::requireGuest();
        view('account/register');
    }

    // =========================================================================
    // LOGIN
    // =========================================================================
    public static function login(): void
    {
        $email    = trim($_POST['email']    ?? '');
        $password = $_POST['password']      ?? '';

        if ($email === '' || $password === '') {
            $_SESSION['error'] = 'Email dan password wajib diisi';
            redirect('/account/login');
        }

        $stmt = db()->prepare("
            SELECT id, slug_uuid, fullname, email, password
            FROM customers
            WHERE LOWER(email) = LOWER(:email)
              AND deleted_at IS NULL
            LIMIT 1
        ");
        $stmt->execute([':email' => $email]);
        $customer = $stmt->fetch();

        if (!$customer || !password_verify($password, $customer['password'])) {
            $_SESSION['error'] = 'Email atau password salah';
            redirect('/account/login');
        }

        $_SESSION['customer'] = [
            'slug_uuid' => $customer['slug_uuid'],
            'fullname'  => $customer['fullname'],
            'email'     => $customer['email'],
        ];

        CustomerAuth::redirectIntended('/account/dashboard');
    }

    // =========================================================================
    // REGISTER
    // =========================================================================
    public static function register(): void
    {
        $fullname             = trim($_POST['fullname']              ?? '');
        $email                = trim($_POST['email']                 ?? '');
        $password             = $_POST['password']                   ?? '';
        $passwordConfirmation = $_POST['password_confirmation']      ?? '';

        // ── Validasi ──────────────────────────────────────────────────────────
        if ($fullname === '' || $email === '' || $password === '' || $passwordConfirmation === '') {
            $_SESSION['error'] = 'Semua field wajib diisi';
            redirect('/account/register');
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $_SESSION['error'] = 'Format email tidak valid';
            redirect('/account/register');
        }

        if (strlen($password) < 6) {
            $_SESSION['error'] = 'Password minimal 6 karakter';
            redirect('/account/register');
        }

        if ($password !== $passwordConfirmation) {
            $_SESSION['error'] = 'Password dan konfirmasi password tidak sama';
            redirect('/account/register');
        }

        // ── Cek email duplikat ────────────────────────────────────────────────
        $stmtCheck = db()->prepare("
            SELECT id FROM customers
            WHERE LOWER(email) = LOWER(:email) AND deleted_at IS NULL
            LIMIT 1
        ");
        $stmtCheck->execute([':email' => $email]);

        if ($stmtCheck->fetch()) {
            $_SESSION['error'] = 'Email sudah terdaftar';
            redirect('/account/register');
        }

        // ── Insert ────────────────────────────────────────────────────────────
        $uuid     = uuid_v4();
        $slug     = slugify($fullname);
        $slugUuid = $slug . '-' . $uuid;
        $now      = date('Y-m-d H:i:s');

        $stmt = db()->prepare("
            INSERT INTO customers
                (slug_uuid, fullname, slug, email, password, created_at)
            VALUES
                (:slug_uuid, :fullname, :slug, :email, :password, :created_at)
        ");
        $stmt->execute([
            ':slug_uuid'  => $slugUuid,
            ':fullname'   => $fullname,
            ':slug'       => $slug,
            ':email'      => $email,
            ':password'   => password_hash($password, PASSWORD_DEFAULT),
            ':created_at' => $now,
        ]);

        // ── Auto login ────────────────────────────────────────────────────────
        $_SESSION['customer'] = [
            'slug_uuid' => $slugUuid,
            'fullname'  => $fullname,
            'email'     => $email,
        ];

        CustomerAuth::redirectIntended('/account/dashboard');
    }

    // =========================================================================
    // LOGOUT
    // =========================================================================
    public static function logout(): void
    {
        unset($_SESSION['customer']);
        redirect('/account/login');
    }
}