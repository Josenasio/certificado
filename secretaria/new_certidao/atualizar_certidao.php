<?php
include_once($_SERVER['DOCUMENT_ROOT'].'/certidao/conexao/connect.php');

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Recebendo os dados do formulário do aluno
    $id              = $_POST['id'];
    $nome            = $_POST['nome'];
    $naturalidade    = $_POST['naturalidade'];
    $nome_mae        = $_POST['nome_mae'];
    $nome_pai        = $_POST['nome_pai'];
    $classe_id       = $_POST['classe_id']; // Classe atual do aluno
    $turma_id        = $_POST['turma_id'];
    $escola_id       = $_POST['escola_id'];
    $data_nascimento = $_POST['data_nascimento'];
    $bi              = $_POST['bi'];
    $id_curso        = $_POST['id_curso'];
    $distrito_id     = $_POST['distrito_id'];
    $genero_id       = $_POST['genero_id'];
    $classificacao_id= $_POST['classificacao_id'];
    $ano_lectivo_id  = $_POST['ano_lectivo_id'];
    $numero          = $_POST['numero'];

    // Atualiza os dados do aluno
    $sql_update_aluno = "UPDATE alunos 
                         SET nome = ?, bi = ?, data_nascimento = ?, naturalidade = ?, nome_mae = ?, nome_pai = ?, 
                             classe_id = ?, turma_id = ?, escola_id = ?, id_curso = ?, distrito_id = ?, 
                             genero_id = ?, classificacao_id = ?, ano_lectivo_id = ?, numero = ? 
                         WHERE id = ?";
    $stmt = $mysqli->prepare($sql_update_aluno);
    $stmt->bind_param("ssssssiiiiiiiiii", $nome, $bi, $data_nascimento, $naturalidade, $nome_mae, $nome_pai, 
                                       $classe_id, $turma_id, $escola_id, $id_curso, $distrito_id, $genero_id, 
                                       $classificacao_id, $ano_lectivo_id, $numero, $id);
    $stmt->execute();

    // Atualiza as disciplinas e notas
    if (isset($_POST['id_disciplina'])) {
        if ($classe_id == 8) {
            // Para classe 8, precisamos atualizar as notas de 10ª, 11ª, 12ª e a nota de exame.
            // Exclui as notas anteriores
            $sql_delete_notas = "DELETE FROM notas WHERE id_aluno = ?";
            $stmt_delete = $mysqli->prepare($sql_delete_notas);
            $stmt_delete->bind_param("i", $id);
            $stmt_delete->execute();

            $sql_delete_exame = "DELETE FROM exame WHERE aluno_id = ?";
            $stmt_delete_exame = $mysqli->prepare($sql_delete_exame);
            $stmt_delete_exame->bind_param("i", $id);
            $stmt_delete_exame->execute();

            // Para cada disciplina, insere as notas, se tiverem sido informadas
            foreach ($_POST['id_disciplina'] as $index => $disciplina_id) {
                // Obtém os valores enviados; se não houver valor (string vazia), o registro não é inserido.
                $nota10 = isset($_POST['nota_10'][$index]) ? $_POST['nota_10'][$index] : '';
                $nota11 = isset($_POST['nota_11'][$index]) ? $_POST['nota_11'][$index] : '';
                $nota12 = isset($_POST['nota_12'][$index]) ? $_POST['nota_12'][$index] : '';
                $exame  = isset($_POST['exame'][$index])   ? $_POST['exame'][$index]   : '';

                if ($nota10 !== '' && $nota10 !== null) {
                    $sql_update_nota = "REPLACE INTO notas (id_aluno, id_disciplina, nota, classe_id) VALUES (?, ?, ?, ?)";
                    $stmt_nota = $mysqli->prepare($sql_update_nota);
                    $classe_nota = 6; // Representa 10ª classe
                    $stmt_nota->bind_param("iiii", $id, $disciplina_id, $nota10, $classe_nota);
                    $stmt_nota->execute();
                }
                if ($nota11 !== '' && $nota11 !== null) {
                    $sql_update_nota = "REPLACE INTO notas (id_aluno, id_disciplina, nota, classe_id) VALUES (?, ?, ?, ?)";
                    $stmt_nota = $mysqli->prepare($sql_update_nota);
                    $classe_nota = 7; // Representa 11ª classe
                    $stmt_nota->bind_param("iiii", $id, $disciplina_id, $nota11, $classe_nota);
                    $stmt_nota->execute();
                }
                if ($nota12 !== '' && $nota12 !== null) {
                    $sql_update_nota = "REPLACE INTO notas (id_aluno, id_disciplina, nota, classe_id) VALUES (?, ?, ?, ?)";
                    $stmt_nota = $mysqli->prepare($sql_update_nota);
                    $classe_nota = 8; // Representa 12ª classe
                    $stmt_nota->bind_param("iiii", $id, $disciplina_id, $nota12, $classe_nota);
                    $stmt_nota->execute();
                }
                if ($exame !== '' && $exame !== null) {
                    $sql_update_exame = "REPLACE INTO exame (aluno_id, disciplina_id, nota_exame) VALUES (?, ?, ?)";
                    $stmt_exame = $mysqli->prepare($sql_update_exame);
                    $stmt_exame->bind_param("iii", $id, $disciplina_id, $exame);
                    $stmt_exame->execute();
                }
            }
        } else {
            // Para demais classes, utiliza a lógica antiga
            $sql_delete_notas = "DELETE FROM notas WHERE id_aluno = ?";
            $stmt_delete = $mysqli->prepare($sql_delete_notas);
            $stmt_delete->bind_param("i", $id);
            $stmt_delete->execute();

            foreach ($_POST['id_disciplina'] as $index => $disciplina_id) {
                $nota = $_POST['nota'][$index];
                // Usa o hidden que traz o classe_id original para cada nota (fallback: $classe_id do aluno)
                $nota_classe_id = isset($_POST['nota_classe_id'][$index]) ? $_POST['nota_classe_id'][$index] : $classe_id;
                $sql_update_nota = "REPLACE INTO notas (id_aluno, id_disciplina, nota, classe_id) VALUES (?, ?, ?, ?)";
                $stmt_nota = $mysqli->prepare($sql_update_nota);
                $stmt_nota->bind_param("iiii", $id, $disciplina_id, $nota, $nota_classe_id);
                $stmt_nota->execute();
            }
        }
    }

    // Redireciona após a atualização
    header("Location:sucesso.php?id=$id");
    exit();
}
?>
