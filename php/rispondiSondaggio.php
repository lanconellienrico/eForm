<?php

require_once("config.php");
session_start();

$user = $_SESSION['user']['Email'];
 ?>

<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <title>answer</title>
  <script src='rispondiSondaggio.js'></script>
  <link href="rispondiSondaggio.css" rel="stylesheet" type="text/css"/>
  <meta name="viewport" content="width=device-width, initial-scale=1">
</head>
<body>
  <header>
    <p id ="home"><a href="./home.html">&#x2190 HOME</a></p>
    <p id='back' onclick="history.back()">&#x2190 BACK</p>
  </header>
  <?php      echo "<p id='user'>Logged as: ".$user."</p>";  ?>
  <h2>Rispondi ad un Sondaggio</h2>
  <form action="./rispondiSondaggi.php" method="post">
    <p class="add">Seleziona il sondaggio: </p>
    <div id="sondaggi">
    <?php
    $none = false;
    $select = "SELECT Codice,Titolo FROM Sondaggio INNER JOIN Invito ON Sondaggio.Codice=Invito.Sondaggio WHERE Invito.Utente='$user' AND Invito.Esito='Accettato'";
    if($result = $connessione->query($select)){
      if($result->num_rows > 0){
        while ($sondaggio = $result->fetch_array(MYSQLI_ASSOC)){
          echo "<input type='radio' id='sondaggio' name='sondaggio' required='required' value='".$sondaggio['Codice']."'>";
          echo "<label for='sondaggio'>". $sondaggio['Titolo'] . "</label><br>";
        }
      }else{
        echo "Non ci sono sondaggi disponibili al momento";
        $none = true;
      }
      echo "<p id='none' style='display: none;'>".$none."</p>";
    }
     ?>
   </div>
    <br>
    <div>
      <input id="submit" type="submit" value="LET'S GO">
    </div>
  </form>
</body>
</html>
