<?php
// Configurações de email na linha Nº 3 onde vem seu-email@exemplo.com devo  substituir pelo email que vai receber o formulario preenchido
$destinatario = "seu-email@exemplo.com"; // Substitua pelo seu email
$assunto_padrao = "Contato do Website da Escola";

// Verifica se o formulário foi enviado
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Coleta os dados do formulário
    $nome = isset($_POST['name']) ? filter_var($_POST['name'], FILTER_SANITIZE_STRING) : "";
    $email = isset($_POST['email']) ? filter_var($_POST['email'], FILTER_SANITIZE_EMAIL) : "";
    $telefone = isset($_POST['phone']) ? filter_var($_POST['phone'], FILTER_SANITIZE_STRING) : "";
    $assunto_selecionado = isset($_POST['subject']) ? filter_var($_POST['subject'], FILTER_SANITIZE_STRING) : "";
    $mensagem = isset($_POST['message']) ? filter_var($_POST['message'], FILTER_SANITIZE_STRING) : "";
    
    // Validações básicas
    if (empty($nome) || empty($email) || empty($telefone) || empty($assunto_selecionado) || empty($mensagem)) {
        echo json_encode(["status" => "error", "message" => "Todos os campos são obrigatórios."]);
        exit;
    }
    
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        echo json_encode(["status" => "error", "message" => "Por favor, forneça um email válido."]);
        exit;
    }
    
    // Mapeamento de assuntos
    $assuntos = [
        "admissao" => "Processo de Admissão",
        "visita" => "Agendar Visita",
        "financeiro" => "Informações Financeiras",
        "academico" => "Informações Acadêmicas",
        "outro" => "Outro Assunto"
    ];
    
    $assunto_texto = isset($assuntos[$assunto_selecionado]) ? $assuntos[$assunto_selecionado] : "Contato";
    $assunto_email = "$assunto_padrao: $assunto_texto";
    
    // Prepara o corpo do email
    $corpo_email = "Nome: $nome\n";
    $corpo_email .= "Email: $email\n";
    $corpo_email .= "Telefone: $telefone\n";
    $corpo_email .= "Assunto: $assunto_texto\n\n";
    $corpo_email .= "Mensagem:\n$mensagem\n";
    
    // Cabeçalhos do email
    $headers = "From: $email" . "\r\n" .
               "Reply-To: $email" . "\r\n" .
               "X-Mailer: PHP/" . phpversion();
    
    // Tenta enviar o email
    if (mail($destinatario, $assunto_email, $corpo_email, $headers)) {
        echo json_encode(["status" => "success", "message" => "Mensagem enviada com sucesso! Entraremos em contato em breve."]);
    } else {
        echo json_encode(["status" => "error", "message" => "Não foi possível enviar a mensagem. Por favor, tente novamente mais tarde."]);
    }
} else {
    // Se alguém tentar acessar diretamente este arquivo
    echo json_encode(["status" => "error", "message" => "Acesso inválido."]);
}
?> 