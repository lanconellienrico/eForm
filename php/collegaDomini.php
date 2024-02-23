<?php

require_once('config.php');
require_once('mongoDB.php');
session_start();

$user = $_SESSION['user']['Email'];
$n = $_SESSION['i'];                 #n interessi da aggiungere
$domains = [];                       #salva i nuovi interessi aggiunti per stamparli
$error = [];                         #salva gli interessi che hanno generato un errore e non sono stati aggiunti

for($i = 0; $i < $n; $i++){
  if(!empty($_POST["domain".$i])){
      $domain = $_POST["domain".$i];
      $call = $connessione->prepare('CALL CollegaDominio(?, ?, @Ok)');
      $call->bind_param("ss", $user, $domain);
      $call->execute();
      /*
      * @Ok restituisce:
      * - FALSE : Se l'interesse da aggiungere ha generato un errore;
      * - TRUE : l'interesse è stato aggiunto correttamente.
      */
      $select = $connessione->query('SELECT @Ok');
      $result = $select->fetch_assoc();
      $ok = $result['@Ok'];
      if(!$ok){
        array_push($error, $domain);
      } else{
        array_push($domains, $domain);

        #MongoDb Log Interesse
        $bulk = new MongoDB\Driver\BulkWrite;
        $doc = [
          'Type' => 'Interesse',
          'Utente' => $user,
          'Dominio' => $domain,
        ];
        $bulk->insert($doc);
        $result = $connection->executeBulkWrite('eForm.Log', $bulk);
      }
  }
}
 ?>

 <!DOCTYPE html>
 <html>
   <head>
     <meta charset="utf-8">
     <title>added</title>
     <meta name="viewport" content="width=device-width, initial-scale=1">
   </head>
   <body>
     <header>
       <p id ="home"><a href="./home.html">&#x2190 HOME</a></p>
       <p id='back' onclick='history.back()'>&#x2190 BACK</p>
     </header>
     <?php
     echo "<p id='user'>Logged as: ".$user."</p>";
     $num = count($domains);
     echo "<h2>Interessi aggiunti con successo: ".$num."</h2>";
      ?>
     <div id="dati">
      <?php
        echo "<b>Profilo Utente: </b>" . $user . "<br>";
        echo "<br><b>Interessi aggiunti: </b><div id='success'>";
        foreach($domains as $d){
          echo "<br>" . $d;
        }
        echo "</div>";
        if(count($error)!=0){
          echo "<br><b>Interessi non aggiunti: </b><div id='failed'>";
          foreach($error as $er){
            echo "<br>" . $er;
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
     #user{
       visibility: visible;
       color: red;
       position: fixed;
       margin-top: 0px;
     }
     #success{
       text-shadow: 1px 1px 3px green, -1px -1px 3px green, -1px 1px 3px green, 1px -1px 3px green;
     }
     #failed{
       text-shadow: 1px 1px 3px red, -1px -1px 3px red, -1px 1px 3px red, 1px -1px 3px red;
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
