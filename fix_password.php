<?php
// Script temporaire pour fixer le mot de passe admin
require __DIR__ . '/config/env.php';
loadEnv(__DIR__ . '/.env');
require __DIR__ . '/config/database.php';

$db = Database::connect();
$hash = password_hash('Admin123!', PASSWORD_BCRYPT, ['cost' => 12]);
$stmt = $db->prepare('UPDATE users SET password = ? WHERE email = ?');
$stmt->execute([$hash, 'admin@breakfree.app']);

echo "Hash updated: " . $hash . PHP_EOL;
echo "Verify: " . (password_verify('Admin123!', $hash) ? 'OK' : 'FAIL') . PHP_EOL;
