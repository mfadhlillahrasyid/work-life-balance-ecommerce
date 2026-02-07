<?php

namespace App\Middleware;

class CustomerAuth
{
    /**
     * Check if customer is logged in
     * 
     * @return bool
     */
    public static function check(): bool
    {
        return !empty($_SESSION['customer']);
    }

    /**
     * Get current logged-in customer
     * 
     * @return array|null
     */
    public static function customer(): ?array
    {
        return $_SESSION['customer'] ?? null;
    }

    /**
     * Require authentication
     * 
     * Redirect to login if not authenticated.
     * Save intended URL to redirect back after login.
     * 
     * @param string|null $redirectTo Optional URL to redirect after login
     * @return void
     */
    public static function require(?string $redirectTo = null): void
    {
        if (!self::check()) {
            // Save intended URL
            $intended = $redirectTo ?? $_SERVER['REQUEST_URI'] ?? '/';
            $_SESSION['intended_url'] = $intended;

            // Flash message
            $_SESSION['info'] = 'Silakan login terlebih dahulu untuk melanjutkan';

            // Redirect to login
            redirect('/account/login');
        }
    }

    /**
     * Redirect to intended URL after login
     * 
     * @param string $default Default URL if no intended URL
     * @return void
     */
    public static function redirectIntended(string $default = '/account/dashboard'): void
    {
        $intended = $_SESSION['intended_url'] ?? $default;
        unset($_SESSION['intended_url']);

        redirect($intended);
    }

    /**
     * Require guest (not logged in)
     * 
     * Redirect to dashboard if already logged in
     * 
     * @return void
     */
    public static function requireGuest(): void
    {
        if (self::check()) {
            redirect('/account/dashboard');
        }
    }
}