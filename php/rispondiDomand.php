<?php
require_once("config.php");
require_once('mongoDB.php');
session_start();

$user = $_SESSION['user']['Email'];
$idDomanda = $_SESSION['idDomanda'];
$questionType = $_SESSION['questionType'];
$points = $_SESSION['point'];

$risposta = null;

if($questionType === 'Open'){
  $risposta = $connessione->real_escape_string($_POST['risposta']);
}else if($questionType === 'Closed'){
  if(!is_null($_POST['options'])){
    foreach($_POST['options'] as $risp){
      $risposta = $risposta.$risp.")";
    }
  }
}

#caricamento della risposta nel database if not null
if(!is_null($risposta)){
  $call = $connessione->prepare('CALL InserisciRisposta(?, ?, ?, @Ok)');
  $call->bind_param("sis", $user, $idDomanda, $risposta);
  $call->execute();

  /* Se un utente tenta di rispondere più volte alla stessa domanda, essa non viene inserita
  *  @Ok -> TRUE : la risposta è nuova ed è stata correttamente inserita
  *  @Ok -> FALSE : è già presente una risposta alla domanda da parte dello stesso utente
  */
  $select = $connessione->query('SELECT @Ok');
  $result = $select->fetch_assoc();
  $ok = $result['@Ok'];
  if($ok){
    #MongoDb Log Risposta
    $bulk = new MongoDB\Driver\BulkWrite;
    $doc = [
      'Type' => 'Risposta',
      'Utente' => $user,
      'Domanda' => $idDomanda,
      'Risposta' => $risposta,
    ];
    $bulk->insert($doc);
    $result = $connection->executeBulkWrite('eForm.Log', $bulk);
  }
}


 ?>

 <!DOCTYPE html>
 <html>
   <head>
     <meta charset="utf-8">
     <title>well done</title>
     <link href="rispondiDomand.css" rel="stylesheet" type="text/css"/>
     <meta name="viewport" content="width=device-width, initial-scale=1">
   </head>
   <body>
     <header>
       <p id ="home"><a href="./home.html">&#x2190 HOME</a></p>
       <p id='back' onclick="history.back()">&#x2190 BACK</p>
     </header>
     <?php
       echo "<p id='user'>Logged as: ".$user."</p>";
       if($ok){
         echo "<h2>Ottimo Lavoro!</h2>";
         echo "<p class='response'>Hai guadagnato: ".$points." punti Stellina</p>";
         if(!is_null($risposta)){
           echo "<br><p class='response' id='testoRisposta'>La tua risposta: ".$risposta."</p>";
         }
       }else{
         echo "<h2 style='color:red;'><i>BECCATO</i></h2>";
         echo "<br><span style='color:red;'><big>-.-</big></span>";
         echo "<p class='second'>Hai già risposto a questa domanda, ".$user."!";
         echo "<p class='second'>Punti fiducia -10";
       }
     ?>
   </body>
 </html>
