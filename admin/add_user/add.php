<?php 
session_start();
if (!isset($_SESSION['id']) || $_SESSION['nivel_acesso'] !== 'Admin') {
    header("Location: ../../index.php");
    exit;
}


?>

<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cadastro de Usuário</title>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">


<link href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css" rel="stylesheet">

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">

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



   
    .card {
        border-radius: 5px;
        overflow: hidden;
    }
    
    .form-control:focus, .form-select:focus {
        border-color: #0d6efd;
        box-shadow: 0 0 0 0.25rem rgba(13, 110, 253, 0.25);
    }
    
    .input-group-text {
        transition: all 0.3s ease;
    }
    
    .toggle-password:hover {
        background-color: rgba(13, 110, 253, 0.1);
    }
    
    .password-strength .progress-bar {
        transition: width 0.3s ease;
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
<form action="salvar_usuario.php" method="post" class="needs-validation" novalidate>
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8 col-lg-6">
                <div class="card shadow-lg border-0">
                    <div class="card-header bg-primary text-white">
                        <h2 class="mb-0 fw-bold"><i class="fas fa-user-plus me-2"></i>Cadastro de Usuário</h2>
                    </div>
                    
                    <div class="card-body p-4">
                        <!-- Nome -->
                        <div class="mb-4">
                            <label for="nome" class="form-label fw-bold">Nome Completo</label>
                            <div class="input-group">
                                <span class="input-group-text bg-primary text-white"><i class="fas fa-user"></i></span>
                                <input type="text" id="nome" name="nome" 
                                       class="form-control form-control-lg" 
                                       placeholder="Digite seu nome completo" 
                                       pattern="[A-Za-zÀ-ÿ\s]+" 
                                       required>
                                <div class="invalid-feedback">
                                    Por favor, insira um nome válido (apenas letras).
                                </div>
                            </div>
                        </div>

                        <!-- Email -->
                        <div class="mb-4">
                            <label for="email" class="form-label fw-bold">E-mail</label>
                            <div class="input-group">
                                <span class="input-group-text bg-primary text-white"><i class="fas fa-envelope"></i></span>
                                <input type="email" id="email" name="email" 
                                       class="form-control form-control-lg" 
                                       placeholder="exemplo@dominio.com" 
                                       required>
                                <div class="invalid-feedback">
                                    Por favor, insira um e-mail válido.
                                </div>
                            </div>
                        </div>

                        <!-- Senha -->
                        <div class="mb-4">
                            <label for="senha" class="form-label fw-bold">Senha</label>
                            <div class="input-group">
                                <span class="input-group-text bg-primary text-white"><i class="fas fa-lock"></i></span>
                                <input type="password" id="senha" name="senha" 
                                       class="form-control form-control-lg" 
                                       placeholder="Crie uma senha segura" 
                                       required
                                       minlength="6">
                                <button type="button" class="btn btn-outline-primary toggle-password">
                                    <i class="fas fa-eye"></i>
                                </button>
                                <div class="invalid-feedback">
                                    A senha deve ter pelo menos 6 caracteres.
                                </div>
                            </div>
                            <div class="password-strength mt-2">
                                <div class="progress" style="height: 4px;">
                                    <div class="progress-bar" role="progressbar" style="width: 0%"></div>
                                </div>
                            </div>
                        </div>

                        <!-- Nível de Acesso -->
                        <div class="mb-4">
                            <label for="nivel_acesso" class="form-label fw-bold">Nível de Acesso</label>
                            <div class="input-group">
                                <span class="input-group-text bg-primary text-white"><i class="fas fa-shield-alt"></i></span>
                                <select id="nivel_acesso" name="nivel_acesso" 
                                        class="form-select form-select-lg" 
                                        required>
                                    <option value="Secretária">Secretária</option>
                                </select>
                            
                            </div>
                        </div>

                        <!-- Botão de Envio -->
                        <div class="d-grid mt-5">
                            <button type="submit" class="btn btn-primary btn-lg fw-bold">
                                <i class="fas fa-save me-2"></i>Salvar Usuário
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</form>

   


<script>
    // Validação customizada
    (function () {
        'use strict'
        var forms = document.querySelectorAll('.needs-validation')
        Array.prototype.slice.call(forms)
            .forEach(function (form) {
                form.addEventListener('submit', function (event) {
                    if (!form.checkValidity()) {
                        event.preventDefault()
                        event.stopPropagation()
                    }
                    form.classList.add('was-validated')
                }, false)
            })
    })()
    
    // Toggle Password
    document.querySelector('.toggle-password').addEventListener('click', function() {
        const passwordInput = document.getElementById('senha');
        const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
        passwordInput.setAttribute('type', type);
        this.querySelector('i').classList.toggle('fa-eye-slash');
    });

    // Password Strength
    document.getElementById('senha').addEventListener('input', function() {
        const strength = calculatePasswordStrength(this.value);
        const progressBar = document.querySelector('.progress-bar');
        progressBar.style.width = strength.percentage + '%';
        progressBar.className = 'progress-bar bg-' + strength.color;
    });

    function calculatePasswordStrength(password) {
        const strength = {
            0: { color: 'danger', percentage: 20 },
            1: { color: 'warning', percentage: 40 },
            2: { color: 'info', percentage: 60 },
            3: { color: 'success', percentage: 80 },
            4: { color: 'success', percentage: 100 }
        };
        
        let score = 0;
        if (password.length >= 8) score++;
        if (password.match(/[A-Z]/)) score++;
        if (password.match(/[0-9]/)) score++;
        if (password.match(/[^A-Za-z0-9]/)) score++;
        
        return strength[Math.min(score, 4)];
    }
</script>
</body>
</html>
