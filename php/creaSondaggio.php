<?php
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
  <title>new form</title>
  <link href="creaSondaggio.css" rel="stylesheet" type="text/css"/>
  <meta name="viewport" content="width=device-width, initial-scale=1">
</head>
<body>
  <header>
    <p id ="home"><a href="./home.html">&#x2190 HOME</a></p>
    <p id='back' onclick='history.back()'>&#x2190 BACK</p>
  </header>
  <?php      echo "<p id='user'>Logged as: ".$user."</p>";  ?>
  <h2>Crea un nuovo Sondaggio</h2>
  <form action="./creaSondaggi.php" method="post">
    <div class="add">
      <label for="Titolo">Titolo:&emsp;
        <input type="text" name="Titolo" id="Titolo" placeholder="title here" required="required" maxlength="64">
    </div>
    <br>
    <div class="add">
      <label for="Dominio">Dominio:&emsp;
        <input type="text" name="Dominio" id="Dominio" placeholder="please, insert a valid domain" required="required" maxlength="32">
    </div>
    <br>
    <div class="add">
      <label for="Data_Creazione">Data Creazione:&emsp;
        <input type="date" name="Data_Creazione" id="Data_Creazione" required="required">
    </div>
    <br>
    <div class="add">
      <label for="Data_Chiusura">Data Chiusura:&emsp;
        <input type="date" name="Data_Chiusura" id="Data_Chiusura" required="required">
    </div>
    <br>
    <div class="add">
      <label for="Max_Utenti">Limite massimo utenti:&emsp;
        <input type="number" name="Max_Utenti" id="Max_Utenti" required="required" min="1">
    </div>
    <br>
    <div>
      <input id="submit" type="submit" value="INSERT">
    </div>
  </form>
</body>
</html>
