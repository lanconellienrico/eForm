<?php
require_once('config.php');
session_start();

$user = $_SESSION['user']['Email'];
?>

<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <title>classifica</title>
  <link href="classifica.css" rel="stylesheet" type="text/css"/>
  <meta name="viewport" content="width=device-width, initial-scale=1">
</head>
<body>
  <header>
    <p id ="home"><a href="./home.html">&#x2190 HOME</a></p>
    <p id='back' onclick='history.back()'>&#x2190 BACK</p>&emsp;
  </header>
  <h2>CLASSIFICA</h2>
  <?php
  $select = "SELECT Email, Nome, Cognome, Totale_Bonus FROM Utente ORDER BY Totale_Bonus DESC";
  if($result = $connessione->query($select)){
    if($result->num_rows >0){
      echo "<table id='classifica'>";
      echo "<tr><th>POS</th>";
      echo "<th>Email</th>";
      echo "<th>Nome</th>";
      echo "<th>Cognome</th>";
      echo "<th>Totale Bonus</th></tr>";
      $i = 1;
      while($utente = $result->fetch_array(MYSQLI_ASSOC)){
        if($user===$utente['Email']){
          echo "<tr style='font-weight: bold; color: red;'>";
        }else{
          echo "<tr>";
        }
          echo "<td>".$i."</td>";
          echo "<td>".$utente['Email']."</td>";
          echo "<td>".$utente['Nome']."</td>";
          echo "<td>".$utente['Cognome']."</td>";
          echo "<td>".$utente['Totale_Bonus']."</td>";
          echo "</tr>";
          $i++;
      }
      echo "</table>";
    }
  }
   ?>
</body>
</html>
