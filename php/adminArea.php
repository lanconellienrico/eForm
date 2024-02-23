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
  <link href="adminArea.css" rel="stylesheet" type="text/css"/>
  <meta name="viewport" content="width=device-width, initial-scale=1">
</head>
<body>
  <header>
    <p id ="home"><a href="./home.html">&#x2190 HOME</a></p>
  </header>
  <div id="container">
    <div id="info" class="panel">
      <h2>Utente Amministratore</h2>
      <?php
      $email = $_SESSION['user']['Email'];
      $select = "SELECT * FROM Utente WHERE Email = '$email'";
      if($result = $connessione->query($select)){
        if($result->num_rows > 0){
          $user = $result->fetch_array(MYSQLI_ASSOC);
          echo "<p class='info'>Email:&emsp; " . $user['Email'] . "</p>";
          echo "<p class='info'>Nome:&emsp; " . $user['Nome'] . "</p>";
          echo "<p class='info'>Cognome:&emsp; " . $user['Cognome'] . "</p>";
          echo "<p class='info'>Anno di Nascita:&emsp; " . $user['Anno_Nascita'] . "</p>";
          echo "<p class='info'>Luogo di Nascita:&emsp; " . $user['Luogo_Nascita'] . "</p>";
          echo "<p class='info'>Punti Bonus:&emsp; " . $user['Totale_Bonus'] . "</p>";
        }
      }
      ?>
    </div>
    <a href="collegaDominio.php" id="interesse_a"><div class="panel" id="interesse">
      <h2><small>Collega <br>Interessi</small></h2>
    </div></a>
    <a href='rispondiSondaggio.php' id="risposta_a"><div class="panel" id="risposta">
      <h2><small>Rispondi a un sondaggio</small></h2>
    </div></a>
    <a href="gestioneInvito.php" id="inviti_a"><div class="panel" id="inviti">
      <h2><small>Gestione Inviti</small></h2>
    </div></a>
    <a href='storicoSondaggi.php' id="storico_a"><div class="panel" id="storico">
      <h2><small>Storico Sondaggi</small></h2>
    </div></a>
    <a href='viewPremi.php' id="premi_a"><div class="panel" id="premi">
      <h2><small>Bacheca Premi</small></h2>
    </div></a>
    <a href="newPremio.php" id="inserp_a"><div class="panel" id="inserp">
      <h2><small>Crea Nuovo Premio</small></h2>
    </div></a>
    <a href="newDomain.php" id="domain_a"><div class="panel" id="domain">
      <h2><small>Crea Nuovo Dominio</small></h2>
    </div></a>
    <a href="premi.php" id="premio_a"><div class="panel" id="premio">
      <h2><small>PREMI DISPONIBILI</small></h2>
    </div></a>
    <a href='classifica.php' id="chart_a"><div class="panel" id="chart">
      <h2><small>CLASSIFICA</small></h2>
    </div></a>
  </div>
</body>
</html>
