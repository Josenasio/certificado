<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Erro - Sistema de Gestão Escolar</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #343a40;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
            min-height: 100vh; /* Ajuste para garantir que a altura mínima da tela seja ocupada */
        }




        .error-container {
            background-color: white;
            padding: 40px;
            border-radius: 10px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
            width: 90%;
            max-width: 500px;
            text-align: center;
            margin-top: 70px;
        }

        .error-container h1 {
            font-size: 100px;
            color: #dc3545;
            margin-bottom: 20px;
        }

        .error-container p {
            font-size: 18px;
            color: #333;
            margin-bottom: 20px;
        }

        .pulsating-btn {
            display: inline-block;
            padding: 10px 20px;
            font-size: 18px;
            color: white;
             color: black;
             font-weight: bold;
            border: none;
            border-radius: 5px;
            text-decoration: none;
            position: relative;
            overflow: hidden;
            transition: background-color 0.3s ease;
        }

        .pulsating-btn::before {
            content: "";
            position: absolute;
            top: 50%;
            left: 50%;
            width: 200%;
            height: 200%;
            background:  #5B05E2;
            border-radius: 5%;
            transform: translate(-50%, -50%) scale(0);
            animation: pulse 1.5s infinite;
        }

        @keyframes pulse {
            0% {
                transform: translate(-50%, -50%) scale(0);
                opacity: 1;
            }
            100% {
                transform: translate(-50%, -50%) scale(1.5);
                opacity: 0;
            }
        }

        .error-container img {
            width: 60%;
            max-width: 200px;
            margin-top: 20px;
        }

        @media (max-width: 576px) {
            .error-container h1 {
                font-size: 80px;
            }

            .error-container p {
                font-size: 16px;
            }

            .error-container img {
                width: 80%;
                max-width: 150px;
            }
        }
    </style>
</head>
<body>

    <div class="error-container">
    
        <p><strong style="color: red;">Erro!</strong> O <span style="color: red; font-weight: bold;">email</span> ou a <span style="color: red; font-weight: bold;">senha</span>
        estão incorretos. Por favor,</p>
        <a href="../index.php" class="pulsating-btn">Tente Novamente</a>

        <p class="mt-3"></p>
        <img src="cadeado.webp" alt="Imagem de erro">
        <br>
        <br>
    
        <p class="alert alert-warning text-center">
            Caso esqueceu a Senha ou o Email, entre imediatamente em contacto com a 
        
            <strong><a href="tel:+2399971781" class="text-decoration-none" style="letter-spacing: 2px;">DESTP</a></strong>.
        </p>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
