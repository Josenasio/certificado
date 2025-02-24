<?php

include_once($_SERVER['DOCUMENT_ROOT'].'/certidao/conexao/connect.php');

// Se o código da certidão não for informado, exibe o formulário de verificação
if (!isset($_GET['codigo_certidao'])) {
    ?>


    <!DOCTYPE html>
    <html lang="pt">


<!-- Certifique-se de incluir o Font Awesome no seu projeto -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">

<!-- Certifique-se de incluir os Bootstrap Icons no seu projeto -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css">


    <head>
        <meta charset="UTF-8">
        <title>Verificar Certidão</title>
        <style>
            body {
                font-family: Arial, sans-serif;
                background-color: #1B203B;
                display: flex;
                align-items: center;
                justify-content: center;
                height: 100vh;
                margin: 0;
            }
            .form-container {
                background: #fff;
                width: 320px;
                padding: 20px;
                border: 1px solid #ccc;
                border-radius: 8px;
                box-shadow: 0 0 10px rgba(0,0,0,0.1);
            }
            .certidao-box {
            max-width: 800px;
            margin: 0 auto;
            background: #282C4A;
            border-radius: 10px;
            box-shadow: 0 0 20px rgba(0,0,0,0.1);
            padding: 2rem;
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
   

    <div class="main-content">
        <div class="container">

        <div class="certidao-box">

        <h2 class="text-center mb-4" style="color: green;">
    <i class="fas fa-certificate"></i> VALIDAÇÃO DO CERTIFICADO
</h2>
            <form method="GET" class="mb-4">
                <div class="mb-3">
                    <label for="codigo_certidao" class="form-label" style="color: #ffffff;">Código do certificado</label>
                    <input type="text" 
       class="form-control form-control-lg form-control-uppercase" 
       id="codigo_certidao" 
       name="codigo_certidao" 
       required
       maxlength="10"
       pattern="[A-Z0-9]{10}"
       title="Digite exatamente 10 caracteres alfanuméricos"
       onkeydown="if(event.key === ' ') { event.preventDefault(); }"
       oninput="this.value = this.value.replace(/\s/g, '').toUpperCase()">

                </div>
                <div class="d-grid">
    <button type="submit" class="btn btn-primary btn-lg">
        <i class="fa fa-check-circle"></i> Verificar
    </button>
</div>


            </form>
        </div>
        </div>
        </div>
    </body>
    </html>



    <?php
    exit;
} else {
    // Processa a verificação da certidão
    $codigo = trim($_GET['codigo_certidao']);

    $query = "SELECT 
        a.id, 
        a.nome, 
        a.naturalidade, 
        a.nome_mae, 
        a.nome_pai, 
        a.codigo_certidao, 
        a.status_certidao, 
        a.data_registro, 
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
        d.nome AS distrito
    FROM alunos a
    LEFT JOIN cursos c ON a.id_curso = c.id
    LEFT JOIN classe cl ON a.classe_id = cl.id
    LEFT JOIN turma t ON a.turma_id = t.id
    LEFT JOIN escola e ON a.escola_id = e.id
    LEFT JOIN genero g ON a.genero_id = g.id
    LEFT JOIN classificacao class ON a.classificacao_id = class.id
    LEFT JOIN ano_lectivo al ON a.ano_lectivo_id = al.id
    LEFT JOIN distrito d ON a.distrito_id = d.id
    WHERE a.codigo_certidao = ?";

    $stmt = $mysqli->prepare($query);
    if (!$stmt) {
        die("Erro na preparação da consulta: " . $mysqli->error);
    }
    $stmt->bind_param("s", $codigo);
    $stmt->execute();
    $result = $stmt->get_result();














    

    // Se o código não for encontrado, exibe mensagem de erro
    if ($result->num_rows <= 0) {
        echo <<<HTML
<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Certidão Inválida</title>
    <!-- Ícones Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" crossorigin="anonymous" referrerpolicy="no-referrer" />

    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;700&display=swap" rel="stylesheet">
    <style>
        /* Estilos Globais */
        body {
            font-family: 'Roboto', sans-serif;
            background: rgba(231, 77, 60, 0.37);
            margin: 0;
            padding: 0;
            display: flex;
            align-items: center;
            justify-content: center;
            height: 100vh;
        }
        /* Container Central */
        .container {
            background: #fff;
            padding: 40px;
            border-radius: 12px;
            box-shadow: 0 4px 15px rgba(252, 4, 4, 0.36);
            text-align: center;
            max-width: 400px;
            width: 90%;
            border: 1px solid red;
        }
        /* Ícone de Alerta */
        .icon {
            font-size: 60px;
            color:rgb(255, 25, 0);
            margin-bottom: 20px;
        }
        /* Título */
        h2 {
            margin: 0;
            font-size: 28px;
            color: red;
            letter-spacing:1px;
        }
        /* Mensagem */
        p {
            color: #FFA500;;
            margin: 20px 0;
            font-size: 16px;
        }
        /* Botão de Ação */
        a {
            display: inline-block;
            text-decoration: none;
            color: #fff;
            background:red;
            padding: 12px 25px;
            border-radius: 5px;
            font-weight: bold;
            transition: background 0.3s;
          
        }
        a:hover {
            background: transparent;
            color:red;
            border: 1px solid red;
            padding: 11px 24px;
        }
    </style>
</head>
<body>
    <div class="container">
    <i class="fa-solid fa-triangle-exclamation icon"></i>

        <h2>Certificado Inválido</h2>
        <p>O código do Certificado não consta na base de dados da DESTP.<br>Por favor contacte a Direção </p>




        
        <a href="https://wa.me/2399940636?text=Olá%2C+gostaria+de+saber+mais">Enviar mensagem no WhatsApp</a>

    </div>
</body>
</html>
HTML;
        exit;
    }
    $aluno = $result->fetch_assoc();
    $id = $aluno['id'];
    $stmt->close();

    // Busca as notas do aluno
    $query_notas = "SELECT 
        n.nota, 
        d.nome_disciplina
    FROM notas n
    LEFT JOIN disciplina d ON n.id_disciplina = d.id
    WHERE n.id_aluno = ?
    ORDER BY d.numero_ordem ASC";
    $stmt_notas = $mysqli->prepare($query_notas);
    $stmt_notas->bind_param("i", $id);
    $stmt_notas->execute();
    $result_notas = $stmt_notas->get_result();

    $notas = [];
    while ($row = $result_notas->fetch_assoc()) {
        $notas[] = $row;
    }
    $stmt_notas->close();






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







    // Calcula a média das notas
    $totalNotas = array_sum(array_column($notas, 'nota'));
    $quantidadeNotas = count($notas);
    $media = $quantidadeNotas > 0 ? round($totalNotas / $quantidadeNotas) : 0;

    // Função para converter números em extenso (até 20)
    function numeroPorExtenso($numero) {
        $numero = round($numero);
        $extenso = [
            0 => "ZERO VALOR", 1 => "UM VALOR", 2 => "DOIS VALORES", 3 => "TRÊS VALORES", 4 => "QUATRO VALORES", 5 => "CINCO VALORES",
            6 => "SEIS VALORES", 7 => "SETE VALORES", 8 => "OITO VALORES", 9 => "NOVE VALORES", 10 => "DEZ VALORES", 11 => "ONZE VALORES",
            12 => "DOZE VALORES", 13 => "TREZE VALORES", 14 => "CATORZE VALORES", 15 => "QUINZE VALORES", 16 => "DEZASSEIS VALORES",
            17 => "DEZASSETE VALORES", 18 => "DEZOITO VALORES", 19 => "DEZENOVE VALORES", 20 => "VINTE VALORES"
        ];
        return isset($extenso[$numero]) ? $extenso[$numero] : "";
    }
    $mysqli->close();
}
?>

<!DOCTYPE html>
<html lang="pt">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Certificado de Habilitação</title>
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@fortawesome/fontawesome-free@6.4.0/css/all.min.css">
  

  <link href="https://fonts.googleapis.com/css2?family=Patrick+Hand&display=swap" rel="stylesheet">

  
  <style>
    /* Estilo geral para centralizar a página na tela */
    body {
      background-color: #1B203B;
      margin: 0;
      padding: 20px;
      display: flex;
      align-items: center;
      justify-content: center;
    }
    /* Container que simula uma folha A4, com medidas em mm e responsivo */
    .container {
      background: #ffffff;
      width: 200mm;
      max-width: 100%;
      min-height: 253mm;
      padding: 1cm;
      margin: auto;
      box-shadow: 0 0 10px rgba(0,0,0,0.3);
      position: relative;
      font-family: monospace;
      color: #333399;
    }
    /* Classes utilitárias */
    .center { text-align: center; }
    .justify { text-align: justify; }
    .red { color: red; }
    .vermelho { color: red; }
    
    /* Elementos posicionados */
    .signature-line {
      position: absolute;
      top: 10cm; /* ajuste conforme necessário */
      right: 1cm;
      width: 3cm;
      height: 1px;
      background-color: black;
      transform: rotate(55deg);
      font-weight: bold;
    }
    .signature-underline {
      text-align: right;
      margin-top: 1cm;
      margin-right: 1cm;
    }
    .footer {
      position: absolute;
      bottom: 1cm;
      right: 1cm;
      font-size: 14px;
      background-color: white;
      padding: 5px;
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


    .watermark {
      position: absolute;
      top: 50%;
      left: 50%;
      transform: translate(-50%, -50%) rotate(0deg);
      width: 80%;           /* Ajuste conforme necessário */
      height: 80%;          /* Ajuste conforme necessário */
      background: url('admin/certidao_feita/imagem/brasao.webp') no-repeat center center;
      background-size: contain;
      opacity: 0.1;         /* Ajuste a opacidade se necessário */
      z-index: 0;
      pointer-events: none;
      user-select: none;
      -webkit-print-color-adjust: exact; /* Para o Chrome e Safari */
      print-color-adjust: exact;
    }
       
    
    /* Media query para dispositivos móveis */
    @media screen and (max-width: 600px) {
 

      .container {
        width: calc(100% - 1px); /* ocupa quase toda a largura da tela */
        padding: 10px;
        /* Removemos a altura fixa para que o conteúdo se ajuste naturalmente */
        min-height: 200mm;
        font-size: 10px;
      }



      .republica {
        font-size: 105% !important;
      }

      .center img{
        width: 25px !important;
      }
      .unidade {
        font-size: 11px !important;
      }

      .ministerio {
        font-size: 10px !important;
      }
     #certificado {
        font-size: 13px !important;
        margin-top:-3%!important;
      }


      #depoiscertificado {
        font-size:9px !important;
      }
      #arcangela {
        font-size: 9.5px !important;
      }

      #linha {
        font-size: 4.3px !important;
       font-weight: bolder !important;
      }

      #linha2 {
        font-size: 7.2444px !important;
       font-weight: bolder !important;
      }


      #corpo {
        font-size: 9px !important;
      }


      .center {
        margin-top: 12% !important;
      }



      #certifico {
        font-size: 10px !important;
      }
      .nomealuno {
        font-size: 10px !important;
      }
      #classifica {
        font-size: 11px !important;
      }

      #classe_aluno {
        font-size: 10.5px !important;
      }

      #curso_aluno {
        font-size: 10.5px !important;
      }

      #escola_aluno {
        font-size: 11px !important;
      }
      .notafinal {
        font-size: 11.2px !important;
      }

      #classif_final {
        font-size: 11px !important;
      }

      #consta_livro {
        font-size: 9px !important;
      }
      #guiche_aluno {
        font-size: 9px !important;
      }

      #chefesecretaria {
        font-size: 9px !important;
      }

      #artigo {
        font-size: 9px !important;
      }

      #obs {
        font-size: 9px !important;
      }

      /* Ajustes nos elementos posicionados */
      .signature-line {
        top: calc(10cm * 0.8); /* reduzindo proporcionalmente */
        right: 1cm;
        width: calc(3cm * 0.8);
      }
      .signature-underline {
        margin-top: calc(1cm * 0.8);
        margin-right: 1cm;
      }
      .footer {
        font-size: 11px;
        padding: 3px;
        bottom: .2cm;
        right: .2cm;
      }


      #obliquap {
        margin-top: 20px !important; 
        margin-left: 150px !important;
      }


      #obliqua {
        left: 10% !important;
 
    
       transform: rotate(36deg) !important;
      }




      table {
        width: 100% !important;
        font-size: 10px !important;
      }

      #visto{
        margin-left: 250px !important;
        font-size: 10px !important;
      }


      .footer{
       
        font-size: 10px !important;
      }
    }
  </style>
