<?php 
session_start();
if (!isset($_SESSION['id']) || $_SESSION['nivel_acesso'] !== 'Admin') {
    header("Location: ../index.php");
    exit;
}

include_once($_SERVER['DOCUMENT_ROOT'].'/certidao/conexao/connect.php');

// Consulta todos os usuários para preencher o select
$user_query = "SELECT id, nome FROM usuarios ORDER BY nome ASC";
$user_result = mysqli_query($mysqli, $user_query);
if (!$user_result) {
    die("Erro ao consultar usuários: " . mysqli_error($mysqli));
}

// Recupera os filtros enviados via GET
$selected_user = isset($_GET['usuario']) ? $_GET['usuario'] : '';
$search_name   = isset($_GET['search']) ? $_GET['search'] : '';
?>
<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <title>Painel de Controle - Alunos</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome para ícones -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;700&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Roboto', sans-serif;
            background-color: #f8f9fa;
        }
        /* Card de Filtros Centralizado e Fixo */
        .fixed-filter {
            position: fixed;
            top: 100px;
            left: 50%;
            transform: translateX(-50%);
            width: 50%;
            z-index: 1000;
        }
        /* Conteúdo Principal com margem superior para não sobrepor o filtro fixo */
        .main-content {
            margin-top: 180px;
            padding: 20px;
        }
        .card {
            margin-bottom: 30px;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
        }
        .table thead {
            margin-top: 200px;
            background-color: #343a40;
            color: #ffffff;
        }
        .table-hover tbody tr:hover {
            background-color: #f1f1f1;
        }
        .form-control, .form-select {
            transition: all 0.3s ease;
        }
        .form-control:focus, .form-select:focus {
            border-color: #495057;
            box-shadow: none;
        }
        h1, h2 {
            color: #343a40;
        }
        /* Faz a tabela ocupar toda a largura da tela */
        .table-responsive table {
            width: 100%;
            min-width: 100%;
        }
        /* Ícones nos cabeçalhos */
        .header-icon {
            margin-right: 5px;
            color: #ffc107;
        }


        .fixed-top-button {
            margin-top: -2px;
            position: fixed;
            top: 0;
            left: 50%;
            transform: translateX(-50%);
            width: 100%;
            max-width: 100%;
            z-index: 1000;
            background-color: #ffffff;
            border: none;
            color: black;
            padding: 15px;
            text-align: center;
            font-size: 16px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
            cursor: pointer;
            letter-spacing: 2px;
         
     
        }

        .fixed-top-button:hover {
            background-color: red;
        }
    </style>
</head>
<body style="background-color: #1B203B">
<button class="fixed-top-button" onclick="window.location.href='/certidao/admin/index.php'">
    <i class="fa fa-arrow-left"></i> Voltar a Pagina Inicial
</button>





