<?php
include_once($_SERVER['DOCUMENT_ROOT'].'/certidao/conexao/connect.php'); // Conexão com o banco de dados

// Verifica se o código da certidão foi passado na URL
if (!isset($_GET['codigo_certidao'])) {
    die("Código da certidão não informado.");
}

$codigo_certidao = $mysqli->real_escape_string($_GET['codigo_certidao']);

// Registra o scan no banco de dados
$sql = "INSERT INTO qr_scans (codigo_certidao, data_hora) VALUES (?, NOW())";
$stmt = $mysqli->prepare($sql);
$stmt->bind_param("s", $codigo_certidao);
$stmt->execute();
$stmt->close();

// Redireciona para a página de confirmação do QR Code
header("Location: https://certificados.escoladados.store/certidao/confirmar_qr.php?codigo_certidao=" . $codigo_certidao);

exit;
?>

