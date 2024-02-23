<?php

require_once("config.php");
session_start();
$_SESSION['i'] = 0;
$premium = $_SESSION['user']['Email'];

 ?>

<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <title>new invite</title>
  <script src="invito.js"></script>
  <link href="invito.css" rel="stylesheet" type="text/css"/>
  <meta name="viewport" content="width=device-width, initial-scale=1">
</head>
<body>
  <header>
    <p id ="home"><a href="./home.html">&#x2190 HOME</a></p>
    <p id='back' onclick='history.back()'>&#x2190 BACK</p>
  </header>
  <?php      echo "<p id='userLog'>Logged as: ".$premium."</p>";  ?>
  <h2>Invita un utente a partecipare ad un sondaggio</h2>&emsp;
  <form action="./invit.php" method="post">
    <p class="add">Invita gli utenti: </p>
    <div id="utenti">
    <?php
    $i = 0;
    $select = "SELECT Email FROM Utente WHERE Email != '$premium'";
    if($result = $connessione->query($select)){
      if($result->num_rows > 0){
        while ($mail = $result->fetch_array(MYSQLI_ASSOC)){
          echo "<input type='checkbox' id='mail".$i."' name='mail".$i."' value='".$mail['Email']."'>";
          echo "<label for='mail".$i."'>". $mail['Email'] . "</label><br>";
          $i++;
        }
      }
    }
    $_SESSION['i'] = $i;
     ?>
   </div>
    <br>
    <div class="add">
      <label for="sondaggio">Codice del sondaggio:</label>
        <input type="number" name="sondaggio" id="sondaggio" required="required" min="1" placeholder="1">
        <?php
        $selek = "SELECT Codice, Titolo, Azienda FROM Sondaggio WHERE Stato=1";
        if($sondagg = $connessione->query($selek)){
          if($sondagg->num_rows > 0){
            while($sondd = $sondagg->fetch_array(MYSQLI_ASSOC)){
              if(is_null($sondd['Azienda'])){
                echo "<label class='sondaggiGreen' for='sondaggio' id='".$sondd['Codice']."' style='display: none;'><small><b>". $sondd['Titolo'] ."</b></small></label>";
              }else{
                echo "<label class='sondaggiRed' for='sondaggio' id='".$sondd['Codice']."' style='display: none;'><small><b>". $sondd['Titolo'] ."</b></small></label>";
              }
            }
          }
        }
         ?>
    </div>
    <div>
      <input id="submit" type="submit" value="SEND">
    </div>
  </form>
</body>
</html>
