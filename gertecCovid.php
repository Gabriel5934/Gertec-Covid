<?php 
ini_set('display_errors', '0');
require 'vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

date_default_timezone_set("America/Sao_Paulo");

if (!empty($_POST)) {
    $firstFormData = array(
        $_POST["name"],
        $_POST["area"],
        $_POST["symptoms"],
        $_POST["fever"],
        $_POST["gasp"],
    );
    
    $extraSymptoms = array(
        "congestion",
        "tasteless",
        "soreThroat",
        "jointPain",
        "cough",
        "noneAbove"
    );
    $firstFormExtra = array();
    
    foreach ($extraSymptoms as $symptom) {
        if (isset($_POST[$symptom])) {
            array_push($firstFormExtra, $_POST[$symptom]);
        }
    }
    
    $spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load("registros.xlsx");
    $sheet = $spreadsheet->getActiveSheet();
    $value = $sheet->getCell("A2")->getValue();

    if ($value == null) {
        $nextRow = 2;
    } else {
        $highestRow = $sheet->getHighestDataRow();
        $nextRow = $highestRow + 1;
    }

    $sheet->setCellValue("A$nextRow", date("d/m/Y"));
    $sheet->setCellValue("B$nextRow", date("h:i:s:u"));
    $sheet->setCellValue("C$nextRow", $_POST["name"]);
    $sheet->setCellValue("D$nextRow", $_POST["area"]);
    $sheet->setCellValue("E$nextRow", $_POST["symptoms"]);
    $sheet->setCellValue("F$nextRow", $_POST["fever"]);
    $sheet->setCellValue("G$nextRow", $_POST["gasp"]);
    foreach ($firstFormExtra as $symptom) {
        $value = $sheet->getCell("H$nextRow")->getValue();
        if ($value == null) {
            $sheet->setCellValue("H$nextRow", $symptom);
        } else {
            $sheet->setCellValue("H$nextRow", $value.", ".$symptom);
        }
    }

    $writer = new Xlsx($spreadsheet);

    try {
        $writer->save("registros.xlsx");
    } catch (Exception $e) {
        echo  "<script>alert('Algo deu errado, tente novamente');</script>";
        header("Refresh:0");
    }    

    session_start();
    $_SESSION["nextRow"] = $nextRow;

    # Se o colaborador estiver saudável
    if ($firstFormData[2] == "assintomatico" && $firstFormData[3] == "nao" && $firstFormData[4] == "nao" && $firstFormExtra[0] == "nenhumAcima") { 
        ?>
        <style type="text/css">
            form {
                display: none !important;
            }
            body {
                background-color: #37A647 !important;
            }
            #content-wrapper {
                display: none !important;
            }
        </style>
        <?php
        $name = $_POST["name"];
        $area = ucfirst($_POST["area"]);
        $date = date("d/m/Y\, H:i");
        $message = "<div id='message-wrapper'>
        <div id='positiveMessage'>
            <img id='check' src='assets/media/check.png'>
            <h3 class='positiveText'>Baseado nas suas respostas, você está liberado(a) para trabalhar</h3>
            <h1 id='associate'>$name, $area, $date</h1>
            <h3 class='positiveText'>A apresentação deste cartão e obrigatória para sua entrada na Gertec</h3>
        </div>
        </div>";
        echo($message);
    } else { # Se o colaborador não estiver saudável
        header("Location: formContinuacao.php");
        exit();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" type="text/css" href="assets/styles/gertecCovid.css" media="screen" />
    <script type="text/javascript" src="assets/scripts/gertecCovid.js"></script>
    <title>Avaliação de Saúde</title>
</head>
<body>
    <div id="content-wrapper">
        <form action="" method="post">
            <h1>AVALIAÇÃO DE SAÚDE - COVID-19</h1>
            <p>
                Antes de sair de casa para ir ao escritório, nós da Gertec queremos saber como está sua saúde. 
                <br>Com uma combinação de perguntas e respostas, vamos entender se você tem chances de estar com o 
                conjunto de sintomas da doença causada pelo novo coronavírus (COVID-19), mas saiba que essa 
                avaliação não é um diagnóstico. Ela serve principalmente para orientar sobre a necessidade de 
                trabalhar em home office ou sobre a necessidade de procurar os cuidados adequados. Combinado?
            </p>

            <div id="form-wrapper">
                <div class="field-container">
                    <label for="name">Nome:</label>
                    <input type="text" name="name" required>
                </div>
                <div class="field-container">
                    <label for="area">Área:</label>
                    <select id="area" name="area" required>
                        <option value="engenharia">Engenharia</option>
                        <option value="desenvolvimentoDeSoftware">Desenvolvimento de Software</option>
                        <option value="financeiro">Financeiro</option>
                        <option value="administrativo">Administrativo</option>
                        <option value="comercial">Comercial</option>
                        <option value="fabricaManaus">Fábrica Manaus</option>
                        <option value="fabricaIlheus">Fábrica Ilhéus</option>
                        <option value="assistenciaTecnica">Assistência técnica</option>
                    </select>
                </div>
                <div class="field-container">
                    <h3>Como está sua saúde no momento?<h3>
                    <input type="radio" id="symptomatic" name="symptoms" value="assintomatico" required>
                    <label for="symptomatic">Estou bem e sem sintomas</label><br>
                    <input type="radio" id="asymptomatic" name="symptoms" value="sintomatico">
                    <label for="asymptomatic">Estou com alguns sintomas</label><br>
                </div>
                <div class="field-container">
                    <h3>Você teve febre (temperatura igual ou superior a 37.8°)?<h3>
                    <input type="radio" id="withFever" name="fever" value="sim" required>
                    <label for="withFever">Sim</label><br>
                    <input type="radio" id="noFever"name="fever" value="nao">
                    <label for="nofever">Não</label><br>
                </div>
                <div class="field-container">
                    <h3>Está sentindo falta de ar ao realizar esforços ou está com respiração ofegante?<h3>
                    <input type="radio" id="gasping" name="gasp" value="sim" required>
                    <label for="gasping">Sim</label><br>
                    <input type="radio" id="noGasping" name="gasp" value="nao">
                    <label for="noGasping">Não</label><br>
                </div>
                <div class="field-container">
                    <h3>Possui alguma dessas doenças ou condições de saúde?<h3>
                    <input onchange="extraSelected('congestion')" class="extra" type="checkbox" name="congestion" value="congestaoNasal">
                    <label for="congestion">Congestão/Corrimento nasal</label><br>
                    <input onchange="extraSelected('tasteless')" class="extra" type="checkbox" name="tasteless" value="perdaPaladar">
                    <label for="tasteless">Diminuição de olfato/paladar</label><br>
                    <input onchange="extraSelected('soreThroat')" class="extra" type="checkbox" name="soreThroat" value="dorGarganta">
                    <label for="soreThroat">Dor de Garganta</label><br>
                    <input onchange="extraSelected('jointPain')" class="extra" type="checkbox" name="jointPain" value="dorArticulacao">
                    <label for="jointPain">Dores nas articulações</label><br>
                    <input onchange="extraSelected('cough')" class="extra" type="checkbox" name="cough" value="tosse">
                    <label for="cough">Tosse</label><br>
                    <input onchange="noneSelected()" id="noneAbove" type="checkbox" name="noneAbove" value="nenhumAcima" checked>
                    <label for="noneAbove">Não tenho nenhum dos sintomas acima</label><br>
                </div>
                <input type="submit" value="ENVIAR">
            </div>
        </form>
    </div>
</body>
</html>

