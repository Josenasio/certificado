<?php
session_start();
$id_usuario = $_SESSION['id'];
include_once($_SERVER['DOCUMENT_ROOT'].'/certidao/conexao/connect.php');

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nome = $mysqli->real_escape_string($_POST['nome']);
    $classe_id = (int) $_POST['classe_id'];
    $data_nascimento = $mysqli->real_escape_string($_POST['data_nascimento']); // Campo data_nascimento
    $distrito_id = (int) $_POST['distrito_id'];
    $escola_id = (int) $_POST['escola_id'];
    $id_curso = (int) $_POST['id_curso'];
    $turma_nome = $mysqli->real_escape_string($_POST['turma']);
    $numero = (int) $_POST['numero'];
    $bi = (int) $_POST['bi'];
    $ano_lectivo_id = (int) $_POST['ano_lectivo_id'];
    $nome_mae = $mysqli->real_escape_string($_POST['nome_mae']);
    $nome_pai = $mysqli->real_escape_string($_POST['nome_pai']);
    $genero_id = (int) $_POST['genero_id'];
    $naturalidade = $mysqli->real_escape_string($_POST['naturalidade']);
    $classificacao_id = (int) $_POST['classificacao_id'];

    do {
        $codigo_certidao = strtoupper(substr(str_shuffle('ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789'), 0, 10));
        $result = $mysqli->query("SELECT id FROM alunos WHERE codigo_certidao = '$codigo_certidao'");
    } while ($result->num_rows > 0);

    // Verifica ou insere a turma
    $turma_id = null;
    $result = $mysqli->query("SELECT id FROM turma WHERE nome_turma = '$turma_nome'");
    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $turma_id = $row['id'];
    } else {
        $mysqli->query("INSERT INTO turma (nome_turma) VALUES ('$turma_nome')");
        $turma_id = $mysqli->insert_id;
    }

    $sql = "INSERT INTO alunos (nome, bi, id_curso, data_nascimento, distrito_id, classe_id, escola_id, turma_id, numero, ano_lectivo_id, nome_mae, nome_pai, genero_id, naturalidade, codigo_certidao, classificacao_id, status_certidao, id_usuarios) 
    VALUES ('$nome', '$bi', '$id_curso', '$data_nascimento', '$distrito_id', $classe_id, $escola_id, $turma_id, $numero, $ano_lectivo_id, '$nome_mae', '$nome_pai', $genero_id, '$naturalidade', '$codigo_certidao', $classificacao_id, 'secretaria', $id_usuario)";

    if ($mysqli->query($sql)) {
        $aluno_id = $mysqli->insert_id;

        if ($classe_id == 8) {
            // Se for classe 8, insere as notas para 10ª, 11ª, 12ª e o exame
            if (!empty($_POST['disciplinas'])) {
                foreach ($_POST['disciplinas'] as $key => $disciplina_id) {
                    // Nota 10
                    if (isset($_POST['nota_10'][$key]) && $_POST['nota_10'][$key] !== '') {
                        $nota10 = (float) $_POST['nota_10'][$key];
                        $mysqli->query("INSERT INTO notas (id_aluno, id_disciplina, nota, classe_id) VALUES ($aluno_id, $disciplina_id, $nota10, 6)");
                    }
                    // Nota 11
                    if (isset($_POST['nota_11'][$key]) && $_POST['nota_11'][$key] !== '') {
                        $nota11 = (float) $_POST['nota_11'][$key];
                        $mysqli->query("INSERT INTO notas (id_aluno, id_disciplina, nota, classe_id) VALUES ($aluno_id, $disciplina_id, $nota11, 7)");
                    }
                    // Nota 12
                    if (isset($_POST['nota_12'][$key]) && $_POST['nota_12'][$key] !== '') {
                        $nota12 = (float) $_POST['nota_12'][$key];
                        $mysqli->query("INSERT INTO notas (id_aluno, id_disciplina, nota, classe_id) VALUES ($aluno_id, $disciplina_id, $nota12, 8)");
                    }
                    // Exame
                    if (isset($_POST['exame'][$key]) && $_POST['exame'][$key] !== '') {
                        $notaExame = (float) $_POST['exame'][$key];
                        $mysqli->query("INSERT INTO exame (aluno_id, disciplina_id, nota_exame) VALUES ($aluno_id, $disciplina_id, $notaExame)");
                    }
                }
            }
        } else {
            // Para outras classes, insere a nota única conforme o valor de classe selecionado
            if (!empty($_POST['disciplinas'])) {
                foreach ($_POST['disciplinas'] as $key => $disciplina_id) {
                    if (isset($_POST['notas'][$key]) && $_POST['notas'][$key] !== '') {
                        $nota = (float) $_POST['notas'][$key];
                        $mysqli->query("INSERT INTO notas (id_aluno, id_disciplina, nota, classe_id) VALUES ($aluno_id, $disciplina_id, $nota, $classe_id)");
                    }
                }
            }
        }

        header('Location: sucesso.php');
        exit();
    } else {
        echo "Erro ao criar uma nova certidão: " . $mysqli->error;
    }
}
?>

