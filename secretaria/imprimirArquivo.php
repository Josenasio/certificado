<?php 
include_once($_SERVER['DOCUMENT_ROOT'].'/certidao/conexao/connect.php');

if ($_SERVER['REQUEST_METHOD'] == 'GET' && isset($_GET['id'])) {
    $id = $_GET['id'];
   
    // Atualiza os dados do aluno
    $sql_update_aluno = "UPDATE alunos SET status_certidao = 'retificado' WHERE id = ?";
    $stmt = $mysqli->prepare($sql_update_aluno);
    $stmt->bind_param("i", $id);
    $stmt->execute();

    // Redireciona após a atualização
    header("Location:pedir_imprimir.php?id=$id");
    exit();
}
?>
