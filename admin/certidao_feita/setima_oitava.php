<?php
session_start();
if (!isset($_SESSION['id']) || $_SESSION['nivel_acesso'] !== 'Admin') {
    header("Location: ../index.php");
    exit;
}

include_once($_SERVER['DOCUMENT_ROOT'].'/certidao/conexao/connect.php');

// Verificar se o ID foi passado via URL
if (isset($_GET['id'])) {
    $id = intval($_GET['id']); // Segurança contra SQL Injection

    /*
     * 1. Buscar informações do aluno e resolver referências das chaves estrangeiras
     */
    $query = "
    SELECT 
        a.nome, 
        a.naturalidade, 
        a.nome_mae, 
        a.nome_pai, 
        a.codigo_certidao, 
        a.status_certidao, 
        a.data_imprimir, 
        a.numero,
        a.data_nascimento, 
        a.bi, 
        a.classe_id, 
        c.nome_curso AS curso,
        cl.nome_classe AS classe,
        t.nome_turma AS turma,
        e.nome_escola AS escola,
        g.tipo_genero AS genero,
        class.tipo_classificacao AS classificacao,
        al.numero_ano_letivo, 
        al.nome_extenso, 
        d.nome AS distrito,
        class.id
    FROM alunos a
    LEFT JOIN cursos c ON a.id_curso = c.id
    LEFT JOIN classe cl ON a.classe_id = cl.id
    LEFT JOIN turma t ON a.turma_id = t.id
    LEFT JOIN escola e ON a.escola_id = e.id
    LEFT JOIN genero g ON a.genero_id = g.id
    LEFT JOIN classificacao class ON a.classificacao_id = class.id
    LEFT JOIN ano_lectivo al ON a.ano_lectivo_id = al.id
    LEFT JOIN distrito d ON a.distrito_id = d.id
    WHERE a.id = ?
    ";
    
    $stmt = $mysqli->prepare($query);
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        $aluno = $result->fetch_assoc();
    } else {
        die("Aluno não encontrado.");
    }
    $stmt->close();
    
    
    /*
     * 2. Buscar as notas da tabela "notas" (consulta original – usada no else)
     */
    $query_notas = "
        SELECT 
            n.nota, 
            d.nome_disciplina,
            n.classe_id
        FROM notas n
        LEFT JOIN disciplina d ON n.id_disciplina = d.id
        WHERE n.id_aluno = ?
        ORDER BY d.numero_ordem ASC
    ";
    
    $stmt_notas = $mysqli->prepare($query_notas);
    $stmt_notas->bind_param("i", $id);
    $stmt_notas->execute();
    $result_notas = $stmt_notas->get_result();
    
    $notas = [];
    while ($row = $result_notas->fetch_assoc()) {
        $notas[] = $row;
    }
    
    $stmt_notas->close();
    
    // Calcular a média das notas (para uso posterior, se necessário)
    $totalNotas = array_sum(array_column($notas, 'nota'));
    $quantidadeNotas = count($notas);
    $media = $quantidadeNotas > 0 ? round($totalNotas / $quantidadeNotas) : 0;
    
    
    /*
     * 3. Se o aluno for da 12ª classe (classe_id==8), buscar as notas agregadas por disciplina,
     *    separando por classe e também as notas de exame.
     */
    if ($aluno['classe_id'] == 8) {
        $query_agregado = "
            SELECT 
                d.id AS disciplina_id,
                d.nome_disciplina AS disciplina,
                GROUP_CONCAT(DISTINCT CASE WHEN n.classe_id = 6 THEN n.nota END 
                    ORDER BY n.id_nota SEPARATOR ', ') AS `10ª_classe`,
                GROUP_CONCAT(DISTINCT CASE WHEN n.classe_id = 7 THEN n.nota END 
                    ORDER BY n.id_nota SEPARATOR ', ') AS `11ª_classe`,
                GROUP_CONCAT(DISTINCT CASE WHEN n.classe_id = 8 THEN n.nota END 
                    ORDER BY n.id_nota SEPARATOR ', ') AS `12ª_classe`,
                GROUP_CONCAT(DISTINCT e.nota_exame 
                    ORDER BY e.id_exame SEPARATOR ', ') AS exame
            FROM disciplina d
            LEFT JOIN notas n ON d.id = n.id_disciplina AND n.id_aluno = ?
            LEFT JOIN exame e ON d.id = e.disciplina_id AND e.aluno_id = ?
            WHERE n.id_aluno = ? OR e.aluno_id = ?
            GROUP BY d.id, d.nome_disciplina
            ORDER BY d.numero_ordem ASC
        ";
        
        $stmt_agregado = $mysqli->prepare($query_agregado);
        $stmt_agregado->bind_param("iiii", $id, $id, $id, $id);
        $stmt_agregado->execute();
        $result_agregado = $stmt_agregado->get_result();
        
        $disciplinas_agregado = [];
        while ($row = $result_agregado->fetch_assoc()) {
            $disciplinas_agregado[] = $row;
        }
        $stmt_agregado->close();
    }
    
    $mysqli->close();
    
} else {
    die("ID do aluno não fornecido.");
}