</head>
<body>







  <div class="container">
      <!-- Marca d'água -->
  <div class="watermark"></div>


  <img src="admin/certidao_feita/imagem/brasao.webp" alt="Brasão de S. Tomé e Príncipe" width="60" style="margin-left:39.5%; margin-top:-3%; position:absolute">

    <!-- Cabeçalho principal -->
    <div class="center" style="margin-top: 4%;">

      <span style="font-family: 'Lucida Sans', sans-serif; font-size: 25px;" class="republica">REPÚBLICA DEMOCRÁTICA DE SÃO TOMÉ E PRÍNCIPE </span><br> 
      
      <span style="font-family: 'Lucida Sans', sans-serif; font-size: 16.5px; font-weight: bold;" class="ministerio">
        MINISTÉRIO DA EDUCAÇÃO, CULTURA, CIÊNCIA E ENSINO SUPERIOR
      </span><br>
      <span style="font-family: 'Lucida Sans', sans-serif; font-size: 16.5px; font-weight: bold;" class="ministerio">
        Direcção do Ensino Secundário e Técnico Profissional
      </span>
    </div>
    <!-- Bloco posicionado no canto superior direito -->
    <div style="margin-left: 600px; font-family: 'Bookman Old Style', serif; font-size: 14px; font-style: italic; text-align:center" id="visto">VISTO <br>
    O<span> DIRECTOR</span>
    <img src="assinaturas/assinaD.png" alt="Brasão de S. Tomé e Príncipe" width="60" 
    style="position: absolute; top: 13%; left: 82%; ">
    </div>
    <br>
    <!-- Título do certificado -->
    <div class="center" style="font-family: 'Cambria', serif; font-size: 24px; font-weight: bold; text-decoration: underline;" id="certificado">
      CERTIFICADO DE HABILITAÇÃO
    </div>
    <br>
    <!-- Parágrafo introdutório -->
    <div class="justify" style="font-family: 'Times New Roman', serif; font-size: 13px; line-height: 1.2; margin-bottom:5px; word-wrap: break-word;" id="depoiscertificado">
      <span style="font-family: Arial, sans-serif; font-size: 14px; font-style: italic;" id="arcangela">
        Arcângela Ferreira do Nascimento Luís Miguel
      </span>, Chefe do departamento e de secretaria da Direcção
      do Ensino Secundário e Técnico Profissional em São Tomé
      <span id="linha">----------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------</span>
    </div>
    <!-- Corpo do certificado -->
    <div class="justify" style="font-family: 'Times New Roman', serif; font-size: 17px; line-height: 1.5;" id="corpo">
      <span style="font-family: 'Arial', serif; font-size: 22px; font-style: italic;" id="certifico">
        CERTIFICO,
      </span> em cumprimento do despacho exarado em requerimento que fica arquivado neste
      Guichê que, <span class="nomealuno" style="font-family: 'Lucida Sans', sans-serif; font-size: 22px; font-weight: bold;"><?php echo mb_strtoupper(htmlspecialchars($aluno['nome']), 'UTF-8'); ?></span>,
      natural de <?php echo htmlspecialchars($aluno['naturalidade']); ?> São-Tomé,
      Distrito de <?php echo htmlspecialchars($aluno['distrito']); ?>, nascido(a) em <?php 
        setlocale(LC_TIME, 'pt_PT.UTF-8', 'Portuguese_Portugal', 'pt_BR.UTF-8', 'Portuguese_Brazil');
        $data_nascimento = $aluno['data_nascimento'];
        $formatter = new IntlDateFormatter('pt_PT', IntlDateFormatter::LONG, IntlDateFormatter::NONE);
        echo $formatter->format(new DateTime($data_nascimento));
      ?>,
      portador(a) de Bilhete de Identidade n.º <?php echo htmlspecialchars($aluno['bi']); ?>, filho(a) de <?php echo htmlspecialchars($aluno['nome_pai']); ?>
      e de <?php echo htmlspecialchars($aluno['nome_mae']); ?>,
      <span id="classifica" style="font-family: 'Broadway', sans-serif; font-size: 22px; font-weight: bold; color: #333399">
        <?php echo htmlspecialchars($aluno['classificacao']); ?>
      </span> no ano lectivo <?php echo htmlspecialchars($aluno['nome_extenso']); ?> como
      aluno(a) <?php echo htmlspecialchars($aluno['genero']); ?> da 
      <span id="classe_aluno" style="font-family: 'Lucida Handwriting', cursive; font-size: 19px; text-decoration: underline; font-weight: bold; color:red">
        "<?php echo htmlspecialchars($aluno['classe']); ?>"
      </span>

      
      <?php if ($aluno['curso'] != 'Geral'): ?>
        curso de <span id="curso_aluno" style="font-family: 'Garamond', serif; font-size: 19px; font-weight: bold;">
          <?php echo htmlspecialchars($aluno['curso']); ?>
        </span>
      <?php endif; ?>
      na <span id="escola_aluno" style="font-family: 'Garamond', serif; font-size: 21px; font-weight: bold;">
        <?php echo htmlspecialchars($aluno['escola']); ?>
      </span> com os seguintes resultados: 
      
      
      
      
    
    
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

 
if ($aluno['classe_id'] == 8) {  
    // Variáveis para acumular a soma das médias e a contagem de disciplinas
    $somaMedias = 0;
    $contadorMedias = 0;
    
    // Exibe a tabela agregada com as disciplinas, notas e média
    echo '<table style="width: 100%; border-collapse: collapse; margin: 2px 0; font-size: 20px; line-height: 1;">';
    echo '<tr>';
    echo '<th style="border: .1px solid #000; padding: 2px; text-align: center; font-weight: 700;">DISCIPLINAS</th>';
    echo '<th style="border: .1px solid #000; padding: 2px; text-align: center; font-weight: 700;">10ª</th>';
    echo '<th style="border: .1px solid #000; padding: 2px; text-align: center; font-weight: 700;">11ª</th>';
    echo '<th style="border: .1px solid #000; padding: 2px; text-align: center; font-weight: 700;">12ª</th>';
    echo '<th style="border: .1px solid #000; padding: 2px; text-align: center; font-weight: 700;">EXAME</th>';
    echo '<th style="border: .1px solid #000; padding: 2px; text-align: center; font-weight: 700;">FINAL</th>';
    echo '</tr>';
    
    foreach ($disciplinas_agregado as $disciplina) {
        // Flag para identificar se alguma nota é menor que 10
        $hasRed = false;
        
        // Obtém os valores originais (se existirem) ou atribui traço
        $nota10 = (isset($disciplina['10ª_classe']) && $disciplina['10ª_classe'] !== '') ? $disciplina['10ª_classe'] : '-';
        $nota11 = (isset($disciplina['11ª_classe']) && $disciplina['11ª_classe'] !== '') ? $disciplina['11ª_classe'] : '-';
        $nota12 = (isset($disciplina['12ª_classe']) && $disciplina['12ª_classe'] !== '') ? $disciplina['12ª_classe'] : '-';
        $exame  = (isset($disciplina['exame'])       && $disciplina['exame']       !== '') ? $disciplina['exame']       : '-';
        
        // Processa as notas para destacar (em vermelho) as que forem menores que 10
        $displayNota10 = processNotes($nota10, $hasRed);
        $displayNota11 = processNotes($nota11, $hasRed);
        $displayNota12 = processNotes($nota12, $hasRed);
        $displayExame   = processNotes($exame, $hasRed);
        
        // Obtém os valores numéricos ou null se não existirem
        $grade10 = (isset($disciplina['10ª_classe']) && $disciplina['10ª_classe'] !== '') ? floatval($disciplina['10ª_classe']) : null;
        $grade11 = (isset($disciplina['11ª_classe']) && $disciplina['11ª_classe'] !== '') ? floatval($disciplina['11ª_classe']) : null;
        $grade12 = (isset($disciplina['12ª_classe']) && $disciplina['12ª_classe'] !== '') ? floatval($disciplina['12ª_classe']) : null;
        $examVal = (isset($disciplina['exame'])       && $disciplina['exame']       !== '') ? floatval($disciplina['exame'])       : null;
        
        // Conta quantos dos campos 10ª, 11ª e 12ª foram informados
        $countFields = 0;
        if ($grade10 !== null) $countFields++;
        if ($grade11 !== null) $countFields++;
        if ($grade12 !== null) $countFields++;
        
        // Calcula a média apenas se houver ao menos um dos campos 10ª, 11ª ou 12ª
        if ($countFields > 0) {
            if ($examVal !== null) {
                // Se existir nota de exame, aplica a lógica:
                // (10ª + 11ª + (12ª * 0.7) + (exame * 0.3)) dividido pela quantidade de campos informados entre 10ª, 11ª e 12ª
                $sum = 0;
                if ($grade10 !== null) {
                    $sum += $grade10;
                }
                if ($grade11 !== null) {
                    $sum += $grade11;
                }
                if ($grade12 !== null) {
                    $sum += $grade12 * 0.7;
                }
                $media = ($sum + $examVal * 0.3) / $countFields;
                $media = round($media);
            } else {
                // Se não existir nota de exame, a média é a soma dos campos existentes dividida pela quantidade deles
                $sum = 0;
                if ($grade10 !== null) {
                    $sum += $grade10;
                }
                if ($grade11 !== null) {
                    $sum += $grade11;
                }
                if ($grade12 !== null) {
                    $sum += $grade12;
                }
                $media = $sum / $countFields;
                $media = round($media);
            }
            
            // Acumula a média desta disciplina
            $somaMedias += $media;
            $contadorMedias++;
        } else {
            $media = '-';
        }
        
        // Se a média for menor que 10, exibe em vermelho
        $mediaStyle = ($media !== '-' && $media < 10) ? "color:red;" : "";
        
        echo '<tr>';
        echo '<td style="border: 1px solid #000; padding: 2px; line-height: 1;">'
                . htmlspecialchars($disciplina['disciplina']) . '</td>';
        echo '<td style="border: 1px solid #000; padding: 2px; text-align: center;">'
                . $displayNota10 . '</td>';
        echo '<td style="border: 1px solid #000; padding: 2px; text-align: center;">'
                . $displayNota11 . '</td>';
        echo '<td style="border: 1px solid #000; padding: 2px; text-align: center;">'
                . $displayNota12 . '</td>';
        echo '<td style="border: 1px solid #000; padding: 2px; text-align: center;">'
                . $displayExame . '</td>';
        echo '<td style="border: 1px solid #000; padding: 2px; text-align: center; ' . $mediaStyle . '">'
                . htmlspecialchars($media) . '</td>';
        echo '</tr>';
    }
    echo '</table>';
    
    // Calcula a média final geral (soma de todas as médias dividida pela quantidade de disciplinas)
    if ($contadorMedias > 0) {
        $mediaFinal = round($somaMedias / $contadorMedias);
    } else {
        $mediaFinal = 0;
    }
    // Define a variável que será usada para exibição na classificação
    $mediaExibicao = $mediaFinal;
    
} else {
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
    
    // Calcula a média geral para as outras classes, para definir o $mediaExibicao
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
}
?>




    </div>
    <div style="margin-top: 10px;  margin-bottom:-30px">
    <?php if ($aluno['classificacao'] !== 'FREQUENTOU') : ?>
        <?php
        // Define o estilo base
        $estilo = "";
        // Se a média for menor que 10, acrescenta a cor vermelha
        if ($mediaExibicao < 10) {
            $estilo .= " color: red;";
        }
        ?>
        <span class="notafinal" style="font-family: 'Times New Roman', serif; font-size: 25px; line-height: .1; font-weight: bold; font-style: italic;">
            Foi-lhe atribuída a classificação final de <span style="<?php echo $estilo; ?>"><?php echo $mediaExibicao . ' (' . numeroPorExtenso($mediaExibicao) . ')'; ?></span>
        </span>
    <?php endif; ?>
