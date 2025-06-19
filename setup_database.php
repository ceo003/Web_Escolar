<?php
$host = 'localhost';
$username = 'root';
$password = '';

try {
    // Conectar ao MySQL sem selecionar banco de dados
    $pdo = new PDO("mysql:host=$host", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Criar o banco de dados se não existir
    $pdo->exec("CREATE DATABASE IF NOT EXISTS imectec_db");
    
    // Selecionar o banco de dados
    $pdo->exec("USE imectec_db");
    
    // Criar a tabela de mensagens
    $pdo->exec("CREATE TABLE IF NOT EXISTS contact_messages (
        id INT AUTO_INCREMENT PRIMARY KEY,
        name VARCHAR(100) NOT NULL,
        email VARCHAR(100) NOT NULL,
        phone VARCHAR(20) NOT NULL,
        subject VARCHAR(50) NOT NULL,
        message TEXT NOT NULL,
        created_at DATETIME NOT NULL,
        status ENUM('new', 'read', 'replied') DEFAULT 'new'
    )");
    
    echo "Banco de dados e tabela criados com sucesso!";
    
} catch(PDOException $e) {
    echo "Erro: " . $e->getMessage();
}
?> 