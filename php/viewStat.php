<?php
require_once("config.php");
session_start();

$user;
if($_SESSION['human']){
  $user = $_SESSION['user']['Email'];
} else{
  $user = $_SESSION['azienda']['Codice_Fiscale'];
}

$idSondaggio = $_POST['sondaggio'];

$title = "";
$premium = null;
$azienda = null;

#seleziona il titolo e l'autore( PREMIUM o AZIENDA) del sondaggio
$select = "SELECT Titolo, Premium, Azienda FROM Sondaggio WHERE '$idSondaggio'=Codice";
if($result = $connessione->query($select)){
  if($result->num_rows == 1){
    $r = $result->fetch_array(MYSQLI_ASSOC);
    $title = $r['Titolo'];
    if(is_null($r['Premium'])){
      $azienda = $r['Azienda'];
    }else{
      $premium = $r['Premium'];
    }
    $author = $azienda.$premium;
  }
}
 ?>

 <!DOCTYPE html>
 <html>
   <head>
     <meta charset="utf-8">
     <title>view stats</title>
     <link href="viewStat.css" rel="stylesheet" type="text/css"/>
     <meta name="viewport" content="width=device-width, initial-scale=1">
   </head>
   <body>
     <header>
       <p id ="home"><a href="./home.html">&#x2190 HOME</a></p>
       <p id='back' onclick='history.back()'>&#x2190 BACK</p>
     </header>
     <?php
      echo "<p id='user'>Logged as: ".$user."</p>";
      echo "<h2>Sondaggio ". $idSondaggio." <br><br> ".$title."</h2>";
      echo "<p id='author'>Creato da: ".$author."</p>";

      $arrayDomanda = [];
      $select = "SELECT Domanda FROM Composto WHERE Sondaggio='$idSondaggio'";
      if($result = $connessione->query($select)){
        if($result->num_rows >0){
          while($d = $result->fetch_array(MYSQLI_ASSOC)){
            array_push($arrayDomanda, $d['Domanda']);
          }
          foreach($arrayDomanda as $idDomanda){
            $type = 2;
            $Max_Caratteri;
            $testo;
            #si ricavano il testo e il tipo della domanda [type(1)-> Aperta; type(2)-> Chiusa]
            $se = "SELECT Testo FROM Domanda WHERE Id = '$idDomanda'";
            if($re = $connessione->query($se)){
              if($re->num_rows > 0){
                $do = $re->fetch_array(MYSQLI_ASSOC);
                $testo = $do['Testo'];
              }
            }
            $s = "SELECT Max_Caratteri FROM Domanda_Aperta WHERE Domanda = '$idDomanda'";
            if($r = $connessione->query($s)){
              if($r->num_rows > 0){
                $m = $r->fetch_array(MYSQLI_ASSOC);
                $type = 1;
                $Max_Caratteri = $m['Max_Caratteri'];
              }
            }

            echo "<p class='questionTitle'>Domanda".$idDomanda." - <small>".$testo."</small></p>";
            echo "<div class='infoRisposte'>";
            #Display statistiche per le risposte ad una domanda aperta
            if($type == 1){
              $maxCaratteri = 0;
              $minCaratteri = $Max_Caratteri;
              $medCaratteri = 0;
              $sum = 0; #somma totale dei caratteri delle risposte
              $n = 0; #numero di risposte
              $selek = "SELECT Risposta FROM Risposte WHERE Domanda = '$idDomanda'";
              if($resk = $connessione->query($selek)){
                if($resk->num_rows > 0){
                  while($risposta = $resk->fetch_array(MYSQLI_ASSOC)){
                    $ansLength = strlen($risposta['Risposta']);
                    if($maxCaratteri < $ansLength){
                      $maxCaratteri = $ansLength;
                    }
                    if($minCaratteri > $ansLength){
                      $minCaratteri = $ansLength;
                    }
                    $sum = $sum + $ansLength;
                    $n = $n + 1;
                  }
                }
              }
              echo "<b>Numero di risposte:</b> ".$n. "<br>";
              if($n > 0){
                echo "<b>Massimo caratteri:</b> ".$maxCaratteri." <br>";
                echo "<b>Minimo caratteri:</b> ".$minCaratteri." <br>";
                $medCaratteri = $sum / $n;
                $medCaratteri = round($medCaratteri, 2);
                echo "<b>Media caratteri:</b> " .$medCaratteri;
              }
            }
            #Display statistiche per le risposte ad una domanda chiusa
            else if($type ==2){

              $n = 0; #totale delle risposte
              $nOpzioni = 0; #numero delle opzioni per la domanda
              $info = []; #info raccoglie il testo di ogni opzione

              $sk = "SELECT Testo FROM Opzione WHERE Domanda = '$idDomanda'";
              if($rk = $connessione->query($sk)){
                if($rk->num_rows > 0){
                  while($t = $rk->fetch_array(MYSQLI_ASSOC)){
                    array_push($info, $t['Testo']);
                    $nOpzioni = $nOpzioni + 1;
                  }
                }
              }

              $risposte = [$nOpzioni]; #raccoglie le n risposte per ogni opzione, ogni valore viene inizializzato a 0
              for($i = 0; $i < $nOpzioni; $i++){
                $risposte[$i] = 0;
              }
              $selek = "SELECT Risposta FROM Risposte WHERE Domanda = '$idDomanda'";
              if($resk = $connessione->query($selek)){
                if($resk->num_rows > 0){
                  while($risposta = $resk->fetch_array(MYSQLI_ASSOC)){
                    /*
                    * Risposta = 1)testo opzione 1) 2)testo opzione 2)...
                    *ans:
                    * [0]: 1
                    * [1]: testo opzione 1
                    * [2]: 2
                    * [3]: testo opzione 2
                    * ...
                    */
                    $ans = explode(")", $risposta['Risposta']);
                    for($i = 0; $i < count($ans); $i=$i+2){
                      $index = $i / 2; #0,1,2,...
                      for($number = 1; $number <= $nOpzioni; $number++){
                        if($ans[$i] == $number){
                        $position = $number - 1;
                        $risposte[$position] = $risposte[$position] + 1;
                        }
                      }
                    }
                    $n = $n + 1;
                  }
                  echo "<b>Numero di risposte:</b> ".$n. "<br>";
                  for($i = 0; $i < count($risposte); $i++){
                    $perc = $risposte[$i]*100 / $n;
                    $percentuale = round($perc, 2)."%";
                    $opt = $i + 1;
                    echo $opt. ") " .$info[$i]. " : " .$percentuale. "<br>";
                  }
                }else{
                  echo "<b>Nessuna risposta al momento</b>";
                }
              }



            }
            echo "</div>";
          }
        }else{
          echo "<p style='color:white;'>Non ci sono domande.</p>";
        }
      }
     ?>
   </body>
 </html>
