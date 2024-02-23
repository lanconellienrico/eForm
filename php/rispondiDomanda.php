<?php
require_once("config.php");
session_start();

$idDomanda = $_POST['domanda'];
$user = $_SESSION['user']['Email'];

$_SESSION['questionType'] = 'Open';
$_SESSION['idDomanda'] = $idDomanda;
#$_SESSION['point'];

$tipo;
$maxCaratteri;
$testo;
$img;
$autore;
 ?>

 <!DOCTYPE html>
 <html>
   <head>
     <meta charset="utf-8">
     <title>answer</title>
     <script src='rispondiDomanda.js'></script>
     <link href="rispondiDomanda.css" rel="stylesheet" type="text/css"/>
     <meta name="viewport" content="width=device-width, initial-scale=1">
   </head>
   <body>
     <header>
       <p id ="home"><a href="./home.html">&#x2190 HOME</a></p>
       <p id='back' onclick="history.back()">&#x2190 BACK</p>
     </header>
     <?php
       echo "<p id='user'>Logged as: ".$user."</p>";
       echo "<h2>Domanda ". $idDomanda."</h2>";
     ?>
     <form action="./rispondiDomand.php" method="post">
     <div id="domanda">
     <?php
       if(!empty($_POST["domanda"])){

         #Si ricava il tipo della domanda (Aperta o Chiusa), così da adeguare il display
         $selek = "SELECT Max_Caratteri FROM Domanda_Aperta WHERE Domanda = '$idDomanda'";
         if($resulk = $connessione->query($selek)){
           if($resulk->num_rows > 0){
             $domanda = $resulk->fetch_array(MYSQLI_ASSOC);
             $tipo = 'aperta';
             $maxCaratteri = $domanda['Max_Caratteri'];
           }else{
             $tipo = 'chiusa';
             $_SESSION['questionType'] = 'Closed';
           }
           echo "<p id='openClosed' style='display: none;'>".$tipo."</p>";
         }

         $select = "SELECT Testo, Premium, Azienda, Foto, Punteggio FROM Domanda WHERE Id='$idDomanda'";
         if($result = $connessione->query($select)){
           if($result->num_rows > 0){
             $domanda = $result->fetch_array(MYSQLI_ASSOC);

             #si ricavano e si mostrano testo, autore ed eventuale immagine
             $testo = $domanda['Testo'];
             if($domanda['Premium']!=null){
               $autore = $domanda['Premium'];
             }else{
               $autore = $domanda['Azienda'];
             }
             if($domanda['Foto']!=null){
               $img = $domanda['Foto'];
               echo "<img id='imgDomanda'src='data:image/jpg;base64,".base64_encode($domanda['Foto'])."'/>";
             }
             echo "<p id='testoDomanda'>".$testo."</p><br>";
             echo "<p id='autoreDomanda'><small>~ ".$autore."</small></p>";

             #si salva il punteggio della domanda
             $_SESSION['point'] = $domanda['Punteggio'];
           }
         }
      }
     ?>
     </div>
     <div id='domandaAperta'>
       <?php
       if($tipo==='aperta'){
         echo "<textarea id='risposta' name='risposta' maxlength='".$maxCaratteri."' cols='50' required='required' placeholder='insert answer here...'/></textarea>";
       }
       ?>
     </div>
     <div id='domandaChiusa'>
       <?php
       if($tipo==='chiusa'){
         $select = "SELECT Numero_Progressivo, Testo FROM Opzione WHERE Domanda = '$idDomanda'";
         if($result = $connessione->query($select)){
           if($result->num_rows > 0){
             while($opzione = $result->fetch_array(MYSQLI_ASSOC)){
               $nOption = $opzione['Numero_Progressivo'];
               $textOption = $opzione['Testo'];
               $opz = $nOption.")".$textOption;
               echo "<br><input type='checkbox' name='options[]' class='options' id='option".$nOption."' value='".$opz."'/>";
               echo "<label for='option".$nOption."'>".$opz."</label>";
             }
           }
         }
       }
       ?>
     </div>
     <div>
       <input id="submit" type="submit" value="SUBMIT">
     </div>
    </form>
   </body>
 </html>