/*
 * Função para converter número para extenso (já existente)
 */
function numeroPorExtenso($numero) {
    // Arredonda a nota para o número inteiro mais próximo
    $numero = round($numero);

    $extenso = [
        0 => "ZERO VALOR", 1 => "UM VALOR", 2 => "DOIS VALORES", 3 => "TRÊS VALORES", 4 => "QUATRO VALORES", 5 => "CINCO VALORES",
        6 => "SEIS VALORES", 7 => "SETE VALORES", 8 => "OITO VALORES", 9 => "NOVE VALORES", 10 => "DEZ VALORES", 11 => "ONZE VALORES",
        12 => "DOZE VALORES", 13 => "TREZE VALORES", 14 => "CATORZE VALORES", 15 => "QUINZE VALORES", 16 => "DEZASSEIS VALORES",
        17 => "DEZASSETE VALORES", 18 => "DEZOITO VALORES", 19 => "DEZENOVE VALORES", 20 => "VINTE VALORES", 21 => "VINTE E UM VALORES",
        22 => "VINTE E DOIS VALORES", 23 => "VINTE E TRÊS VALORES", 24 => "VINTE E QUATRO VALORES", 25 => "VINTE E CINCO VALORES",
        26 => "VINTE E SEIS VALORES", 27 => "VINTE E SETE VALORES", 28 => "VINTE E OITO VALORES", 29 => "VINTE E NOVE VALORES",
        30 => "TRINTA VALORES", 31 => "TRINTA E UM VALORES", 32 => "TRINTA E DOIS VALORES", 33 => "TRINTA E TRÊS VALORES",
        34 => "TRINTA E QUATRO VALORES", 35 => "TRINTA E CINCO VALORES", 36 => "TRINTA E SEIS VALORES", 37 => "TRINTA E SETE VALORES",
        38 => "TRINTA E OITO VALORES", 39 => "TRINTA E NOVE VALORES", 40 => "QUARENTA VALORES", 41 => "QUARENTA E UM VALORES",
        42 => "QUARENTA E DOIS VALORES", 43 => "QUARENTA E TRÊS VALORES", 44 => "QUARENTA E QUATRO VALORES", 45 => "QUARENTA E CINCO VALORES",
        46 => "QUARENTA E SEIS VALORES", 47 => "QUARENTA E SETE VALORES", 48 => "QUARENTA E OITO VALORES", 49 => "QUARENTA E NOVE VALORES",
        50 => "CINQUENTA VALORES", 51 => "CINQUENTA E UM VALORES", 52 => "CINQUENTA E DOIS VALORES", 53 => "CINQUENTA E TRÊS VALORES",
        54 => "CINQUENTA E QUATRO VALORES", 55 => "CINQUENTA E CINCO VALORES", 56 => "CINQUENTA E SEIS VALORES",
        57 => "CINQUENTA E SETE VALORES", 58 => "CINQUENTA E OITO VALORES", 59 => "CINQUENTA E NOVE VALORES",
        60 => "SESSENTA VALORES", 61 => "SESSENTA E UM VALORES", 62 => "SESSENTA E DOIS VALORES", 63 => "SESSENTA E TRÊS VALORES",
        64 => "SESSENTA E QUATRO VALORES", 65 => "SESSENTA E CINCO VALORES", 66 => "SESSENTA E SEIS VALORES",
        67 => "SESSENTA E SETE VALORES", 68 => "SESSENTA E OITO VALORES", 69 => "SESSENTA E NOVE VALORES",
        70 => "SETENTA VALORES", 71 => "SETENTA E UM VALORES", 72 => "SETENTA E DOIS VALORES", 73 => "SETENTA E TRÊS VALORES",
        74 => "SETENTA E QUATRO VALORES", 75 => "SETENTA E CINCO VALORES", 76 => "SETENTA E SEIS VALORES",
        77 => "SETENTA E SETE VALORES", 78 => "SETENTA E OITO VALORES", 79 => "SETENTA E NOVE VALORES",
        80 => "OITENTA VALORES", 81 => "OITENTA E UM VALORES", 82 => "OITENTA E DOIS VALORES", 83 => "OITENTA E TRÊS VALORES",
        84 => "OITENTA E QUATRO VALORES", 85 => "OITENTA E CINCO VALORES", 86 => "OITENTA E SEIS VALORES",
        87 => "OITENTA E SETE VALORES", 88 => "OITENTA E OITO VALORES", 89 => "OITENTA E NOVE VALORES",
        90 => "NOVENTA VALORES", 91 => "NOVENTA E UM VALORES", 92 => "NOVENTA E DOIS VALORES", 93 => "NOVENTA E TRÊS VALORES",
        94 => "NOVENTA E QUATRO VALORES", 95 => "NOVENTA E CINCO VALORES", 96 => "NOVENTA E SEIS VALORES",
        97 => "NOVENTA E SETE VALORES", 98 => "NOVENTA E OITO VALORES", 99 => "NOVENTA E NOVE VALORES",
        100 => "CEM VALORES"
    ];
    
    
    return isset($extenso[$numero]) ? $extenso[$numero] : "";
}
?>


