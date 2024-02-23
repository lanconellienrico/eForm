<?php

require_once('config.php');
require_once('mongoDB.php');
session_start();

/* Si controlla sia stato selezionato almeno un sondaggio a cui collegare la domanda.
*  Altrimenti si torna alla pagina precedente.
*/
if($_SESSION['nSondaggi'] > 0){
  $atLeastOne = FALSE;
  for($i = 0; $i < $_SESSION['nSondaggi']; $i++){
    if(isset($_POST[$i])){
      $atLeastOne = TRUE;
    }
  }
  if(!$atLeastOne){
    header("location: insertQuestion.php");
    exit();
  }
}else{
  header("location: insertQuestion.php");
  exit();
}

$premium = null;
$company = null;
$creator = "";
if($_SESSION['human']){
  $premium = $_SESSION['user']['Email'];
  $creator = $premium;
} else{
  $company = $_SESSION['azienda']['Codice_Fiscale'];
  $creator = $company;
}

$testo = $connessione->real_escape_string($_POST['Testo']);
$punteggio = $_POST['Punteggio'];
$max_caratteri = $_POST['Max_Caratteri'];
$type = $_POST['Type'];
$nOpzioni;
$options = [];
$tipoDomanda = 1; #tipoDomanda(1) -> aperta; tipoDomanda(2) -> Chiusa

#Gestione Immagine
$showImage = false;
if(!empty($_FILES['Foto']['tmp_name'] ) ) {
  $foto = file_get_contents($_FILES['Foto']['tmp_name']);
  $image_binary = fread(fopen($_FILES['Foto']['tmp_name'], "r"),filesize($_FILES['Foto']['tmp_name']));
  $encoded_image =  base64_encode($image_binary);
  $showImage = true;
}

if($type === "Chiusa"){
  $tipoDomanda = 2;
  $max_caratteri = null;
  $nOpzioni = $_POST['nClosed'];
}

if($punteggio===''){
  $punteggio = 0;
}

#invio della Domanda
$call = $connessione->prepare('CALL InsertDomanda(?, ?, ?, ?, ?, ?, ?)');
$call->bind_param("sbiissi", $testo, $foto, $punteggio, $max_caratteri, $premium, $company, $tipoDomanda);
$call->send_long_data(1, $foto);
$call->execute();

#MongoDb Log Domanda (Aperta o Chiusa)
$domandatype = 'Domanda ' . $type;
$bulk = new MongoDB\Driver\BulkWrite;
if($type === 'Aperta'){
  $doc = [
    'Type' => $domandatype,
    'Testo' => $testo,
    'Punteggio' => $punteggio,
    'Max Caratteri' => $max_caratteri,
    'Creator' => $creator,
  ];
} else if($type === 'Chiusa'){
  $doc = [
    'Type' => $domandatype,
    'Testo' => $testo,
    'Punteggio' => $punteggio,
    'Creator' => $creator,
  ];
}
$bulk->insert($doc);
$result = $connection->executeBulkWrite('eForm.Log', $bulk);

#selezione dell'id della domanda appena inserita
$questionId = 0;
$selek = "SELECT Id FROM Domanda ORDER BY Id DESC LIMIT 1";
if($resk = $connessione->query($selek)){
  if($resk->num_rows == 1){
    $queryId = $resk->fetch_array(MYSQLI_ASSOC);
    $questionId = $queryId['Id'];
  }
}
/* Se la domanda inserita è Chiusa, vengono inserite anche le relative Opzioni di risposta
*  @OptionOk -> TRUE (l'opzione è stata regolarmente inserita)
*  @OptionOk -> FALSE (l'inserimento non è avvenuto con successo)
*/
if($type === "Chiusa"){
  if($questionId != 0){
    $call = $connessione->prepare('CALL InserisciOpzione(?, ?, @OptionOk)');
    for($i = 1; $i <= $nOpzioni; $i++){
      $optionName = 'option'.$i;
      $opzioneTesto = $_POST[$optionName];
      /*Controllo che nel Testo dell'Opzione non siano state inserite parentesi chiuse, che servono
      * per la lettura della risposta */
      $opzioneCheck = str_split($opzioneTesto, 1);
      $opzioneTesto = '';
      foreach($opzioneCheck as $opzioneChar){
        if($opzioneChar !== ')'){
          $opzioneTesto = $opzioneTesto.$opzioneChar;
        }
      }
      $call->bind_param("is", $questionId, $opzioneTesto);
      $call->execute();

      #se l'opzione è stata correttamente inserita, viene salvata su un array per poi essere stampata a schermo
      $select = $connessione->query('SELECT @OptionOk');
      $result = $select->fetch_assoc();
      $ok = $result['@OptionOk'];
      if($ok){
        $opz = $i.") ".$opzioneTesto;
        array_push($options, $opz);

        #MongoDb Log Opzione
        $bulk = new MongoDB\Driver\BulkWrite;
        $doc = [
          'Type' => 'Opzione',
          'Domanda' => $questionId,
          'Testo' => $opzioneTesto,
        ];
        $bulk->insert($doc);
        $result = $connection->executeBulkWrite('eForm.Log', $bulk);
      }
    }
  }
}

