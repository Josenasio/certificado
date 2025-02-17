<?php 
include_once($_SERVER['DOCUMENT_ROOT'].'/certidao/conexao/connect.php');

// Verifica se o ID foi passado na URL
if (isset($_GET['id'])) {
    $id = $_GET['id'];
    
    // Consulta para pegar os dados do aluno
    $sql_aluno = "SELECT * FROM alunos WHERE id = ?";
    $stmt = $mysqli->prepare($sql_aluno);
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    $aluno = $result->fetch_assoc();
    
    // Outras consultas para preencher os selects (distrito, curso, classe, turma, etc.)
    $sql_distrito = "SELECT * FROM distrito";
    $sql_curso    = "SELECT * FROM cursos";
    $sql_classe   = "SELECT * FROM classe";
    $sql_turma    = "SELECT * FROM turma";
    $sql_escola   = "SELECT * FROM escola";
    $sql_genero   = "SELECT * FROM genero";
    $sql_classificacao = "SELECT * FROM classificacao";
    $sql_ano_lectivo   = "SELECT * FROM ano_lectivo";
    $sql_disciplina    = "SELECT * FROM disciplina";
    
    $distrito_result   = $mysqli->query($sql_distrito);
    $curso_result      = $mysqli->query($sql_curso);
    $classe_result     = $mysqli->query($sql_classe);
    $turma_result      = $mysqli->query($sql_turma);
    $escola_result     = $mysqli->query($sql_escola);
    $genero_result     = $mysqli->query($sql_genero);
    $classificacao_result = $mysqli->query($sql_classificacao);
    $ano_lectivo_result   = $mysqli->query($sql_ano_lectivo);
    $disciplina_result    = $mysqli->query($sql_disciplina);
    
    // Consulta para pegar as disciplinas já cadastradas para o aluno na tabela 'notas'
    $sql_notas = "SELECT * FROM notas WHERE id_aluno = ?";
    $stmt_notas = $mysqli->prepare($sql_notas);
    $stmt_notas->bind_param("i", $id);
    $stmt_notas->execute();
    $notas_result = $stmt_notas->get_result();
    
    // Carrega todas as disciplinas (para preencher os selects)
    $sql_disc = "SELECT id, nome_disciplina FROM disciplina";
    $disciplina_all_result = $mysqli->query($sql_disc);
    $disciplinas = [];
    while ($disc = $disciplina_all_result->fetch_assoc()) {
        $disciplinas[] = $disc;
    }
    // Disponibiliza o array de disciplinas para o JavaScript (para inclusão dinâmica)
    echo '<script>var disciplinas = ' . json_encode($disciplinas) . ';</script>';
    
    // Se o aluno é da classe 8, precisamos agrupar as notas de 10ª, 11ª e 12ª 
    // (que foram inseridas com classe_id 6, 7 e 8, respectivamente)
    if ($aluno['classe_id'] == 8) {
        $disciplinasNotas = [];
        // Armazena todas as linhas retornadas
        $allNotas = [];
        while ($nota = $notas_result->fetch_assoc()) {
            $allNotas[] = $nota;
        }
        // Agrupa por disciplina
        foreach ($allNotas as $nota) {
            $disc_id = $nota['id_disciplina'];
            if (!isset($disciplinasNotas[$disc_id])) {
                $disciplinasNotas[$disc_id] = ['10' => '', '11' => '', '12' => ''];
            }
            if ($nota['classe_id'] == 6) {
                $disciplinasNotas[$disc_id]['10'] = $nota['nota'];
            } elseif ($nota['classe_id'] == 7) {
                $disciplinasNotas[$disc_id]['11'] = $nota['nota'];
            } elseif ($nota['classe_id'] == 8) {
                $disciplinasNotas[$disc_id]['12'] = $nota['nota'];
            }
        }
        
        // Consulta para obter as notas de exame (tabela exame)
        $sql_exame = "SELECT * FROM exame WHERE aluno_id = ?";
        $stmt_exame = $mysqli->prepare($sql_exame);
        $stmt_exame->bind_param("i", $id);
        $stmt_exame->execute();
        $exame_result = $stmt_exame->get_result();
        $exames = [];
        while ($exame = $exame_result->fetch_assoc()) {
            $exames[$exame['disciplina_id']] = $exame['nota_exame'];
        }
    }
}
?>

