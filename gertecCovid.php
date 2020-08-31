<?php 
// ini_set('display_errors', '0');
require 'vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Socketlabs\Message\BasicMessage;
use Socketlabs\Message\EmailAddress;
use Socketlabs\SocketLabsClient; 

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();

date_default_timezone_set("America/Sao_Paulo");

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
    "Tecnologia Da Informacao - TI"
);

if (!empty($_POST)) {
    $extraSymptoms = array(
        "pain",
        "soreThroat",
        "diarreia",
        "headache",
        "tasteless",
        "congestion",
        "conjunctivitis",
        "tired"
    );
    
    $firstFormExtra = array();
    
    foreach ($extraSymptoms as $symptom) {
        if (isset($_POST[$symptom])) {
            array_push($firstFormExtra, $_POST[$symptom]);
        }
    }

    $currentTime =  date("d/m/Y \às H:i");

    if ($_POST["name"] == $_ENV["RH_1"] || $_POST["name"] == $_ENV["RH_2"] || $_POST["name"] == $_ENV["RH_3"]) { # se o nome for um email do RH
        $serverId = $_ENV["SERVER_ID"];
        $injectionApiKey = $_ENV["API_KEY"];

        $client = new SocketLabsClient($serverId, $injectionApiKey);
        
        $message = new BasicMessage(); 

        $message->subject = "Registro de entrada de colaboradores";
        $message->htmlBody = "<html>Planilha com o registro de entrada de funcionários até $currentTime</html>";
        $message->plainTextBody = "Planilha com o registro de entrada de funcionários até $currentTime";

        $message->from = new EmailAddress($_ENV["FROM_EMAIL"]);

        $att = \Socketlabs\Message\Attachment::createFromPath("registros.xlsx");
        $message->attachments[] = $att;

        $message->addToAddress($_POST["name"]);
        
        $response = $client->send($message);
    } elseif (isset($_POST["noneAbove"])) { # Se o colaborador estiver saudável
        $spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load("registros.xlsx");
        $sheet = $spreadsheet->getActiveSheet();
        $highestRow = $sheet->getHighestDataRow();
        $nextRow = $highestRow + 1;
    
        # Fazendo backup
        if ($highestRow % 100 == 0){
            $nameDate = date("d\_m\_H\-i");
            copy("registros.xlsx", "backup_$nameDate.xlsx");
        }

        $sheet->getStyle("A$nextRow")->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setRGB("32CD32");  
        $sheet->setCellValue("B$nextRow", date("d/m/Y"));
        $sheet->setCellValue("C$nextRow", date("H:i:s"));
        $sheet->setCellValue("D$nextRow", $_POST["name"]);
        $sheet->setCellValue("E$nextRow", preg_replace('/(?<!\ )[A-Z, &]/', ' $0', $_POST["area"]));
        $sheet->setCellValue("F$nextRow", $_POST["contact"]);
        $sheet->setCellValue("G$nextRow", $_POST["doctor"]);

        $writer = new Xlsx($spreadsheet);
    
        $caught = false;
        try {
            $writer->save("registros.xlsx");
        } catch (Exception $e) {
            $caught = true;
            echo  "<script>alert('Algo deu errado, tente novamente');</script>";
            header("Refresh:0");
        }

        if (!$caught) {
            session_start();       
            $_SESSION["name"] = $_POST["name"];
            $_SESSION["area"] = ucfirst($_POST["area"]);
            $_SESSION["date"] = date("d/m/Y\, H:i");
            $_SESSION["symptoms"] = $firstFormExtra;
            header("Location: liberado.php");
            exit();
        }
    } else { # Se o colaborador não estiver saudável
        $spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load("registros.xlsx");
        $sheet = $spreadsheet->getActiveSheet();
        $highestRow = $sheet->getHighestDataRow();
        $nextRow = $highestRow + 1;

        session_start();   
        $_SESSION["name"] = $_POST["name"];  
        $_SESSION["area"] = $_POST["area"] ;
        $_SESSION["symptoms"] = $firstFormExtra;
        $_SESSION["contact"] = $_POST["contact"];
        $_SESSION["doctor"] = $_POST["doctor"];
        $_SESSION["nextRow"] = $nextRow;
        header("Location: formContinuacao.php");
        exit();
    }
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
    <div id="content-wrapper">
        <form id="form" action="" method="post">
            <img id="logo" src="assets/media/logo.png">
            <h1>AVALIAÇÃO DE SAÚDE - COVID-19</h1>
            <p>
                Antes de sair de casa faça sua autoavaliação e se dirija ao escritório de forma consciente e segura se estiver liberado.
                O objetivo da anamnese é mitigar o risco de disseminação da doença entre nossos colaboradores e preservar a sua saúde, 
                bem como de seus familiares. Porem, é sua responsabilidade examinar-se diariamente e responder o questionário assertivamente. 
                <br>#juntosSomosMaisFortes

            </p>

            <div id="form-wrapper">
                <div class="field-container">
                    <label for="name">Nome:</label>
                    <input type="text" name="name" required>
                </div>
                <div class="field-container">
                    <label for="area">Área:</label>
                    <select id="area" name="area" required>
                        <?php 
                            foreach ($setores as $setor) {
                                echo '<script>';
                                echo 'console.log('. json_encode( "Hello World" ) .')';
                                echo '</script>';
                                echo("<option value='$setor'>$setor</option>");
                            }
                        ?>
                    </select>
                </div>
                <div class="field-container">
                    <h3>Teve contato com alguém contaminado COVID-19?<h3>
                    <input type="radio" id="withFever" name="contact" value="Sim" required>
                    <label for="withFever">Sim</label><br>
                    <input type="radio" id="noFever"name="contact" value="Não">
                    <label for="nofever">Não</label><br>
                </div>
                <div class="field-container">
                    <h3>Reside com algum agente da saúde (enfermeiro, médico, etc..)?<h3>
                    <input type="radio" id="withFever" name="doctor" value="Sim" required>
                    <label for="withFever">Sim</label><br>
                    <input type="radio" id="noFever"name="doctor" value="Não">
                    <label for="nofever">Não</label><br>
                </div>
                <div class="field-container">
                    <h3>Você apresenta um ou mais dos sintomas abaixo?<h3>
                    <input onchange="extraSelected('pain')" class="extra" type="checkbox" name="pain" value="DorEDesconforto">
                    <label for="pain">Dores e desconfortos </label><br>
                    <input onchange="extraSelected('soreThroat')" class="extra" type="checkbox" name="soreThroat" value="DorDeGarganta">
                    <label for="soreThroat">Dor de Garganta</label><br>
                    <input onchange="extraSelected('diarreia')" class="extra" type="checkbox" name="diarreia" value="Diarreia">
                    <label for="diarreia">Diarréia</label><br>
                    <input onchange="extraSelected('headache')" class="extra" type="checkbox" name="headache" value="DorDeCabeca">
                    <label for="headache">Dor de Cabeça</label><br>
                    <input onchange="extraSelected('tasteless')" class="extra" type="checkbox" name="tasteless" value="PerdaDePaladar">
                    <label for="tasteless">Diminuição de olfato/paladar</label><br>
                    <input onchange="extraSelected('congestion')" class="extra" type="checkbox" name="congestion" value="CongestaoNasal">
                    <label for="congestion">Congestão/Corrimento nasal</label><br>
                    <input onchange="extraSelected('cough')" class="extra" type="checkbox" name="cough" value="tosse">
                    <label for="cough">Tosse</label><br>
                    <input onchange="extraSelected('conjunctivitis')" class="extra" type="checkbox" name="conjunctivitis" value="Conjuntivite">
                    <label for="conjunctivitis">Conjuntivite</label><br>
                    <input onchange="extraSelected('tired')" class="extra" type="checkbox" name="tired" value="CansacoOuFaltaDeAr">
                    <label for="tired">Cansaço ou falta de ar</label><br>
                    <input onchange="noneSelected()" id="noneAbove" type="checkbox" name="noneAbove" value="NenhumAcima" checked>
                    <label for="noneAbove">Estou bem de saúde, não apresento nenhum dos sintomas citados.</label><br>
                </div>
                <input type="submit" value="ENVIAR">
            </div>
        </form>
    </div>
</body>
</html>