<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Certificado de Habilitação</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css" rel="stylesheet">

    <style>
        body {
            font-family: monospace;
           
            color: black;
            padding: 1cm;
            display: flex;
            justify-content: center;
            align-items: center;
            position: relative;
            color:rgb(42, 42, 131);
        }
        .container {
            border: 1px solid black;
            padding: 1cm;
            position: relative;
            z-index: 1; /* Garante que o conteúdo fique acima da marca d'água */
           
        }
        @page {
  margin-left: 1cm; /* ajuste conforme necessário */
  margin-right: 1cm;
  margin-bottom: 1cm;
  margin-top: 0.3cm;

}

        .red {
            color: red;
        }
        .center {
            text-align: center;
        }
        .justify {
            text-align: justify;
        }
        .vermelho {
            color: red;
        }
        
        /* Estilo do Botão de Impressão */
        #printButton {
            position: fixed;
            bottom: 20px;
            margin-bottom: 850px;
            right: 1600px;
            padding: 15px 25px;
            background-color: #4CAF50;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 16px;
            z-index: 1000;
        }


        /* Footer fixo: QR code à esquerda e código da certidão à direita */
    .footer {
        position: static;
      bottom: 0;
      left: 0;
      width: 100%;
      display: flex;
      justify-content: space-between;
      align-items: center;
      padding: 0px;
      background-color: transparent;
      z-index: 1000;
      
    }
    .footer-left { margin-left: 10px; }
    .footer-right {margin-right: 10px;text-align: right; font-size: 20px;}




        #fixed-top-button {
            position: fixed;
            bottom: 20px;
            right: 150px;
            padding: 15px 25px;
            background-color: black;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 16px;
            z-index: 1000;
            margin-bottom: 850px;
        }

        
        #printButton:hover {
            background-color:rgb(0, 255, 13);
        }

        #fixed-top-button:hover {
            background-color: red;
        }

        @media print {
            body {
                padding: 0;
                margin: 0;
            }
         


            
            .container {
                border: none;
                /* Removendo quebras de página */
                page-break-before: avoid;
                page-break-after: avoid;
                page-break-inside: avoid;
                break-inside: avoid;
            }
            
            #printButton {
                display: none;
            }
            #fixed-top-button {
                display: none;
        }
        .footer {
        position: fixed;
        bottom: 10px;
        left: 0;
        width: 100%;
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 10px;
        background-color: white;
        z-index: 1000;
      }

      .watermark {
      position: fixed;
      top: 50%;
      left: 50%;
      transform: translate(-50%, -50%) rotate(-0.3deg);
      width: 80%;           /* Ajuste conforme necessário */
      height: 80%;          /* Ajuste conforme necessário */
      background: url('imagem/brasao.webp') no-repeat center center;
      background-size: contain;
      opacity: 0.1;         /* Ajuste a opacidade se necessário */
      z-index: 0;
      pointer-events: none;
      user-select: none;
      -webkit-print-color-adjust: exact; /* Para o Chrome e Safari */
      print-color-adjust: exact;
    }
        }

       
    </style>
  
