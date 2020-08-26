<?php

session_start();
if (!isset($_SESSION["time"])) {
    header("Refresh:0");
    header("Location: gertecCovid.php");
} else {
    $name = $_SESSION["name"];
    $area = $_SESSION["area"];
}

?>

<!DOCTYPE html>
<html lang="pt-br">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <link rel="stylesheet" type="text/css" href="assets/styles/formContinuacao.css" media="screen" />
        <script type="text/javascript" src="assets/scripts/gertecCovid.js"></script>
        <title>Avaliação de Saúde</title>
    </head>
    <body>
        <style>
            body {
                background-color: #D13636 !important;
            }
        </style>
        <div id='message-wrapper'>
            <div id='negativaMessage'>
                <img id='alert' src='assets/media/alert.png'>
                <h3 class='negativeText'>
                    Baseado em suas respostas, por precaução você deverá permanecer em home office e acionar a área 
                    de RH imediatamente por Telefone ou WhatsApp: (11)98247–5717 e/ou (11)98177–3519.
                </h3>
                <br><?php echo"<h3 class='negativeText'>$name, $area</h3>" ?><br>
                <h3 class='negativeText'>
                    Recomendamos que procure atendimento médico o mais breve possível para realização do diagnóstico.
                </h3>
            </div>
        </div>
    </body>
<html>

<?php $_SESSION=array(); ?>