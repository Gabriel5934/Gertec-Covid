<?php 
if (!empty($_POST)) {
    ?>
    <style type="text/css">
        form {
            display: none !important;
        }
        body {
            background-color: #D13636 !important;
        }
        #content-wrapper {
            display: none !important;
        }
    </style>
    <?php
    $message = "<div id='message-wrapper'>
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
    </div>";
    echo($message);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" type="text/css" href="assets/styles/formContinuacao.css" media="screen" />
    <script type="text/javascript" src="assets/scripts/formContinuacao.js"></script>
    <title>Avaliação de Saúde</title>
</head>
<body>
    <div id="content-wrapper">
        <form action="" method="post">
            <h1>AVALIAÇÃO DE SAÚDE - COVID-19</h1>
            <h2>Continuação</h2>
            <div id="form-wrapper">
                <div class="field-container">
                    <h3>Há quanto tempo está com estes sintomas?<h3>
                    <input type="radio" id="today" name="today" value="hoje" required>
                    <label for="today">Hoje</label><br>
                    <input type="radio" id="fiveDays" name="fiveDays" value="cincoDias">
                    <label for="fiveDays">1 a 5 dias</label><br>
                    <input type="radio" id="tenDays" name="tenDays" value="seisDias">
                    <label for="tenDays">6 a 10 dias</label><br>
                    <input type="radio" id="thirteenDays" name="thirteenDays" value="trezeDias">
                    <label for="thirteenDays">11 a 13 dias</label><br>
                    <input type="radio" id="moreThan" name="moreThan" value="maisDias">
                    <label for="moreThan">Mais de 14 dias</label><br>
                </div>
                <div class="field-container">
                    <h3>Possui alguma dessas doenças ou condição de saúde?<h3>
                    <input onchange="extraSelected('psoriatic')" class="extra" type="checkbox" name="psoriatic" value="psoriatica">
                    <label for="psoriatic">Artrite Psoriática</label><br>
                    <input onchange="extraSelected('rheumatoid')" class="extra" type="checkbox" name="rheumatoid" value="reumatoide">
                    <label for="rheumatoid">Artrite Reumatoide</label><br>
                    <input onchange="extraSelected('asthma')" class="extra" type="checkbox" name="asthma" value="asma">
                    <label for="asthma">Asma/Bronquite</label><br>
                    <input onchange="extraSelected('cancer')" class="extra" type="checkbox" name="cancer" value="cancer">
                    <label for="cancer">Câncer</label><br>
                    <input onchange="extraSelected('diabetes')" class="extra" type="checkbox" name="diabetes" value="diabete">
                    <label for="diabetes">Diabetes</label><br>
                    <input onchange="extraSelected('inflammation')" class="extra" type="checkbox" name="inflammation" value="inflamatoria">
                    <label for="inflammation">Doenças inflamatórias</label><br>
                    <input onchange="extraSelected('hypertension')" class="extra" type="checkbox" name="hypertension" value="hipertensao">
                    <label for="hypertension">Hipertensão</label><br>
                    <input onchange="extraSelected('pregnant')" class="extra" type="checkbox" name="pregnant" value="gestante">
                    <label for="pregnant">Gestante</label><br>
                    <input onchange="extraSelected('transplanted')" class="extra" type="checkbox" name="transplanted" value="transplantado">
                    <label for="transplanted">Transplantado</label><br>
                    <input onchange="noneSelected()" id="noneAbove" type="checkbox" name="noneAbove" value="nenhumAcima" checked>
                    <label for="noneAbove">Não possuo nenhuma doença/condição acima</label><br>
                </div>
                <input type="submit" value="ENVIAR">
            </div>
        </form>
    </div>
</body>
</html>