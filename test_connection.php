<?php
// Configurações do banco de dados
$host = 'localhost';
$dbname = 'imectec_db';
$username = 'root';
$password = '';

try {
    // Tenta conectar ao MySQL sem selecionar banco de dados
    $pdo = new PDO("mysql:host=$host", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    echo "Conexão com MySQL bem sucedida!<br>";
    
    // Verifica se o banco de dados existe
    $stmt = $pdo->query("SELECT SCHEMA_NAME FROM INFORMATION_SCHEMA.SCHEMATA WHERE SCHEMA_NAME = '$dbname'");
    if ($stmt->rowCount() > 0) {
        echo "Banco de dados '$dbname' existe!<br>";
        
        // Conecta ao banco de dados específico
        $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        
        // Verifica se a tabela existe
        $stmt = $pdo->query("SHOW TABLES LIKE 'contact_messages'");
        if ($stmt->rowCount() > 0) {
            echo "Tabela 'contact_messages' existe!<br>";
            
            // Mostra a estrutura da tabela
            $stmt = $pdo->query("DESCRIBE contact_messages");
            echo "<br>Estrutura da tabela:<br>";
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                echo "- {$row['Field']}: {$row['Type']}<br>";
            }
        } else {
            echo "Tabela 'contact_messages' não existe!<br>";
        }
    } else {
        echo "Banco de dados '$dbname' não existe!<br>";
    }
    
} catch(PDOException $e) {
    echo "Erro: " . $e->getMessage();
}
?> 