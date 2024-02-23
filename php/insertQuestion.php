<?php
require_once("config.php");
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
  <title>new question</title>
  <link href="insertQuestion.css" rel="stylesheet" type="text/css"/>
  <script src="insertQ.js"></script>
  <meta name="viewport" content="width=device-width, initial-scale=1">
</head>
<body>
  <header>
    <p id ="home"><a href="./home.html">&#x2190 HOME</a></p>
    <p id='back' onclick='history.back()'>&#x2190 BACK</p>
  </header>
  <?php      echo "<p id='userLog'>Logged as: ".$user."</p>";  ?>
  <h2>Inserisci una nuova domanda</h2>
  <form action="./insertQ.php" method="post" enctype="multipart/form-data">
    <textarea name="Testo" id="field" cols="51" row="5" maxlength="255" required="required" placeholder="enter text here..."></textarea>
    <br>
    <div class="add">
      <label for="Foto">Allega un'immagine:&emsp;</label>
      <input type="file" id="foto" name="Foto" accept="image/png, image/jpeg, image/jpg">
    </div>
    <br>
    <div class="add">
      <label for="Max_Caratteri">Lunghezza limite della risposta:&emsp;</label>
        <input type="number" name="Max_Caratteri" id="maxChar" value="2550" min="1" max="5000"/>
    </div>
    <br>
    <div class="add">
      <label for="Punteggio">Punteggio:&emsp;</label>
        <input type="number" name="Punteggio" id="points" placeholder="1" min="0">
    </div>
    <br>
    <div class="add">
      <label for="Sondaggio">Sondaggio:&emsp;</label>

        <?php
        $ciSonoSondaggi = TRUE;
        $_SESSION['nSondaggi'] = 0;
        $select = "SELECT Codice, Titolo FROM Sondaggio WHERE Stato = 1";
        if($result = $connessione->query($select)){
          if($result->num_rows > 0){
            while($sondaggio = $result->fetch_array(MYSQLI_ASSOC)){
              $codiceSondaggio = $sondaggio['Codice'];
              echo "<br><input class='sondaggi' type='checkbox' name='".$_SESSION['nSondaggi']."' value='".$codiceSondaggio."' id='".$codiceSondaggio."'>";
              echo "<label for='".$codiceSondaggio."'>".$sondaggio['Titolo']."</label>";
              $_SESSION['nSondaggi'] = $_SESSION['nSondaggi'] + 1;
            }
          }else{
            $ciSonoSondaggi = FALSE;
            echo "nessun sondaggio disponibile";
          }
        }
         ?>
    </div>
    <br>
    <div class="add">
      <p style='margin-top: 0px;'>Tipo di domanda:&emsp;</p>
        <label for="user">Aperta</label>
        <input type="radio" id="user" name="Type" value="Aperta" checked onclick="show()"/>
        <label for="premium">Chiusa</label>
        <input type="radio" id="premium" name="Type" value="Chiusa" onclick="shut()"/>
    </div>
    <div id='opzione'>
      <br><label for='nClosed'>Numeri di opzioni: </label>
        <input type='number' id='nClosed' name='nClosed' min='1' max='9' step='1'/>
    </div>
    <?php
    if($ciSonoSondaggi){
      echo "<div><input id='submit' type='submit' value='INSERT'></div>";
    }else{
      echo "<br><p class='add'>Non è possibile inserire domande,<br> manca qualcosa...</p>";
    }
    ?>
  </form>
</body>
</html>
