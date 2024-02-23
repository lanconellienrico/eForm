<?php

require_once('config.php');
require_once('mongoDB.php');

$email = $connessione->real_escape_string($_POST['Email']);
$cf = $_POST['Codice_Fiscale'];
$nome = $connessione->real_escape_string($_POST['Nome']);
$sede = $connessione->real_escape_string($_POST['Sede']);

#Registrazione dell'Azienda
$call = $connessione->prepare('CALL InsertAzienda(?, ?, ?, ?, @Ok)');
$call->bind_param("isss", $cf, $email, $nome, $sede);
$call->execute();

/*
* ok controlla se il Codice Fiscale inserito è già presente nel Database
* ok = TRUE -> CF nuovo
* ok = FALSE -> CF già presente
*
* Se Ok = TRUE si procede anche con l'inserimento del Log su MongoDB
*/
$select = $connessione->query('SELECT @Ok');
$result = $select->fetch_assoc();
$ok = $result['@Ok'];

if(!$ok || $cf<10000000000 || $cf>99999999999){
  header("location: doubleCF.html");
}else{
  #MongoDb Log Azienda
  $bulk = new MongoDB\Driver\BulkWrite;
  $doc = [
    'Type' => 'Azienda',
    'Codice Fiscale' => $cf,
    'Email' => $email,
    'Nome' => $nome,
    'Sede' => $luogo,
  ];
  $bulk->insert($doc);
  $result = $connection->executeBulkWrite('eForm.Log', $bulk);
}


 ?>
 <!DOCTYPE html>
 <html>
   <head>
     <meta charset="utf-8">
     <title>signed up</title>
     <link href="registerAz.css" rel="stylesheet" type="text/css"/>
     <meta name="viewport" content="width=device-width, initial-scale=1">
   </head>
   <body>
     <header>
       <p id ="home"><a href="./home.html">&#x2190 HOME</a></p>
     </header>
     <h2>Registrazione avvenuta con successo</h2>
     <div id="dati">
      <?php
        echo "<br>" . "Email: " . $email . "<br>";
        echo " Codice Fiscale: " . $cf . "<br>";
        echo " Nome: " . $nome . "<br>";
        echo " Sede: " . $sede;
      ?>
    </div>
   </body>
 </html>
