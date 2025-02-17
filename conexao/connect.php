<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "certidao";

// Criar conexão
$mysqli = new mysqli($servername, $username, $password, $dbname);

// Verificar conexão
if ($mysqli->connect_error) {
    die("Conexão falhou: " . $mysqli->connect_error);
}
?>
