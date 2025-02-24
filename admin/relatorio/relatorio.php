<?php 
include_once($_SERVER['DOCUMENT_ROOT'].'/certidao/conexao/connect.php');

// Consulta para total de Certificados
$queryTotal = "SELECT COUNT(*) AS total FROM alunos";
$resultTotal = $mysqli->query($queryTotal);
$totalCertidoes = $resultTotal->fetch_assoc()['total'];

// Consulta para Certificados por Escola (mostrando o nome da escola)
$queryEscolas = "SELECT e.nome_escola AS escola, COUNT(*) AS total 
                 FROM alunos a 
                 JOIN escola e ON a.escola_id = e.id
                 GROUP BY a.escola_id 
                 ORDER BY total DESC";
$resultEscolas = $mysqli->query($queryEscolas);
$escolas = [];
while($row = $resultEscolas->fetch_assoc()){
    $escolas[] = $row;
}
$escolaMais = reset($escolas);
$escolaMenos = end($escolas);

// Consulta para Certificados por Distrito (mostrando o nome do distrito)
$queryDistritos = "SELECT d.nome AS distrito, COUNT(*) AS total 
                   FROM alunos a 
                   JOIN distrito d ON a.distrito_id = d.id
                   GROUP BY a.distrito_id 
                   ORDER BY total DESC";
$resultDistritos = $mysqli->query($queryDistritos);
$distritos = [];
while($row = $resultDistritos->fetch_assoc()){
    $distritos[] = $row;
}

// Consulta para Certificados por Classe (mostrando o nome da classe)
$queryClasses = "SELECT c.numeroclasse AS classe, COUNT(*) AS total 
                 FROM alunos a 
                 JOIN classe c ON a.classe_id = c.id
                 GROUP BY a.classe_id 
                 ORDER BY total DESC";
$resultClasses = $mysqli->query($queryClasses);
$classes = [];
while($row = $resultClasses->fetch_assoc()){
    $classes[] = $row;
}
$classeMais = reset($classes);
$classeMenos = end($classes);

// Consulta para distribuição por Status (se status_certidao for um campo texto)
$queryStatus = "SELECT status_certidao, COUNT(*) AS total 
                FROM alunos 
                GROUP BY status_certidao";
$resultStatus = $mysqli->query($queryStatus);
$statusStats = [];
while($row = $resultStatus->fetch_assoc()){
    $statusStats[] = $row;
}
?>
<!DOCTYPE html>
<html lang="pt">
<head>
  <meta charset="UTF-8">
  <title>Relatório de Certificados Emitidos</title>
  <!-- Bootstrap CSS para layout profissional -->


  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">

  <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
  <style>
    body {
      background-color: #f8f9fa;
      padding-top: 20px;
    }
    .report-header {
      margin-bottom: 30px;
      text-align: center;
    }
    .report-section {
      margin-bottom: 40px;
    }
    .card {
      box-shadow: 0 2px 8px rgba(0,0,0,0.1);
      margin-bottom: 20px;
    }
    canvas {
  background-color: #fff;
  padding: 15px;
  border-radius: 8px;
  width: 100px; /* Defina a largura desejada */
  height: 100px; /* Defina a altura desejada */
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
            background-color:rgb(255, 255, 255);
            border: none;
            color:rgb(0, 0, 0);
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



        #fixo {
            margin-top: -2px;
            position: fixed;
            top: 150px;
            left: -70%;
      
         
            max-width: 100%;
            z-index: 1000;
           
        
         
         
     
        }

  </style>
</head>
<body style="background-color: #1B203B">
<button class="fixed-top-button" onclick="window.location.href='/certidao/admin/index.php'">
    <i class="fa fa-arrow-left"></i> Voltar a Pagina Inicial
</button>
<br> <br>
<div class="col-md-4 text-end" style="margin-left:-400px" id="fixo">
      <button class="btn btn-secondary" onclick="window.print()">
        <i class="fa fa-print"></i> Imprimir
      </button>
    </div>

