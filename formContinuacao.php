<?php 
// ini_set('display_errors', '0');
require 'vendor/autoload.php';
include_once ('./vendor/autoload.php'); 

date_default_timezone_set("America/Sao_Paulo");

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Socketlabs\SocketLabsClient;
use Socketlabs\Message\BasicMessage;
use Socketlabs\Message\EmailAddress;

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();

$setores = array(
    "Diretoria Executiva & Juridico",
    "Sistema De Gestao Da Qualidade - Sgq",
    "Recursos Humanos",
    "Servicos Gerais - Sp",
    "Financeiro",
    "Compras Internacionais",
    "Compras Nacionais",
    "Controladoria",
    "Bu I - Corporativo",
    "Bu II - Corporativo",
    "Bu III - Varejo",
    "Comercial, Produto",
    "Integracao Tecnologica",
    "Marketing",
    "Bu Solucoes",
    "Desenvolvimento Hardware",
    "Desenvolvimento Software - Varejo",
    "Engenharia De Produtos",
    "Engenharia Mecanica",
    "Engenharia De Processos",
    "Desenvolvimento De Software",
    "Q.A",
    "Engenharia De Tecnologia",
    "Customizacao",
    "Suporte Tecnico",
    "Segurança & Arquitetura",
    "Sistemas",
    "Tecnologia Da Informacao - TI",
    "Servicos Gerais - Ilhéus",
    "Administracao -  Ilhéus",
    "Fiscal",
    "Contabil",
    "Pcp - Ilhéus",
    "Processo Fabril-  Ilhéus",
    "Qualidade - Ilhéus",
    "Estoque - Ilhéus",
    "Desenvolvimento E Qualidade",
    "Estoque - Manaus",
    "Processo Fabril - Manaus",
    "Pcp - Manaus",
    "Administrativo - Diadema",
    "Servicos Gerais - Diadema",
    "Laboratorio",
    "Administrativo Laboratorio",
    "Projetos On Site",
    "Estoque - Diadema",
    "Venda Direta",
    "Depto Tecnico - Diadema"
);

$emailsGestores = array(
    $setores[0] => "ana.garcez@gertec.com.br",
    $setores[1] => "marcelo.graglia@gertec.com.br",
    $setores[2] => "edinalva.soares@gertec.com.br",
    $setores[3] => "edinalva.soares@gertec.com.br",
    $setores[4] => "rebecca.leite@gertec.com.br",
    $setores[5] => "beatriz.conegero@gertec.com.br",
    $setores[6] => "marcia.miranda@gertec.com.br",
    $setores[7] => "rebecca.leite@gertec.com.br",
    $setores[8] => "sidney.loureiro@gertec.com.br",
    $setores[9] => "marcelo@gertec.com.br",
    $setores[10] => "edilson.goncalves@gertec.com.br",
    $setores[11] => "wilson.antunes@gertec.com.br",
    $setores[12] => "sidney.loureiro@gertec.com.br",
    $setores[13] => "claudenir.andrade@gertec.com.br",
    $setores[14] => "wilmar.poli@gertec.com.br",
    $setores[15] => "paulo.pompilha@gertec.com.br",
    $setores[16] => "paulo.pompilha@gertec.com.br",
    $setores[17] => "kleber.paranhos@gertec.com.br",
    $setores[18] => "paulo.brum@gertec.com.br",
    $setores[19] => "jorge.mitsuo@gertec.com.br",
    $setores[20] => "rafael.silva@gertec.com.br",
    $setores[21] => "keren.dantas@gertec.com.br",
    $setores[22] => "robinson@gertec.com.br",
    $setores[23] => "augusto.kranz@gertec.com.br",
    $setores[24] => "welton.balani@gertec.com.br",
    $setores[25] => "welton.balani@gertec.com.br",
    $setores[26] => "welton.balani@gertec.com.br",
    $setores[27] => "welton.balani@gertec.com.br",
    $setores[28] => "claudia.farias@gertec.com.br",
    $setores[29] => "claudia.farias@gertec.com.br",
    $setores[30] => "jessika.frizo@gertec.com.br",
    $setores[31] => "raonni.fonseca@gertec.com.br",
    $setores[32] => "eduardo.freitas@gertec.com.br",
    $setores[33] => "eduardo.freitas@gertec.com.br",
    $setores[34] => "bruno.oliveira@gertec.com.br",
    $setores[35] => "kelson.souza@gertec.com.br",
    $setores[36] => "everton.costa@gertec.com.br",
    $setores[37] => "raimundo.andrade@gertec.com.br",
    $setores[38] => "luiz.caton@gertec.com.br",
    $setores[39] => "luiz.caton@gertec.com.br",
    $setores[40] => "robson.balog@gertec.com.br",
    $setores[41] => "robson.balog@gertec.com.br",
    $setores[42] => "welison.paiva@gertec.com.br",
    $setores[43] => "leticia.gomes@gertec.com.br",
    $setores[44] => "robson.balog@gertec.com.br",
    $setores[45] => "joao.oliveira@gertec.com.br",
    $setores[46] => "robson.balog@gertec.com.br",
    $setores[47] => "welison.paiva@gertec.com.br"
);