<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Certidão</title>
    <link rel="stylesheet" href="style.css">
    <script src="https://kit.fontawesome.com/a076d05399.js"></script>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">


<link href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css" rel="stylesheet">



<style>
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
<button class="fixed-top-button" onclick="window.location.href='/certidao/secretaria/index.php'">
    <i class="fa fa-arrow-left"></i> Voltar a Pagina Inicial
</button>
<br>
<br>
<br>




<div class="container text-center p-4" style="margin-bottom: 20px;">
  <h1 class="text-center mb-4" style="font-size: 2.5rem; font-weight: bold;">Editar Certidão do(a) Aluno(a)</h1>
</div>

    <form action="atualizar_certidao.php" method="POST" class="well form-horizontal">
        <input type="hidden" name="id" value="<?= $aluno['id'] ?>">

        <!-- Campos de aluno -->
        <div class="form-group">
    <label class="col-md-4 control-label" for="nome">Nome:</label>
    <div class="col-md-4 inputGroupContainer">
        <div class="input-group">
            <span class="input-group-addon"><i class="glyphicon glyphicon-user"></i></span>
            <input type="text" id="nome" name="nome" value="<?= $aluno['nome'] ?>" placeholder="Nome" class="form-control" required oninput="this.value = this.value.replace(/[^A-Za-zÀ-ÿ\s]/g, '');" oninvalid="this.setCustomValidity('Apenas letras são permitidas neste campo.')" oninput="this.setCustomValidity('')">
        </div>
    </div>
</div>

        
<div class="form-group">
    <label class="col-md-4 control-label" for="genero_id">Categoria do(a) Aluno(a):</label>
    <div class="col-md-4 inputGroupContainer">
        <div class="input-group">
            <span class="input-group-addon"><i class="fas fa-list"></i></span>
            <select name="genero_id" id="genero_id" class="form-control" required>
                <?php while ($genero = $genero_result->fetch_assoc()): ?>
                    <option value="<?= $genero['id'] ?>" <?= $aluno['genero_id'] == $genero['id'] ? 'selected' : '' ?>><?= $genero['tipo_genero'] ?></option>
                <?php endwhile; ?>
            </select>
        </div>
    </div>
</div>


<div class="form-group">
    <label class="col-md-4 control-label" for="data_nascimento">Data de Nascimento:</label>
    <div class="col-md-4 inputGroupContainer">
        <div class="input-group">
            <span class="input-group-addon"><i class="glyphicon glyphicon-calendar"></i></span>
            <input type="date" id="data_nascimento" name="data_nascimento" value="<?= $aluno['data_nascimento'] ?>" class="form-control" required>
        </div>
    </div>
</div>




<div class="form-group">
    <label class="col-md-4 control-label" for="bi">Número de BI:</label>
    <div class="col-md-4 inputGroupContainer">
        <div class="input-group">
            <span class="input-group-addon"><i class="glyphicon glyphicon-sort-numeric-asc"></i></span>
            <input type="text" id="bi" name="bi" value="<?= $aluno['bi'] ?>" placeholder="BI do Aluno" class="form-control" required
                pattern="^\d{6}$" maxlength="6" 
                oninput="this.value = this.value.replace(/[^0-9]/g, '').slice(0, 6);" 
                oninvalid="this.setCustomValidity('Por favor, insira exatamente 6 dígitos numéricos.')" 
                oninput="this.setCustomValidity('')">
        </div>
    </div>
</div>




<div class="form-group">
    <label class="col-md-4 control-label" for="distrito_id">Distrito:</label>
    <div class="col-md-4 inputGroupContainer">
        <div class="input-group">
            <span class="input-group-addon"><i class="glyphicon glyphicon-list-alt"></i></span>
            <select name="distrito_id" id="distrito_id" class="form-control" required>
                <?php while ($distrito = $distrito_result->fetch_assoc()): ?>
                    <option value="<?= $distrito['id'] ?>" <?= $aluno['distrito_id'] == $distrito['id'] ? 'selected' : '' ?>><?= $distrito['nome'] ?></option>
                <?php endwhile; ?>
            </select>
        </div>
    </div>
</div>




