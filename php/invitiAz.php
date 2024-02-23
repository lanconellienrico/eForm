<?php
require_once('config.php');
require_once('mongoDB.php');
session_start();

$idSondaggio = $_SESSION['codiceSondaggio'];
$azienda = $_SESSION['azienda']['Codice_Fiscale'];
$mails = $_SESSION['mails'];
$nInviti = $_POST['nInviti'];

if($nInviti != 0){
  shuffle($mails);
  $nSpediti = 0;
  for($i = 0; $i < $nInviti; $i++){

    $call = $connessione->prepare('Call InvitaUtentiAz(?, ?, ?, @Ok)');
    $call->bind_param("iis", $azienda, $idSondaggio, $mails[$i]);
    $call->execute();

    /* @Ok è un boolean che restituisce:
    *  - TRUE : l'invito è stato correttamente inviato;
    *  - FALSE : l'invito non è stato inviato.
    */
    $select = $connessione->query('SELECT @Ok');
    $result = $select->fetch_assoc();
    $ok = $result['@Ok'];
    if($ok){
      $nSpediti++;

      #MongoDb Log Invito
      $bulk = new MongoDB\Driver\BulkWrite;
      $doc = [
        'Type' => 'Invito',
        'Mittente - Azienda' => $azienda,
        'Destinatario - Utente' => $mails[$i],
        'Sondaggio' => $idSondaggio,
      ];
      $bulk->insert($doc);
      $result = $connection->executeBulkWrite('eForm.Log', $bulk);
    }
  }
}
?>

<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
 <title>invite</title>
 <link href="invitiAz.css" rel="stylesheet" type="text/css"/>
 <meta name="viewport" content="width=device-width, initial-scale=1">
</head>
<body>
 <header>
   <p id ="home"><a href="./home.html">&#x2190 HOME</a></p>
   <p id='back' onclick='history.back()'>&#x2190 BACK</p>
 </header>
 <?php
  echo "<p id='userLog'>Logged as: ".$azienda."</p><br>";
  echo "<h2>Inviti automatici spediti con successo ";
  echo "<h2>Sono stati mandati ".$nSpediti." inviti.</h2>";
 ?>
</body>
</html>
