<?php
// Ativa o log de erros
error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('log_errors', 1);
ini_set('error_log', 'php_errors.log');

// Configurações do SMTP
ini_set("SMTP", "smtp.gmail.com");
ini_set("smtp_port", "587");
ini_set("sendmail_from", "info@imectec.co.mz");

// Configurações do banco de dados
$host = 'localhost';
$dbname = 'imectec_db';
$username = 'root';
$password = '';

// Inicializa a resposta
$response = ['status' => 'error', 'message' => ''];

try {
    // Conexão com o banco de dados
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Log de conexão bem sucedida
    error_log("Conexão com o banco de dados estabelecida com sucesso");
    
    // Verifica se a tabela existe, se não, cria
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

    // Verifica se o formulário foi enviado
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        // Log dos dados recebidos
        error_log("Dados recebidos do formulário: " . print_r($_POST, true));
        
        // Validação dos campos
        $required_fields = ['name', 'email', 'phone', 'subject', 'message'];
        $missing_fields = [];
        
        foreach ($required_fields as $field) {
            if (empty($_POST[$field])) {
                $missing_fields[] = $field;
            }
        }
        
        if (!empty($missing_fields)) {
            throw new Exception("Campos obrigatórios não preenchidos: " . implode(", ", $missing_fields));
        }
        
        // Obtém e sanitiza os dados do formulário
        $name = filter_var($_POST['name'], FILTER_SANITIZE_STRING);
        $email = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);
        $phone = filter_var($_POST['phone'], FILTER_SANITIZE_STRING);
        $subject = filter_var($_POST['subject'], FILTER_SANITIZE_STRING);
        $message = filter_var($_POST['message'], FILTER_SANITIZE_STRING);
        $date = date('Y-m-d H:i:s');

        // Log dos dados sanitizados
        error_log("Dados sanitizados: name=$name, email=$email, phone=$phone, subject=$subject");

        // Prepara a query SQL
        $sql = "INSERT INTO contact_messages (name, email, phone, subject, message, created_at) 
                VALUES (:name, :email, :phone, :subject, :message, :date)";
        
        $stmt = $pdo->prepare($sql);
        
        // Executa a query com os parâmetros
        $stmt->execute([
            ':name' => $name,
            ':email' => $email,
            ':phone' => $phone,
            ':subject' => $subject,
            ':message' => $message,
            ':date' => $date
        ]);

        // Log de sucesso
        error_log("Mensagem salva com sucesso no banco de dados");

        // Envia por e-mail
        $destinatario = "info@imectec.co.mz";
        $assuntoEmail = "Nova mensagem do website do nosso instituto: $subject";

        $corpoEmail = "Nova mensagem recebida pelo site:\n\n";
        $corpoEmail .= "Nome: $name\n";
        $corpoEmail .= "Email: $email\n";
        $corpoEmail .= "Telefone: $phone\n";
        $corpoEmail .= "Assunto: $subject\n";
        $corpoEmail .= "Mensagem:\n$message\n\n";
        $corpoEmail .= "Data/Hora: $date\n";

        $headers = "From: $email\r\n";
        $headers .= "Reply-To: $email\r\n";
        $headers .= "X-Mailer: PHP/" . phpversion();

        if(mail($destinatario, $assuntoEmail, $corpoEmail, $headers)) {
            error_log("Email enviado com sucesso");
            $response = [
                'status' => 'success',
                'message' => 'Mensagem enviada com sucesso!'
            ];
        } else {
            error_log("Erro ao enviar email");
            $response = [
                'status' => 'warning',
                'message' => 'Mensagem salva, mas houve um erro ao enviar o email.'
            ];
        }
    } else {
        throw new Exception("Método de requisição inválido");
    }
} catch(PDOException $e) {
    error_log("Erro no banco de dados: " . $e->getMessage());
    $response = [
        'status' => 'error',
        'message' => 'Erro ao conectar com o banco de dados. Por favor, tente novamente mais tarde.'
    ];
} catch(Exception $e) {
    error_log("Erro geral: " . $e->getMessage());
    $response = [
        'status' => 'error',
        'message' => $e->getMessage()
    ];
}

// Retorna a resposta em JSON
header('Content-Type: application/json');
echo json_encode($response);
?>
