<?php 
session_start();
if (!isset($_SESSION['id']) || $_SESSION['nivel_acesso'] !== 'Secretária') {
    header("Location: ../../index.php");
    exit;
}

include_once($_SERVER['DOCUMENT_ROOT'].'/certidao/conexao/connect.php');

function gerarCodigoCertidao() {
    return strtoupper(substr(str_shuffle("ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789"), 0, 10));
}

function getOptions($mysqli, $query, $valueField, $textField) {
    $result = $mysqli->query($query);
    $options = "";
    while ($row = $result->fetch_assoc()) {
        $options .= "<option value='" . $row[$valueField] . "'>" . $row[$textField] . "</option>";
    }
    return $options;
}

$escolas = getOptions($mysqli, "SELECT id, nome_escola FROM escola ORDER BY nome_escola ASC", "id", "nome_escola");
$distritos = getOptions($mysqli, "SELECT id, nome FROM distrito ORDER BY nome ASC", "id", "nome");
$cursos = getOptions($mysqli, "SELECT id, nome_curso FROM cursos ORDER BY nome_curso ASC", "id", "nome_curso");
$classes = getOptions($mysqli, "SELECT id, nome_classe FROM classe ORDER BY id DESC", "id", "nome_classe");
$anos = getOptions($mysqli, "SELECT id, numero_ano_letivo FROM ano_lectivo ORDER BY numero_ano_letivo DESC", "id", "numero_ano_letivo");
$generos = getOptions($mysqli, "SELECT id, tipo_genero FROM genero", "id", "tipo_genero");
$classificacoes = getOptions($mysqli, "SELECT id, tipo_classificacao FROM classificacao", "id", "tipo_classificacao");
$disciplinas = getOptions($mysqli, "SELECT id, nome_disciplina FROM disciplina ORDER BY nome_disciplina", "id", "nome_disciplina");
?>


<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cadastro de Aluno</title>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">


    <link href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css" rel="stylesheet">

    <link href='https://unpkg.com/boxicons@2.1.1/css/boxicons.min.css' rel='stylesheet'>


     
    <!-- ====== ionicons ======= -->
    <script type="module" src="https://unpkg.com/ionicons@5.5.2/dist/ionicons/ionicons.esm.js"></script>
    <script nomodule src="https://unpkg.com/ionicons@5.5.2/dist/ionicons/ionicons.js"></script>



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
 
    <form action="salvar_aluno.php" method="post" class="well form-horizontal">



    <div class="container text-center p-4" style="margin-bottom: 20px;">
  <h1 class="text-center mb-4" style="font-size: 2.5rem; font-weight: bold;">NOVO CERTIFICADO  <ion-icon name="ribbon-outline" style="color: #D4AF37;"></ion-icon>
  </h1>
</div>


    <div class="form-group">
  <label class="col-md-4 control-label">Ano Letivo: <span style="color: red; font-weight:bold ; font-size: 1.7rem;">*</span></label>
  <div class="col-md-4 inputGroupContainer">
    <div class="input-group">
      <span class="input-group-addon"><i class="fas fa-calendar-alt"></i></span> <!-- Ícone de calendário -->
      <select name="ano_lectivo_id" class="form-control" required>
        <option value="" disabled selected>Selecione o ano letivo</option>
        <?= $anos ?>
      </select>
    </div>
  </div>
</div>


<div class="form-group">
  <label class="col-md-4 control-label">Escola do(a) Aluno(a): <span style="color: red; font-weight:bold ; font-size: 1.7rem;">*</span></label>
  <div class="col-md-4 inputGroupContainer">
    <div class="input-group">
      <span class="input-group-addon"><i class="fas fa-school"></i></span> <!-- Ícone de escola -->
      <select name="escola_id" class="form-control" required>
        <option value="" disabled selected>Selecione a escola</option>
        <?= $escolas ?>
      </select>
    </div>
  </div>
</div>


   <!-- Campo de Seleção da Classe -->
   <div class="form-group">
            <label class="col-md-4 control-label">Classe do(a) Aluno(a): <span style="color: red; font-weight:bold ; font-size: 1.7rem;">*</span></label>
            <div class="col-md-4 inputGroupContainer">
                <div class="input-group">
                    <span class="input-group-addon"><i class="fas fa-chalkboard-teacher"></i></span>
                    <select name="classe_id" id="classe_id" class="form-control" required>
                        <option value="" disabled selected>Selecione a Classe</option>
                        <?= $classes ?>
                    </select>
                </div>
            </div>
        </div>

        <!-- Campo de Seleção do Curso (será atualizado conforme a classe selecionada) -->
        <div class="form-group">
            <label class="col-md-4 control-label">Curso do(a) Aluno(a): <span style="color: red; font-weight:bold ; font-size: 1.7rem;">*</span></label>
            <div class="col-md-4 inputGroupContainer">
                <div class="input-group">
                    <span class="input-group-addon"><i class="fas fa-chalkboard-teacher"></i></span>
                    <select name="id_curso" id="id_curso" class="form-control" required>
                        <option value="" disabled selected>Selecione o curso</option>
                        <?= $cursos ?>
                    </select>
                </div>
            </div>
        </div>