</head>
<body>
  <!-- Marca d'água -->
  <div class="watermark"></div>

<button id="printButton" onclick="updateStatusAndPrint()">
    <i class="fas fa-print"></i> Imprimir Certificado
</button>

<button id="fixed-top-button" onclick="window.location.href='/certidao/admin/'">
<i class="fas fa-times"></i> Cancelar Impressão
</button>

<div class="container" style="margin-top: -40px; ">

<img src="imagem/brasao.webp" alt="Brasão de S. Tomé e Príncipe" width="150" style="margin-left: 440px; position:absolute">
        <div class="center" style="margin-top: 135px;">
     

            <span style="font-family: 'Lucida Sans', sans-serif; font-size: 31px;">REPÚBLICA DEMOCRÁTICA DE SÃO TOMÉ E PRÍNCIPE</span>
       <br> 
            <span style="font-family: 'Lucida Sans', sans-serif; font-size: 22.5px; font-weight: bold;">MINISTÉRIO DA EDUCAÇÃO, CULTURA, CIÊNCIA E ENSINO SUPERIOR</span><br>
           <span style="font-family: 'Lucida Sans', sans-serif; font-size: 22.5px; font-weight: bold;"> Direcção do Ensino Secundário e Técnico Profissional</span>
        </div>
        

                                             <div style="margin-left: 830px; font-family: 'Bookman Old Style', serif; font-size: 20px; font-style: italic; text-align:center">
                                                      VISTO<br>
                                                    O DIRECTOR
                                                   
                                                </div>
<br>
                                              <div class="center" style="font-family: 'Cambria', serif; font-size: 30px; font-weight: bold; text-decoration: underline;">
                                            CERTIFICADO DE HABILITAÇÃO
                                            </div>
<BR>
     
        <div class="justify" style="font-family: 'Times New Roman', serif; font-size: 20px; line-height: 1.2; margin-bottom:5px">
            <span style="font-family: Arial, sans-serif; font-size: 23px; font-style: italic;">Arcângela Ferreira do Nascimento Luís Miguel</span>, Chefe do departamento e de secretaria da Direcção
            do Ensino Secundário e Técnico Profissional em São Tomé   -------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------
        </div>
      
      

        <div class="justify" style="font-family: 'Times New Roman', serif; font-size: 23px; line-height: 1.6;">
    <span style="font-family: 'Arial', serif; font-size: 28px; font-style: italic;">CERTIFICA</span>, em cumprimento do despacho exarado em requerimento que fica arquivado neste
    Guichê que, <span style="font-family: 'Lucida Sans', sans-serif; font-size: 27px; font-weight: bold;"><?php echo mb_strtoupper(htmlspecialchars($aluno['nome']), 'UTF-8'); ?>
    </span>, natural de <?php echo htmlspecialchars($aluno['naturalidade']); ?> São-Tomé,
    Distrito de <?php echo htmlspecialchars($aluno['distrito']); ?>, nascido(a) em <?php 
    setlocale(LC_TIME, 'pt_PT.UTF-8', 'Portuguese_Portugal', 'pt_BR.UTF-8', 'Portuguese_Brazil');

    $data_nascimento = $aluno['data_nascimento']; // Exemplo: "2001-10-25"

    $formatter = new IntlDateFormatter('pt_PT', IntlDateFormatter::LONG, IntlDateFormatter::NONE);
    echo $formatter->format(new DateTime($data_nascimento));
    ?>
    ,
    portador(a) de Bilhete de Identidade n.º <?php echo htmlspecialchars($aluno['bi']); ?>, filho(a) de <?php echo htmlspecialchars($aluno['nome_pai']); ?>
    e de <?php echo htmlspecialchars($aluno['nome_mae']); ?>,<span style="font-family: 'Broadway', sans-serif; font-size: 28px; font-weight: bold; color: #333399"> <?php echo htmlspecialchars($aluno['classificacao']); ?></span> no ano lectivo <?php echo htmlspecialchars($aluno['nome_extenso']); ?> como
    aluno(a) <?php echo htmlspecialchars($aluno['genero']); ?> da <span style="font-family: 'Lucida Handwriting', cursive; font-size: 25px; font-weight: bold; color:red">"</span><span style="font-family: 'Lucida Handwriting', cursive; font-size: 25px; text-decoration: underline; font-weight: bold; color:red"><?php echo htmlspecialchars($aluno['classe']); ?></span><span style="font-family: 'Lucida Handwriting', cursive; font-size: 25px; font-weight: bold; color:red">"</span>
    
    


    <?php
