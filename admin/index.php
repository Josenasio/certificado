<?php 
session_start();
if (!isset($_SESSION['id']) || $_SESSION['nivel_acesso'] !== 'Admin') {
    header("Location: ../index.php");
    exit;
}

include_once($_SERVER['DOCUMENT_ROOT'].'/certidao/conexao/connect.php');

$count_query = mysqli_query($mysqli, "SELECT COUNT(*) as total FROM alunos WHERE status_certidao = 'secretaria'");
$count_result = mysqli_fetch_assoc($count_query);
$total_pedidos = $count_result['total'];


$consulta_contagem = mysqli_query($mysqli, "SELECT COUNT(*) as total FROM alunos WHERE status_certidao = 'retificado'");
$resultado_contagem = mysqli_fetch_assoc($consulta_contagem);
$total_alunos = $resultado_contagem['total'];

$query_distintos_certidao = mysqli_query($mysqli, "SELECT COUNT(DISTINCT codigo_certidao) as total_distintos FROM alunos WHERE status_certidao = 'arquivado'");
$resultado_distintos_certidao = mysqli_fetch_assoc($query_distintos_certidao);
$total_certidoes_distintos = $resultado_distintos_certidao['total_distintos'];



$consulta_certidoes = mysqli_query($mysqli, "
    SELECT COUNT(*) as total 
    FROM alunos 
    WHERE codigo_certidao IN (SELECT codigo_certidao FROM qr_scans)
");

$resultado_certidoes = mysqli_fetch_assoc($consulta_certidoes);
$total_certidoes_validas = $resultado_certidoes['total'];

?>


<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin</title>
    <!-- ======= Styles ====== -->
    <link rel="stylesheet" href="../personalizar/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">


   <link rel="shortcut icon" href="/icon.ico" type="image/x-icon">
    <!-- ====== ionicons ======= -->
    <script type="module" src="https://unpkg.com/ionicons@5.5.2/dist/ionicons/ionicons.esm.js"></script>
    <script nomodule src="https://unpkg.com/ionicons@5.5.2/dist/ionicons/ionicons.js"></script>

    

    <style>
.popup-notification {
            position: fixed;
            top: 20px;
            right: 250px;
            background:rgb(255, 255, 255);
            color:rgb(102, 255, 0);
            padding: 15px 25px;
            border-radius: 8px;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
            display: none;
            z-index: 1000;
            animation: slideIn 0.5s ease-out;
        }

        @keyframes slideIn {
            from { transform: translateX(100%);}
            to { transform: translateX(0);}
        }

        .popup-notification.show {
            display: block;
        }



 .footer {
            margin-top: 50px;
            text-align: center;
            color: #6c757d;
        }

        .user3 {
        display: flex;
        align-items: center; /* Alinha o ícone e o texto verticalmente */
        font-size: 20px; /* Define o tamanho da fonte para o texto */
        color: #333; /* Cor do texto */
    }

    .user3 i {
        font-size: 24px; /* Tamanho do ícone */
        margin-right: 10px; /* Espaço entre o ícone e o texto */
        cursor: pointer; /* Torna o ícone clicável */
        transition: transform 0.3s ease; /* Adiciona transição suave para o efeito de hover */
    }

   
    .user3:hover {
        color: #007bff; /* Muda a cor do texto e ícone quando passar o mouse sobre a div */
    }







    
    
    </style>

</head>

<body>
<div id="popup" class="popup-notification"></div>
<div id="initialCount" style="display: none;"><?php echo $total_pedidos; ?></div>


    
    <!-- =============== Navigation ================ -->
    <div class="container">
        <div class="navigation">
            <ul>
                <li>
                    <a href="#">
                    <div class="user">
                   <img src="imagem_user/image.png" alt="usuário">

                </div>
                <span class="title" style="color: #ffff;">
    <?php echo isset($_SESSION['nome']) ? $_SESSION['nome'] : 'Usuário'; ?>
</span>
                    </a>
                </li>

                <li >
                    <a href="#">
                        <span class="icon">
                            <ion-icon name="home-outline"></ion-icon>
                        </span>
                        <span class="title" style="color: #ffff;">Dashboard</span>
                    </a>
                </li>


                <li id="addCard">
    <a href="add_user/add.php">
        <span class="icon">
            <ion-icon name="add-circle-outline" style="color: yellow;"></ion-icon>
        </span>
        <span class="title">
             Utilizador
        </span>
    </a>
</li>


<li id="confirmCard">
    <a href="confirmar/confirmacao.php">
        <span class="icon">
            <!-- Ícone de confirmação -->
            <ion-icon name="checkmark-circle-outline" style="color: green;"></ion-icon>
            <!-- Ou ícone de erro (descomente para usar) -->
            <!-- <ion-icon name="close-circle-outline" style="color: red;"></ion-icon> -->
        </span>
        <span class="title">
            Confirmação
        </span>
    </a>
</li>




<li>
    <a href="gestao.php" >
        <span class="icon" >
            <ion-icon name="settings-outline" style="color: #ffffff;"></ion-icon>
        </span>
        <span class="title">
            Gestão
        </span>
    </a>
</li>



 
<li>
    <a href="certidao_feita/total_scans.php">
        <span class="icon">
            <ion-icon name="qr-code-outline" style="color: #ffffff;"></ion-icon>
        </span>
        <span class="title">
        <span style="color: #00ff55;"><?php echo $total_certidoes_validas ?></span>  QR Code
        </span>
    </a>
</li>



<li>
    <a href="relatorio/relatorio.php">
        <span class="icon">
        <ion-icon name="newspaper-outline" style="color: #ffffff;"></ion-icon>

        </span>
        <span class="title">
        <span style="color: #00ff55;"></span>  Relatórios
        </span>
    </a>
</li>




<br>
<br>
<br>
<br>
<br>
<br>
<br>
<br>
<br>
<br>
<br>
<br>
<br>
<br>
<br>
<br>
<br>
<br>
<br>
<br>
<br>
<br>
<br>
 
<li id="addCard">
    <a href="../sair/exit.php">
        <span class="icon">
            <ion-icon name="log-out-outline" style="color: red;"></ion-icon>
        </span>
        <span class="title">
             Sair
        </span>
    </a>
</li>







            </ul>
        </div>

        <!-- ========================= Main ==================== -->
        <div class="main">
            <div class="topbar">
                <div class="toggle">
                    <ion-icon name="menu-outline" style="color: #ffff;"></ion-icon>
                </div>



               

                <div class="user3">
   <a href="add_user/listar_user.php" style="color: #ffffff;"> <i class="fas fa-users"></i> </a><!-- Ícone de usuários -->
</div>






            </div>

            <!-- ======================= Cards ================== -->
            <div class="cardBox">
              

            <div class="card" onclick="showDetails('certidao')">
    <div>
        <div class="numbers" style="color: #ffff;"><?php echo $total_pedidos ?></div>
        <div class="cardName"> RETIFICAR AGORA</div>
    </div>

    <div class="iconBx">
    <ion-icon name="create-outline" style="color: red;"></ion-icon>
    </div>
</div>


                <div class="card"  onclick="showDetails('retificar')">
                    <div>
                        <div class="numbers" style="color: #ffff;"><?php echo $total_alunos ?></div>
                        <div class="cardName">CERTIFICADOS RETIFICADOS</div>
                    </div>

                    <div class="iconBx">
                    <ion-icon name="checkmark-done-outline" style="color: #00ff55;"></ion-icon>
                    <ion-icon name="print-outline" style="color: #B0B0B0;"></ion-icon>

                  
                    </div>
                </div>


                <div class="card" onclick="showDetails('arquivo')">
                    <div>
                        <div class="numbers" style="color: #ffff;"> </div>
                        <div class="cardName">CERTIFICADOS ARQUIVADOS</div>
                    </div>

                    <div class="iconBx">
                    <ion-icon name="folder-outline" style="color: yellow;"></ion-icon>
                    </div>
                </div>


                <div class="card" style="background-color:rgb(255, 255, 255);">
    <div>
    <div class="numbers" style="color: #D4AF37; font-size:50px; font-family: 'Agency FB', sans-serif;">
    <?php echo $total_certidoes_distintos; ?>
</div>

        <div class="cardNamee" style="color: #D4AF37; font-family: 'Agency FB', sans-serif; font-size:25px;">TOTAL DE CERTIFICADOS EMITIDOS</div>
    </div>

    <div class="iconBx">
    <ion-icon name="ribbon-outline" style="color: #D4AF37;"></ion-icon>
</div>

</div>



           
           
            </div>
<!-- ======================= Fim Cards ================== -->

 
            <!-- ================ Order Details List ================= -->
            <div id="details" class="details">
            <div class="footer" style="margin-top: 540px;">
        <p>&copy; 2025 Sistema Interno - Todos os direitos reservados</p>
    </div>
           
            </div>
         
            <script>
                function showDetails(type) {
                    const xhr = new XMLHttpRequest();
                    xhr.open('GET', `pedidos.php?type=${type}`, true);
                    xhr.onload = function() {
                        if (xhr.status === 200) {
                            const data = JSON.parse(xhr.responseText);
                            let detailsContent = '';

                            if (type === 'certidao') {
                                detailsContent = `<div class="recentOrders">
                                    <div class="cardHeader">
                                        <h2>LISTA DE CERTIFICADOS PARA SEREM RETIFICADAS</h2>
                                    </div>
                                    <table style="border: 1px solid #007bff">
                                        <thead>
                                            <tr style="background-color:#3a4179; border-radius:10px;">
                                                <td><i class="fas fa-hashtag"></i> Número</td>
                                                <td><i class="fas fa-user"></i> Nome</td>
                                                <td><i class="fas fa-school"></i> Escola</td>
                                                <td><i class="fas fa-users"></i> Classe</td>
                                                <td><i class="fas fa-chalkboard-teacher"></i> Turma</td>
                                                <td><i class="fas fa-calendar-alt"></i> Ano Letivo</td>
                                                <td><i class="fas fa-cogs"></i> Ação</td>
                                            </tr>
                                        </thead>
                                        <tbody>`;
                                data.forEach(row => {
                                    detailsContent += `<tr>
                                        <td>${row.numero}</td>
                                        <td>${row.nome}</td>
                                        <td>${row.escola}</td>
                                        <td>${row.classe}</td>
                                        <td>${row.turma}</td>
                                        <td>${row.ano_letivo}</td>
                                        <td>
                                            <a href="retificar/editar_certidao.php?id=${row.id}" class="edit-btn" style="display: inline-block; padding: 10px 20px; background-color: #28a745; color: white; text-decoration: none; border-radius: 5px; font-weight: bold; transition: background-color 0.3s;">
                                                <i class="fas fa-edit me-2"></i> Retificar
                                            </a>
                                        </td>
                                    </tr>`;
                                });
                                detailsContent += `</tbody></table></div>
                                    <div class="footer">
                                        <p>&copy; 2024 Sistema Interno - Todos os direitos reservados</p>
                                    </div>`;
                            }

                            if (type === 'arquivo') {
                                detailsContent = `<div class="recentOrders">
                                    <div class="cardHeader">
                                        <h2>LISTA DE CERTIFICADOS ARQUIVADOS</h2>

                                           <div class="search">
                    <label>
                        <input type="text"  id="filterName" placeholder="Buscar nome do(a) aluno(a)" onkeyup="filterTable()" >
                        <ion-icon name="search-outline"></ion-icon>
                    </label>
                </div>
                                     

                                    </div>
                                    <table id="arquivoTable" style="border: 1px solid #007bff">
                                        <thead>
                                            <tr style="background-color:#3a4179; border-radius:10px;">
                                                <td style="text-align:left"><i class="fas fa-hashtag"></i> Número</td>
                                                <td style="text-align:left"><i class="fas fa-user"></i> Nome</td>
                                                <td style="text-align:left"><i class="fas fa-school"></i> Escola</td>
                                                <td style="text-align:left"><i class="fas fa-users"></i> Classe</td>
                                                <td style="text-align:left"><i class="fas fa-chalkboard-teacher"></i> Turma</td>
                                                <td style="text-align:left"><i class="fas fa-calendar-alt"></i> Ano Letivo</td>
                                                <td style="text-align:center"><i class="fas fa-cogs"></i> Ação</td>
                                            </tr>
                                        </thead>
                                        <tbody>`;
                                        data.forEach(row => {
    let urlImprimir;

    if (row.classe_id == 1 || row.classe_id == 2) {
        urlImprimir = 'certidao_feita/quinta_sexta.php?id=' + row.id;
    } else if (row.classe_id == 8) {
        urlImprimir = 'certidao_feita/index12.php?id=' + row.id;
    } 
    
    else if (row.classe_id == 3 || row.classe_id == 4) {
    urlImprimir = 'certidao_feita/setima_oitava.php?id=' + row.id;
}
    
    else {
        urlImprimir = 'certidao_feita/index.php?id=' + row.id;
    }

    detailsContent += `<tr>
        <td style="text-align:left">${row.numero}</td>
        <td style="text-align:left">${row.nome}</td>
        <td style="text-align:left">${row.escola}</td>
        <td style="text-align:left">${row.classe}</td>
        <td style="text-align:left">${row.turma}</td>
        <td style="text-align:left">${row.ano_letivo}</td>
        <td>
            <a href="${urlImprimir}" class="edit-btn" style="display: inline-block; padding: 10px 20px; background-color: #007bff; color: white; text-decoration: none; border-radius: 5px; font-weight: bold; transition: background-color 0.3s;">
              <span>Imprimir</span>
            </a>
        </td>
    </tr>`;
});

                                detailsContent += `</tbody></table></div>
                                    <div class="footer">
                                        <p>&copy; 2024 Sistema Interno - Todos os direitos reservados</p>
                                    </div>`;
                            }

                            if (type === 'retificar') {
                                detailsContent = `<div class="recentOrders">
                                    <div class="cardHeader">
                                        <h2>LISTA DE CERTIFICADOS PRONTOS PARA IMPRIMIR</h2>


                                           <div class="search">
                    <label>
                        <input type="text"  id="filterName" placeholder="Buscar nome do(a) aluno(a)" onkeyup="filterTable()" >
                        <ion-icon name="search-outline"></ion-icon>
                    </label>
                </div>

                                    </div>
                                    <table style="border: 1px solid #007bff" id="arquivoTable">
                                        <thead>
                                            <tr style="background-color:#3a4179; border-radius:10px;">
                                                <td><i class="fas fa-hashtag"></i> Número</td>
                                                <td><i class="fas fa-user"></i> Nome</td>
                                                <td><i class="fas fa-school"></i> Escola</td>
                                                <td><i class="fas fa-users"></i> Classe</td>
                                                <td><i class="fas fa-chalkboard-teacher"></i> Turma</td>
                                                <td><i class="fas fa-calendar-alt"></i> Ano Letivo</td>
                                                <td><i class="fas fa-cogs"></i> Ação</td>
                                            </tr>
                                        </thead>
                                        <tbody>`;
                                data.forEach(row => {

                                    let urlImprimir;

if (row.classe_id == 1 || row.classe_id == 2) {
    urlImprimir = 'certidao_feita/quinta_sexta.php?id=' + row.id;
} else if (row.classe_id == 8) {
    urlImprimir = 'certidao_feita/index12.php?id=' + row.id;
} 
else if (row.classe_id == 3 || row.classe_id == 4) {
    urlImprimir = 'certidao_feita/setima_oitava.php?id=' + row.id;
}
else {
    urlImprimir = 'certidao_feita/index.php?id=' + row.id;
}


                                    detailsContent += `<tr>
                                        <td>${row.numero}</td>
                                        <td>${row.nome}</td>
                                        <td>${row.escola}</td>
                                        <td>${row.classe}</td>
                                        <td>${row.turma}</td>
                                        <td>${row.ano_letivo}</td>
                                        <td>
                                            <a href="${urlImprimir}" class="edit-btn" style="display: inline-block; padding: 10px 20px; background-color: #007bff; color: white; text-decoration: none; border-radius: 5px; font-weight: bold; transition: background-color 0.3s;">
                                                Imprimir
                                            </a>
                                        </td>
                                    </tr>`;
                                });
                                detailsContent += `</tbody></table></div>
                                    <div class="footer">
                                        <p>&copy; 2024 Sistema Interno - Todos os direitos reservados</p>
                                    </div>`;
                            }
                            // Atualiza o conteúdo da div 'details'
                            document.getElementById('details').innerHTML = detailsContent;
                        }
                    };
                    xhr.send();
                }

                // Função para filtrar as linhas da tabela de arquivos pelo nome
                function filterTable() {
                    var input, filter, table, tr, td, i, txtValue;
                    input = document.getElementById("filterName");
                    if (!input) return;
                    filter = input.value.toUpperCase();
                    table = document.getElementById("arquivoTable");
                    if (!table) return;
                    tr = table.getElementsByTagName("tr");
                    // Começa em i = 1 para pular o cabeçalho da tabela
                    for (i = 1; i < tr.length; i++) {
                        td = tr[i].getElementsByTagName("td")[1]; // coluna "Nome" (segunda coluna)
                        if (td) {
                            txtValue = td.textContent || td.innerText;
                            if (txtValue.toUpperCase().indexOf(filter) > -1) {
                                tr[i].style.display = "";
                            } else {
                                tr[i].style.display = "none";
                            }
                        }
                    }
                }
            </script>

 <!-- =========== Scripts ========= -->
 <script src="../personalizar/main.js"></script>


 <script>
        // Configuração do pop-up
        let lastCount = parseInt(document.getElementById('initialCount').textContent);
        const popup = document.getElementById('popup');

        function checkNewCertidoes() {
            fetch('get_count.php')
                .then(response => response.text())
                .then(currentCount => {
                    currentCount = parseInt(currentCount);
                    if (currentCount > lastCount) {
                        const newEntries = currentCount - lastCount;
                        showPopup(`você tem ${newEntries} novo certificado para ser retificado!`);
                        lastCount = currentCount;
                    }
                })
                .catch(error => console.error('Erro:', error));
        }

        function showPopup(message) {
            popup.textContent = message;
            popup.classList.add('show');
            setTimeout(() => {
                popup.classList.remove('show');
            }, 10000);
        }

        // Verificar a cada 5 segundos
        setInterval(checkNewCertidoes, 2000);
    </script>


</body>

</html>