<?php

$host = getenv('DB_HOST') ?: '172.17.0.1';
$port = getenv('DB_PORT') ?: '5432';
$db   = getenv('DB_DATABASE') ?: 'postgres';
$user = getenv('DB_USERNAME') ?: 'postgres';
$pass = getenv('DB_PASSWORD') ?: 'postgres';

$dsn = "pgsql:host=$host;port=$port;dbname=$db";

try {
    $pdo = new PDO($dsn, $user, $pass, [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]);
    echo "Conexão bem-sucedida\n";
} catch (PDOException $e) {
    echo "Erro detalhado: " . $e->getMessage() . "\n";
    exit(1);
}
