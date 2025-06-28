<?php
/**
 * Generate Password Hash
 * Simple script to generate the correct password hash for admin123
 */

header('Content-Type: application/json');

$password = 'admin123';

// Generate hash using the same method as in auth_utils.php
$hash = password_hash($password, PASSWORD_ARGON2ID, [
    'memory_cost' => 65536, // 64 MB
    'time_cost' => 4,       // 4 iterations
    'threads' => 3          // 3 threads
]);

// Also generate a simple bcrypt hash as fallback
$bcrypt_hash = password_hash($password, PASSWORD_DEFAULT);

$response = [
    'password' => $password,
    'argon2id_hash' => $hash,
    'bcrypt_hash' => $bcrypt_hash,
    'verify_argon2id' => password_verify($password, $hash),
    'verify_bcrypt' => password_verify($password, $bcrypt_hash),
    'timestamp' => date('Y-m-d H:i:s')
];

echo json_encode($response, JSON_PRETTY_PRINT);
?>
