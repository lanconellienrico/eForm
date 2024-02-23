<?php
require_once("config.php");
session_start();

$user = $_SESSION['user']['Email'];
$idSondaggio = $_POST['sondaggio'];
$_SESSION['nSondaggio'] = $idSondaggio;
$title = "";

#seleziona il titolo del sondaggio a cui si sta rispondendo
$select = "SELECT Titolo FROM Sondaggio WHERE '$idSondaggio'=Codice";
if($result = $connessione->query($select)){
  if($result->num_rows == 1){
    $r = $result->fetch_array(MYSQLI_ASSOC);
    $title = $r['Titolo'];
  }
}
 ?>

 <!DOCTYPE html>
 <html>
   <head>
     <meta charset="utf-8">
     <title>choose</title>
     <script src='rispondiSondaggi.js'></script>
     <link href="rispondiSondaggi.css" rel="stylesheet" type="text/css"/>
     <meta name="viewport" content="width=device-width, initial-scale=1">
   </head>
   <body>
     <header>
       <p id ="home"><a href="./home.html">&#x2190 HOME</a></p>
       <p id='back' onclick='history.back()'>&#x2190 BACK</p>
     </header>
     <?php
      echo "<p id='user'>Logged as: ".$user."</p>";
      echo "<h2>Sondaggio ". $idSondaggio." <br> ".$title."</h2>";
     ?>
     <form action="./rispondiDomanda.php" method="post">
     <p class="add">Seleziona domanda: </p>
     <div id="domande">
     <?php
       $none = false;
       $lackOfQuestion = true;
       if(!empty($_POST["sondaggio"])){
         $select = "SELECT Id, Testo FROM Domanda INNER JOIN Composto ON Domanda.Id = Composto.Domanda WHERE Composto.Sondaggio='$idSondaggio'";
         if($result = $connessione->query($select)){
           if($result->num_rows > 0){
             $lackOfQuestion = false;
             $domande = [];
             $domande['Id'] = [];
             $domande['Testo'] = [];
             while ($domanda = $result->fetch_array(MYSQLI_ASSOC)){
               array_push($domande['Id'], $domanda['Id']);
               array_push($domande['Testo'], $domanda['Testo']);
             }
           } else{
             $none = true;
             echo "Non ci sono domande a cui rispondere :(";
           }
         }
         #controllo che l'utente non abbia già risposto alla domanda
         #in caso affermativo( @notAnsYet = TRUE), la domanda viene mostrata
         #@Ok controlla che non ci siano stati errori durante il controllo
         if(!$lackOfQuestion){
          $nDomande = sizeof($domande['Id']);
          if($nDomande > 0){
            for($i = 0; $i < $nDomande; $i++){
                $call = $connessione->prepare('CALL ContaRisposte(?, ?, @notAnsYet, @Ok)');
                $call->bind_param("is", $domande['Id'][$i], $user);
                $call->execute();
                $select = $connessione->query('SELECT @Ok, @notAnsYet');
                $result = $select->fetch_assoc();
                if($result['@Ok']){
                  if($result['@notAnsYet']){
                    echo "<input type='radio' id='domanda' name='domanda' required='required' value='".$domande['Id'][$i]."'>";
                    echo "<label for='domanda'>". $domande['Testo'][$i] . "</label><br>";
                    $none = false;
                  }
                }
              }
            }
          }
      }
      echo "<p id='none' style='display: none;'>".$none."</p>";
     ?>
     </div>
     <br>
     <div>
       <input id="submit" type="submit" value="CHOOSE">
     </div>
    </form>
   </body>
 </html>
