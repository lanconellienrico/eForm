<?php
require_once("config.php");
session_start();

$user = $_SESSION['user']['Email'];
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
     <title>view ans</title>
     <link href="viewRispost.css" rel="stylesheet" type="text/css"/>
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
     ?>
     <?php
     $select = "SELECT Risposta, Utente, Composto.Domanda FROM Risposte INNER JOIN Composto ON Risposte.Domanda=Composto.Domanda WHERE Composto.Sondaggio='$idSondaggio' ORDER BY Domanda ASC";
     if($result = $connessione->query($select)){
       if($result->num_rows >0){
         echo "<table id='risposte'>";
         echo "<tr><th>Id Domanda</th>";
         echo "<th>Risposta</th>";
         echo "<th>Utente</th>";
         $idDomanda = 0;
         $className = "ans";
         $sameDomanda;
         $dclass='';
         while($risposta = $result->fetch_array(MYSQLI_ASSOC)){
           if($risposta['Domanda'] != $idDomanda){
             $sameDomanda = $risposta['Domanda'];
             if($idDomanda != 0){
               $className = "firstOne";
               $dclass = $className;
             }
           }else{
             $sameDomanda = "";
             $dclass = "";
           }
           echo "<tr><td class='".$dclass."'>".$sameDomanda."</td>";
           echo "<td style='flex-wrap:wrap;' class='".$className."'><small>".$risposta['Risposta']."</small></td>";
           if(!is_null($premium)){
             echo "<td class='".$className."'>".$risposta['Utente']."</td>";
           }else{
             echo "<td class='".$className."'>anonimo</td>";
           }
           echo "</tr>";
           $idDomanda = $risposta['Domanda'];
           $className = "ans";
         }
         echo "</table>";
       }else{
         echo "<p style='color:red; margin-top:10%;'><big>Ancora nessuna risposta</big></p>";
       }
     }
     ?>
   </body>
 </html>
