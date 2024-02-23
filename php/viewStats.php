<?php

require_once("config.php");
session_start();

$user;
if($_SESSION['human']){
  $user = $_SESSION['user']['Email'];
} else{
  $user = $_SESSION['azienda']['Codice_Fiscale'];
}

 ?>

<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <title>view stats</title>
  <script src='rispondiSondaggio.js'></script>
  <link href="viewStats.css" rel="stylesheet" type="text/css"/>
  <meta name="viewport" content="width=device-width, initial-scale=1">
</head>
<body>
  <header>
    <p id ="home"><a href="./home.html">&#x2190 HOME</a></p>
    <p id='back' onclick="history.back()">&#x2190 BACK</p>
  </header>
  <?php      echo "<p id='user'>Logged as: ".$user."</p>";  ?>
  <h2>Visualizza Statistiche Aggregate</h2>
  <form action="./viewStat.php" method="post">
    <p class="add">Seleziona il sondaggio: </p>
    <div id="sondaggi">
    <?php
    $none = false;
    $select = "SELECT Codice,Titolo, Premium, Azienda FROM Sondaggio";
    if($result = $connessione->query($select)){
      if($result->num_rows > 0){
        while ($sondaggio = $result->fetch_array(MYSQLI_ASSOC)){
          echo "<input type='radio' id='sondaggio' name='sondaggio' required='required' value='".$sondaggio['Codice']."'>";
          echo "<label for='sondaggio'>". $sondaggio['Titolo'] . "</label><br>";
        }
      }else{
        echo "Non ci sono sondaggi";
        $none = true;
      }
      echo "<p id='none' style='display: none;'>".$none."</p>";
    }
     ?>
   </div>
    <br>
    <div>
      <input id="submit" type="submit" value="VIEW">
    </div>
  </form>
</body>
</html>
