<?php 
ini_set('display_errors', '0');
require 'vendor/autoload.php';
include_once ('./vendor/autoload.php'); 

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Socketlabs\SocketLabsClient;
use Socketlabs\Message\BasicMessage;
use Socketlabs\Message\EmailAddress;

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();

if (!empty($_POST)) {
    $conditions = array(
        "psoriatic",
        "rheumatoid",
        "asthma",
        "cancer",
        "diabetes",
        "inflammation",
        "hypertension",
        "pregnant",
        "transplanted",
        "noneAbove"
    );
    $presentConditions = array();
    
    foreach ($conditions as $condition) {
        if (isset($_POST[$condition])) {
            array_push($presentConditions, $_POST[$condition]);
        }
    }

    $spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load("registros.xlsx");
    $sheet = $spreadsheet->getActiveSheet();

    session_start();
    $nextRow = $_SESSION["nextRow"];
    $_SESSION["time"] = $_POST["time"];

    if (isset($nextRow)) {
        $sheet->setCellValue("I$nextRow", $_POST["time"]);
        foreach ($presentConditions as $condition) {
            $value = $sheet->getCell("J$nextRow")->getValue();
            if ($value == null) {
                $sheet->setCellValue("J$nextRow", $condition);
            } else {
                $sheet->setCellValue("J$nextRow", $value.", ".$condition);
            }
        }
    }

    $writer = new Xlsx($spreadsheet);

    try {
        $writer->save("registros.xlsx");
    } catch (Exception $e) {
        echo  "<script>alert('Algo deu errado, tente novamente');</script>";
        header("Refresh:0");
    }    

    $serverId = $_ENV["SERVER_ID"];
    $injectionApiKey = $_ENV["API_KEY"];

    $client = new SocketLabsClient($serverId, $injectionApiKey);
    
    $message = new BasicMessage(); 

    $message->subject = "Sending A Basic Message";
    $message->htmlBody = "<html>This is the Html Body of my message.</html>";
    $message->plainTextBody = "This is the Plain Text Body of my message.";

    $message->from = new EmailAddress($_ENV["FROM_EMAIL"]);
    $message->addToAddress("gabriel.andrade@outlook.com.br");
    
    $response = $client->send($message);

    header("Location: barrado.php");
    exit();
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
                    <input type="radio" id="today" name="time" value="hoje" required>
                    <label for="today">Hoje</label><br>
                    <input type="radio" id="fiveDays" name="time" value="de1a5dias">
                    <label for="fiveDays">1 a 5 dias</label><br>
                    <input type="radio" id="tenDays" name="time" value="de6a10dias">
                    <label for="tenDays">6 a 10 dias</label><br>
                    <input type="radio" id="thirteenDays" name="time" value="de11a13dias">
                    <label for="thirteenDays">11 a 13 dias</label><br>
                    <input type="radio" id="moreThan" name="time" value="maisDe14Dias">
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