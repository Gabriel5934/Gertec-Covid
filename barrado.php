<?php

session_start();
if (!isset($_SESSION["time"])) {
    header("Refresh:0");
    header("Location: gertecCovid.php");
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
                Baseado nas suas respostas, você pode ter sido exposto ao coronavírus (COVID-19) e necessita de cuidados especiais. 
                Recomendamos que procure atendimento com seu médico de confiança ou vá para uma unidade de saúde. Sobre os cuidados 
                no atendimento hospitalar. Use uma máscara e cubra a boca quando tossir ou espirrar; Não cubra a boca e nariz com a 
                mão, use um lenço de papel ou braço; Tente evitar metrô, ônibus e outros transportes públicos, e lugares lotados; 
                Veículos devem ser desinfetados; Informe os médicos sobre seu histórico de saúde; Após a visita ao pronto-socorro, 
                caso não haja internação volte para casa o mais rápido possível.
                Equipe de Saúde e Segurança do Trabalho - GERTEC
                </h3>
            </div>
        </div>
    </body>
<html>

<?php $_SESSION=array(); ?>