/*
* Si tenta di collegare ai sondaggi selezionati la domanda appena inserita.
* Pertanto se ne ricava l'id, considerando che sarà l'ultimo inserita (Id è auto_increment).
*
*case(0)->sondaggio non scelto,
*case(1)->sondaggio collegato con successo,
*case(2)->sondaggio non collegato.
*/

$case = [];
if($questionId != 0){
  for($i = 0; $i < $_SESSION['nSondaggi']; $i++){
    if(isset($_POST[$i])){
      $kol = $connessione->prepare('CALL CollegaSondaggio(?, ?, @Ok)');
      $kol->bind_param("ii",$_POST[$i], $questionId);
      $kol->execute();
      /*
      * Per ogni sondaggio selezionato nel FORM,
      * si è tentato di collegarci la domanda.
      * @Ok restituisce l'esito dell'operazione:
      * TRUE - eseguita con successo,
      * FALSE - non eseguita, errore sollevato.
      */
      $select = $connessione->query('SELECT @Ok');
      $result = $select->fetch_assoc();
      $ok = $result['@Ok'];
      if($ok){
        $case[$i] = 1;
      }else{
        $case[$i] = 2;
      }
    }else{
      $case[$i] = 0;
    }
  }
}



 ?>

 <!DOCTYPE html>
 <html>
   <head>
     <meta charset="utf-8">
     <title>placed</title>
     <meta name="viewport" content="width=device-width, initial-scale=1">
   </head>
   <body>
     <header>
       <p id ="home"><a href="./home.html">&#x2190 HOME</a></p>
       <p id='back' onclick='history.back()'>&#x2190 BACK</p>
     </header>
     <?php      echo "<p id='userLog'>Logged as: ".$creator."</p>";  ?>
     <h2>Domanda inserita con successo</h2>
     <div id="dati">
      <?php
        echo "<b>Tipo di domanda: </b>" . $type . "<br>";
        echo "<p id='corpo'><b>Corpo: </b>" . $testo . "</p>";
        if($type === "Aperta"){
          echo "<b>Max_caratteri: </b>" . $max_caratteri . "<br>";
        }
        echo "<b>Punteggio: </b>" . $punteggio . "<br>";
        for($i = 0; $i < $_SESSION['nSondaggi']; $i++){
          if($case[$i] != 0){
            if($case[$i] == 1){
                echo "Collegata con successo al Sondaggio ".$_POST[$i]."<br>";
              }else if($case[$i] == 2){
                echo "Non collegata al sondaggio ".$_POST[$i]."<br>";
              }
            }
        }
        if(sizeof($options)>=1){
          echo "Opzioni:";
          foreach ($options as $o) {
            echo "<br>".$o;
          }
        }
        echo "<br>" . "<b>Un lavoro di: </b>" . $creator . "<br>";
        if($showImage){
          echo "<img id='img' src='data:image/jpg;base64, " . $encoded_image . "' />";
        }
      ?>
    </div>
   </body>
   <style>
     html{
       visibility: hidden;
       font-family: sans-serif;
       text-align: center;
       padding-top: 7.5vh;
       padding-bottom: 100px;
       background-color: rgb(20, 20, 20);
     }
     *{
       color: white;
     }
     #img{
       margin: 5px;
       width: 50%;
       border-radius: 5px;
     }
     h2{
       visibility: visible;
     }
     div{
       visibility: visible;
     }
     #userLog{
       visibility: visible;
       color: red;
       position: fixed;
       margin-top: 0px;
     }
     #corpo{
       padding-left: 20%;
       padding-right: 20%;
       margin-top: 0px;
       margin-bottom: 0px;
       word-break: break-all;
     }
     #home a{
       text-decoration: none;
       color:white;
     }
     #home{
       visibility: visible;
       padding-top: 9px;
       height: 30px;
       width: 100px;
       border-radius: 3px;
       background-color: rgb(156, 81, 182);
       position: fixed;
       top:-5px;
     }
     #home:hover{
       background-color: rgb(140, 75, 170);
       box-shadow: inset -1px -1px 3px rgba(0,0,0,0.5);
       cursor: pointer;
     }
     #back{
       color: white;
       visibility: visible;
       padding-top: 9px;
       height: 30px;
       width: 100px;
       border-radius: 3px;
       background-color: rgb(86, 53, 218);
       position: fixed;
       top:-5px;
       left: 130px;
     }
     #back:hover{
       background-color: rgb(68,39,188);
       box-shadow: inset -1px -1px 3px rgba(0,0,0,0.5);
       cursor: pointer;
     }
   </style>
 </html>
