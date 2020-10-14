<?php 

require "functions.php";
date_default_timezone_set("America/Sao_Paulo");
$exec = "exec";
$query = "query";

?>

<!DOCTYPE html>
<html lang="pt-br">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <link rel="stylesheet" type="text/css" href="assets/styles/gertecCovid.css" media="screen" />
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
        <script type="text/javascript" src="assets/scripts/gertecCovid.js"></script>
        <title>Avaliação de Saúde</title>
    </head>
    <body>
        <div id="content-wrapper">
            <form id="form" action="gertecCovid.php" method="post">
                <img id="logo" src="assets/media/logo.png">
                <h1>AVALIAÇÃO DE SAÚDE - COVID-19</h1>
                <div class="field-container">
                    <h3>Selecione sua unidade<h3>
                    <?php 
                        $sql = "SELECT unidade_nome FROM unidades";
                        $result = databaseRequestHandler($sql, $query);
                        $unidades = array();
                        foreach ($result as $unidade) {
                            $aux = $unidade["unidade_nome"];
                            array_push($unidades, $aux);
                            $pos = array_search($aux, $unidades) + 1;
                            echo("<input style='padding:3px' type='radio' id='unity' name='unity' value='$pos'><label for='unity'>$aux</label><br>");
                        }
                    ?>
                </div>
                <input type="submit" value="ENVIAR" id="sendFormInput">
            </form>
        </div>
    </body>
</html>
