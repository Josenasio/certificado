<?php
session_start();
// Restrição de acesso – ajuste ou remova conforme necessário
if (!isset($_SESSION['id']) || $_SESSION['nivel_acesso'] !== 'Admin') {
    header("Location: ../index.php");
    exit;
}

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
    <button class="fixed-top-button" onclick="window.location.href='/certidao/Admin/index.php'">
    <i class="fas fa-arrow-left"></i> Voltar à Página Inicial
</button> 

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
        a.numero,
        a.data_nascimento, 
        a.bi, 
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
        <p>O código do Certificado não foi encontrado na base de dados.<br>Por favor, verifique o código e tente novamente!</p>
        <a href="{$_SERVER['PHP_SELF']}">Tentar novamente</a>
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
            12 => "DOZE VALORES", 13 => "TREZE VALORES", 14 => "QUATORZE VALORES", 15 => "QUINZE VALORES", 16 => "DEZASSEIS VALORES",
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
        /* Container que simula uma folha A4, com largura fixa e responsiva */
        .container {
            background: #ffffff;
            width: 200mm;
            max-width: 90%;
            min-height: 253mm;
            padding: 1cm;
            margin: auto;
            box-shadow: 0 0 10px rgba(0,0,0,0.3);
            position: relative;
            font-family: monospace;
            color: #333399;
        }
        /* Ajustes gerais para textos e alinhamento */
        .center { text-align: center; }
        .justify { text-align: justify; }
        .red { color: red; }
        .vermelho { color: red; }

        /* Posições ajustadas com valores relativos */
     
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
    </style>
</head>
<body>
    
    <button class="fixed-top-button" onclick="window.location.href='<?php echo $_SERVER['PHP_SELF']; ?>'">
         <i class="fa-solid fa-check icon"></i> Voltar para verificação
    </button>
