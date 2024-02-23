<?php

require_once('config.php');
session_start();
$user = $_SESSION['user']['Email'];

$n = $_SESSION['i'];                 #n inviti da controllare
$cod = $_SESSION['codici'];          #codici degli inviti
$tit = $_SESSION['titoli'];          #titoli dei sondaggi degli inviti
$accepted = [];                      #salva i codici degli inviti accettati
$refused =[];                        #salva i codici degli inviti rifiutati
$error = [];                         #salva i codici degli inviti la cui risposta ha sollevato un errore e non è stata aggiornata

for($i = 0; $i < $n; $i++){
  if(!empty($_POST["invite".$i])){
      $invite = $connessione->real_escape_string($_POST["invite".$i]);
      $call = $connessione->prepare('CALL RispondiInvito(?, ?, @Ok)');
      $esito;           #Invito.Esito-> (1: 'Accettato', 2: 'Rifiutato')
      if($invite=='Acc'){
        $esito = 1;
      }else if($invite=='Ref'){
        $esito = 2;
      }
      $call->bind_param("ii", $cod[$i], $esito);
      $call->execute();
      /*
      * @Ok restituisce:
      * - FALSE : Se la risposta all'invito ha sollevato un errore;
      * - TRUE : l'esito dell'invito è stato correttamente aggiornato.
      */
      $select = $connessione->query('SELECT @Ok');
      $result = $select->fetch_assoc();
      $ok = $result['@Ok'];
      if($ok){
        if($esito==1){
          array_push($accepted, "(".$cod[$i].") --" . $tit[$i]."--");
        }else if($esito==2){
          array_push($refused, "(".$cod[$i].") --" . $tit[$i]."--");
        }
      } else{
        array_push($error, "(".$cod[$i].") --" . $tit[$i]."--");
      }
  }
}
 ?>

 <!DOCTYPE html>
 <html>
   <head>
     <meta charset="utf-8">
     <title>invite</title>
     <meta name="viewport" content="width=device-width, initial-scale=1">
   </head>
   <body>
     <header>
       <p id ="home"><a href="./home.html">&#x2190 HOME</a></p>
       <p id='back' onclick='history.back()'>&#x2190 BACK</p>
     </header>
     <?php
      echo "<p id='userLog'>Logged as: ".$user."</p>";
      echo "<h2>Hai risposto ai seguenti inviti:</h2>";
     ?>
     <div id="dati">
      <?php
        echo "<b>Profilo Utente: </b>" . $user . "<br>";
        #print accepted invites
        echo "<br><b>Inviti accettati: </b><div id='accepted'>";
        foreach($accepted as $acp){
          echo "<br>Invito" . $acp . " : accettato";
        }
        echo "</div>";
        #print refused invites
        echo "<br><b>Inviti rifiutati: </b><div id='refused'>";
        foreach($refused as $ref){
          echo "<br>Invito" . $ref . " : rifiutato";
        }
        echo "</div>";
        #print error invites, if
        if(count($error)!=0){
          echo "<br><b>Interessi non aggiunti: </b><div id='error'>";
          foreach($error as $er){
            echo "<br>Your reply to Invito" . $er . " has generated an error :/";
          }
        }
        echo "</div>";
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
     #accepted{
       text-shadow: 1px 1px 3px green, -1px -1px 3px green, -1px 1px 3px green, 1px -1px 3px green;
     }
     #refused{
       text-shadow: 1px 1px 3px red, -1px -1px 3px red, -1px 1px 3px red, 1px -1px 3px red;
     }
     #error{
       text-color: red;
       text-shadow: 1px 1px 3px yellow, -1px -1px 3px yellow, -1px 1px 3px yellow, 1px -1px 3px yellow;
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
