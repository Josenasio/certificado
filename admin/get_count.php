<?php
session_start();
if (!isset($_SESSION['id']) || $_SESSION['nivel_acesso'] !== 'Admin') {
    exit;
}

include_once($_SERVER['DOCUMENT_ROOT'].'/certidao/conexao/connect.php');

$count_query = mysqli_query($mysqli, "SELECT COUNT(*) as total FROM alunos WHERE status_certidao = 'secretaria'");
$count_result = mysqli_fetch_assoc($count_query);
echo $count_result['total'];
?>