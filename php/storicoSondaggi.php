<?php
require_once('config.php');
session_start();

$user = $_SESSION['user']['Email'];
?>

<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <title>storico</title>
  <link href="storicoSondaggi.css" rel="stylesheet" type="text/css"/>
  <meta name="viewport" content="width=device-width, initial-scale=1">
</head>
<body>
  <header>
    <p id ="home"><a href="./home.html">&#x2190 HOME</a></p>
    <p id='back' onclick='history.back()'>&#x2190 BACK</p>
  </header>
  <?php  echo "<p id='userLog'>Logged as: ".$user."</p>";  ?>
  <h2>STORICO SONDAGGI</h2>
  <?php
  $select = "SELECT Composto.Sondaggio, Risposta, Risposte.Domanda FROM Risposte INNER JOIN Composto ON Risposte.Domanda=Composto.Domanda WHERE Risposte.Utente='$user' ORDER BY Sondaggio ASC";
  if($result = $connessione->query($select)){
    if($result->num_rows >0){
      echo "<table id='sondaggiRisposti'>";
      echo "<tr><th>Codice Sondaggio</th>";
      echo "<th>Id Domanda</th>";
      echo "<th style='flew-wrap: wrap;'>Risposta</th>";
      $idSondaggio = 0;
      $className = "";
      $sameSondaggio;
      while($risposta = $result->fetch_array(MYSQLI_ASSOC)){
        if($risposta['Sondaggio'] != $idSondaggio){
          $sameSondaggio = $risposta['Sondaggio'];
          if($idSondaggio != 0){
            $className = "firstOne";
          }
        }else{
          $sameSondaggio = "";
        }
        echo "<tr><td class='".$className."'>".$sameSondaggio."</td>";
        echo "<td class='".$className."'>".$risposta['Domanda']."</td>";
        echo "<td class='".$className."'><small>".$risposta['Risposta']."</small></td>";
        echo "</tr>";
        $idSondaggio = $risposta['Sondaggio'];
        $className = "";
      }
      echo "</table>";
    }else{
      echo "<p style='color:red; margin-top:10%;'><big>Non hai ancora risposto ad alcun sondaggio</big></p>";
    }
  }
  ?>
</body>
</html>