<br>

    <div class="container">
        <!-- Cabeçalho principal -->
        <div class="center">
            <span style="font-family: 'Lucida Sans', sans-serif; font-size: 25px;">REPÚBLICA DEMOCRÁTICA </span>
            <img src="../certidao_feita/imagem/brasao.webp" alt="Brasão de S. Tomé e Príncipe" width="40">
            <span style="font-family: 'Lucida Sans', sans-serif; font-size: 25px;">DE SÃO TOMÉ E PRÍNCIPE</span><br> 
            <span style="font-family: 'Bookman Old Style', serif; font-size: 14px; font-weight: bold; font-style: italic;">
                (Unidade - Disciplina - Trabalho)
            </span><br>
            <span style="font-family: 'Lucida Sans', sans-serif; font-size: 16.5px; font-weight: bold;">
                MINISTÉRIO DA EDUCAÇÃO, CULTURA, CIÊNCIAS E ENSINO SUPERIOR
            </span><br>
            <span style="font-family: 'Lucida Sans', sans-serif; font-size: 16.5px; font-weight: bold;">
                Direcção do Ensino Secundário e Técnico Profissional
            </span>
        </div>
        <!-- Bloco posicionado no canto superior direito -->
        <div style="margin-left: 600px; font-family: 'Bookman Old Style', serif; font-size: 14px; font-style: italic; text-align:center">
                                                      VISTO<br>
                                                    O DIRECTOR
                                                   
                                                </div>
        <br>
        <!-- Título do certificado -->
        <div class="center" style="font-family: 'Cambria', serif; font-size: 24px; font-weight: bold;">
            CERTIFICADO DE HABILITAÇÃO
        </div>
        <br>
        <!-- Parágrafo introdutório -->
        <div class="justify" style="font-family: 'Times New Roman', serif; font-size: 13px; line-height: 1.2; margin-bottom:5px; word-wrap: break-word;">
            <span style="font-family: Arial, sans-serif; font-size: 14px; font-style: italic;">
                Arcângela Ferreira do Nascimento Luís Miguel
            </span>, Chefe do departamento e de secretaria da Direcção
            do Ensino Secundário e Técnico Profissional em São Tomé
         ----------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------
        </div>
        <!-- Corpo do certificado -->
        <div class="justify" style="font-family: 'Times New Roman', serif; font-size: 17px; line-height: 1.5;">
            <span style="font-family: 'Arial', serif; font-size: 22px; font-style: italic;">
                CERTIFICO
            </span>, em cumprimento do despacho exarado em requerimento que fica arquivado neste
            Guichê que, <span style="font-family: 'Lucida Sans', sans-serif; font-size: 21px; font-weight: bold;">
                <?php echo mb_strtoupper(htmlspecialchars($aluno['nome']), 'UTF-8'); ?>
            </span>, natural de <?php echo htmlspecialchars($aluno['naturalidade']); ?> São-Tomé,
            Distrito de <?php echo htmlspecialchars($aluno['distrito']); ?>, nascido(a) em <?php 
                setlocale(LC_TIME, 'pt_PT.UTF-8', 'Portuguese_Portugal', 'pt_BR.UTF-8', 'Portuguese_Brazil');
                $data_nascimento = $aluno['data_nascimento'];
                $formatter = new IntlDateFormatter('pt_PT', IntlDateFormatter::LONG, IntlDateFormatter::NONE);
                echo $formatter->format(new DateTime($data_nascimento));
            ?>,
            portador(a) de Bilhete de Identidade n.º <?php echo htmlspecialchars($aluno['bi']); ?>, filho(a) de <?php echo htmlspecialchars($aluno['nome_pai']); ?>
            e de <?php echo htmlspecialchars($aluno['nome_mae']); ?>,
            <span style="font-family: 'Broadway', sans-serif; font-size: 22px; font-weight: bold; color: #333399">
                <?php echo htmlspecialchars($aluno['classificacao']); ?>
            </span> no ano lectivo <?php echo htmlspecialchars($aluno['nome_extenso']); ?> como
            aluno(a) <?php echo htmlspecialchars($aluno['genero']); ?> da 
            <span style="font-family: 'Lucida Handwriting', cursive; font-size: 19px; text-decoration: underline; font-weight: bold; color:red">
                "<?php echo htmlspecialchars($aluno['classe']); ?>"
            </span>
            <?php if ($aluno['curso'] != 'Geral'): ?>
                curso de <span style="font-family: 'Garamond', serif; font-size: 19px; font-weight: bold;">
                    <?php echo htmlspecialchars($aluno['curso']); ?>
                </span>
            <?php endif; ?>
            na <span style="font-family: 'Garamond', serif; font-size: 21px; font-weight: bold;">
                <?php echo htmlspecialchars($aluno['escola']); ?>
            </span> com os seguintes resultados: 
            <?php 
            $total_notas = count($notas);
            $count = 0;
            foreach ($notas as $nota) {
                if ($count > 0) {
                    echo $count == $total_notas - 1 ? ' e ' : ', ';
                }
                $classe = $nota['nota'] < 10 ? 'vermelho' : '';
                $nota_extenso = numeroPorExtenso($nota['nota']);
                echo '<span class="' . $classe . '">' . htmlspecialchars($nota['nome_disciplina']) . ' ' . htmlspecialchars($nota['nota']) . ' (' . $nota_extenso . ')</span>';
                $count++;
            }
            ?>
        </div>
      
        <!-- Bloco da classificação final -->
        <div style="margin-top: 10px;">
            <?php if ($aluno['classificacao'] !== 'FREQUENTOU') : ?>
                <span style="font-family: 'Times New Roman', serif; font-size: 17px; line-height: .1; font-weight: bold; font-style: italic;">
                    Foi-lhe atribuído(a) a classificação final de <?php echo $media . ' (' . numeroPorExtenso($media) . ')'; ?>
                </span>
            <?php endif; ?>
            <br>
            <span>___________________________________________________</span>
            <div style="position: absolute; margin-top: 37.5px; margin-left: 352px;">
                <div style="position: absolute; top: 0; left: 90%; width: 3cm; height: 1px; background-color: #ffffff; transform: rotate(55deg); font-weight:bold">_________</div>
            </div>
        </div>
        <!-- Linha diagonal para assinatura -->
        
        <!-- Linha sublinhada para assinatura -->
        <div class="signature-underline">
          ____________________________________________
        </div>
        <br>
        <!-- Bloco com informações complementares -->
        <div class="justify" style="font-family: 'Times New Roman', serif; font-size: 17px; line-height: 1.5;">
            Consta do livro de termos da turma <?php echo htmlspecialchars($aluno['turma']); ?>, sob o n.º <?php echo htmlspecialchars($aluno['numero']); ?> do ano lectivo <?php echo htmlspecialchars($aluno['numero_ano_letivo']); ?>,
            e leva o selo branco em uso nesta Direcção
        </div>
        <br>
        <span style="font-family: 'Times New Roman', serif; font-size: 17px;">
            Guichê do aluno em São Tomé, <?php
                $meses = [
                    1 => 'janeiro', 2 => 'fevereiro', 3 => 'março', 4 => 'abril',
                    5 => 'maio', 6 => 'junho', 7 => 'julho', 8 => 'agosto',
                    9 => 'setembro', 10 => 'outubro', 11 => 'novembro', 12 => 'dezembro'
                ];
                $date = new DateTime();
                $dia = $date->format('d');
                $mes = $meses[(int)$date->format('m')];
                $ano = $date->format('Y');
                echo "{$dia} de {$mes} de {$ano}";
            ?>.
        </span>
        <br><br>
        <div style="text-align: right; position: relative; font-family: 'Times New Roman'; font-size:16px">
            <span style="text-align: center;">
                O Chefe da Secretaria,<br> 
                __________________
            </span>
        </div>
        <br>
        <div style="position: relative; font-family: 'Times New Roman', serif; font-size: 17px; line-height: 1.2;">
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
         
        <div class="justify" style="font-family: 'Times New Roman', serif; font-size: 17px;">
            Obs.: O emolumento é cobrado por meio de estampilha fiscal nos termos do artigo 26.º 
            do Decreto Lei n.º58/80 de 18/12------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------
            
            
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
