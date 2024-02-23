<?php

require_once('config.php');
require_once('mongoDB.php');
session_start();

$title = $connessione->real_escape_string($_POST['Nome']);
$description = $connessione->real_escape_string($_POST['Descrizione']);
$min = $_POST['Min_Punti'];
$admin = $_SESSION['user']['Email'];

$showImage = false;
if(!empty($_FILES['Foto']['tmp_name'] ) ) {
  $foto = file_get_contents($_FILES['Foto']['tmp_name']);
  $image_binary = fread(fopen($_FILES['Foto']['tmp_name'], "r"),filesize($_FILES['Foto']['tmp_name']));
  $encoded_image =  base64_encode($image_binary);
  $showImage = true;
}

$call = $connessione->prepare('CALL InsertPremio(?, ?, ?, ?, ?, @Ok)');
$call->bind_param("ssibs", $title, $description, $min, $foto, $admin);
$call->send_long_data(3, $foto);
$call->execute();

/*
* ok controlla se il nome del premio inserito è già presente nel Database
* ok = TRUE -> nome nuovo
* ok = FALSE -> nome già presente
*/
$select = $connessione->query('SELECT @Ok');
$result = $select->fetch_assoc();
$ok = $result['@Ok'];

if(!$ok){
  header("location: error.html");
}else{
  #MongoDb Log Premio
  $bulk = new MongoDB\Driver\BulkWrite;
  $doc = [
    'Type' => 'Premio',
    'Titolo' => $title,
    'Descrizione' => $description,
    'Minimo Punti' => $min,
    'Creatore' => $admin,
  ];
  $bulk->insert($doc);
  $result = $connection->executeBulkWrite('eForm.Log', $bulk);
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
     <?php      echo "<p id='userLog'>Logged as: ".$admin."</p>";  ?>
     <h2>Nuovo Premio inserito con successo</h2>&emsp;
     <div id="dati">
      <?php
        echo "<b>Nome: </b>" . $title;
        echo "<p id='testo'><b>Descrizione: </b>" . $description . "</p>";
        echo "<b>Minimo Punti: </b>" . $min . "<br>";
        echo "<b>Un lavoro di: </b>" . $admin . "<br>";
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
       padding-bottom: 200px;
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
     #testo{
       margin-left: 20%;
       margin-right: 20%;
       word-break: break-all;
       margin-top: 0px;
       margin-bottom: 0px;
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
     #img{
       margin-top: 5px;
       width: 50%;
       border-radius: 5px;
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