if (!empty($_POST)) {
    session_start();
    $_SESSION["time"] = $_POST["time"];
    $name = $_SESSION["name"];
    $area = $_SESSION["area"];  
    $symptoms = $_SESSION["symptoms"];
    $nextRow = $_SESSION["nextRow"];
    $contact = $_SESSION["contact"];
    $doctor = $_SESSION["doctor"];
    $_SESSION["name"] = $name;
    $_SESSION["area"] = preg_replace('/(?<!\ )[A-Z, &]/', ' $0', $area);

    $spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load("registros.xlsx");
    $sheet = $spreadsheet->getActiveSheet();
    $highestRow = $sheet->getHighestDataRow();
    $nextRow = $highestRow + 1;

    $sheet->getStyle("A$nextRow")->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setRGB("25500");  
    $sheet->setCellValue("B$nextRow", date("d/m/Y"));
    $sheet->setCellValue("C$nextRow", date("H:i:s"));
    $sheet->setCellValue("D$nextRow", $name);
    $sheet->setCellValue("E$nextRow", preg_replace('/(?<!\ )[A-Z, &]/', ' $0', $area));
    $sheet->setCellValue("F$nextRow", $contact);
    $sheet->setCellValue("G$nextRow", $doctor);
    foreach ($symptoms as $symptom) {
        $value = $sheet->getCell("H$nextRow")->getValue();
        if ($value == null) {
            $sheet->setCellValue("H$nextRow", preg_replace('/(?<!\ )[A-Z]/', ' $0', $symptom));
        } else {
            $sheet->setCellValue("H$nextRow", $value.", ".preg_replace('/(?<!\ )[A-Z]/', ' $0', $symptom));
        }
    }
    $sheet->setCellValue("I$nextRow", $_POST["time"]);

    $writer = new Xlsx($spreadsheet);

    $caught = false;
    try {
        $writer->save("registros.xlsx");
    } catch (Exception $e) {
        $caught = true;
        echo  "<script>alert('Algo deu errado, tente novamente');</script>";
        header("Refresh:0");
    }   

    # Disparando e-mails
    function sendEmail($subject, $textMessage, $recipient) {
        $client = new SocketLabsClient($_ENV["SERVER_ID"], $_ENV["API_KEY"]);
        $message = new BasicMessage(); 
        $message->subject = $subject;
        $message->htmlBody = "<html>$textMessage</html>";
        $message->plainTextBody = "$textMessage";
        $message->from = new EmailAddress($_ENV["FROM_EMAIL"]);
        $message->addToAddress($recipient);
        $response = $client->send($message);
    }

    # Fazendo backup
    if ($nextRow % 100 == 0) {
        $nameDate = date("d\_m\_H\-i");
        copy("registros.xlsx", "backup_$nameDate.xlsx");
    }

    # Para o gestor
    $currentDate = date("d/m/Y");
    $currentTime = date("H:i");
    $formatedArea = substr(preg_replace('/(?<!\ )[A-Z, &]/', ' $0', $area), 1);
    $areaEmail = $emailsGestores[$area];
    $textMessage = "Prezado Gestor,<br>Seu colaborador, $name, respondeu o formulário de saúde na 
    data de hoje, $currentDate, às $currentTime, apresentando riscos a saúde e de contaminação. Por favor pedimos 
    para imediatamente procurá-lo e conversar com o RH.";
    sendEmail("Alerta de suspeita de COVID", $textMessage, $areaEmail); 

    # Para o RH
    $currentDate = date("d/m/Y");
    $currentTime = date("H:i");
    $textMessage = "Informamos que o colaborador, $name, preencheu o formulário na data de hoje 
    $currentDate, às $currentTime, e respondeu que apresenta os sintomas da COVID-19.";
    sendEmail("Alerta de suspeita de COVID", $textMessage, $_ENV["RH_1"]); 

    # Para o RH
    $currentDate = date("d/m/Y");
    $currentTime = date("H:i");
    $textMessage = "Informamos que o colaborador, $name, preencheu o formulário na data de hoje 
    $currentDate, às $currentTime, e respondeu que apresenta os sintomas da COVID-19.";
    sendEmail("Alerta de suspeita de COVID", $textMessage, $_ENV["RH_2"]); 

    # Para o RH
    $currentDate = date("d/m/Y");
    $currentTime = date("H:i");
    $textMessage = "Informamos que o colaborador, $name, preencheu o formulário na data de hoje 
    $currentDate, às $currentTime, e respondeu que apresenta os sintomas da COVID-19.";
    sendEmail("Alerta de suspeita de COVID", $textMessage, $_ENV["RH_3"]); 

    # Para o RH
    $currentDate = date("d/m/Y");
    $currentTime = date("H:i");
    $textMessage = "Informamos que o colaborador, $name, preencheu o formulário na data de hoje 
    $currentDate, às $currentTime, e respondeu que apresenta os sintomas da COVID-19.";
    sendEmail("Alerta de suspeita de COVID", $textMessage, $_ENV["SST"]); 

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