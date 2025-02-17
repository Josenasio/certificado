<?php
include 'phpqrcode/qrlib.php';

if (isset($_GET['codigo'])) {
    $codigo = $_GET['codigo'];
    // Define o cabeçalho para imagem PNG
    header("Content-Type: image/png");
    // Gera e exibe o QR Code diretamente no navegador
    QRcode::png($codigo, false, QR_ECLEVEL_L, 3, 1);
    exit(); // Encerra o script para não enviar dados adicionais
} else {
    header("Content-Type: image/png");
    // Cria uma imagem em branco de 200x200 pixels
    $im = imagecreatetruecolor(100, 100);
    $bg = imagecolorallocate($im, 255, 255, 255);
    imagefill($im, 0, 0, $bg);
    imagepng($im);
    imagedestroy($im);
    exit();
}
?>
