<?php
session_start();
include_once($_SERVER['DOCUMENT_ROOT'].'/certidao/conexao/connect.php');

// Verifica se o usuário tem permissão para realizar a operação
if (!isset($_SESSION['id']) || $_SESSION['nivel_acesso'] !== 'Admin') {
    die("Acesso negado.");
}

// Verifica se o ID do aluno foi enviado
if (isset($_POST['id'])) {
    $id = intval($_POST['id']); // Segurança contra SQL Injection

    // Atualiza o status da certidão para 'arquivado'
    $query = "UPDATE alunos SET status_certidao = 'arquivado' WHERE id = ?";
    
    $stmt = $mysqli->prepare($query);
    $stmt->bind_param("i", $id);
    
    if ($stmt->execute()) {
        echo "Status atualizado com sucesso!";
    } else {
        echo "Erro ao atualizar o status.";
    }
    
    $stmt->close();
} else {
    echo "ID do aluno não fornecido.";
}

$mysqli->close();
?>
