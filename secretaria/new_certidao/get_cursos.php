<?php
include_once($_SERVER['DOCUMENT_ROOT'].'/certidao/conexao/connect.php');

if (isset($_GET['classe_id'])) {
    $classe_id = (int) $_GET['classe_id'];
    
    // Seleciona os cursos distintos vinculados à classe (através da tabela classe_curso_disciplina)
    $query = "
        SELECT DISTINCT c.id, c.nome_curso
        FROM cursos c
        INNER JOIN classe_curso_disciplina ccd ON c.id = ccd.curso_id
        WHERE ccd.classe_id = {$classe_id}
        ORDER BY c.nome_curso ASC
    ";
    
    $result = $mysqli->query($query);
    
    if ($result) {
        while ($row = $result->fetch_assoc()) {
            echo "<option value='" . $row['id'] . "'>" . $row['nome_curso'] . "</option>";
        }
    }
}
?>