// Cálculo da média deve ocorrer antes da utilização:
$somaNotas = 0;
$contadorNotas = 0;
foreach ($notas as $nota) {
    if (isset($nota['nota']) && is_numeric($nota['nota'])) {
        $somaNotas += $nota['nota'];
        $contadorNotas++;
    }
}
if ($contadorNotas > 0) {
    $mediaExibicao = round($somaNotas / $contadorNotas);
} else {
    $mediaExibicao = 0;
}
?>




    <?php if ($aluno['curso'] != 'Geral'): ?>
                curso de 
                        <span style="font-family: 'Garamond', serif; font-size: 26px; font-weight: bold;">
                            <?php echo htmlspecialchars($aluno['curso']); ?>
                        </span>
            <?php endif; ?>
             na(o) 
                        <span style="font-family: 'Garamond', serif; font-size: 26px; font-weight: bold;">
                <?php echo htmlspecialchars($aluno['escola']); ?> 
                        </span>  

                        <?php if ($aluno['classe_id'] == 3 || $aluno['classe_id'] == 4): ?>
    <span style="font-family: 'Garamond', serif; font-size: 24px; font-weight: bold; font-weight: normal;">
      <span style="font-family: 'Times New Roman', serif; font-size: 23px;">  e ficou </span>
        <span style="font-weight: bold; <?php echo ($mediaExibicao < 10) ? 'color: red;' : ''; ?>">
            <?php echo ($mediaExibicao < 10) ? "Reprovado(a)" : "Aprovado(a)"; ?>
        </span>
    </span>
<?php endif; ?>


  com os seguintes resultados:
      <?php

 




// Função auxiliar para processar a string de notas e destacar em vermelho as notas menores que 10
function processNotes($noteString, &$flag) {
    $noteString = trim($noteString);
    // Se estiver vazia ou for o traço, retorna o próprio traço
    if ($noteString === '' || $noteString === '-') {
        return '-';
    }
    $arr = explode(',', $noteString);
    $resultArr = [];
    foreach ($arr as $note) {
        $note = trim($note);
        if (is_numeric($note)) {
            if ($note < 10) {
                $flag = true; // Sinaliza que pelo menos uma nota é menor que 10
                $resultArr[] = "<span style='color:red;'>$note</span>";
            } else {
                $resultArr[] = $note;
            }
        } else {
            $resultArr[] = $note;
        }
    }
    return implode(', ', $resultArr);
}

 

    // Para outras classes, a exibição das notas permanece como estava
    $total_notas = count($notas);
    $count = 0;
    foreach ($notas as $nota) {
        if ($count > 0) {
            echo $count == $total_notas - 1 ? ' e ' : ', ';
        }
        // Aplica a cor vermelha caso a nota seja menor que 10
        $classe = $nota['nota'] < 10 ? 'vermelho' : '';
        $nota_extenso = numeroPorExtenso($nota['nota']);
        echo '<span class="' . $classe . '">'
             . htmlspecialchars($nota['nome_disciplina']) . ' ' . htmlspecialchars($nota['nota'])
             . ' (' . $nota_extenso . ')</span>';
        $count++;
    }
    
    

?>

<!-- Bloco de exibição da classificação final -->
<div style="margin-top: 10px;  margin-bottom:-30px">
    <?php if ($aluno['classificacao'] !== 'FREQUENTOU') : ?>
        <?php
        // Define o estilo base
        $estilo = "";
        // Se a média for menor que 10, acrescenta a cor vermelha
        if ($mediaExibicao < 50) {
            $estilo .= " color: red;";
        }
        ?>
       
    <?php endif; ?>
</div>

            <span>___________________________________________</span><br>
            <div style="position: absolute; margin-top: 22px; margin-left: 495px;">
                <div style="position: absolute; top: 0; left: 90%; width: 3cm; height: 1px; transform: rotate(55deg); font-weight:bold">____________________</div>
            </div>
           
        </div>
        
        <div style="position: relative; top: 168px; left: 628px;">
        ____________________________________________________
          </div>