<div class="form-group">
    <label class="col-md-4 control-label" for="naturalidade">Naturalidade:</label>
    <div class="col-md-4 inputGroupContainer">
        <div class="input-group">
            <span class="input-group-addon"><i class="glyphicon glyphicon-map-marker"></i></span>
            <input type="text" id="naturalidade" name="naturalidade" value="<?= $aluno['naturalidade'] ?>" placeholder="Naturalidade" class="form-control" required>
        </div>
    </div>
</div>

<div class="form-group">
    <label class="col-md-4 control-label" for="nome_mae">Nome da Mãe:</label>
    <div class="col-md-4 inputGroupContainer">
        <div class="input-group">
            <span class="input-group-addon"><i class="glyphicon glyphicon-user"></i></span>
            <input type="text" id="nome_mae" name="nome_mae" value="<?= $aluno['nome_mae'] ?>" placeholder="Nome da Mãe" class="form-control" required oninput="this.value = this.value.replace(/[^A-Za-zÀ-ÿ\s]/g, '');" oninvalid="this.setCustomValidity('Apenas letras são permitidas neste campo.')" oninput="this.setCustomValidity('')">
        </div>
    </div>
</div>

<div class="form-group">
    <label class="col-md-4 control-label" for="nome_pai">Nome do Pai:</label>
    <div class="col-md-4 inputGroupContainer">
        <div class="input-group">
            <span class="input-group-addon"><i class="glyphicon glyphicon-user"></i></span>
            <input type="text" id="nome_pai" name="nome_pai" value="<?= $aluno['nome_pai'] ?>" placeholder="Nome do Pai" class="form-control" required oninput="this.value = this.value.replace(/[^A-Za-zÀ-ÿ\s]/g, '');" oninvalid="this.setCustomValidity('Apenas letras são permitidas neste campo.')" oninput="this.setCustomValidity('')">
        </div>
    </div>
</div>

<div class="form-group">
    <label class="col-md-4 control-label" for="escola_id">Escola:</label>
    <div class="col-md-4 inputGroupContainer">
        <div class="input-group">
            <span class="input-group-addon"><i class="glyphicon glyphicon-education"></i></span>
            <select name="escola_id" id="escola_id" class="form-control" required>
                <?php while ($escola = $escola_result->fetch_assoc()): ?>
                    <option value="<?= $escola['id'] ?>" <?= $aluno['escola_id'] == $escola['id'] ? 'selected' : '' ?>><?= $escola['nome_escola'] ?></option>
                <?php endwhile; ?>
            </select>
        </div>
    </div>
</div>



<div class="form-group">
    <label class="col-md-4 control-label" for="classe_id">Classe:</label>
    <div class="col-md-4 inputGroupContainer">
        <div class="input-group">
            <span class="input-group-addon"><i class="glyphicon glyphicon-list-alt"></i></span>
            <select name="classe_id" id="classe_id" class="form-control" required>
                <?php while ($classe = $classe_result->fetch_assoc()): ?>
                    <option value="<?= $classe['id'] ?>" <?= $aluno['classe_id'] == $classe['id'] ? 'selected' : '' ?>><?= $classe['nome_classe'] ?></option>
                <?php endwhile; ?>
            </select>
        </div>
    </div>
</div>


<div class="form-group">
    <label class="col-md-4 control-label" for="id_curso">Curso:</label>
    <div class="col-md-4 inputGroupContainer">
        <div class="input-group">
            <span class="input-group-addon"><i class="glyphicon glyphicon-education"></i></span>
            <select name="id_curso" id="id_curso" class="form-control" required>
                <?php while ($curso = $curso_result->fetch_assoc()): ?>
                    <option value="<?= $curso['id'] ?>" <?= $aluno['id_curso'] == $curso['id'] ? 'selected' : '' ?>><?= $curso['nome_curso'] ?></option>
                <?php endwhile; ?>
            </select>
        </div>
    </div>
</div>


<div class="form-group">
    <label class="col-md-4 control-label" for="turma_id">Turma:</label>
    <div class="col-md-4 inputGroupContainer">
        <div class="input-group">
            <span class="input-group-addon"><i class="glyphicon glyphicon-folder-open"></i></span>
            <select name="turma_id" id="turma_id" class="form-control" required>
                <?php while ($turma = $turma_result->fetch_assoc()): ?>
                    <option value="<?= $turma['id'] ?>" <?= $aluno['turma_id'] == $turma['id'] ? 'selected' : '' ?>><?= $turma['nome_turma'] ?></option>
                <?php endwhile; ?>
            </select>
        </div>
    </div>
