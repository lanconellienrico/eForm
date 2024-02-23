<?php
session_start();
$user = $_SESSION['user']['Email'];
 ?>

<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <title>new domain</title>
  <link href="newDomain.css" rel="stylesheet" type="text/css"/>
  <meta name="viewport" content="width=device-width, initial-scale=1">
</head>
<body>
  <header>
    <p id ="home"><a href="./home.html">&#x2190 HOME</a></p>
    <p id='back' onclick='history.back()'>&#x2190 BACK</p>
  </header>
  <?php      echo "<p id='userLog'>Logged as: ".$user."</p>";  ?>
  <h2>Crea un nuovo Dominio</h2>
  <form action="./newDomai.php" method="post">
    <div class="add">
      <label for="Parola_Chiave">Parola Chiave:&emsp;
        <input type="text" name="Parola_Chiave" id="Parola_Chiave" placeholder="il dono della sintesi" required="required" maxlength="32">
    </div>
    <br>
    <div class="add">
      <label for="Descrizione">Descrizione:&emsp;
        <textarea name="Descrizione" id="Descrizione" cols="51" row="5" maxlength="255" placeholder="description here..."></textarea>
    </div>
    <div>
      <input id="submit" type="submit" value="CRAFT">
    </div>
  </form>
</body>
</html>
