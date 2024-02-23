<?php

require_once('config.php');
require_once('mongoDB.php');
session_start();

$premium = $_SESSION['user']['Email'];
$sondaggio = $connessione->real_escape_string($_POST['sondaggio']);

/* controllo che il sondaggio selezionato sia stato creato da un utente PREMIUM,
*  e che il suo stato sia 'Aperto', in caso negativo si ritorna alla pagina precedente.
*/
$execute = true;
$select = "SELECT Premium, Azienda FROM Sondaggio WHERE Codice='$sondaggio' AND Stato = 1";
if($result = $connessione->query($select)){
  if($result->num_rows == 1){
    $res = $result->fetch_array(MYSQLI_ASSOC);
    if(is_null($res['Premium'])){
      header('location: invito.php');
      $execute = false;
    }
  }
}

if($execute){
  $n = $_SESSION['i'];         #n possibili email di utenti da invitare
  $mails = [];                 #salva le email degli utenti a cui l'invito viene spedito con successo
  $error = [];                 #salva le email degli utenti il cui invito ha generato un errore

  for($i = 0; $i < $n; $i++){
    if(!empty($_POST["mail".$i])){
        $email = $_POST["mail".$i];
        $call = $connessione->prepare('CALL InvitaUtente(?, ?, ?, @Ok)');
        $call->bind_param("sss", $premium, $email, $sondaggio);
        $success = true;
        try{
          $call->execute();
        }catch(Exception $e){
          $success = false;
        }
        /*
        *Gestione Errore Condizioni Stored Procedure non rispettate -> $success :
        * - TRUE : procedure eseguita senza errori;
        * - FALSE : la procedure solleva errori.
        *
        *Gestione Errore Doppio Invito -> @Ok :
        * TRUE se l'invito è stato inserito correttamente;
        * FALSE se l'invito non viene inserito perché l'utente risulta già invitato allo stesso sondaggio.
        *
        * Se l'invito non è stato inserito o se ha generato un errore, salvo l'email in error[],
        * altrimenti (l'invito è stato correttamente inserito) aggiungo l'email in mails[].
        */
        $select = $connessione->query('SELECT @Ok');
        $result = $select->fetch_assoc();
        $ok = $result['@Ok'];
        if(!$ok || !$success){
          array_push($error, $email);
        } else{
          array_push($mails, $email);

          #MongoDb Log Invito
          $bulk = new MongoDB\Driver\BulkWrite;
          $doc = [
            'Type' => 'Invito',
            'Mittente - Premium' => $premium,
            'Destinatario - Utente' => $email,
            'Sondaggio' => $sondaggio,
          ];
          $bulk->insert($doc);
          $result = $connection->executeBulkWrite('eForm.Log', $bulk);
        }
      }
    }
}

 ?>

 <!DOCTYPE html>
 <html>
   <head>
     <meta charset="utf-8">
     <title>sended</title>
     <meta name="viewport" content="width=device-width, initial-scale=1">
   </head>
   <body>
     <header>
       <p id ="home"><a href="./home.html">&#x2190 HOME</a></p>
       <p id='back' onclick='history.back()'>&#x2190 BACK</p>
     </header>
     <?php
     echo "<p id='userLog'>Logged as: ".$premium."</p>";
     $num = count($mails);
     echo "<h2>Inviti spediti con successo: ".$num."</h2>";
      ?>
     <div id="dati">
      <?php
        echo "<b>Mandato da: </b>" . $premium . "<br>";
        #si vuole mostrare il titolo del sondaggio,
        #nel caso il codice corrisponda ad uno esistente
        $k = "SELECT Titolo FROM Sondaggio WHERE Codice = '$sondaggio'";
        if($rest = $connessione->query($k)){
          if($rest->num_rows >0){
            $infoSondaggio = $rest->fetch_array(MYSQLI_ASSOC);
            echo "<br><b>Per il sondaggio: </b>" . $sondaggio . ") " . $infoSondaggio['Titolo'] . "<br>";
          }else{
            echo "<br>sondaggio non trovato<br>";
          }
        }
        echo "<br><b>Utenti invitati con successo: </b><div id='success'>";
        foreach($mails as $m){
          echo "<br>" . $m;
        }
        echo "</div>";
        echo "<br><b>Utenti non invitati: </b><div id='failed'>";
        foreach($error as $er){
          echo "<br>" . $er;
        }
        echo "</div>";
      ?>
    </div>
   </body>
   <style>
     html{
       visibility: hidden;
       font-family: sans-serif;
       text-align: center;
       padding-top: 7.5vh;
       padding-bottom: 100px;
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
     #success{
       text-shadow: 1px 1px 3px green, -1px -1px 3px green, -1px 1px 3px green, 1px -1px 3px green;
     }
     #failed{
       text-shadow: 1px 1px 3px red, -1px -1px 3px red, -1px 1px 3px red, 1px -1px 3px red;
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
