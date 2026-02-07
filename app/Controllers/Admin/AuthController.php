<?php

namespace App\Controllers\Admin;

class AuthController
{
    public static function redirect()
    {
        if (!empty($_SESSION['admin_logged_in'])) {
            return redirect('/admin/dashboard');
        }

        return redirect('/admin/login');
    }

    public static function showLogin()
    {
        return view('admin/login');
    }

    public static function login()
    {
        $email = $_POST['email'] ?? '';
        $password = $_POST['password'] ?? '';

        $users = json_read('users.json');

        foreach ($users as $user) {
            if (
                empty($user['deleted_at']) &&
                $user['email'] === $email &&
                password_verify($password, $user['password'])
            ) {
                session_regenerate_id(true);

                $_SESSION['admin_logged_in'] = true;
                $_SESSION['admin'] = [
                    'id' => $user['id'],
                    'name' => $user['nama_lengkap'],
                    'email' => $user['email'],
                ];

                session_write_close();

                return redirect('/admin/dashboard');
            }
        }

        $_SESSION['admin_error'] = 'Email atau password salah';

        return redirect('/admin/login');
    }

    public static function logout()
    {
        session_start();
        $_SESSION = [];
        session_destroy();
        session_regenerate_id(true);

        return redirect('/admin/login');
    }
}