</div>


<div class="form-group">
    <label class="col-md-4 control-label" for="numero">Número do(a) Aluno(a):</label>
    <div class="col-md-4 inputGroupContainer">
        <div class="input-group">
            <span class="input-group-addon"><i class="glyphicon glyphicon-sort-numeric-asc"></i></span>
            <input type="number" id="numero" name="numero" value="<?= $aluno['numero'] ?>" placeholder="Número do Aluno" class="form-control" required min="1" max="99" maxlength="2"
                oninput="if(this.value.length > 2) this.value = this.value.slice(0, 2);"
                oninvalid="this.setCustomValidity('Digite um número entre 1 e 99.')"
                oninput="this.setCustomValidity('')">
        </div>
    </div>
</div>

     

<div class="form-group">
    <label class="col-md-4 control-label" for="classificacao_id">Classificação:</label>
    <div class="col-md-4 inputGroupContainer">
        <div class="input-group">
            <span class="input-group-addon"><i class="glyphicon glyphicon-star"></i></span>
            <select name="classificacao_id" id="classificacao_id" class="form-control" required>
                <?php while ($classificacao = $classificacao_result->fetch_assoc()): ?>
                    <option value="<?= $classificacao['id'] ?>" <?= $aluno['classificacao_id'] == $classificacao['id'] ? 'selected' : '' ?>><?= $classificacao['tipo_classificacao'] ?></option>
                <?php endwhile; ?>
            </select>
        </div>
    </div>
</div>


<div class="form-group">
    <label class="col-md-4 control-label" for="ano_lectivo_id">Ano Lectivo:</label>
    <div class="col-md-4 inputGroupContainer">
        <div class="input-group">
            <span class="input-group-addon"><i class="glyphicon glyphicon-calendar"></i></span>
            <select name="ano_lectivo_id" id="ano_lectivo_id" class="form-control" required>
                <?php while ($ano_lectivo = $ano_lectivo_result->fetch_assoc()): ?>
                    <option value="<?= $ano_lectivo['id'] ?>" <?= $aluno['ano_lectivo_id'] == $ano_lectivo['id'] ? 'selected' : '' ?>><?= $ano_lectivo['numero_ano_letivo'] ?></option>
                <?php endwhile; ?>
            </select>
        </div>
    </div>
</div>


        <hr>

    
<!-- Disciplinas -->
<div class="text-center mb-4">
    <h3 class="text-primary">
        Disciplinas e Notas
    </h3>
</div>


