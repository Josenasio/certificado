<?php

// Aqui você pode definir a URL de destino após o redirecionamento
$redirect_url = "../index.php"; // Substitua com o endereço de destino desejado
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sucesso</title>


     
    <!-- ====== ionicons ======= -->
    <script type="module" src="https://unpkg.com/ionicons@5.5.2/dist/ionicons/ionicons.esm.js"></script>
    <script nomodule src="https://unpkg.com/ionicons@5.5.2/dist/ionicons/ionicons.js"></script>
    <style>
        /* Estilo geral do pop-up */
        body {
            font-family: 'Arial', sans-serif;
            margin: 0;
            padding: 0;
            background: linear-gradient(135deg, #6e7fdb, #5f5fc1);
            height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
        }

        .popup {
            display: flex;
            justify-content: center;
            align-items: center;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.3); /* fundo semi-transparente */
            z-index: 9999;
            visibility: hidden;
            animation: fadeIn 0.5s ease-out forwards;
        }

        .popup-content {
            background-color: #fff;
            border-radius: 15px;
            padding: 40px;
            text-align: center;
            box-shadow: 0px 10px 30px rgba(0, 0, 0, 0.15);
            animation: slideUp 0.5s ease-out forwards;
        }

        .success-message {
            font-size: 28px;
            font-weight: bold;
            color: #28a745;
            margin-bottom: 30px;
            animation: fadeInText 1s ease-out forwards;
        }

        .animation-container {
            display: flex;
            justify-content: center;
            margin-top: 20px;
            animation: fadeIn 1.5s ease-out forwards;
        }

        .checkmark {
            width: 60px;
            height: 60px;
            border: 6px solid #28a745;
            border-radius: 50%;
            position: relative;
            animation: pulse 2s infinite ease-in-out;
        }

        .checkmark:before {
            content: "";
            position: absolute;
            top: 16px;
            left: 8px;
            width: 24px;
            height: 12px;
            border: solid #28a745;
            border-width: 0 0 6px 6px;
            transform: rotate(-45deg);
            animation: checkmark-animation 1s ease-in-out 0.5s forwards;
        }

        @keyframes fadeIn {
            0% {
                opacity: 0;
            }
            100% {
                opacity: 1;
            }
        }

        @keyframes slideUp {
            0% {
                transform: translateY(50px);
            }
            100% {
                transform: translateY(0);
            }
        }

        @keyframes fadeInText {
            0% {
                opacity: 0;
            }
            100% {
                opacity: 1;
            }
        }

        @keyframes pulse {
            0% {
                transform: scale(1);
                opacity: 0.8;
            }
            50% {
                transform: scale(1.2);
                opacity: 1;
            }
            100% {
                transform: scale(1);
                opacity: 0.8;
            }
        }

        @keyframes checkmark-animation {
            0% {
                transform: scale(0);
            }
            100% {
                transform: scale(1);
            }
        }
    </style>
</head>
<body>

    <!-- Pop-up de Sucesso -->
    <div id="popup" class="popup">
        <div class="popup-content">
            <div class="success-message">
                Certificado Elaborada/ Atualizada com sucesso!  <ion-icon name="ribbon-outline" style="color: #D4AF37;"></ion-icon>
            </div>

            <div class="animation-container">
                <div class="checkmark"></div>
            </div>
        </div>
    </div>

    <script>
        // Exibe o pop-up
        document.getElementById("popup").style.visibility = "visible";

        // Redirecionamento após 2 segundos
        setTimeout(function() {
            window.location.href = "<?php echo $redirect_url; ?>";
        }, 2000); // Aguarda 2 segundos antes de redirecionar
    </script>
</body>
</html>
