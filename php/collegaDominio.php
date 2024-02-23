<?php

require_once("config.php");
session_start();
$_SESSION['i'] = 0;
$user = $_SESSION['user']['Email'];

 ?>

<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <title>add domain</title>
  <script src='collegaDominio.js'></script>
  <link href="collegaDominio.css" rel="stylesheet" type="text/css"/>
  <meta name="viewport" content="width=device-width, initial-scale=1">
</head>
<body>
  <header>
    <p id ="home"><a href="./home.html">&#x2190 HOME</a></p>
    <p id='back' onclick='history.back()'>&#x2190 BACK</p>
  </header>
  <?php      echo "<p id='user'>Logged as: ".$user."</p>";  ?>
  <h2>Aggiungi nuovi interessi</h2>&emsp;
  <form action="./collegaDomini.php" method="post">
    <p class="add">Scegli tra questi: </p>
    <div id="domini">
    <?php
    $i = 0;
    $none = false;
    $user = $_SESSION['user']['Email'];

    $select = "SELECT Parola_Chiave FROM Dominio";
    if($result = $connessione->query($select)){
      if($result->num_rows > 0){
        while ($domain = $result->fetch_array(MYSQLI_ASSOC)){
          $dom = $domain['Parola_Chiave'];
          $sel = "SELECT COUNT(*) AS N FROM Interesse WHERE Utente = '$user' AND Dominio = '$dom'";
          $res = $connessione->query($sel);
          $n = mysqli_fetch_assoc($res);
          if($n['N'] == 0){
            echo "<input type='checkbox' id='domain".$i."' name='domain".$i."' value='".$dom."'>";
            echo "<label for='domain".$i."'>". $dom . "</label><br>";
            $i++;
          }
        }
      }
    }
    if($i==0){
      $none = true;
      echo "<p style='white-space: nowrap;'>Non ci sono nuovi interessi da aggiungere, torna più tardi.</p>";
    }
    echo "<p id='none' style='display: none;'>".$none."</p>";
    $_SESSION['i'] = $i;
   ?>
   </div>
    <div>
      <input id="submit" type="submit" value="ADD">
    </div>
  </form>
  <?php
  $select = "SELECT Dominio FROM Interesse WHERE Utente = '$user'";
  if($result = $connessione->query($select)){
    if($result->num_rows >0){
      echo "<br><div id='vecchiInteressi'><b>I tuoi interessi: </b>";
      while($interesse = $result->fetch_array(MYSQLI_ASSOC)){
        echo $interesse['Dominio']." - ";
      }
      echo "</div>";
    }
  }
   ?>
</body>
</html>
