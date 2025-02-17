<?php
include_once($_SERVER['DOCUMENT_ROOT'].'/certidao/conexao/connect.php');

$type = $_GET['type']; // Recebe o tipo de pedido (certidao, retificar, arquivo)

$query = "";

if ($type === 'certidao') {
    $query = "SELECT 
        a.id,
        a.nome, 
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
    WHERE a.status_certidao = 'secretaria' 
    ORDER BY a.data_registro DESC";
}

if ($type === 'retificar') {
    $query = "SELECT 
        a.id,
        a.nome, 
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
    WHERE a.status_certidao = 'retificado' 
    ORDER BY a.data_registro DESC";
}

if ($type === 'arquivo') {
    $query = "SELECT 
        a.id,
        a.nome, 
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
