<?php 
session_start();
if (!isset($_SESSION['id']) || $_SESSION['nivel_acesso'] !== 'Secretária') {
    header("Location: ../index.php");
    exit;
}

include_once($_SERVER['DOCUMENT_ROOT'].'/certidao/conexao/connect.php');

$count_query = mysqli_query($mysqli, "SELECT COUNT(*) as total FROM alunos WHERE status_certidao = 'secretaria'");
$count_result = mysqli_fetch_assoc($count_query);
$total_pedidos = $count_result['total'];



$query_distintos_certidao = mysqli_query($mysqli, "SELECT COUNT(DISTINCT codigo_certidao) as total_distintos FROM alunos WHERE status_certidao = 'arquivado'");
$resultado_distintos_certidao = mysqli_fetch_assoc($query_distintos_certidao);
$total_certidoes_distintos = $resultado_distintos_certidao['total_distintos'];
?>


<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Secretária</title>
    <!-- ======= Styles ====== -->
    <link rel="stylesheet" href="../personalizar/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">


   
    <!-- ====== ionicons ======= -->
    <script type="module" src="https://unpkg.com/ionicons@5.5.2/dist/ionicons/ionicons.esm.js"></script>
    <script nomodule src="https://unpkg.com/ionicons@5.5.2/dist/ionicons/ionicons.js"></script>

    
    

    <style>
        .edit-btn, .delete-btn {
    background: none;
    border: none;
    cursor: pointer;
    font-size: 25px;
    margin: 0 5px;
}

.edit-btn i {
    color: yellow;
}

.delete-btn i {
    color: #dc3545;
}

 

 .footer {
            margin-top: 50px;
            text-align: center;
            color: #6c757d;
        }

    </style>

</head>

<body>
    
    <!-- =============== Navigation ================ -->
    <div class="container">
        <div class="navigation">
            <ul>
                <li>
                    <a href="#">
                    <div class="user">
                   
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
    <a href="new_certidao/certidao.php">
        <span class="icon">
            <ion-icon name="add-circle-outline" style="color: yellow;"></ion-icon>
        </span>
        <span class="title">
             Certificado
        </span>
    </a>
</li>

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

                <div class="search">
                    <label>
                        <input type="text" placeholder="Search here">
                        <ion-icon name="search-outline"></ion-icon>
                    </label>
                </div>




                <div class="user3">
                   
                </div>
            </div>

            <!-- ======================= Cards ================== -->
            <div class="cardBox">
              

            <div class="card" onclick="showDetails('certidao')">
    <div>
        <div class="numbers" style="color: #ffff;"><?php echo $total_pedidos ?></div>
        <div class="cardName"> CERTIFICADO PARA RETIFICAR</div>
    </div>

    <div class="iconBx">
    <ion-icon name="create-outline" style="color: red;"></ion-icon>
    </div>
</div>





           
                <div class="card">
    <div>
        <div class="numbers" style="color: #D4AF37;"><?php echo $total_certidoes_distintos ?></div>
        <div class="cardName">CERTIFICADOS EMITIDOS</div>
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
        <p>&copy; 2025 Sistema Interno - Todos os direitos reservados </p>
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
            <h2>LISTA DE CERTIFICADOS PARA SEREM RETIFICADOS</h2>
        </div>
        <table style="border: 1px solid #007bff">
            <thead>
                <tr style="background-color:#3a4179; border-radius:10px;">
                    <td><i class="fas fa-hashtag"></i> Número</td>
                    <td><i class="fas fa-user"></i> Nome</td>
                    <td><i class="fas fa-school"></i> Escola</td>
                    <td><i class="fas fa-users"></i> Classe</td>
                    <td><i class="fas fa-book"></i> Curso</td>
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
            <td>${row.curso}</td>
            <td>${row.turma}</td>
            <td>${row.ano_letivo}</td>
            <td>
                <a href="new_certidao/editar_certidao.php?id=${row.id}" class="edit-btn">
                    <i class="fas fa-edit"></i>
                </a>
                <a href="new_certidao/excluir_certidao.php?id=${row.id}" class="delete-btn" onclick="return confirm('ATENÇÃO: Tem certeza que deseja excluir este certificado?')">
                    <i class="fas fa-trash-alt" style="color:red"></i>
                </a>
            </td>
        </tr>`;
    });

    detailsContent += `</tbody></table></div>
    <div class="footer">
        <p>&copy; 2024 Sistema Interno - Todos os direitos reservados</p>
    </div>`;
}


// Atualiza o conteúdo da div com id 'details'
document.getElementById('details').innerHTML = detailsContent;
}
    };
    xhr.send();
}
    </script>
 <!-- =========== Scripts ========= -->
 <script src="../personalizar/main.js"></script>


</body>

</html>