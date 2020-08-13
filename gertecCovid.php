<?php
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" type="text/css" href="assets/styles/style.css" media="screen" />
    <script type="text/javascript" src="assets/scripts/script.js"></script>
    <title>Avaliação de Saúde</title>
</head>
<body>
    <div id="content-wrapper">
        <form>
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
                    <input type="text" name="name">
                </div>
                <div class="field-container">
                    <label for="area">Área:</label>
                    <select id="area" name="area">
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
                    <input type="radio" id="symptomatic" name="symptoms" value="sintomatico">
                    <label for="symptomatic">Estou bem e sem sintomas</label><br>
                    <input type="radio" id="asymptomatic" name="symptoms" value="assintomatico">
                    <label for="asymptomatic">Estou com alguns sintomas</label><br>
                </div>
                <div class="field-container">
                    <h3>Você teve febre (temperatura igual ou superior a 37.8°)?<h3>
                    <input type="radio" id="withFever" name="fever" value="sim">
                    <label for="withFever">Sim</label><br>
                    <input type="radio" id="noFever"name="fever" value="nao">
                    <label for="nofever">Não</label><br>
                </div>
                <div class="field-container">
                    <h3>Está sentindo falta de ar ao realizar esforços ou está com respiração ofegante?<h3>
                    <input type="radio" id="gasping" name="gasp" value="sim">
                    <label for="gasping">Sim</label><br>
                    <input type="radio" id="noGasping" name="gasp" value="nao">
                    <label for="noGasping">Não</label><br>
                </div>
                <div class="field-container">
                    <h3>Como está sua saúde no momento?<h3>
                    <input onchange="extraSelected('congestion')" class="extra" type="checkbox" name="congestion" value="congestaoNasal">
                    <label for="congestion">Cogestão/Corrimento nasal</label><br>
                    <input onchange="extraSelected('tasteless')" class="extra" type="checkbox" name="tasteless" value="perdaPaladar">
                    <label for="tasteless">Diminuição de olfato/paladar</label><br>
                    <input onchange="extraSelected('soreThroat')" class="extra" type="checkbox" name="soreThroat" value="dorGarganta">
                    <label for="soreThroat">Dor de Garganta</label><br>
                    <input onchange="extraSelected('jointPain')" class="extra" type="checkbox" name="jointPain" value="dorArticulacao">
                    <label for="jointPain">Dores nas articulações</label><br>
                    <input onchange="extraSelected('cough')" class="extra" type="checkbox" name="cough" value="tosse">
                    <label for="cough">Tosse</label><br>
                    <input onchange="noneSelected()" id="noneAbove" type="checkbox" name="noneAbove" value="nenhumAcima">
                    <label for="noneAbove">Não tenho nenhum dos sintomas acima</label><br>
                </div>
                <button type="submit">ENVIAR</button>
            </div>
        </form>
    </div>
</body>
</html>
?>