<div class="form-group">
  <label class="col-md-4 control-label">Turma do(a) Aluno(a): <span style="color: red; font-weight:bold ; font-size: 1.7rem;">*</span></label>
  <div class="col-md-4 inputGroupContainer">
    <div class="input-group">
      <span class="input-group-addon"><i class="fas fa-users"></i></span> <!-- Ícone de turma -->
      <input type="text" name="turma" placeholder="Nome da turma" class="form-control" required maxlength="3" pattern="[A-Za-z][A-Za-z0-9]{0,2}"
        oninput="this.value = this.value.toUpperCase().replace(/[^A-Z0-9]/g, ''); 
                 if (!/^[A-Z]/.test(this.value)) this.value = this.value.replace(/^[^A-Z]/, '');" 
        title="O primeiro caractere deve ser uma letra sem acento e os dois seguintes podem ser letras ou números.">
    </div>
  </div>
</div>


    
<div class="form-group">
  <label class="col-md-4 control-label">Número do(a) Aluno(a): <span style="color: red; font-weight:bold ; font-size: 1.7rem;">*</span></label>
  <div class="col-md-4 inputGroupContainer">
    <div class="input-group">
      <span class="input-group-addon"><i class="glyphicon glyphicon-sort"></i></span>
      <input type="number" name="numero" placeholder="Número" class="form-control" required min="1" max="99" maxlength="2" 
        oninput="if(this.value.length > 2) this.value = this.value.slice(0, 2);"
        oninvalid="this.setCustomValidity('Digite um número entre 1 e 99.')"
        oninput="this.setCustomValidity('')">
    </div>
  </div>
</div>


       <div class="form-group">
    <label class="col-md-4 control-label">Nome do(a) Aluno(a): <span style="color: red; font-weight:bold ; font-size: 1.7rem;">*</span></label> 
    <div class="col-md-4 inputGroupContainer">
    <div class="input-group">
    <span class="input-group-addon"><i class="glyphicon glyphicon-user"></i></span>
    <input type="text" name="nome" placeholder="Nome do(a) aluno(a)" class="form-control" required
             oninput="this.value = this.value.replace(/[^A-Za-zÀ-ÿ\s]/g, ''); if (this.value.length) { this.value = this.value.charAt(0).toUpperCase() + this.value.slice(1); } this.setCustomValidity('');"
             oninvalid="this.setCustomValidity('Apenas letras são permitidas neste campo.')">
    </div>
    </div>
    </div>

   





    <?php
// Calcula a data mínima e máxima permitida com base na data atual
$minDate = date("Y-m-d", strtotime("-66 years"));
$maxDate = date("Y-m-d", strtotime("-12 years"));
?>

<div class="form-group">
  <label class="col-md-4 control-label">Data Nascimento do(a) Aluno(a): <span style="color: red; font-weight:bold ; font-size: 1.7rem;">*</span></label>
  <div class="col-md-4 inputGroupContainer">
    <div class="input-group">
      <span class="input-group-addon">
        <i class="glyphicon glyphicon-calendar"></i>
      </span>
      <input type="date" name="data_nascimento" class="form-control" required
             min="<?php echo $minDate; ?>" max="<?php echo $maxDate; ?>"
             oninvalid="this.setCustomValidity('Por favor, insira uma data de nascimento válida. (Entre <?php echo $minDate; ?> e <?php echo $maxDate; ?>)')"
             oninput="this.setCustomValidity('')">
    </div>
  </div>
</div>







<div class="form-group">
  <label class="col-md-4 control-label">Categoria do(a) Aluno(a): <span style="color: red; font-weight:bold ; font-size: 1.7rem;">*</span></label>
  <div class="col-md-4 inputGroupContainer">
    <div class="input-group">
      <span class="input-group-addon"><i class="fas fa-list"></i>
      </span> <!-- Ícone de gênero -->
      <select name="genero_id" class="form-control" required>
        <option value="" disabled selected>Selecione o categoria</option>
        <?= $generos ?>
      </select>
    </div>
  </div>
</div>



<div class="form-group">
  <label class="col-md-4 control-label">Número do BI do(a) Aluno(a): <span style="color: red; font-weight:bold ; font-size: 1.7rem;">*</span></label>
  <div class="col-md-4 inputGroupContainer">
    <div class="input-group">
      <span class="input-group-addon"><i class="glyphicon glyphicon-sort"></i></span>
      <input type="number" name="bi" placeholder="Número" class="form-control" required 
        min="100000" max="999999" 
        oninput="if(this.value.length > 6) this.value = this.value.slice(0, 6);" 
        oninvalid="this.setCustomValidity('Digite um número de 6 dígitos entre 100000 e 999999.')" 
        oninput="this.setCustomValidity('')">
    </div>
  </div>
</div>



