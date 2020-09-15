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

    $currentTime =  date("d/m/Y H:i");

    if ($_POST["name"] == $_ENV["RH_1"] || $_POST["name"] == $_ENV["RH_2"] || $_POST["name"] == $_ENV["RH_3"] || $_POST["name"] == $_ENV["EXTRA"] || $_POST["name"] == $_ENV["TESTING"]) { # se o nome for um email do RH
        # Encapsulando credenciais da database
        $host = $_ENV["HOST"];
        $dbname = $_ENV["DBNAME"];
        $username = $_ENV["USERNAME"];
        $password = $_ENV["PASSWORD"];
        
        $retrieveData = "SELECT * FROM condicao_de_saude WHERE data_registro = CURDATE() - 1 OR data_registro = CURDATE() ORDER BY data_registro ASC";
        
        try {
            $conn = new PDO("mysql:host=$host;dbname=$dbname", $username, $password); // Instanciando o PDO
            // echo "Connected to $dbname at $host successfully."; // DEBUG
            $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION); // Setando o erro mode do PDO
            $data = $conn->query($retrieveData);
            // echo "New record created successfully"; // DEBUG
        } catch(PDOException $e) {
            // echo $sql . "<br>" . $e->getMessage(); // DEBUG
            echo  "<script>alert('Algo deu errado, tente novamente');</script>";
            $caught = true;
            // header("Refresh:0");
        }

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        $aux = 2;
        $oneStyle = [
            'font' => [
                'bold' => true,
            ]
        ];

        $sheet->setCellValue("B1", "Data");
        $sheet->setCellValue("C1", "Hora");
        $sheet->setCellValue("D1", "Nome");
        $sheet->setCellValue("E1", "Área");
        $sheet->setCellValue("F1", "Contato com contaminado");
        $sheet->setCellValue("G1", "Contato com agente da saúde");
        $sheet->setCellValue("H1", "Sintomas");
        $sheet->setCellValue("I1", "Tempo dos sintomas");

        $columns = array("B", "C", "D", "E", "F", "G", "H", "I");
        foreach ($columns as $column){
            $spreadsheet->getActiveSheet()->getStyle($column."1")->applyFromArray($oneStyle);
        }

        $spreadsheet->getActiveSheet()->getColumnDimension("B")->setWidth(12);
        $spreadsheet->getActiveSheet()->getColumnDimension("C")->setWidth(12);
        $spreadsheet->getActiveSheet()->getColumnDimension("D")->setWidth(50);
        $spreadsheet->getActiveSheet()->getColumnDimension("E")->setWidth(50);
        $spreadsheet->getActiveSheet()->getColumnDimension("F")->setWidth(24);
        $spreadsheet->getActiveSheet()->getColumnDimension("G")->setWidth(27);
        $spreadsheet->getActiveSheet()->getColumnDimension("H")->setWidth(50);
        $spreadsheet->getActiveSheet()->getColumnDimension("I")->setWidth(30);

        foreach ($data as $row) {
            $aStyle = [
                'fill' => [
                    'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                    'rotation' => 90,
                    'startColor' => [
                        'argb' => $row["color"],
                    ]
                ]
            ];
            $spreadsheet->getActiveSheet()->getStyle("A$aux")->applyFromArray($aStyle);
            $sheet->setCellValue("B$aux", $row["data_registro"]);
            $sheet->setCellValue("C$aux", $row["hora_registro"]);
            $sheet->setCellValue("D$aux", $row["colaborador_nome"]);
            $sheet->setCellValue("E$aux", $row["colaborador_area"]);
            $sheet->setCellValue("F$aux", $row["contato_contaminado"]);
            $sheet->setCellValue("G$aux", $row["contato_agente"]);
            $sheet->setCellValue("H$aux", $row["sintomas"]);
            $sheet->setCellValue("I$aux", $row["tempo_sintomas"]);
            $aux++;
        }

        $writer = new Xlsx($spreadsheet);
        $writer->save('registro_de_entrada.xlsx');
    } elseif (isset($_POST["noneAbove"])) { # Se o colaborador estiver saudável
        # Variaveis para o query 
        $color = "32CD32";
        $name = $_POST["name"];
        $area = preg_replace('/(?<!\ )[A-Z, &]/', ' $0', $_POST["area"]);
        $contact = $_POST["contact"];
        $doctor = $_POST["doctor"];

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
        $area = strtr($area, $map);

        $contact = (int)$contact;
        $doctor = (int)$doctor;

        # Criando query para o MySQL
        $sql = "INSERT INTO condicao_de_saude (
                    color, 
                    data_registro, 
                    hora_registro, 
                    colaborador_nome, 
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
                    '$area', 
                    '$contact', 
                    '$doctor', 
                    'Null', 
                    'Null'
                )";

        # Encapsulando credenciais da database
        $host = $_ENV["HOST"];
        $dbname = $_ENV["DBNAME"];
        $username = $_ENV["USERNAME"];
        $password = $_ENV["PASSWORD"];

        try {
            $conn = new PDO("mysql:host=$host;dbname=$dbname", $username, $password); // Instanciando o PDO
            // echo "Connected to $dbname at $host successfully."; // DEBUG
            $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION); // Setando o erro mode do PDO
            $conn->exec($sql); // Executando o query
            // echo "New record created successfully"; // DEBUG
        } catch(PDOException $e) {
            // echo $sql . "<br>" . $e->getMessage(); // DEBUG
            echo  "<script>alert('Algo deu errado, tente novamente');</script>";
            $caught = true;
            // header("Refresh:0");
        }

        $conn = null;

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
                Faça sua autoavaliação diariamente de forma consciente. O objetivo da análise é mitigar 
                o risco de disseminação da doença entre nossos colaboradores e preservar a sua saúde, 
                bem como de seus familiares. Porém, é sua responsabilidade examinar-se diariamente 
                e responder o questionário assertivamente.
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
                                echo("<option value='$setor'>$setor</option>");
                            }
                        ?>
                    </select>
                </div>
                <div class="field-container">
                    <h3>Teve contato com alguém contaminado COVID-19?<h3>
                    <input type="radio" id="withFever" name="contact" value="1" required>
                    <label for="withFever">Sim</label><br>
                    <input type="radio" id="noFever"name="contact" value="0">
                    <label for="nofever">Não</label><br>
                </div>
                <div class="field-container">
                    <h3>Reside com algum agente da saúde (enfermeiro, médico, etc..)?<h3>
                    <input type="radio" id="withFever" name="doctor" value="1" required>
                    <label for="withFever">Sim</label><br>
                    <input type="radio" id="noFever"name="doctor" value="0">
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



