<?php

require_once('config.php');
require_once('mongoDB.php');
session_start();

$titolo = $connessione->real_escape_string($_POST['Titolo']);
$dataCr = $connessione->real_escape_string($_POST['Data_Creazione']);
$dataCh = $connessione->real_escape_string($_POST['Data_Chiusura']);
$max = $connessione->real_escape_string($_POST['Max_Utenti']);
$dom = $connessione->real_escape_string($_POST['Dominio']);

$premium = null;
$company = null;
$creator = "";
if($_SESSION['human']){
  $premium = $_SESSION['user']['Email'];
  $creator = $premium;
} else{
  $company = $_SESSION['azienda']['Codice_Fiscale'];
  $creator = $company;
}

$call = $connessione->prepare('CALL InsertSondaggio(?, ?, ?, ?, ?, ?, ?, @Ok)');
$call->bind_param("sssisss", $titolo, $dataCr, $dataCh, $max, $dom, $premium, $company);
$call->execute();

/*
* ok controlla se il nome del dominio inserito esiste nel Database
* ok = TRUE -> il dominio a cui si fa riferimento esiste
* ok = FALSE -> dominio inesistente
*/
$select = $connessione->query('SELECT @Ok');
$result = $select->fetch_assoc();
$ok = $result['@Ok'];

if(!$ok){
  header("location: error.html");
}else{
  #MongoDb Log Sondaggio
  $bulk = new MongoDB\Driver\BulkWrite;
  $doc = [
    'Type' => 'Sondaggio',
    'Titolo' => $titolo,
    'Data Creazione' => $dataCr,
    'Data Chiusura' => $dataCh,
    'Max Utenti' => $max,
    'Dominio' => $dom,
    'Creator' => $creator,
  ];
  $bulk->insert($doc);
  $result = $connection->executeBulkWrite('eForm.Log', $bulk);
}
 ?>

 <!DOCTYPE html>
 <html>
   <head>
     <meta charset="utf-8">
     <title>created</title>
     <meta name="viewport" content="width=device-width, initial-scale=1">
   </head>
   <body>
     <header>
       <p id ="home"><a href="./home.html">&#x2190 HOME</a></p>
       <p id='back' onclick='history.back()'>&#x2190 BACK</p>
     </header>
     <?php      echo "<p id='userLog'>Logged as: ".$creator."</p>";  ?>
     <h2>Sondaggio creato con successo</h2>
     <div id="dati">
      <?php
        echo "<b>Titolo: </b>" . $titolo . "<br>";
        echo "<b>Data creazione: </b>" . $dataCr . "<br>";
        echo "<b>Data chiusura: </b>" . $dataCh . "<br>";
        echo "<b>Max utenti: </b>" . $max . "<br>";
        echo "<b>Dominio: </b>" . $dom . "<br>";
        echo "<b>Un lavoro di: </b>" . $creator;
      ?>
    </div>
   </body>
   <style>
     html{
       visibility: hidden;
       font-family: sans-serif;
       text-align: center;
       padding-top: 7.5vh;
       background-color: rgb(20, 20, 20);
     }
     *{
       color: white;
     }
     h2{
       visibility: visible;
     }
     div{
       visibility: visible;
     }
     #userLog{
       visibility: visible;
       color: red;
       position: fixed;
       margin-top: 0px;
     }
     #home a{
       text-decoration: none;
       color:white;
     }
     #home{
       visibility: visible;
       padding-top: 9px;
       height: 30px;
       width: 100px;
       border-radius: 3px;
       background-color: rgb(156, 81, 182);
       position: fixed;
       top:-5px;
     }
     #home:hover{
       background-color: rgb(140, 75, 170);
       box-shadow: inset -1px -1px 3px rgba(0,0,0,0.5);
       cursor: pointer;
     }
     #back{
       color: white;
       visibility: visible;
       padding-top: 9px;
       height: 30px;
       width: 100px;
       border-radius: 3px;
       background-color: rgb(86, 53, 218);
       position: fixed;
       top:-5px;
       left: 130px;
     }
     #back:hover{
       background-color: rgb(68,39,188);
       box-shadow: inset -1px -1px 3px rgba(0,0,0,0.5);
       cursor: pointer;
     }
   </style>
 </html>
