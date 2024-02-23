<?php

require_once("config.php");
session_start();

$azienda = $_SESSION['azienda']['Codice_Fiscale'];

 ?>

<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <title>invite</title>
  <link href="invitiAzienda.css" rel="stylesheet" type="text/css"/>
  <meta name="viewport" content="width=device-width, initial-scale=1">
</head>
<body>
  <header>
    <p id ="home"><a href="./home.html">&#x2190 HOME</a></p>
    <p id='back' onclick='history.back()'>&#x2190 BACK</p>
  </header>
  <?php      echo "<p id='userLog'>Logged as: ".$azienda."</p>";  ?>
  <h2>Invita utenti ad un sondaggio</h2>&emsp;
  <form action="./invitiAziend.php" method="post">
    <br>
    <p style='color:white;'>Seleziona sondaggio: </p>
    <?php
      $zero = false;
      $selek = "SELECT Codice, Titolo, Dominio FROM Sondaggio WHERE Premium IS NULL AND Stato=1";
      echo "<div id='sondaggi'>";
      if($sondagg = $connessione->query($selek)){
        if($sondagg->num_rows > 0){
          while($sondd = $sondagg->fetch_array(MYSQLI_ASSOC)){
            echo "<input type='radio' class='radio' name='sondaggi' value='".$sondd['Codice']." ".$sondd['Dominio']."' id='".$sondd['Codice']."'>";
            echo "<label for='".$sondd['Codice']."'>".$sondd['Titolo']."</label><br>";
            }
          }else{
            echo "non ci sono sondaggi disponibili";
            $zero = true;
          }
        }
        echo "</div>";
        if(!$zero){
          echo "<div><input id='submit' type='submit' value='SELECT'></div>";
        }
      ?>
  </form>
</body>
</html>
