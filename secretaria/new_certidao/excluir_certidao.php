<?php 
session_start();

// Verifica se o usuário está logado e se possui o nível de acesso correto
if (!isset($_SESSION['id']) || $_SESSION['nivel_acesso'] !== 'Secretária') {
    header("Location: ../../index.php");
    exit;
}

include_once($_SERVER['DOCUMENT_ROOT'].'/certidao/conexao/connect.php');

// Verifica se o ID foi informado na URL
if (isset($_GET['id'])) {
    // Sanitiza o ID convertendo para inteiro
    $id = (int) $_GET['id'];
    
    // Inicia uma transação para garantir que ambas as exclusões ocorram juntas
    $mysqli->autocommit(FALSE);

    // Query para excluir as notas referentes a este aluno
    $sqlNotas = "DELETE FROM notas WHERE id_aluno = {$id}";
    // Query para excluir o aluno
    $sqlAluno = "DELETE FROM alunos WHERE id = {$id}";

    // Executa ambas as queries
    if ($mysqli->query($sqlNotas) && $mysqli->query($sqlAluno)) {
        // Se ambas forem executadas com sucesso, confirma a transação
        $mysqli->commit();
        header("Location: excluido_sucesso.php");
        exit();
    } else {
        // Em caso de erro, desfaz a transação
        $mysqli->rollback();
        echo "Erro ao excluir o aluno e suas notas: " . $mysqli->error;
    }
} else {
    // Se o ID não for informado, redireciona para a página inicial com mensagem de erro
    header("Location: ../index.php?msg=ID+inv%C3%A1lido");
    exit();
}
?>
