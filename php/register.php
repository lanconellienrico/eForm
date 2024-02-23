<?php

require_once('config.php');
require_once('mongoDB.php');

$email = $connessione->real_escape_string($_POST['Email']);
$nome = $connessione->real_escape_string($_POST['Nome']);
$cognome = $connessione->real_escape_string($_POST['Cognome']);
$luogo = $connessione->real_escape_string($_POST['Luogo_Nascita']);
$anno = $_POST['Anno_Nascita'];
$usertype = $connessione->real_escape_string($_POST['Usertype']);
$inizio = "";
$fine = "";

/*Nel caso si voglia registrare un Utente Premium vengono controllate
* le date d'inizio e fine abbonamento:
* @Ok[TRUE]-> date corrette;
* @Ok[false]-> non corrette.
*
* Nel caso non siano corrette, l'utente viene reindirizzato alla pagina di SignUP
*/

if($usertype === "Premium"){
  $inizio = $connessione->real_escape_string($_POST['Data_Inizio_Abb']);
  $fine = $connessione->real_escape_string($_POST['Data_Fine_Abb']);
  $call = $connessione->prepare('CALL erCheckData(?, ?, @Ok)');
  $call->bind_param("ss", $inizio, $fine);
  $call->execute();

  $select = $connessione->query('SELECT @Ok');
  $result = $select->fetch_assoc();
  $dataOk = $result['@Ok'];
  if(!$dataOk){
    header('location: signUp.html');
    exit();
  }
}

#registrazione nella tabella Utente
$call = $connessione->prepare('CALL InsertUtente(?, ?, ?, ?, ?, @Ok)');
$call->bind_param("ssssi", $email, $nome, $cognome, $luogo, $anno);
$call->execute();

#se l'utente è amministratore lo aggiungo anche nella tabella Administrator
if($usertype === "Admin"){
  $admin_call = $connessione->prepare('CALL InsertAdmin(?)');
  $admin_call->bind_param("s", $email);
  try{
    $admin_call->execute();
  } catch(Exception $e){
    header("location: doublemail.html");
  }
}

#se l'utente è premium lo aggiungo alla tabella Premium
if($usertype === "Premium"){
  $costo = $_POST['Costo'];
  $p_call = $connessione->prepare('CALL InsertPremium(?, ?, ?, ?)');
  $p_call->bind_param("sssi", $email, $inizio, $fine, $costo);
  try{
    $p_call->execute();
  } catch(Exception $e){
    header("location: doublemail.html");
  }
}

/*
* ok controlla se l'email inserita è già presente nel Database
* ok = TRUE -> email nuova
* ok = FALSE -> email già presente
*
* Se Ok = TRUE, si procede anche con i Log su MongoDB
*/
$select = $connessione->query('SELECT @Ok');
$result = $select->fetch_assoc();
$ok = $result['@Ok'];

if(!$ok){
  header("location: doublemail.html");
}else{
  if($usertype === 'User'){
    #MongoDb Log Utente
    $bulk = new MongoDB\Driver\BulkWrite;
    $doc = [
      'Type' => 'Utente',
      'Email' => $email,
      'Nome' => $nome,
      'Cognome' => $cognome,
      'Luogo di Nascita' => $luogo,
      'Anno di Nascita' => $anno,
    ];
    $bulk->insert($doc);
    $result = $connection->executeBulkWrite('eForm.Log', $bulk);
  }else if($usertype === "Premium"){
    #MongoDb Log Premium
    $bulk = new MongoDB\Driver\BulkWrite;
    $doc = [
      'Type' => 'Premium',
      'Email' => $email,
      'Nome' => $nome,
      'Cognome' => $cognome,
      'Luogo di Nascita' => $luogo,
      'Anno di Nascita' => $anno,
      'Inizio Abbonamento' => $inizio,
      'Fine Abbonamento' => $fine,
      'Costo Abbonamento' => $costo,
    ];
    $bulk->insert($doc);
    $result = $connection->executeBulkWrite('eForm.Log', $bulk);
  }else if($usertype === 'Administrator'){
    #MongoDB Log Administrator
    $bulk = new MongoDB\Driver\BulkWrite;
    $doc = [
      'Type' => 'Administrator',
      'Email' => $email,
      'Nome' => $nome,
      'Cognome' => $cognome,
      'Luogo di Nascita' => $luogo,
      'Anno di Nascita' => $anno,
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
     <title>signed up</title>
     <link href="register.css" rel="stylesheet" type="text/css"/>
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
        echo " Nome: " . $nome . "<br>";
        echo " Cognome: " . $cognome . "<br>";
        echo " Luogo di Nascita: " . $luogo . "<br>";
        echo " Anno di Nascita: " . $anno . "<br>";
        if($usertype === "Admin"){
          echo "Usertype : Administrator";
        }else if($usertype === "User"){
          echo "Usertype : User";
        } else if($usertype === "Premium"){
          echo "Usertype : Premium";
          echo "<br>" . "Inizio Abbonamento: " . $inizio. "<br>";
          echo "Fine Abbonamento: " . $fine . "<br>";
          echo "Costo: " . $costo;
        }
      ?>
    </div>
   </body>
 </html>