<?php if ($aluno['classe_id'] == 8): ?>
    <!-- Exibição em tabela para classe 8 -->
    <div id="disciplinas-container">
      <table class="table table-bordered" id="disciplinas-table">
        <thead>
          <tr>
            <th><i class="fas fa-book"></i> Disciplina</th>
            <th><i class="fas fa-chalkboard-teacher"></i> 10ª Classe</th>
            <th><i class="fas fa-chalkboard-teacher"></i> 11ª Classe</th>
            <th><i class="fas fa-chalkboard-teacher"></i> 12ª Classe</th>
            <th><i class="fas fa-file-alt"></i> Exame</th>
            <th><i class="fas fa-cogs"></i> Ação</th>
          </tr>
        </thead>
        <tbody>
          <?php 
          if (!empty($disciplinasNotas)):
              foreach ($disciplinasNotas as $disc_id => $notaValues):
                  // Procura o nome da disciplina no array $disciplinas
                  $discName = '';
                  foreach ($disciplinas as $disc) {
                      if ($disc['id'] == $disc_id) {
                          $discName = $disc['nome_disciplina'];
                          break;
                      }
                  }
                  $nota10   = $notaValues['10'];
                  $nota11   = $notaValues['11'];
                  $nota12   = $notaValues['12'];
                  $exameVal = isset($exames[$disc_id]) ? $exames[$disc_id] : '';
          ?>
          <tr class="disciplina-item">
            <td>
              <input type="hidden" name="id_disciplina[]" value="<?= htmlspecialchars($disc_id) ?>">
              <input type="text" class="form-control" value="<?= htmlspecialchars($discName) ?>" readonly>
            </td>
            <td>
              <input type="number" name="nota_10[]" class="form-control" placeholder="-" min="0" max="20" value="<?= htmlspecialchars($nota10) ?>">
            </td>
            <td>
              <input type="number" name="nota_11[]" class="form-control" placeholder="-" min="0" max="20" value="<?= htmlspecialchars($nota11) ?>">
            </td>
            <td>
              <input type="number" name="nota_12[]" class="form-control" placeholder="-" min="0" max="20" value="<?= htmlspecialchars($nota12) ?>">
            </td>
            <td>
              <input type="number" name="exame[]" class="form-control" placeholder="-" min="0" max="20" value="<?= htmlspecialchars($exameVal) ?>">
            </td>
            <td>
              <button type="button" class="btn btn-danger remove-disciplina">Remover</button>
            </td>
          </tr>
          <?php 
              endforeach;
          endif;
          ?>
        </tbody>
      </table>
    </div>
    <button type="button" id="add-disciplina" class="btn btn-success">
      Adicionar Disciplina <span class="glyphicon glyphicon-plus-sign"></span>
    </button>
  <?php else: ?>
    <!-- Exibição para demais classes (layout tradicional) -->
    <?php 
      // Reinicia o ponteiro do resultado (caso seja necessário)
      $notas_result->data_seek(0);
      while ($nota = $notas_result->fetch_assoc()):
    ?>
      <div class="disciplina">
          <label for="disciplina_<?= htmlspecialchars($nota['id_disciplina']) ?>">Disciplina:</label>
          <select name="id_disciplina[]" id="disciplina_<?= htmlspecialchars($nota['id_disciplina']) ?>" required>
              <?php 
              // Reinicia o ponteiro para as opções
              $disciplina_all_result->data_seek(0);
              while ($disciplina = $disciplina_all_result->fetch_assoc()):
              ?>
                  <option value="<?= htmlspecialchars($disciplina['id']) ?>" <?= $nota['id_disciplina'] == $disciplina['id'] ? 'selected' : '' ?>>
                    <?= htmlspecialchars($disciplina['nome_disciplina']) ?>
                  </option>
              <?php endwhile; ?>
          </select>
          <label for="nota_<?= htmlspecialchars($nota['id_disciplina']) ?>">Nota:</label>
          <input type="number" name="nota[]" id="nota_<?= htmlspecialchars($nota['id_disciplina']) ?>" value="<?= htmlspecialchars($nota['nota']) ?>" required>
          <!-- Campo oculto com o classe_id original da nota -->
          <input type="hidden" name="nota_classe_id[]" value="<?= htmlspecialchars($nota['classe_id']) ?>">
          <button type="button" class="remove-discipline" data-id="<?= htmlspecialchars($nota['id_disciplina']) ?>">Remover</button>
      </div>
    <?php endwhile; ?>
    <button type="button" id="add-discipline" class="btn btn-success">
      Adicionar Disciplina <span class="glyphicon glyphicon-plus-sign"></span>
    </button>
  <?php endif; ?>

  <br><br>
  <button type="submit" class="btn btn-primary">
      Atualizar Certidão <span class="glyphicon glyphicon-refresh"></span>
  </button>
</form>

