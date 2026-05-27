<?php
try {
    $dbPath = "c:/Users/jorge/Desktop/proyecto maquinas/Asistentes_IA_Maquina_Cima/database/database.sqlite";
    echo "Opening DB: $dbPath\n";
    if (!file_exists($dbPath)) {
        echo "ERROR: DB file does not exist!\n";
        exit(1);
    }
    $pdo = new PDO("sqlite:$dbPath");
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // List tables
    $tables = $pdo->query("SELECT name FROM sqlite_master WHERE type='table'")->fetchAll(PDO::FETCH_COLUMN);
    echo "Tables: " . implode(', ', $tables) . "\n\n";

    if (in_array('users', $tables)) {
        $stmt = $pdo->query("SELECT * FROM users");
        $users = $stmt->fetchAll(PDO::FETCH_ASSOC);
        echo "USERS:\n";
        foreach ($users as $user) {
            echo "ID: {$user['id']} | Name: {$user['name']} | Email: {$user['email']} | Password Hash: {$user['password']}\n";
            $check = password_verify('1234', $user['password']) ? 'VALID (1234)' : 'INVALID (1234)';
            echo "  Verify '1234': $check\n";
        }
    } else {
        echo "ERROR: 'users' table does not exist!\n";
    }
} catch (Exception $e) {
    echo "EXCEPTION: " . $e->getMessage() . "\n";
}