<div class="container" style="border: 2px solid black; background-color:#ffffff">
  <div class="report-header">
    <h1>Relatório dos Certificados</h1>
    <p class="lead">Estatísticas e gráficos interativos para análise dos registros.</p>
  </div>

  <!-- Seção de Totais e Destaques -->
  <div class="row report-section">
    <div class="col-md-3">
      <div class="card text-center">
        <div class="card-body">
          <h4 class="card-title">Total de Certificados</h4>
          <p class="card-text display-4"><?php echo $totalCertidoes; ?></p>
        </div>
      </div>
    </div>
    <div class="col-md-3">
      <div class="card text-center">
        <div class="card-body">
          <h4 class="card-title">Escola com Mais Certificados</h4>
          <p class="card-text">
            <?php echo $escolaMais['escola']; ?><br>
            (<?php echo $escolaMais['total']; ?> Certificados)
          </p>
        </div>
      </div>
    </div>
    <div class="col-md-3">
      <div class="card text-center">
        <div class="card-body">
          <h4 class="card-title">Escola com Menos Certificados</h4>
          <p class="card-text">
            <?php echo $escolaMenos['escola']; ?><br>
            (<?php echo $escolaMenos['total']; ?> Certificados)
          </p>
        </div>
      </div>
    </div>
    <div class="col-md-3">
      <div class="card text-center">
        <div class="card-body">
          <h4 class="card-title">Classe com Mais Certificados</h4>
          <p class="card-text">
            <?php echo $classeMais['classe']; ?><br>
            (<?php echo $classeMais['total']; ?> Certificados)
          </p>
        </div>
      </div>
    </div>
  </div>

  <!-- Gráficos -->
  <div class="row report-section">
    <div class="col-md-6">
      <h4 class="mb-3">Certificados por Escola</h4>
      <canvas id="chartEscolas"></canvas>
    </div>
    <div class="col-md-6">
      <h4 class="mb-3">Certificados por Distrito</h4>
      <canvas id="chartDistritos"></canvas>
    </div>
  </div>

  <div class="row report-section">
    <div class="col-md-6">
      <h4 class="mb-3">Certificados por Classe</h4>
      <canvas id="chartClasses"></canvas>
    </div>
    <div class="col-md-6">
      <h4 class="mb-3">Status das Certificados</h4>
      <canvas id="chartStatus"></canvas>
    </div>
  </div>

  <!-- Tabela Detalhada -->
  <div class="row report-section">
    <div class="col-md-12">
      <h4 class="mb-3">Detalhes dos Registros</h4>
      <div class="table-responsive">
        <?php 
          // Exemplo de query que une informações de alunos com escolas, classes e distritos
          $queryDetalhes = "SELECT a.id, a.nome, e.nome_escola AS escola, c.numeroclasse AS classe, d.nome AS distrito, a.status_certidao, a.data_registro 
                            FROM alunos a
                            JOIN escola e ON a.escola_id = e.id
                            JOIN classe c ON a.classe_id = c.id
                            JOIN distrito d ON a.distrito_id = d.id
                            ORDER BY a.data_registro DESC LIMIT 20";
          $resultDetalhes = $mysqli->query($queryDetalhes);
        ?>
        <table class="table table-striped table-bordered">
          <thead class="thead-dark">
            <tr>
              <th>ID</th>
              <th>Nome</th>
              <th>Escola</th>
              <th>Classe</th>
              <th>Distrito</th>
              <th>Status</th>
              <th>Data Registro</th>
            </tr>
          </thead>
          <tbody>
            <?php while($row = $resultDetalhes->fetch_assoc()): ?>
            <tr>
              <td><?php echo $row['id']; ?></td>
              <td><?php echo $row['nome']; ?></td>
              <td><?php echo $row['escola']; ?></td>
              <td><?php echo $row['classe']; ?></td>
              <td><?php echo $row['distrito']; ?></td>
              <td><?php echo $row['status_certidao']; ?></td>
              <td><?php echo $row['data_registro']; ?></td>
            </tr>
            <?php endwhile; ?>
          </tbody>
        </table>
      </div>
      <p class="text-muted">Exibindo os 20 registros mais recentes.</p>
    </div>
  </div>

</div>

