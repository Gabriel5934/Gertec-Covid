<?php

session_start();

if (isset($_SESSION["name"])) {
    $name = $_SESSION["name"];
    $unidade = $_SESSION["unity"];
    $area = $_SESSION["area"];
    $date = $_SESSION["date"];
    $message = "<h1 id='associate'>$name, $unidade, $area, $date</h1>";
} else {
    header("Location: unidade.php");
}

?>

<!DOCTYPE html>
<html lang="pt-br">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <link rel="stylesheet" type="text/css" href="assets/styles/gertecCovid.css" media="screen" />
        <script type="text/javascript" src="assets/scripts/gertecCovid.js"></script>
        <title>Avaliação de Saúde</title>
    </head>
    <body>
        <style>
            body {
                background-color: #37A647 !important;
            }
        </style>
        <div id='message-wrapper'>
            <div id='positiveMessage'>
                <img id='check' src='assets/media/check.png'>
                <h3 class='positiveText'>Baseado nas suas respostas, você está liberado(a) para trabalhar</h3>
                <?php echo($message) ?>
                <h3 class='positiveText'>A apresentação deste cartão é obrigatória para sua entrada na Gertec</h3>
            </div>
        </div>
    </body>
<html>

<?php $_SESSION=array(); ?>