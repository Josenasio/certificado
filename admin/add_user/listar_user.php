<?php
session_start();
if (!isset($_SESSION['id']) || $_SESSION['nivel_acesso'] !== 'Admin') {
    header("Location: ../../index.php");
    exit;
}

include_once($_SERVER['DOCUMENT_ROOT'].'/certidao/conexao/connect.php');

// Buscar usuários do tipo direção na escola logada e incluir o nome da escola
$query = "SELECT * FROM usuarios WHERE nivel_acesso IN ('Secretária', 'Confirmação')";

$stmt = $mysqli->prepare($query);
$stmt->execute();
$result = $stmt->get_result();

// Função para deletar usuário
if (isset($_GET['delete_id'])) {
    $delete_id = $_GET['delete_id'];
    $delete_query = "DELETE FROM usuarios WHERE id = ?";
    $delete_stmt = $mysqli->prepare($delete_query);
    $delete_stmt->bind_param('i', $delete_id);
    $delete_stmt->execute();
    header('Location: listar_user.php'); // Redireciona após excluir
    exit;
}

// Função para editar usuário
if (isset($_POST['edit_id'])) {
    $edit_id = $_POST['edit_id'];
    $nome = $_POST['nome'];
    $email = $_POST['email'];
    $senha = password_hash($_POST['senha'], PASSWORD_DEFAULT);

    $update_query = "UPDATE usuarios SET nome = ?, email = ?, senha = ? WHERE id = ?";
    $update_stmt = $mysqli->prepare($update_query);
    $update_stmt->bind_param('sssi', $nome, $email, $senha, $edit_id);
    $update_stmt->execute();
    header('Location: listar_user.php'); // Redireciona após editar
    exit;
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lista Usuário</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">



    <link rel="stylesheet" href="../personalizar/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">


   
    <!-- ====== ionicons ======= -->
    <script type="module" src="https://unpkg.com/ionicons@5.5.2/dist/ionicons/ionicons.esm.js"></script>
    <script nomodule src="https://unpkg.com/ionicons@5.5.2/dist/ionicons/ionicons.js"></script>


    
<style>
        body {

            background-color: #1B203B;
        }

        /* Estilo personalizado */
        .container {
            max-width: 1200px;
            margin-top: 20px;
        }

        table th, table td {
            text-align: center;
        }

        .table th {
            background-color: #f1f1f1;
        }

        .btn-sm {
            margin: 5px;
        }

        .modal-header {
            background-color: #007bff;
            color: white;
        }

        .modal-body {
            padding: 20px;
        }

        /* Responsividade */
        @media (max-width: 768px) {
            .table-responsive {
                overflow-x: scroll;
            }

            .table th, .table td {
                font-size: 14px;
            }
        }

        /* Estilo dos botões de ação */
        .btn-warning {
            background-color: #f0ad4e;
            border-color: #f0ad4e;
        }

        .btn-warning:hover {
            background-color: #ec971f;
            border-color: #ec971f;
        }

        .btn-danger {
            background-color: #d9534f;
            border-color: #d9534f;
        }

        .btn-danger:hover {
            background-color: #c9302c;
            border-color: #c9302c;
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
            background-color: black;
            border: none;
            color: white;
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
<body>
<button class="fixed-top-button" onclick="window.location.href='/certidao/Admin/'">
  <i class="fas fa-arrow-left"></i> Voltar a Página Inicial
</button>
<br><br>

<div class="container">
    <h1 class="text-center text-primary my-4">USUÁRIOS DO SISTEMA</h1>

    <div class="table-responsive">
        <table class="table table-bordered table-striped">
            <thead>
                <tr>
                <th><i class="fas fa-user"></i> Nome</th>
        <th><i class="fas fa-envelope"></i> Email</th>
        <th><i class="fas fa-lock"></i> Senha</th>
        <th><i class="fas fa-user-shield"></i> Nível Acesso</th>
        <th><i class="fas fa-calendar-alt"></i> Criado em</th>
        <th><i class="fas fa-cogs"></i> Ações</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($usuario = $result->fetch_assoc()): ?>
                    <tr>
                    <td style="color: #ffff;"><?php echo htmlspecialchars($usuario['nome'] ?? 'Não atribuída'); ?></td>
                        <td style="color: #ffff;"><?php echo htmlspecialchars($usuario['email']); ?></td>
                        <td style="color: red;">*******</td>

                        <td style="color: #ffff;"><?php echo htmlspecialchars($usuario['nivel_acesso']); ?></td>
                    
                        <td style="color: #ffff;"><?php echo htmlspecialchars($usuario['criado_em'] ?? 'Data não atribuída'); ?></td>
                        <td>
                            <!-- Botões para Editar e Deletar -->
                            <button class="btn btn-warning btn-sm" data-bs-toggle="modal" data-bs-target="#editModal" data-id="<?php echo $usuario['id']; ?>" data-nome="<?php echo htmlspecialchars($usuario['nome']); ?>" data-email="<?php echo htmlspecialchars($usuario['email']); ?>"> <ion-icon name="create-outline"></ion-icon></button>
                            <a href="listar_user.php?delete_id=<?php echo $usuario['id']; ?>" class="btn btn-danger btn-sm" onclick="return confirm('Tem certeza que deseja excluir?')"><ion-icon name="trash-outline"></ion-icon></a>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Modal de Edição -->
<div class="modal fade" id="editModal" tabindex="-1" aria-labelledby="editModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editModalLabel">Editar Usuário</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form method="POST" action="listar_user.php">
                    <input type="hidden" name="edit_id" id="edit_id">
                    <div class="mb-3">
                        <label for="nome" class="form-label">Nome</label>
                        <input type="text" class="form-control" name="nome" id="nome">
                    </div>
                    <div class="mb-3">
                        <label for="email" class="form-label">Email</label>
                        <input type="email" class="form-control" name="email" id="email">
                    </div>
                    <div class="mb-3">
                        <label for="senha" class="form-label">Senha</label>
                        <input type="password" class="form-control" name="senha" id="senha">
                    </div>

        
                    <button type="submit" class="btn btn-primary">Atualizar</button>
                </form>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
<script>
    // Script para preencher o modal com os dados do usuário
    var editModal = document.getElementById('editModal');
    editModal.addEventListener('show.bs.modal', function (event) {
        var button = event.relatedTarget;
        var id = button.getAttribute('data-id');
        var nome = button.getAttribute('data-nome');
        var email = button.getAttribute('data-email');

        var modalId = editModal.querySelector('#edit_id');
        var modalNome = editModal.querySelector('#nome');
        var modalEmail = editModal.querySelector('#email');
        var modalSenha = editModal.querySelector('#senha');

        modalId.value = id;
        modalNome.value = nome;
        modalEmail.value = email;
        modalSenha.value = ''; // Senha ficará em branco para o usuário preencher
    });
</script>
</body>
</html>