<!-- Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
  // Dados vindos do PHP convertidos em JSON
  const escolasData = <?php echo json_encode($escolas); ?>;
  const distritosData = <?php echo json_encode($distritos); ?>;
  const classesData = <?php echo json_encode($classes); ?>;
  const statusData = <?php echo json_encode($statusStats); ?>;

  // Gráfico: Certificados por Escola (Barras)
  const escolaLabels = escolasData.map(item => item.escola);
  const escolaCounts = escolasData.map(item => item.total);
  const ctxEscolas = document.getElementById('chartEscolas').getContext('2d');
  const chartEscolas = new Chart(ctxEscolas, {
    type: 'bar',
    data: {
      labels: escolaLabels,
      datasets: [{
        label: 'Certificados',
        data: escolaCounts,
        backgroundColor: 'rgba(54, 162, 235, 0.6)',
        borderColor: 'rgba(54, 162, 235, 1)',
        borderWidth: 1
      }]
    },
    options: {
      scales: { y: { beginAtZero: true } },
      plugins: { legend: { display: false } }
    }
  });

  // Gráfico: Certificados por Distrito (Pizza)
  const distritoLabels = distritosData.map(item => item.distrito);
  const distritoCounts = distritosData.map(item => item.total);
  const ctxDistritos = document.getElementById('chartDistritos').getContext('2d');
  const chartDistritos = new Chart(ctxDistritos, {
    type: 'pie',
    data: {
      labels: distritoLabels,
      datasets: [{
        data: distritoCounts,
        backgroundColor: [
          'rgba(255, 99, 132, 0.6)',
          'rgba(54, 162, 235, 0.6)',
          'rgba(255, 206, 86, 0.6)',
          'rgba(75, 192, 192, 0.6)',
          'rgba(153, 102, 255, 0.6)',
          'rgba(8, 241, 0, 0.6)',
             'rgba(236, 20, 20, 0.6)',
          'rgba(37, 13, 126, 0.6)'
        ]
      }]
    },
    options: { responsive: true }
  });

  // Gráfico: Certificados por Classe (Linha)
  const classeLabels = classesData.map(item => item.classe);
  const classeCounts = classesData.map(item => item.total);
  const ctxClasses = document.getElementById('chartClasses').getContext('2d');
  const chartClasses = new Chart(ctxClasses, {
    type: 'line',
    data: {
      labels: classeLabels,
      datasets: [{
        label: 'Certificados',
        data: classeCounts,
        borderColor: 'rgba(75, 192, 192, 1)',
        backgroundColor: 'rgba(75, 192, 192, 0.3)',
        fill: true,
        tension: 0.3,
        borderWidth: 2
      }]
    },
    options: {
      scales: { y: { beginAtZero: true } },
      plugins: { legend: { display: false } }
    }
  });

  // Gráfico: Status das Certificados (Doughnut)
  const statusLabels = statusData.map(item => item.status_certidao);
  const statusCounts = statusData.map(item => item.total);
  const ctxStatus = document.getElementById('chartStatus').getContext('2d');
  const chartStatus = new Chart(ctxStatus, {
    type: 'doughnut',
    data: {
      labels: statusLabels,
      datasets: [{
        data: statusCounts,
        backgroundColor: [
          'rgba(255, 205, 86, 0.6)',
          'rgba(75, 192, 192, 0.6)',
          'rgba(255, 99, 132, 0.6)',
          'rgba(54, 162, 235, 0.6)'
        ]
      }]
    },
    options: { responsive: true }
  });
</script>













  <!-- Footer -->
  <footer class="bg-da text-light py-4" style="margin-top: 10px; background-color:black">
    <div class="container">
      <div class="row align-items-center">
        <!-- Imagem à esquerda -->
        <div class="col-md-4 text-center text-md-start mb-3 mb-md-0">
          <img src="../../login/imagem/image.webp" alt="Logo" style="max-height: 50px;">
        </div>
        <!-- Texto central -->
        <div class="col-md-4 text-center mb-3 mb-md-0">
          <p class="mb-0" style="color: #ffffff;">&copy; 2025 Ministério da Educação, Cultura Ciência e Ensino Superior. Todos os direitos reservados.</p>
        </div>
        <!-- Ícones à direita -->
        <div class="col-md-4 text-center text-md-end">
          <a href="https://www.facebook.com/educacao.stp/" target="_blank" class="text-light me-3">
            <i class="fab fa-facebook fa-2x" style="color:#1877F2; font-size:30px"></i>
          </a>
          <a href="https://wa.me/2392399971781" target="_blank" class="text-light">
            <i class="fab fa-whatsapp fa-2x" style="color:#25D366; font-size:30px"></i>
          </a>
        </div>
      </div>
    </div>
  </footer>
<!-- Bootstrap JS e dependências (opcional para interações adicionais) -->
<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.1/dist/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
