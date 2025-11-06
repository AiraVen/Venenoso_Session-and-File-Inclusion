<?php

class Session {
    private static $instance = null;
    private const SESSION_NAME = 'NETHSHOP_SESSION';
    private const SESSION_LIFETIME = 3600; // 1 hour

    private function __construct() {
        if (session_status() === PHP_SESSION_NONE) {
            // Configure secure session settings
            ini_set('session.use_strict_mode', 1);
            ini_set('session.use_only_cookies', 1);
            ini_set('session.cookie_httponly', 1);
            ini_set('session.cookie_secure', isset($_SERVER['HTTPS']));
            ini_set('session.cookie_samesite', 'Lax');
            ini_set('session.gc_maxlifetime', self::SESSION_LIFETIME);

            session_name(self::SESSION_NAME);
            session_start();
        }

        // Regenerate session ID periodically to prevent fixation
        if (!isset($_SESSION['last_regeneration'])) {
            $this->regenerateId();
        } elseif (time() - $_SESSION['last_regeneration'] > 1800) { // 30 minutes
            $this->regenerateId();
        }
    }

    // Prevent cloning of the instance
    private function __clone() {}

    // Get the singleton instance
    public static function getInstance(): Session {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    // Regenerate session ID
    private function regenerateId(): void {
        session_regenerate_id(true);
        $_SESSION['last_regeneration'] = time();
    }

    // Set a session value
    public function set(string $key, $value): void {
        $_SESSION[$key] = $value;
    }

    // Get a session value
    public function get(string $key, $default = null) {
        return $_SESSION[$key] ?? $default;
    }

    // Remove a session value
    public function remove(string $key): void {
        unset($_SESSION[$key]);
    }

    // Check if a session value exists
    public function has(string $key): bool {
        return isset($_SESSION[$key]);
    }

    // Set user data and mark as authenticated
    public function login(array $userData): void {
        $this->set('user', $userData);
        $this->set('authenticated', true);
        $this->set('login_time', time());
    }

    // Clear user session data
    public function logout(): void {
        // Clear all session data
        $_SESSION = [];

        // Clear session cookie
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

        // Destroy session
        session_destroy();
    }

    // Check if user is logged in
    public function isAuthenticated(): bool {
        return $this->get('authenticated', false) === true;
    }

    // Get current user data
    public function getUser(): ?array {
        return $this->get('user');
    }

    // Get session ID
    public function getId(): string {
        return session_id();
    }

    // Flash message functionality
    public function setFlash(string $key, $message): void {
        $_SESSION['flash'][$key] = $message;
    }

    public function getFlash(string $key, $default = null) {
        $message = $_SESSION['flash'][$key] ?? $default;
        unset($_SESSION['flash'][$key]);
        return $message;
    }

    public function hasFlash(string $key): bool {
        return isset($_SESSION['flash'][$key]);
    }
}