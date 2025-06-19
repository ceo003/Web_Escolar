<?php
$host = "localhost";
$usuario = "root";
$senha = "";
$banco = "imectec_db";

// Criar conexão
$conexao = new mysqli($host, $usuario, $senha, $banco);

// Verificar
if ($conexao->connect_error) {
    die("Erro na conexão: " . $conexao->connect_error);
}
?>