<div class="form-group">
  <label class="col-md-4 control-label">Distrito do(a) Aluno(a): <span style="color: red; font-weight:bold ; font-size: 1.7rem;">*</span></label>
  <div class="col-md-4 inputGroupContainer">
    <div class="input-group">
      <span class="input-group-addon"><i class="fas fa-map-marker-alt"></i></span> <!-- Ícone de local (pin) -->
      <select name="distrito_id" class="form-control" required>
        <option value="" disabled selected>Selecione o distrito</option>
        <?= $distritos ?>
      </select>
    </div>
  </div>
</div>

       
        

<div class="form-group">
  <label class="col-md-4 control-label">
    Naturalidade do(a) Aluno(a): 
    <span style="color: red; font-weight:bold; font-size: 1.7rem;">*</span>
  </label>
  <div class="col-md-4 inputGroupContainer">
    <div class="input-group">
      <span class="input-group-addon">
        <i class="glyphicon glyphicon-map-marker"></i>
      </span>
      <input type="text" name="naturalidade" placeholder="Naturalidade" class="form-control"  
             required
             oninput="this.value = this.value.replace(/[^A-Za-zÀ-ÿ\s]/g, ''); if (this.value.length) { this.value = this.value.charAt(0).toUpperCase() + this.value.slice(1); } this.setCustomValidity('');"
             oninvalid="this.setCustomValidity('Apenas letras são permitidas neste campo.')">
    </div>
  </div>
</div>






        <input type="hidden" name="codigo_certidao" value="<?= gerarCodigoCertidao(); ?>">

      
        
        <div class="form-group">
  <label class="col-md-4 control-label">
    Nome da Mãe do(a) Aluno(a): 
    <span style="color: red; font-weight:bold; font-size: 1.7rem;">*</span>
  </label> 
  <div class="col-md-4 inputGroupContainer">
    <div class="input-group">
      <span class="input-group-addon">
        <i class="glyphicon glyphicon-user"></i>
      </span>
      <input type="text" name="nome_mae" placeholder="Nome da mãe" class="form-control" required
             oninput="this.value = this.value.replace(/[^A-Za-zÀ-ÿ\s]/g, ''); if (this.value.length) { this.value = this.value.charAt(0).toUpperCase() + this.value.slice(1); } this.setCustomValidity('');"
             oninvalid="this.setCustomValidity('Apenas letras são permitidas neste campo.')">
    </div>
  </div>
</div>




    <div class="form-group">
    <label class="col-md-4 control-label">Nome do Pai do(a) Aluno(a): <span style="color: red; font-weight:bold ; font-size: 1.7rem;">*</span></label> 
    <div class="col-md-4 inputGroupContainer">
    <div class="input-group">
    <span class="input-group-addon"><i class="glyphicon glyphicon-user"></i></span>
    <input type="text" name="nome_pai" placeholder="Nome do pai" class="form-control" required
             oninput="this.value = this.value.replace(/[^A-Za-zÀ-ÿ\s]/g, ''); if (this.value.length) { this.value = this.value.charAt(0).toUpperCase() + this.value.slice(1); } this.setCustomValidity('');"
             oninvalid="this.setCustomValidity('Apenas letras são permitidas neste campo.')">
    </div>
    </div>
    </div>

      

    <div class="form-group">
  <label class="col-md-4 control-label">Classificação do(a) Aluno(a): <span style="color: red; font-weight:bold ; font-size: 1.7rem;">*</span></label>
  <div class="col-md-4 inputGroupContainer">
    <div class="input-group">
      <span class="input-group-addon"><i class="fas fa-star"></i></span> <!-- Ícone de estrela para classificação -->
      <select name="classificacao_id" class="form-control" required>
        <option value="" disabled selected>Selecione a classificação</option>
        <?= $classificacoes ?>
      </select>
    </div>
  </div>
</div>

 <!-- Disciplinas e Notas -->
 <div class="container text-center p-4">
            <div class="form-group">
                <h2 class="text-center">Disciplinas e Notas</h2>
                <!-- Dentro de #disciplinas-container ficará o botão e as linhas adicionadas -->
                <div id="disciplinas-container" class="d-flex flex-column justify-content-center mt-3">
                    <button type="button" class="btn btn-primary" id="add-disciplina" style="margin-bottom: 20px;">
                        <i class="fas fa-plus"></i> Adicionar Disciplinas
                    </button>
                </div>
            </div>
        </div>

<!-- Button -->
<div class="form-group">
  <label class="col-md-4 control-label"></label>
  <div class="col-md-4">
    <button type="submit" class="btn btn-warning" style="letter-spacing:2px">Salvar o Certificado <i class='bx bx-save' style="font-size: 18px;"></i>

    </button>
  </div>
</div>
 
</form>

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

            // Envia os parâmetros para o arquivo get_disciplinas.php
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
                        // Se a classe selecionada for 8, exibe tabela com as colunas: Disciplina, 10ª, 11ª, 12ª e Exame.
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
                                    <input type="hidden" name="disciplinas[]" value="${disciplineId}">
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
                                            <input type="hidden" name="disciplinas[]" value="${disciplineId}">
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

        // Tratamento para remoção das disciplinas adicionadas
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

    <script>
    // Atualiza o select de cursos conforme a classe selecionada
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
</body>
</html>
