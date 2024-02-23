<?php

require_once('config.php');
require_once('mongoDB.php');
session_start();

$user = $_SESSION['user']['Email'];

$keyword = $connessione->real_escape_string($_POST['Parola_Chiave']);
$description = $connessione->real_escape_string($_POST['Descrizione']);

$call = $connessione->prepare('CALL InsertDominio(?, ?, @Ok)');
$call->bind_param("ss", $keyword, $description);
$call->execute();

#Ok è un boolean controlla che il dominio venga inserito con successo(TRUE)
$select = $connessione->query('SELECT @Ok');
$result = $select->fetch_assoc();
$ok = $result['@Ok'];

if(!$ok){
  header("location: error.html");
  exit();
}else{
  #MongoDb Log Dominio
  $bulk = new MongoDB\Driver\BulkWrite;
  $doc = [
    'Type' => 'Dominio',
    'Parola Chiave' => $keyword,
    'Descrizione' => $description,
  ];
  $bulk->insert($doc);
  $result = $connection->executeBulkWrite('eForm.Log', $bulk);

}
 ?>

 <!DOCTYPE html>
 <html>
   <head>
     <meta charset="utf-8">
     <title>placed</title>
     <link href="newDomai.css" rel="stylesheet" type="text/css"/>
     <meta name="viewport" content="width=device-width, initial-scale=1">
   </head>
   <body>
     <header>
       <p id ="home"><a href="./home.html">&#x2190 HOME</a></p>
       <p id='back' onclick='history.back()'>&#x2190 BACK</p>
     </header>
     <?php      echo "<p id='userLog'>Logged as: ".$user."</p>";  ?>
     <h2>Dominio inserito con successo</h2>
     <div id="dati">
      <?php
        echo "Parola Chiave: " . $keyword . "<br>";
        echo "Descrizione: " . $description . "<br>";
      ?>
    </div>
   </body>
 </html>
