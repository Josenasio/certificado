<?php
session_start();
include_once($_SERVER['DOCUMENT_ROOT'].'/certidao/conexao/connect.php');

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $mysqli->real_escape_string($_POST['email']);
    $senha = $mysqli->real_escape_string($_POST['senha']);

    // Consulta ao banco de dados
    $query = "SELECT * FROM usuarios WHERE email = ?";
    $stmt = $mysqli->prepare($query);
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();

        // Verificação da senha (criptografada no banco com password_hash)
        if (password_verify($senha, $user['senha'])) {
            // Armazenar dados na sessão
            $_SESSION['id'] = $user['id'];
            $_SESSION['nivel_acesso'] = $user['nivel_acesso'];
            $_SESSION['nome'] = $user['nome'];


            // Redirecionamento baseado no nível de acesso
            switch ($user['nivel_acesso']) {
                case 'Admin':
                    header("Location: ../admin/");
                    break;
                case 'Secretária':
                    header("Location: ../secretaria/");
                    break;
                case 'Confirmação':
                    header("Location: ../confirmacao/");
                    break;
                default:
                    echo "Tipo de usuário não reconhecido!";
            }
            exit;
        } else {
            header("Location: ../erro/senha_errada.php");
        }
    } else {
        header("Location: ../erro/senha_errada.php");
    }
} else {
    echo "Método de requisição inválido!";
}
?>
