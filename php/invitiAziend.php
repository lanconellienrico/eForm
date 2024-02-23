<?php
require_once('config.php');
session_start();

$azienda = $_SESSION['azienda']['Codice_Fiscale'];

$sondaggio = $_POST['sondaggi'];
$s = preg_split("~\s+~",$sondaggio);
$idSondaggio = $s[0];
$domSondaggio = $s[1];

$_SESSION['codiceSondaggio'] = $idSondaggio;
?>

<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
 <title>invite</title>
 <link href="invitiAziend.css" rel="stylesheet" type="text/css"/>
 <meta name="viewport" content="width=device-width, initial-scale=1">
</head>
<body>
 <header>
   <p id ="home"><a href="./home.html">&#x2190 HOME</a></p>
   <p id='back' onclick='history.back()'>&#x2190 BACK</p>
 </header>
 <?php      echo "<p id='userLog'>Logged as: ".$azienda."</p>";  ?>
 <h2>Invita utenti ad un sondaggio</h2>&emsp;
 <form action="./invitiAz.php" method="post">
   <br>
   <?php
     $_SESSION['mails'] = [];
     echo "<div id='nUtenti'>";
     $select = "SELECT Email FROM Utente INNER JOIN Interesse ON Utente.Email=Interesse.Utente WHERE Interesse.Dominio='$domSondaggio' AND Utente.Email NOT IN "."("."SELECT Utente FROM Invito WHERE Sondaggio='$idSondaggio'".")";
     if($result = $connessione->query($select)){
       if($result->num_rows > 0){
         echo "<p style='color:white;'>Quanti utenti vuoi invitare: </p>";
         while($mail = $result->fetch_array(MYSQLI_ASSOC)){
           array_push($_SESSION['mails'], $mail['Email']);
         }
         echo "<input type='number' name='nInviti' value='1' min='1' max='".count($_SESSION['mails'])."' required='required'>";
       }else{
         echo "<span style='color:white;'>Non ci sono utenti da invitare.</span>";
       }
     }
     echo "</div>";
     if(count($_SESSION['mails']) != 0){
       echo "<div><input id='submit' type='submit' value='SEND'></div>";
     }
     ?>
 </form>
</body>
</html>
