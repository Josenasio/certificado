<!DOCTYPE html>
<html lang="pt">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>E-mail já registrado</title>
  <!-- Ícones Font Awesome -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" crossorigin="anonymous" referrerpolicy="no-referrer" />
  <!-- Fonte Google -->
  <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;700&display=swap" rel="stylesheet">
  <!-- Bootstrap CSS -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <!-- Bootstrap Icons (opcional) -->
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">

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
      animation: zoomIn 0.5s ease-out;
      transition: transform 0.3s;
    }
 
    /* Ícone de E-mail com animação de balanço */
    .icon {
      font-size: 60px;
      color: rgb(255, 25, 0);
      margin-bottom: 20px;
      animation: swing 2s infinite;
    }
    /* Título */
    h2 {
      margin: 0;
      font-size: 28px;
      color: red;
      letter-spacing: 1px;
      text-decoration: underline;
    }
    /* Parágrafo personalizado com animação de fade-in */
    p {
      color: #FFA500;
      margin: 20px 0;
      font-size: 16px;
      line-height: 1.5;
      animation: fadeIn 1s ease-out;
    }
    /* Botão de Ação com efeitos de transição */
    a, button {
      display: inline-block;
      text-decoration: none;
      color: #fff;
      background: red;
      padding: 12px 25px;
      border-radius: 5px;
      font-weight: bold;
      transition: background 0.3s, transform 0.3s, box-shadow 0.3s;
      border: none;
    }
 
    /* Keyframes para animações */
    @keyframes swing {
      0% { transform: rotate(0deg); }
      20% { transform: rotate(15deg); }
      40% { transform: rotate(-10deg); }
      60% { transform: rotate(5deg); }
      80% { transform: rotate(-5deg); }
      100% { transform: rotate(0deg); }
    }
    @keyframes fadeIn {
      from {
        opacity: 0;
        transform: translateY(20px);
      }
      to {
        opacity: 1;
        transform: translateY(0);
      }
    }
    @keyframes zoomIn {
      from {
        opacity: 0;
        transform: scale(0.8);
      }
      to {
        opacity: 1;
        transform: scale(1);
      }
    }
  </style>
</head>
<body>
  <div class="container">
    <i class="fa-solid fa-envelope icon"></i>
    <h2 class="mt-4">E-mail já registrado!</h2>
    <p class="lead mt-3">
      O e-mail inserido já está cadastrado na base de dados.<br>
      Por favor, verifique seus dados e tente com um novo e-mail.
    </p>
    <button class="btn btn-primary mt-4" onclick="voltarPagina()">
      <i class="bi bi-arrow-left"></i> Tentar Novamente
    </button>
  </div>

  <script>
    function voltarPagina() {
      window.history.back();
    }
  </script>
  <!-- Bootstrap Bundle com Popper -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
