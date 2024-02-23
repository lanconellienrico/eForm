<?php

require_once("config.php");
session_start();
$_SESSION['i'] = 0;
$_SESSION['codici'] = [];
$_SESSION['titoli'] = [];
$user = $_SESSION['user']['Email'];

 ?>

<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <title>request</title>
  <script src='gestioneInvito.js'></script>
  <link href="gestioneInvito.css" rel="stylesheet" type="text/css"/>
  <meta name="viewport" content="width=device-width, initial-scale=1">
</head>
<body>
  <header>
    <p id ="home"><a href="./home.html">&#x2190 HOME</a></p>
    <p id='back' onclick='history.back()'>&#x2190 BACK</p>
  </header>
  <?php      echo "<p id='userLog'>Logged as: ".$user."</p>";  ?>
  <h2>Gestisci gli Inviti ai Sondaggi</h2>&emsp;
  <form action="./gestioneInvit.php" method="post">
    <p class="add">Inviti in sospeso: </p>
    <table id="inviti">
      <tr id="titleTable">
        <th>Codice</th>
        <th>Titolo</th>
        <th>Mandato da</th>
        <th>Accetta</th>
        <th>Rifiuta</th>
      </tr>
    <?php
    $i = 0;
    $none = false;
    $select = "SELECT Cod, Sondaggio, Invito.Premium, Invito.Azienda, Titolo FROM Invito INNER JOIN Sondaggio ON Invito.Sondaggio=Sondaggio.Codice WHERE Utente = '$user' AND Esito=0 ";
    if($result = $connessione->query($select)){
      if($result->num_rows > 0){
        while ($invite = $result->fetch_array(MYSQLI_ASSOC)){
          echo "<tr>";
          echo "<td>". $invite['Sondaggio'] ."</td>";
          echo "<td>". $invite['Titolo'] ."</td>";
          if(!empty($invite['Premium'])){
            echo "<td>". $invite['Premium'] ."</td>";
          }else{
            echo "<td>". $invite['Azienda'] . "</td>";
          }
          echo "<td class='radioButton'><input type='radio' id='invite".$i."' name='invite".$i."' value='Acc' class='A'></td>";
          echo "<td class='radioButton'><input type='radio' id='invite".$i."' name='invite".$i."' value='Ref' class='R'></td>";
          $i++;
          array_push($_SESSION['codici'], $invite['Cod']);
          array_push($_SESSION['titoli'], $invite['Titolo']);
        }
      }
    }
    if($i==0){
      $none = true;
      echo "<p style='white-space: nowrap;'>Non ci sono nuovi inviti a cui rispondere.</p>";
      echo "<script type='text/javascript'>document.getElementById('inviti').style.visibility='hidden';</script>";
    }
    echo "<p id='none' style='display: none;'>".$none."</p>";
    $_SESSION['i'] = $i;
   ?>
 </table>
    <div>
      <input id="submit" type="submit" value="SUBMIT">
    </div>
  </form>
</body>
</html>