<!-- Scripts para adição e remoção dinâmica de disciplinas -->
<script>
  $(document).ready(function () {
    $("#add-disciplina").click(function () {
      // Captura os valores da classe e do curso selecionados
      let classeId = $("#classe_id").val();
      let cursoId = $("#id_curso").val();

      if (!classeId) {
        alert("Por favor, selecione uma classe primeiro.");
        return;
      }
      if (!cursoId) {
        alert("Por favor, selecione um curso primeiro.");
        return;
      }

      // Envia os parâmetros para o arquivo get_disciplinas.php para obter as opções
      $.ajax({
        url: 'get_disciplinas.php',
        type: 'POST',
        data: { classe_id: classeId, curso_id: cursoId },
        dataType: 'html',
        success: function (data) {
          let options = $(data);
          if (options.length === 0) {
            alert("Nenhuma disciplina encontrada para essa combinação.");
            return;
          }

          if (classeId == 8) {
            // Se a classe for 8, exibe em formato de tabela
            if ($("#disciplinas-table").length == 0) {
              $("#disciplinas-container").html(`
                <table class="table table-bordered" id="disciplinas-table">
                  <thead>
                    <tr>
                      <th><i class="fas fa-book"></i> Disciplina</th>
                      <th><i class="fas fa-chalkboard-teacher"></i> 10ª Classe</th>
                      <th><i class="fas fa-chalkboard-teacher"></i> 11ª Classe</th>
                      <th><i class="fas fa-chalkboard-teacher"></i> 12ª Classe</th>
                      <th><i class="fas fa-file-alt"></i> Exame</th>
                      <th><i class="fas fa-cogs"></i> Ação</th>
                    </tr>
                  </thead>
                  <tbody></tbody>
                </table>
              `);
            }
            options.each(function(){
              let disciplineId = $(this).val();
              let disciplineName = $(this).text();
              let row = `<tr class="disciplina-item">
                <td>
                  <input type="hidden" name="id_disciplina[]" value="${disciplineId}">
                  <input type="text" class="form-control" value="${disciplineName}" readonly>
                </td>
                <td>
                  <input type="number" name="nota_10[]" class="form-control" placeholder="-" min="0" max="20">
                </td>
                <td>
                  <input type="number" name="nota_11[]" class="form-control" placeholder="-" min="0" max="20">
                </td>
                <td>
                  <input type="number" name="nota_12[]" class="form-control" placeholder="-" min="0" max="20">
                </td>
                <td>
                  <input type="number" name="exame[]" class="form-control" placeholder="-" min="0" max="20">
                </td>
                <td>
                  <button type="button" class="btn btn-danger remove-disciplina">Remover</button>
                </td>
              </tr>`;
              $("#disciplinas-table tbody").append(row);
            });
            $("#add-disciplina").prop("disabled", true);
          } else {
            // Para demais classes, adiciona cada disciplina com um único campo de nota.
            options.each(function() {
              let disciplineId = $(this).val();
              let disciplineName = $(this).text();
              let disciplinaRow = `
                <div class="disciplina-item form-group row">
                  <div class="col-md-2">
                    <button type="button" class="btn btn-danger remove-disciplina">Remover</button>
                  </div>
                  <div class="col-md-5">
                    <div class="input-group">
                      <span class="input-group-addon"><i class="fas fa-book"></i></span>
                      <input type="hidden" name="id_disciplina[]" value="${disciplineId}">
                      <input type="text" class="form-control" value="${disciplineName}" readonly>
                    </div>
                  </div>
                  <div class="col-md-5">
                    <div class="input-group">
                      <span class="input-group-addon"><i class="fas fa-pencil-alt"></i></span>
                      <input type="number" name="notas[]" class="form-control" required placeholder="Nota" min="0" max="20">
                    </div>
                  </div>
                </div>`;
              $("#disciplinas-container").append(disciplinaRow);
            });
            $("#add-disciplina").prop("disabled", true);
          }
        },
        error: function () {
          alert("Erro ao carregar as disciplinas. Tente novamente.");
        }
      });
    });

    // Remoção dinâmica das disciplinas adicionadas
    $(document).on("click", ".remove-disciplina", function () {
      if ($(this).closest("tr").length) {
        $(this).closest("tr").remove();
        if ($("#disciplinas-table tbody tr").length === 0) {
          $("#add-disciplina").prop("disabled", false);
          $("#disciplinas-container").empty();
        }
      } else {
        $(this).closest(".disciplina-item").remove();
        if ($("#disciplinas-container .disciplina-item").length === 0) {
          $("#add-disciplina").prop("disabled", false);
        }
      }
    });
  });
</script>

<!-- Opcional: script para atualizar o select de cursos conforme a classe selecionada -->
<script>
  document.getElementById('classe_id').addEventListener('change', function() {
    var classeId = this.value;
    var cursoSelect = document.getElementById('id_curso');
    cursoSelect.innerHTML = '<option value="" disabled selected>Selecione o curso</option>';
    var xhr = new XMLHttpRequest();
    xhr.open('GET', 'get_cursos.php?classe_id=' + encodeURIComponent(classeId), true);
    xhr.onreadystatechange = function() {
      if (xhr.readyState === 4 && xhr.status === 200) {
        cursoSelect.innerHTML += xhr.responseText;
      }
    };
    xhr.send();
  });
</script>    
<script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>


</body>
</html>
