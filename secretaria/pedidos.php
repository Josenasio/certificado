<?php
include_once($_SERVER['DOCUMENT_ROOT'].'/certidao/conexao/connect.php');
session_start(); // Inicia a sessão

// Verifica se o usuário está logado e recupera o ID do usuário
if (!isset($_SESSION['id'])) {
    die("Usuário não autenticado.");
}
$usuario_id = $_SESSION['id'];

$type = $_GET['type']; // Recebe o tipo de pedido (certidao, miniaturas, etc.)

$query = "";

if ($type === 'certidao') {
    $query = "SELECT 
        a.id,
        a.nome, 
        cur.nome_curso AS curso, 
        t.nome_turma AS turma, 
        c.nome_classe AS classe, 
        e.nome_escola AS escola, 
        a.numero, 
        al.numero_ano_letivo AS ano_letivo
    FROM alunos a
    JOIN cursos cur ON a.id_curso = cur.id
    JOIN turma t ON a.turma_id = t.id
    JOIN classe c ON a.classe_id = c.id
    JOIN escola e ON a.escola_id = e.id
    JOIN ano_lectivo al ON a.ano_lectivo_id = al.id
    WHERE a.status_certidao = 'secretaria'
    AND a.id_usuarios = $usuario_id
    ORDER BY a.data_registro DESC";
}




if ($type === 'arquivo') {
    $query = "SELECT 
        a.id,
        a.nome, 
        a.classe_id,
        t.nome_turma AS turma, 
        c.nome_classe AS classe, 
        e.nome_escola AS escola, 
        a.numero, 
        al.numero_ano_letivo AS ano_letivo
    FROM alunos a
    JOIN turma t ON a.turma_id = t.id
    JOIN classe c ON a.classe_id = c.id
    JOIN escola e ON a.escola_id = e.id
    JOIN ano_lectivo al ON a.ano_lectivo_id = al.id
    WHERE a.status_certidao = 'arquivado' 
    ORDER BY a.data_registro DESC";
}

$result = mysqli_query($mysqli, $query);

if (!$result) {
    die("Erro na consulta: " . mysqli_error($mysqli));
}

$data = mysqli_fetch_all($result, MYSQLI_ASSOC);
echo json_encode($data);
?>
