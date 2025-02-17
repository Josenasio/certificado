<?php
include_once($_SERVER['DOCUMENT_ROOT'].'/certidao/conexao/connect.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $classe_id = isset($_POST['classe_id']) ? (int) $_POST['classe_id'] : 0;
    $curso_id  = isset($_POST['curso_id']) ? (int) $_POST['curso_id'] : 0;

    // Consulta para buscar as disciplinas associadas à classe e ao curso na tabela classe_curso_disciplina
    $query = "
        SELECT d.id, d.nome_disciplina 
        FROM disciplina d
        INNER JOIN classe_curso_disciplina ccd ON d.id = ccd.disciplina_id
        WHERE ccd.classe_id = {$classe_id} AND ccd.curso_id = {$curso_id}
        ORDER BY d.nome_disciplina ASC
    ";

    $result = $mysqli->query($query);

    if ($result) {
        while ($row = $result->fetch_assoc()) {
            echo "<option value='" . $row['id'] . "'>" . $row['nome_disciplina'] . "</option>";
        }
    } else {
        // Opcional: exibir mensagem de erro caso a consulta falhe
        echo "<option value=''>Erro ao carregar disciplinas</option>";
    }
}
?>
