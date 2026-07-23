<?php
$hash = password_hash('admin@123', PASSWORD_BCRYPT, ['cost' => 12]);
file_put_contents(__DIR__ . '/fix_password.sql',
    "UPDATE users SET password='" . addslashes($hash) . "', email_verified_at=NOW() WHERE email='admin@crewrent.in';"
);
file_put_contents(__DIR__ . '/hash_check.txt', "Hash: " . $hash . "\n");
echo "done";
