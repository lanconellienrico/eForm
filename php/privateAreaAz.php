<?php
require_once('config.php');
session_start();
if(!isset($_SESSION['log']) || $_SESSION['log'] !== true){
  header("location: login.html");
  exit;
}

 ?>

<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <title>Private Area</title>
  <link href="privateAreaAz.css" rel="stylesheet" type="text/css"/>
  <meta name="viewport" content="width=device-width, initial-scale=1">
</head>
<body>
  <header>
    <p id ="home"><a href="./home.html">&#x2190 HOME</a></p>
  </header>
  <div id="container">
    <div id="info" class="panel">
      <h2>Profilo Aziendale</h2>&emsp;
      <?php
      $cf = $_SESSION['azienda']['Codice_Fiscale'];
      $select = "SELECT * FROM Azienda WHERE Codice_Fiscale = '$cf'";
      if($result = $connessione->query($select)){
        if($result->num_rows > 0){
          $azienda = $result->fetch_array(MYSQLI_ASSOC);
          echo "<p class='info'>Codice Fiscale:&emsp; " . $azienda['Codice_Fiscale'] . "</p>";
          echo "<p class='info'>Email:&emsp; " . $azienda['Email'] . "</p>";
          echo "<p class='info'>Nome:&emsp; " . $azienda['Nome'] . "</p>";
          echo "<p class='info'>Sede:&emsp; " . $azienda['Sede'] . "</p>";
        }
      }
      ?>
    </div>
    <a id="insert_a" href="insertQuestion.php"><div class="panel" id="insert">
      <h2><small>Inserisci una <br>nuova domanda</small></h2>
    </div></a>
    <a href="creaSondaggio.php" id="sondaggio_a"><div class="panel" id="sondaggio">
      <h2><small>Crea un nuovo<br> Sondaggio</small></h2>
    </div></a>
    <a href='invitiAzienda.php' id="storico_a"><div class="panel" id="storico">
      <h2><small>Manda Inviti<br><small>(invio automatico)</small></small></h2>
    </div></a>
    <a href='viewStats.php' id="stats_a"><div class="panel" id="stats">
      <h2><small>Stastiche Aggregate sui sondaggi</small></h2>
    </div></a>
  </div>
</body>
</html>