<br>
<br>
    <br><br> <br><br> <br><br> <br><br><br> <br>  
       <div>

<div style="margin-bottom:-10px" class="primeira">
    

<div class="justify" style="font-family: 'Times New Roman', serif; font-size: 23px; line-height: 1.3;">
            Consta do livro de termos da turma <?php echo htmlspecialchars($aluno['turma']); ?>, sob o n.º <?php echo htmlspecialchars($aluno['numero']); ?> do ano lectivo <?php echo htmlspecialchars($aluno['numero_ano_letivo']); ?>,
            e leva o selo branco em uso nesta Direcção
        </div>
      
        
        <span style="font-family: 'Times New Roman', serif; font-size: 23px;"> 
    Guichê do aluno em São Tomé, 
    <?php 
    // Verifica se a data existe e não está vazia
    $data_imprimir = !empty($aluno['data_imprimir']) ? $aluno['data_imprimir'] : 'now';
    $data = new DateTime($data_imprimir);

    $meses = [
        1 => "janeiro",
        2 => "fevereiro",
        3 => "março",
        4 => "abril",
        5 => "maio",
        6 => "junho",
        7 => "julho",
        8 => "agosto",
        9 => "setembro",
        10 => "outubro",
        11 => "novembro",
        12 => "dezembro"
    ];

    $dia = $data->format("d");
    $mes = $data->format("n");
    $ano = $data->format("Y");
    $hora = $data->format("H:i:s");

    echo "{$dia} de {$meses[$mes]} de {$ano}";
?>

</span>


        <br>
        <div style="text-align: right; position: relative;  margin-top: 15px; font-family: 'Times New Roman'; font-size:22px">
            <span style="text-align: center;">
            O Chefe da Secretaria,<br> 
            
            </span>
        </div>

<br> 


         
     <div style="position: relative;  font-family: 'Times New Roman', serif; font-size: 23px; line-height: 1.3; margin-top: -15px;">
      <span>
        Art.º 8.º 10,00 <br>
        Art.º 9.º 15,00 <br>
     
       
        <span >Art.º 20º 20,00</span> <br> 
        <span style="color: red;">Soma 45,00</span><br> 
        <span>Imp. Esp: 9,00 <br>
    Papel e selo: 50,00 <br>
 <span style="color: red;">Total: 104,00</span></span>
      </span>
     </div>

      
        <div class="justify" style="font-family: 'Times New Roman', serif; font-size: 23px; margin-top:7px">
            Obs.: O emolumento é cobrado por meio de estampilha fiscal nos termos do artigo 26.º 
            do  <span style="margin-left: 15px;"></span> Decreto <span style="margin-left: 15px;"></span>Lei n.º58/80 de 18/12 --------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------
 
        </div>

</div>



              <!-- Footer fixo: QR code à esquerda e código da certidão à direita -->
              <div class="footer" style="background-color: transparent;">
    <div class="footer-left">
    <img src="../../gerar_qr.php?codigo=https://certificados.escoladados.store/certidao/admin/certidao_feita/track_qr.php?codigo_certidao=<?php echo htmlspecialchars($aluno['codigo_certidao']); ?>" alt="QR Code do aluno" style="max-height: 100px;">

      <span style="margin-left: 700px; font-size:18px; position:absolute; margin-bottom:0px">
   MECCES <span style="font-weight: bolder; letter-spacing:1px;">
        <?php echo htmlspecialchars($aluno['codigo_certidao']); ?>
      </span>
   </span>

    </div>
 
  </div>
       </div>
        


        </div>



    <script>
function updateStatusAndPrint() {
    // Obtém o ID do aluno via GET ou outra maneira
    const alunoId = <?php echo $id; ?>;
    
    // Envia a requisição AJAX para atualizar o status
    const xhr = new XMLHttpRequest();
    xhr.open('POST', 'atualiza_status.php', true);
    xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
    xhr.onreadystatechange = function() {
        if (xhr.readyState === 4 && xhr.status === 200) {
            // Quando o status for atualizado com sucesso, chama a função de impressão
            window.print();
        }
    };
    xhr.send('id=' + alunoId);
}
</script>

</body>
</html>