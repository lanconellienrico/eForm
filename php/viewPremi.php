<?php
require_once('config.php');
session_start();

$user = $_SESSION['user']['Email'];
?>

<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <title>premi</title>
  <link href="viewPremi.css" rel="stylesheet" type="text/css"/>
  <meta name="viewport" content="width=device-width, initial-scale=1">
</head>
<body>
  <header>
    <p id ="home"><a href="./home.html">&#x2190 HOME</a></p>
    <p id='back' onclick='history.back()'>&#x2190 BACK</p>&emsp;
  </header>
  <?php  echo "<p id='userLog'>Logged as: ".$user."</p>";  ?>
  <h2>I TUOI PREMI</h2>
  <?php
  $select = "SELECT Nome, Descrizione, Min_Punti, Foto, Administrator FROM Premio INNER JOIN Storico ON Premio.Nome=Storico.Premio WHERE Storico.Utente='$user' ORDER BY Min_Punti ASC";
  if($result = $connessione->query($select)){
    if($result->num_rows >0){
      echo "<table id='premiNoiosi'>";
      echo "<tr><th>Nome</th>";
      echo "<th style='flex-wrap: wrap;'>Descrizione</th>";
      echo "<th>Punti Richiesti</th>";
      echo "<th>Foto</th>";
      echo "<th>Creato da</th></tr>";
      while($premio = $result->fetch_array(MYSQLI_ASSOC)){
        echo "<tr><td>".$premio['Nome']."</td>";
        echo "<td><small>".$premio['Descrizione']."</small></td>";
        echo "<td>".$premio['Min_Punti']."</td>";
        echo "<td class='imgTd'><img class='imgBrutte' src='data:image/jpg;base64,".base64_encode($premio['Foto'])."'/></td>";
        echo "<td>".$premio['Administrator']."</td>";
        echo "</tr>";
      }
      echo "</table>";
    }else{
      echo "<p style='color:red; margin-top:10%;'><big>Ancora nessun premio, datti da fare!</big></p>";
    }
  }
  ?>
</body>
</html>