<div class="container-fluid">
    <!-- Card de Filtros (fixo e centralizado) -->
    <div class="fixed-filter">
        <div class="card">
            <div class="card-header">
                <strong>Filtros</strong>
            </div>
            <div class="card-body">
                <form method="GET" action="" id="filterForm">
                    <div class="mb-3">
                        <label for="usuario" class="form-label">Selecione o Usuário:</label>
                        <select name="usuario" id="usuario" class="form-select" required>
                            <option value="">-- Selecione --</option>
                            <?php while($user = mysqli_fetch_assoc($user_result)): ?>
                                <option value="<?php echo $user['id']; ?>" <?php if($user['id'] == $selected_user) echo 'selected'; ?>>
                                    <?php echo htmlspecialchars($user['nome']); ?>
                                </option>
                            <?php endwhile; ?>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="search" class="form-label">Buscar por Nome do Aluno:</label>
                        <input type="text" name="search" id="search" class="form-control" value="<?php echo htmlspecialchars($search_name); ?>">
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Conteúdo Principal (Tabela) -->
    <div class="main-content">
        <h1 class="mb-4 text-center">Painel de Controle - Alunos</h1>
        <?php
        if (!empty($selected_user)) {
            $alunos_query = "
            SELECT 
                al_lectivo.numero_ano_letivo AS ano_lectivo,
                e.nome_escola AS escola,
                c.numeroclasse AS classe,
                t.nome_turma AS turma,
                cur.nome_curso AS curso,
                cl.tipo_classificacao AS classificacao,
                a.numero,
                a.nome,
                a.bi,
                a.data_nascimento,
                g.tipo_genero AS genero,
                d.nome AS distrito,
                a.naturalidade,
                a.nome_mae,
                a.nome_pai,
                a.codigo_certidao,
                a.data_registro
            FROM alunos a
            JOIN ano_lectivo al_lectivo ON a.ano_lectivo_id = al_lectivo.id
            JOIN escola e ON a.escola_id = e.id
            JOIN classe c ON a.classe_id = c.id
            JOIN turma t ON a.turma_id = t.id
            JOIN cursos cur ON a.id_curso = cur.id
            JOIN classificacao cl ON a.classificacao_id = cl.id
            JOIN genero g ON a.genero_id = g.id
            JOIN distrito d ON a.distrito_id = d.id
            WHERE a.id_usuarios = " . intval($selected_user);
            
            if (!empty($search_name)) {
                $search_name_escaped = mysqli_real_escape_string($mysqli, $search_name);
                $alunos_query .= " AND a.nome LIKE '%" . $search_name_escaped . "%'";
            }
            
            $alunos_query .= " ORDER BY a.data_registro DESC";
            
            $alunos_result = mysqli_query($mysqli, $alunos_query);
            if (!$alunos_result) {
                die("Erro ao consultar alunos: " . mysqli_error($mysqli));
            }
            ?>
            <div class="card" style="margin-top: 120px; margin-bottom:-600px">
                <div class="card-header">
                    <strong>Lista de Alunos</strong>
                </div>
                <div class="card-body" >
                    <?php if (mysqli_num_rows($alunos_result) > 0): ?>
                        <div class="table-responsive">
                            <table class="table table-striped table-hover">
                                <thead>
                                    <tr>
                                        <th><i class="fa-solid fa-calendar header-icon"></i>A. Lect.</th>
                                        <th><i class="fa-solid fa-school header-icon"></i>Escola</th>
                                        <th><i class="fa-solid fa-chalkboard header-icon"></i>Classe</th>
                                        <th><i class="fa-solid fa-users header-icon"></i>Turma</th>
                                        <th><i class="fa-solid fa-book header-icon"></i>Curso</th>
                                        <th><i class="fa-solid fa-star header-icon"></i>Classificação</th>
                                        <th><i class="fa-solid fa-hashtag header-icon"></i>Nº</th>
                                        <th><i class="fa-solid fa-user header-icon"></i>Nome</th>
                                        <th><i class="fa-solid fa-id-card header-icon"></i>BI</th>
                                        <th><i class="fa-solid fa-birthday-cake header-icon"></i>Data de Nascimento</th>
                                        <th><i class="fa-solid fa-venus-mars header-icon"></i>Gênero</th>
                                        <th><i class="fa-solid fa-map-marker header-icon"></i>Distrito</th>
                                        <th><i class="fa-solid fa-flag header-icon"></i>Naturalidade</th>
                                        <th><i class="fa-solid fa-female header-icon"></i>Mãe</th>
                                        <th><i class="fa-solid fa-male header-icon"></i>Pai</th>
                                        <th><i class="fa-solid fa-file header-icon"></i>Código</th>
                                        <th><i class="fa-solid fa-clock header-icon"></i>D.Registro</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php while ($aluno = mysqli_fetch_assoc($alunos_result)): ?>
                                        <tr>
                                            <td><?php echo htmlspecialchars($aluno['ano_lectivo']); ?></td>
                                            <td><?php echo htmlspecialchars($aluno['escola']); ?></td>
                                            <td><?php echo htmlspecialchars($aluno['classe']); ?></td>
                                            <td><?php echo htmlspecialchars($aluno['turma']); ?></td>
                                            <td><?php echo htmlspecialchars($aluno['curso']); ?></td>
                                            <td><?php echo htmlspecialchars($aluno['classificacao']); ?></td>
                                            <td><?php echo htmlspecialchars($aluno['numero']); ?></td>
                                            <td><?php echo htmlspecialchars($aluno['nome']); ?></td>
                                            <td><?php echo htmlspecialchars($aluno['bi']); ?></td>
                                            <td><?php echo htmlspecialchars($aluno['data_nascimento']); ?></td>
                                            <td><?php echo htmlspecialchars($aluno['genero']); ?></td>
                                            <td><?php echo htmlspecialchars($aluno['distrito']); ?></td>
                                            <td><?php echo htmlspecialchars($aluno['naturalidade']); ?></td>
                                            <td><?php echo htmlspecialchars($aluno['nome_mae']); ?></td>
                                            <td><?php echo htmlspecialchars($aluno['nome_pai']); ?></td>
                                            <td><?php echo htmlspecialchars($aluno['codigo_certidao']); ?></td>
                                            <td><?php echo htmlspecialchars($aluno['data_registro']); ?></td>
                                        </tr>
                                    <?php endwhile; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php else: ?>
                        <p class="text-center">Nenhum aluno encontrado para o usuário selecionado.</p>
                    <?php endif; ?>
                </div>
            </div>
            <?php
        }
        ?>
    </div>
</div>
<!-- Bootstrap JS (Opcional, para funcionalidades interativas) -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
    // Autoenvia o formulário quando o usuário selecionado muda ou quando há digitação na busca (com debounce de 500ms)
    document.addEventListener("DOMContentLoaded", function() {
        const form = document.getElementById("filterForm");
        const selectUser = document.getElementById("usuario");
        const searchInput = document.getElementById("search");

        // Envia o formulário ao alterar o select
        selectUser.addEventListener("change", function() {
            form.submit();
        });

        // Envia o formulário ao digitar no campo de busca (com debounce)
        let timeout = null;
        searchInput.addEventListener("input", function() {
            clearTimeout(timeout);
            timeout = setTimeout(function() {
                form.submit();
            }, 500);
        });
    });
</script>







 <!-- Footer -->
 <footer class="bg-da text-light py-4" style="margin-top: 650px; background-color:#ffffff">
    <div class="container">
      <div class="row align-items-center">
        <!-- Imagem à esquerda -->
        <div class="col-md-4 text-center text-md-start mb-3 mb-md-0">
          <img src="../login/imagem/image.webp" alt="Logo" style="max-height: 50px;">
        </div>
        <!-- Texto central -->
        <div class="col-md-4 text-center mb-3 mb-md-0">
          <p class="mb-0" style="color: black;">&copy; 2025 Ministério da Educação, Cultura Ciência e Ensino Superior. Todos os direitos reservados.</p>
        </div>
        <!-- Ícones à direita -->
        <div class="col-md-4 text-center text-md-end">
          <a href="https://www.facebook.com/educacao.stp/" target="_blank" class="text-light me-3">
            <i class="fab fa-facebook fa-2x" style="color:#1877F2; font-size:30px"></i>
          </a>
          <a href="https://wa.me/2399971781" target="_blank" class="text-light">
            <i class="fab fa-whatsapp fa-2x" style="color:#25D366; font-size:30px"></i>
          </a>
        </div>
      </div>
    </div>
  </footer>
</body>
</html>
