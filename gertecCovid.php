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

// if (isset($_REQUEST["getSectors"])) {
//     $getSectors = $_REQUEST["getSectors"];
//     if ($getSectors !== "") {
//         $sql = "SELECT setor_nome, id_setor FROM setores WHERE unidade_id = $getSectors";
//         $x = databaseRequestHandler($sql, $query);
//         $response = array();
//         foreach ($x as $y) {
//             array_push($response, [$y["setor_nome"], $y["id_setor"]]);
//         }
//         echo (json_encode($response));
//     }
// }   

if (empty($_POST["unity"])) {
    header("Location: unidade.php");
}

if (!empty($_POST["name"])) {

    $area = $_POST["area"];

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

    $spreadsheetRecipients = array(
        $_ENV["RH_1"],
        $_ENV["RH_2"],
        $_ENV["RH_3"],
        $_ENV["EXTRA"],
        $_ENV["TESTING"]
    );

    if (in_array($_POST["name"], $spreadsheetRecipients)) { # se o nome for um email do RH
        generateSpreadsheet();
    } elseif (isset($_POST["noneAbove"])) { # Se o colaborador estiver saudável
        # Variaveis para o query 
        $color = "32CD32";
        $name = $_POST["name"];
        $contact = $_POST["contact"];
        $doctor = $_POST["doctor"];
        
        session_start();
        $unityId = $_SESSION["unityFromForm"]; 
        $unityQuery = databaseRequestHandler("SELECT unidade_nome FROM unidades WHERE id_unidade = $unityId", $query);
        foreach ($unityQuery as $i) {
            $unity = $i["unidade_nome"];
        }

        $areaQuery = databaseRequestHandler("SELECT setor_nome FROM setores WHERE id_setor = $area", $query);
        foreach ($areaQuery as $i) {
            $area = $i["setor_nome"];
        }

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
                    '$area', 
                    '$contact', 
                    '$doctor', 
                    'Null', 
                    'Null'
                )";

        $caught = databaseRequestHandler($sql, $exec);

        if (!$caught) {
            session_start();       
            $_SESSION["name"] = $_POST["name"];
            $_SESSION["unity"] = $unity; 
            $_SESSION["area"] = $area;
            $_SESSION["date"] = date("d/m/Y\, H:i");
            $_SESSION["symptoms"] = $firstFormExtra;
            header("Location: liberado.php");
            exit();
        } else {
            echo  "<script>alert('Algo deu errado, tente novamente');</script>";
            header("Refresh:0");
        }
    } else { # Se o colaborador não estiver saudável
        session_start();   
        $_SESSION["name"] = $_POST["name"];  
        $_SESSION["area"] = $area;
        $_SESSION["unity"] = $_SESSION["unityFromForm"]; 
        $_SESSION["symptoms"] = $firstFormExtra;
        $_SESSION["contact"] = $_POST["contact"];
        $_SESSION["doctor"] = $_POST["doctor"];
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
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
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
                        <select class="sector" id="area" name="area" required>
                            <option disabled selected value>Selecione um setor</option>
                            <?php 
                                session_start();
                                $_SESSION["unityFromForm"] = $_POST["unity"];
                                $unityId = $_POST["unity"];
                                $sql = "SELECT setor_nome, id_setor FROM setores WHERE unidade_id = $unityId";
                                $result = databaseRequestHandler($sql, $query);
                                $unidades = array();
                                foreach ($result as $unidade) {
                                    $aux = $unidade["setor_nome"];
                                    $sectorId = $unidade["id_setor"];
                                    array_push($unidades, $aux);
                                    $pos = array_search($aux, $unidades) + 1;
                                    echo("<option value='$sectorId'>$aux</option>");
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
                    <input type="submit" value="ENVIAR" id="sendFormInput">
                </div>
            </form>
        </div>
    </body>
</html>



