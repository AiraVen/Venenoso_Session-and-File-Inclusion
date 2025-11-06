<?php
require __DIR__ . '/db.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    json_response(['error' => 'Method not allowed'], 405);
}

$data = read_json_body();

$first = trim((string)($data['firstName'] ?? ''));
$last = trim((string)($data['lastName'] ?? ''));
$address = trim((string)($data['address'] ?? ''));
$email = trim((string)($data['email'] ?? ''));
$contact = trim((string)($data['contact'] ?? ''));
$password = (string)($data['password'] ?? '');

// No username field in schema; email is the unique identifier

if ($first === '' || $last === '' || $email === '' || $password === '') {
    json_response(['error' => 'Missing required fields'], 422);
}

$pdo = get_pdo();

// Enforce unique email
$exists = $pdo->prepare('SELECT 1 FROM users WHERE email = :email LIMIT 1');
$exists->execute([':email' => $email]);
if ($exists->fetch()) {
    json_response(['error' => 'Email already exists'], 409);
}

$stmt = $pdo->prepare('INSERT INTO users (email, password_hash, first_name, last_name, contact, address, role)
VALUES (:e, :p, :f, :l, :c, :a, :r)');
$stmt->execute([
    ':e' => $email,
    ':p' => password_hash($password, PASSWORD_DEFAULT),
    ':f' => $first,
    ':l' => $last,
    ':c' => $contact,
    ':a' => $address,
    ':r' => 'customer',
]);

$userId = $pdo->lastInsertId();

json_response(['id' => (int)$userId, 'email' => $email]);


