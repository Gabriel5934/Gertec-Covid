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
                    Baseado em suas respostas, por precaução você deverá permanecer em casa. Acione o RH de sua unidade imediatamente por telefone ou WhatsApp.<br>
                    <br><strong>São Paulo e Diadema</strong><br>
                    (11) 99402-4508<br>
                    (11) 98177-3519<br>
                    <strong>Ilhéus</strong><br>
                    (73) 9136-4746<br>
                    <strong>Manaus</strong><br>
                    (92) 98418-3098<br>
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