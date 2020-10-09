<?php 

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Socketlabs\Message\BasicMessage;
use Socketlabs\Message\EmailAddress;
use Socketlabs\SocketLabsClient; 

require "vendor/autoload.php";

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();

date_default_timezone_set("America/Sao_Paulo");

function generateSpreadsheet() {
    # Encapsulando credenciais da database
    $host = $_ENV["HOST"];
    $dbname = $_ENV["DBNAME"];
    $username = $_ENV["USERNAME"];
    $password = $_ENV["PASSWORD"];
    

    $yesterday = date('Y-m-d',strtotime('-1 days'));

    if (date("D") == "Mon") {
        $retrieveData = "SELECT * FROM condicao_de_saude WHERE data_registro = CURDATE() - 2 OR data_registro = CURDATE() ORDER BY id_registro";
    } else if (date("j") == "1") {
        $retrieveData = "SELECT * FROM condicao_de_saude WHERE data_registro = $yesterday OR data_registro = CURDATE() ORDER BY id_registro";
    } else {
        $retrieveData = "SELECT * FROM condicao_de_saude WHERE data_registro = CURDATE() - 1 OR data_registro = CURDATE() ORDER BY id_registro";
    }
    
    try {
        $conn = new PDO("mysql:host=$host;dbname=$dbname", $username, $password); // Instanciando o PDO
        // echo "Connected to $dbname at $host successfully."; // DEBUG
        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION); // Setando o erro mode do PDO
        $data = $conn->query($retrieveData);
        // echo "New record created successfully"; // DEBUG
    } catch(PDOException $e) {
        // echo $sql . "<br>" . $e->getMessage(); // DEBUG
        $caught = true;
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
    $sheet->setCellValue("E1", "Unidade");
    $sheet->setCellValue("F1", "Área");
    $sheet->setCellValue("G1", "Contato com contaminado");
    $sheet->setCellValue("H1", "Contato com agente da saúde");
    $sheet->setCellValue("I1", "Sintomas");
    $sheet->setCellValue("J1", "Tempo dos sintomas");

    $columns = array("B", "C", "D", "E", "F", "G", "H", "I", "J");
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
        $sheet->setCellValue("E$aux", $row["colaborador_unidade"]);
        $sheet->setCellValue("F$aux", $row["colaborador_area"]);
        $sheet->setCellValue("G$aux", $row["contato_contaminado"]);
        $sheet->setCellValue("H$aux", $row["contato_agente"]);
        $sheet->setCellValue("I$aux", $row["sintomas"]);
        $sheet->setCellValue("J$aux", $row["tempo_sintomas"]);
        $aux++;
    }

    $writer = new Xlsx($spreadsheet);
    $writer->save("registro_de_entrada.xlsx");

    # Variaáveis para o email 
    $emailMessage = "Planilha dos registros de entrada dos dois últimos dias de serviço";

    # Disparando a planilha pro email
    $client = new SocketLabsClient($_ENV["SERVER_ID"], $_ENV["API_KEY"]);
    $message = new BasicMessage(); 
    $message->subject = "Planilha de registros de entrada";
    $message->htmlBody = "<html>$emailMessage</html>";
    $message->plainTextBody = "$emailMessage";
    $message->from = new EmailAddress($_ENV["FROM_EMAIL"]);
    $message->addToAddress($_POST["name"]);
    $att = \Socketlabs\Message\Attachment::createFromPath( __DIR__ . "\\registro_de_entrada.xlsx");
    $message->attachments[] = $att;
    $response = $client->send($message);
    echo  "<script>alert('Planilha enviada para seu e-mail');</script>";
    header("Refresh:0");
};

function databaseRequestHandler($sql, $operation) {
    $exec = "exec";
    $query = "query";

    # Encapsulando credenciais da database
    $host = $_ENV["HOST"];
    $dbname = $_ENV["DBNAME"];
    $username = $_ENV["USERNAME"];
    $password = $_ENV["PASSWORD"];

    try {
        $conn = new PDO("mysql:host=$host;dbname=$dbname", $username, $password); 
        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION); 
        
        if ($operation == $exec) {
            $conn->exec($sql); 
        } else if ($operation == $query) {
            $result = $conn->query($sql);
            return $result;
        }
        
    } catch(PDOException $e) {
        // echo $sql . "<br>" . $e->getMessage(); // DEBUG 
        echo  "<script>alert('Algo deu errado, tente novamente');</script>";
        return true;
        header("Refresh:0");
    }
}

?>