<?php
// Simple PDO connection helper for the 'eshop' database
// Adjust credentials if you changed XAMPP defaults

$DB_HOST = '127.0.0.1';
$DB_PORT = '3306';
$DB_NAME = 'eshop';
$DB_USER = 'root';
$DB_PASS = '12131440763';

function get_pdo(){
    static $pdo = null;
    if ($pdo !== null) return $pdo;
    global $DB_HOST, $DB_PORT, $DB_NAME, $DB_USER, $DB_PASS;

    $host = getenv('DB_HOST') ?: $DB_HOST;
    $port = getenv('DB_PORT') ?: $DB_PORT;
    $name = getenv('DB_NAME') ?: $DB_NAME;
    $user = getenv('DB_USER') ?: $DB_USER;
    $pass = getenv('DB_PASS');
    if ($pass === false || $pass === null) $pass = $DB_PASS;

    $dsn = 'mysql:host=' . $host . ';port=' . $port . ';dbname=' . $name . ';charset=utf8mb4';
    $options = [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES => false,
    ];

    // Try primary credentials
    try {
        $pdo = new PDO($dsn, $user, $pass, $options);
        return $pdo;
    } catch (Throwable $e) {
        // Fallbacks for typical XAMPP setups: blank password and/or localhost
        if ($user === 'root') {
            try {
                $pdo = new PDO($dsn, $user, '', $options);
                return $pdo;
            } catch (Throwable $e2) {
                // try localhost host fallback
                $dsn2 = 'mysql:host=localhost;port=' . $port . ';dbname=' . $name . ';charset=utf8mb4';
                $pdo = new PDO($dsn2, $user, '', $options);
                return $pdo;
            }
        }
        throw $e;
    }
}

function json_response($data, $status = 200){
    http_response_code($status);
    header('Content-Type: application/json');
    echo json_encode($data);
    exit;
}

function read_json_body(){
    $raw = file_get_contents('php://input');
    $json = json_decode($raw, true);
    return is_array($json) ? $json : [];
}


