<?php 

require "vendor/autoload.php";
require "functions.php";
use Socketlabs\Message\BasicMessage;
use Socketlabs\Message\EmailAddress;
use Socketlabs\SocketLabsClient;
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();
date_default_timezone_set("America/Sao_Paulo");
$exec = "exec";
$query = "query";

//------------------------------------------------------------------------//

if (!empty($_POST)) {
    $caught = false;

    # Variaveis para o query 
    session_start(); 
    $color = "BA1818";
    $name = $_SESSION["name"];
    $unity = $_SESSION["unity"];
    $area = $_SESSION["area"];
    $contact = $_SESSION["contact"];  
    $doctor = $_SESSION["doctor"];
    $symptoms = $_SESSION["symptoms"];
    $symptoms = implode(', ', $symptoms);
    $time = $_POST["time"];
    $_SESSION["time"] = $time;

    $map = array(
        'á' => 'a',
        'à' => 'a',
        'ã' => 'a',
        'â' => 'a',
        'é' => 'e',
        'ê' => 'e',
        'í' => 'i',
        'ó' => 'o',
        'ô' => 'o',
        'õ' => 'o',
        'ú' => 'u',
        'ü' => 'u',
        'ç' => 'c',
        'Á' => 'A',
        'À' => 'A',
        'Ã' => 'A',
        'Â' => 'A',
        'É' => 'E',
        'Ê' => 'E',
        'Í' => 'I',
        'Ó' => 'O',
        'Ô' => 'O',
        'Õ' => 'O',
        'Ú' => 'U',
        'Ü' => 'U',
        'Ç' => 'C'
    );
     
    $name = strtr($name, $map);
    $contact = (int)$contact;
    $doctor = (int)$doctor;

    # Encapsulando credenciais da database
    $host = $_ENV["HOST"];
    $dbname = $_ENV["DBNAME"];
    $username = $_ENV["USERNAME"];
    $password = $_ENV["PASSWORD"];
    $areaQuery = databaseRequestHandler("SELECT setor_nome FROM setores WHERE id_setor = $area", $query);
    foreach ($areaQuery as $i) {
        $areaForDB = $i["setor_nome"];
        $_SESSION["areaForDB"] = $areaForDB;
    }

    # Criando query para o MySQL
    $sql = "INSERT INTO condicao_de_saude (
                color, 
                data_registro, 
                hora_registro, 
                colaborador_nome, 
                colaborador_unidade,
                colaborador_area, 
                contato_contaminado, 
                contato_agente, 
                sintomas, 
                tempo_sintomas
            )
            VALUES (
                '$color', 
                CURDATE(), 
                CURTIME(), 
                '$name', 
                '$unity',
                '$areaForDB', 
                '$contact', 
                '$doctor', 
                '$symptoms', 
                '$time'
            )";

    if (!isset($symptoms)) {
        header("Location: gertecCovid.php");
        exit();
    } else {
        $caught = databaseRequestHandler($sql, $exec);
    }

    $emailQuery = databaseRequestHandler("SELECT gestor_email FROM setores WHERE id_setor = $area", $query);
    foreach ($emailQuery as $i) {
        $gestorEmail = $i["gestor_email"];
    }

    // DEBUG
    # Variáveis de ambas as mensagens.
    $currentDate = date("d/m/Y");
    $currentTime = date("H:i");

    # Monstando a mensagem para o Gestor 
    $textMessage = "Prezado Gestor,<br>Seu colaborador, $name, respondeu o formulário de saúde na 
    data de hoje, $currentDate, às $currentTime, apresentando riscos a saúde e de contaminação. Por favor pedimos 
    para imediatamente procurá-lo e conversar com o RH.";

    # Disparando a mensagem para o Gestor
    $client = new SocketLabsClient($_ENV["SERVER_ID"], $_ENV["API_KEY"]);
    $message = new BasicMessage(); 
    $message->subject = "Alerta de suspeita de COVID";
    $message->htmlBody = "<html>$textMessage</html>";
    $message->plainTextBody = "$textMessage";
    $message->from = new EmailAddress($_ENV["FROM_EMAIL"]);
    $message->addToAddress($gestorEmail);
    $response = $client->send($message);

    # Monstando a mensagem para o RH
    $textMessage = "Informamos que o colaborador, $name, preencheu o formulário na data de hoje às $currentTime respondeu que apresenta os sintomas da COVID-19.";

    # Disparando a mensagem para o RH
    $client = new SocketLabsClient($_ENV["SERVER_ID"], $_ENV["API_KEY"]);
    $message = new BasicMessage(); 
    $message->subject = "Alerta de suspeita de COVID";
    $message->htmlBody = "<html>$textMessage</html>";
    $message->plainTextBody = "$textMessage";
    $message->from = new EmailAddress($_ENV["FROM_EMAIL"]);
    $message->addToAddress($_ENV["RH_1"]);
    $message->addCcAddress($_ENV["RH_2"]);
    $message->addCcAddress($_ENV["RH_3"]);
    $message->addCcAddress($_ENV["SST"]);
    $response = $client->send($message);

    // DEBUG
    // $log = fopen("debugLog.txt", "w");
    // fwrite($log, $gestorEmail);

    if (!$caught) {
        header("Location: barrado.php");
        exit();
    } 
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
                <img id="logo" src="assets/media/logo.png">
                <h1>AVALIAÇÃO DE SAÚDE - COVID-19</h1>
                <h2>Continuação</h2>
                <div id="form-wrapper">
                <div class="field-container">
                        <h3>Há quanto tempo está com estes sintomas?<h3>
                        <input type="radio" id="today" name="time" value="Hoje" required>
                        <label for="today">Hoje</label><br>
                        <input type="radio" id="fiveDays" name="time" value="De1A5Dias">
                        <label for="fiveDays">1 a 5 dias</label><br>
                        <input type="radio" id="tenDays" name="time" value="De6A10Dias">
                        <label for="tenDays">6 a 10 dias</label><br>
                        <input type="radio" id="thirteenDays" name="time" value="De11A13Dias">
                        <label for="thirteenDays">11 a 13 dias</label><br>
                        <input type="radio" id="moreThan" name="time" value="MaisDe14Dias">
                        <label for="moreThan">Mais de 14 dias</label><br>
                    </div>
                    <input type="submit" value="ENVIAR">
                </div>
            </form>
        </div>
    </body>
</html>