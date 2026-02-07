<?php

namespace App\Controllers\Customer;

use App\Middleware\CustomerAuth;

class AuthController
{
    public static function loginForm()
    {
        // Redirect if already logged in
        CustomerAuth::requireGuest();

        return view('account/login');
    }

    public static function registerForm()
    {
        // Redirect if already logged in
        CustomerAuth::requireGuest();

        return view('account/register');
    }

    public static function login()
    {
        $email = trim($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';

        if ($email === '' || $password === '') {
            $_SESSION['error'] = 'Email dan password wajib diisi';
            return redirect('/account/login');
        }

        $customers = json_read('customers.json') ?? [];

        foreach ($customers as $c) {
            if (
                strtolower($c['email']) === strtolower($email) &&
                empty($c['deleted_at']) &&
                password_verify($password, $c['password'])
            ) {
                $_SESSION['customer'] = [
                    'uuid' => $c['uuid'],
                    'fullname' => $c['fullname'],
                    'email' => $c['email'],
                    'slug_uuid' => $c['slug_uuid'],
                ];

                // Redirect to intended URL (e.g., /checkout) or dashboard
                CustomerAuth::redirectIntended('/account/dashboard');
                return;
            }
        }

        $_SESSION['error'] = 'Email atau password salah';
        return redirect('/account/login');
    }

    public static function register()
    {
        $fullname = trim($_POST['fullname'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';
        $passwordConfirmation = $_POST['password_confirmation'] ?? '';

        // =========================
        // BASIC VALIDATION
        // =========================
        if ($fullname === '' || $email === '' || $password === '' || $passwordConfirmation === '') {
            $_SESSION['error'] = 'Semua field wajib diisi';
            return redirect('/account/register');
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $_SESSION['error'] = 'Format email tidak valid';
            return redirect('/account/register');
        }

        if (strlen($password) < 6) {
            $_SESSION['error'] = 'Password minimal 6 karakter';
            return redirect('/account/register');
        }

        if ($password !== $passwordConfirmation) {
            $_SESSION['error'] = 'Password dan konfirmasi password tidak sama';
            return redirect('/account/register');
        }

        // =========================
        // LOAD CUSTOMER DATA
        // =========================
        $customers = json_read('customers.json') ?? [];

        foreach ($customers as $c) {
            if (
                strtolower($c['email']) === strtolower($email) &&
                empty($c['deleted_at'])
            ) {
                $_SESSION['error'] = 'Email sudah terdaftar';
                return redirect('/account/register');
            }
        }

        // =========================
        // CREATE CUSTOMER
        // =========================
        $uuid = uuid_v4();
        $slug = slugify($fullname);
        $now = date('Y-m-d H:i:s');

        $customers[] = [
            'uuid' => $uuid,
            'fullname' => $fullname,
            'slug' => $slug,
            'slug_uuid' => $slug . '-' . $uuid,
            'email' => $email,
            'password' => password_hash($password, PASSWORD_DEFAULT),

            'profile_pics' => null,
            'phone_number' => null,
            'alamat_lengkap' => null,
            'provinsi' => null,
            'kabupaten' => null,
            'kota' => null,
            'kode_pos' => null,

            'created_at' => $now,
            'updated_at' => null,
            'deleted_at' => null,
        ];

        json_write('customers.json', $customers);

        // =========================
        // AUTO LOGIN
        // =========================
        $_SESSION['customer'] = [
            'uuid' => $uuid,
            'fullname' => $fullname,
            'email' => $email,
            'slug_uuid' => $slug . '-' . $uuid,
        ];

        // Redirect to intended URL or dashboard
        CustomerAuth::redirectIntended('/account/dashboard');
    }

    public static function logout()
    {
        unset($_SESSION['customer']);
        return redirect('/account/login');
    }
}