</div>
      <br>
      <br>
      <span id="linha">**********************************************************************************************************<br>
      **********************************************************************************************************</span>
      
    <br>
    <!-- Bloco com informações complementares -->
    <div id="consta_livro" class="justify" style="font-family: 'Times New Roman', serif; font-size: 17px; line-height: 1.5;">
      Consta do livro de termos da turma <?php echo htmlspecialchars($aluno['turma']); ?>, sob o n.º <?php echo htmlspecialchars($aluno['numero']); ?> do ano lectivo <?php echo htmlspecialchars($aluno['numero_ano_letivo']); ?>,
      e leva o selo branco em uso nesta Direcção
    </div>
    <br>
    <span id="consta_livro" style="font-family: 'Times New Roman', serif; font-size: 17px;"> 
    Guichê do aluno em São Tomé, 
    <?php 
        $data = new DateTime($aluno['data_imprimir']);
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
    ?>.











</span>
    <br><br>
   
    <div style="text-align: right; position: relative; font-family: 'Times New Roman'; font-size:16px">
      <span style="text-align: center;" id="chefesecretaria">
      <img src="assinaturas/arcang" alt="Brasão de S. Tomé e Príncipe" width="28%" 
      style="position: absolute; top: -5%; left: 75%; ">
      
      </span>
    </div>
    <br>
    <div id="artigo" style="position: relative; font-family: 'Times New Roman', serif; font-size: 17px; line-height: 1.2;"> 
      
    

    <span style="  font-family: 'Patrick Hand', cursive;  position: absolute; margin-top:13.8%; margin-left:32%; z-index: 9999; font-size: 86%;">
  <?php 
        $data = new DateTime($aluno['data_imprimir']);
       
        $dia = $data->format("d");
        $mes = $data->format("n");
        $ano = $data->format("Y");
        $hora = $data->format("H:i:s");
        echo "{$dia} / {$mes}  / {$ano}";
    ?>
    </span>





    <img src="assinaturas/image.png" alt="Brasão de S. Tomé e Príncipe" width="35%" 
    style="position: absolute; top: 27%; left: 20%; ">
      <span>
        Art.º 8.º 10,00 <br>
        Art.º 9.º 15,00 <br>
        <span>Art.º 20º 20,00</span> <br> 
        <span style="color: red;">Soma 45,00</span><br> 
        <span>Imp. Esp: 9,00 <br>
        Papel e selo: 50,00 <br>
        <span style="color: red;">Total: 104,00</span>
        </span>
      </span>
    </div>
     
    <div id="obs" class="justify" style="font-family: 'Times New Roman', serif; font-size: 17px;">
      Obs.: O emolumento é cobrado por meio de estampilha fiscal nos termos do artigo 26.º 
      do Decreto Lei n.º58/80 de 18/12 <span id="linha2">-----------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------</span>
    </div>
    <br>
    <span class="footer">



      MECSES <span style="font-weight: bolder; letter-spacing:1px">
        <?php echo htmlspecialchars($aluno['codigo_certidao']); ?>
      </span>
    </span>
  </div>










  
</body>
</html>
