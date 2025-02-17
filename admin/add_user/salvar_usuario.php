<?php
include_once($_SERVER['DOCUMENT_ROOT'].'/certidao/conexao/connect.php');

// Verificar se o formulário foi submetido
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Obter os dados do formulário
    $nome = $_POST['nome'];
    $email = $_POST['email'];
    $senha = password_hash($_POST['senha'], PASSWORD_DEFAULT); // Criptografando a senha
    $nivel_acesso = $_POST['nivel_acesso'];
    

    // Verificar se o e-mail já existe
    $query = "SELECT * FROM usuarios WHERE email = ?";
    $stmt = $mysqli->prepare($query);
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        // Email já existe
        header('Location: emailexistente.php');
    } else {
        // Inserir os dados no banco de dados
        $query = "INSERT INTO usuarios (nome, email, senha, nivel_acesso) VALUES (?, ?, ?, ?)";
        $stmt = $mysqli->prepare($query);
        $stmt->bind_param("ssss", $nome, $email, $senha, $nivel_acesso);

        if ($stmt->execute()) {
            header('Location: sucesso.php');
        exit();
        } else {
            echo "Erro ao cadastrar o usuário: " . $stmt->error;
        }
    }

    // Fechar a conexão
    $stmt->close();
}

// Fechar a conexão com o banco de dados
$mysqli->close();
?>
