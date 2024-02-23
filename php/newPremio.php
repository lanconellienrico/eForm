<?php
session_start();
$user = $_SESSION['user']['Email'];
 ?>

<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <title>new premio</title>
  <link href="newPremio.css" rel="stylesheet" type="text/css"/>
  <meta name="viewport" content="width=device-width, initial-scale=1">
</head>
<body>
  <header>
    <p id ="home"><a href="./home.html">&#x2190 HOME</a></p>
    <p id='back' onclick='history.back()'>&#x2190 BACK</p>
  </header>
  <?php      echo "<p id='userLog'>Logged as: ".$user."</p>";  ?>
  <h2>Crea un nuovo Premio</h2>
  <form action="newPremi.php" method="post" enctype="multipart/form-data">
    <div class="add">
      <label for="Nome">Nome:&emsp;
        <input type="text" name="Nome" id="nome" placeholder="premio" required="required" maxlength="32">
    </div>
    <br>
    <div class="add">
      <label for="Descrizione">Descrizione:&emsp;
        <textarea name="Descrizione" id="Descrizione" cols="51" row="5" maxlength="255" placeholder="description here..."></textarea>
    </div>
    <br>
    <div class="add">
      <label for="Min_Punti">Minimo Punti:&emsp;
        <input type="number" name="Min_Punti" id="Min_Punti" min="1" step="1" required="required">
    </div>
    <br>
    <div class="add">
      <label for="Foto">Allega un'immagine:&emsp;</label>
      <input type="file" id="foto" name="Foto" accept="image/jpg" required='required'>
    </div>
    <div>
      <input id="submit" type="submit" value="CRAFT">
    </div>
  </form>
</body>
</html>
