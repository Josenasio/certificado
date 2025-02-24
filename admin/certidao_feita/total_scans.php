<?php
session_start();
include_once($_SERVER['DOCUMENT_ROOT'].'/certidao/conexao/connect.php');

// Consulta que junta a tabela alunos com qr_scans e agrupa por aluno
$query = "
    SELECT 
        a.nome, 
      
        a.codigo_certidao,
        COUNT(qs.id) AS total_scans,
        GROUP_CONCAT(qs.data_hora SEPARATOR ' | ') AS scan_times
    FROM alunos a
    JOIN qr_scans qs ON a.codigo_certidao = qs.codigo_certidao
    GROUP BY a.codigo_certidao, a.nome
    ORDER BY a.nome ASC
";

$result = mysqli_query($mysqli, $query);
if (!$result) {
    die("Erro na consulta: " . mysqli_error($mysqli));
}
?>
<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <title>Relatório de Scans de QR Code</title>
    <!-- Bootstrap CSS para uma aparência moderna -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">



    <style>
        body {
            padding: 20px;
            background-color: #f8f9fa;
            font-family: Arial, sans-serif;
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

        h1 {
            margin-bottom: 30px;
        }
        table {
            margin-top: 20px;
        }
        td, th {
            vertical-align: middle;
        }
    </style>
</head>
<body style="background-color: #1B203B">
<button class="fixed-top-button" onclick="window.location.href='/certidao/admin/index.php'">
    <i class="fa fa-arrow-left"></i> Voltar a Pagina Inicial
</button>
<br>
<br>
<br>


    <div class="container">
        <h1 class="text-center" style="color:rgb(255, 255, 255)">Relatório de Scans de QR Code</h1>
        <table class="table table-striped table-bordered">
            <thead class="table-dark">
                <tr>
                <th><i class="fa-solid fa-user"></i> Nome do Aluno</th>
            <th><i class="fa-solid fa-qrcode"></i> Número de Scans</th>
            <th><i class="fa-solid fa-certificate"></i> Código Certificado</th>
            <th><i class="fa-solid fa-calendar"></i> Datas dos Scans</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = mysqli_fetch_assoc($result)): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($row['nome']); ?></td>
                        <td><?php echo htmlspecialchars($row['total_scans']); ?></td>
                        <td><?php echo htmlspecialchars($row['codigo_certidao']); ?></td>
                        <td>
                            <?php 
                                // Separa as datas de scan e exibe cada uma em uma linha
                                $datas = explode(" | ", $row['scan_times']);
                                foreach ($datas as $data) {
                                    echo htmlspecialchars($data) . "<br>";
                                }
                            ?>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>



    <!-- Footer -->
  <footer class="bg-da text-light py-4" style="margin-top: 600px; background-color:#ffffff">
    <div class="container">
      <div class="row align-items-center">
        <!-- Imagem à esquerda -->
        <div class="col-md-4 text-center text-md-start mb-3 mb-md-0">
          <img src="../../login/imagem/image.webp" alt="Logo" style="max-height: 50px;">
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
    <!-- Bootstrap JS (Opcional) -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
