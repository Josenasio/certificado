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
        a.data_registro, 
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
        17 => "DEZASSETE VALORES", 18 => "DEZOITO VALORES", 19 => "DEZENOVE VALORES", 20 => "VINTE VALORES"
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
  margin: 1cm; /* ajuste conforme necessário */

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
   
      bottom: 0;
      left: 0;
      width: 100%;
      display: flex;
      justify-content: space-between;
      align-items: center;
      padding: 10px;
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
        bottom: -20px;
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

<div class="container" style="margin-top: -40px;">
        <div class="center">
            <span style="font-family: 'Lucida Sans', sans-serif; font-size: 31px;">REPÚBLICA DEMOCRÁTICA </span>
            <img src="imagem/brasao.webp" alt="Brasão de S. Tomé e Príncipe" width="60">
            <span style="font-family: 'Lucida Sans', sans-serif; font-size: 31px;">DE SÃO TOMÉ E PRÍNCIPE</span><br> 
            <span style="font-family: 'Bookman Old Style', serif; font-size: 20px; font-weight: bold; font-style: italic;">(Unidade - Disciplina - Trabalho)</span><br>
            <span style="font-family: 'Lucida Sans', sans-serif; font-size: 22.5px; font-weight: bold;">MINISTÉRIO DA EDUCAÇÃO, CULTURA, CIÊNCIA E ENSINO SUPERIOR</span><br>
           <span style="font-family: 'Lucida Sans', sans-serif; font-size: 22.5px; font-weight: bold;"> Direcção do Ensino Secundário e Técnico Profissional</span>
        </div>
        

                                                <div style="margin-left: 830px; font-family: 'Bookman Old Style', serif; font-size: 20px; font-style: italic; text-align:center">
                                                    
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
    <span style="font-family: 'Arial', serif; font-size: 28px; font-style: italic;">CERTIFICO</span>, em cumprimento do despacho exarado em requerimento que fica arquivado neste
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
    
    




    <?php if ($aluno['curso'] != 'Geral'): ?>
                curso de <span style="font-family: 'Garamond', serif; font-size: 26px; font-weight: bold;">
                    <?php echo htmlspecialchars($aluno['curso']); ?>
                </span>
            <?php endif; ?>
             na(o) <span style="font-family: 'Garamond', serif; font-size: 26px; font-weight: bold;">
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

<!-- Bloco de exibição da classificação final -->
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
        <span style="font-family: 'Times New Roman', serif; font-size: 25px; line-height: .1; font-weight: bold; font-style: italic;">
            Foi-lhe atribuída a classificação final de <span style="<?php echo $estilo; ?>"><?php echo $mediaExibicao . ' (' . numeroPorExtenso($mediaExibicao) . ')'; ?></span>
        </span>
    <?php endif; ?>
</div>

            <span>___________________________________________</span><br>
            <div style="position: absolute; margin-top: 22px; margin-left: 495px;">
                <div style="position: absolute; top: 0; left: 90%; width: 3cm; height: 1px; background-color: black; transform: rotate(55deg); font-weight:bold">_________</div>
            </div>
           
        </div>
        
        <div style="position: relative; top: 64px; left: 556px;">
        _______________________________________________________________ 
          </div>

<br>
<br>
    <br><br> <br>
        <div class="justify" style="font-family: 'Times New Roman', serif; font-size: 23px; line-height: 1.3;">
            Consta do livro de termos da turma <?php echo htmlspecialchars($aluno['turma']); ?>, sob o n.º <?php echo htmlspecialchars($aluno['numero']); ?> do ano lectivo <?php echo htmlspecialchars($aluno['numero_ano_letivo']); ?>,
            e leva o selo branco em uso nesta Direcção
        </div>
      
        
        <span style="font-family: 'Times New Roman', serif; font-size: 23px; ">Guichê do aluno em São Tomé, <?php
// Lista dos meses em português
$meses = [
    1 => 'janeiro', 2 => 'fevereiro', 3 => 'março', 4 => 'abril',
    5 => 'maio', 6 => 'junho', 7 => 'julho', 8 => 'agosto',
    9 => 'setembro', 10 => 'outubro', 11 => 'novembro', 12 => 'dezembro'
];

// Criar o objeto DateTime com a data atual
$date = new DateTime(); // Pega a data e hora atual

// Formatar a data manualmente
$dia = $date->format('d');
$mes = $meses[(int)$date->format('m')]; // Mês em português
$ano = $date->format('Y');

// Exibir a data
echo "{$dia} de {$mes} de {$ano}";
?>.

</span>

        <br>
        <div style="text-align: right; position: relative;  margin-top: 15px; font-family: 'Times New Roman'; font-size:22px">
            <span style="text-align: center;">
            O Chefe da Secretaria,<br> 
            
            </span>
        </div>

         
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
      <img src="../../gerar_qr.php?codigo=https://certificados.escoladados.store/certidao/confirmar_qr.php?codigo_certidao=<?php echo htmlspecialchars($aluno['codigo_certidao']); ?>" alt="QR Code do aluno" style="max-height: 100px;">
   
      <span style="margin-left: 700px; font-size:18px; position:absolute; margin-bottom:0px">
   MECCES <span style="font-weight: bolder; letter-spacing:1px;">
        <?php echo htmlspecialchars($aluno['codigo_certidao']); ?>
      </span>
   </span>

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
