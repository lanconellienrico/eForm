<?php
require_once('config.php');
?>

<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <title>premi</title>
  <script src="premi.js"></script>
  <link href="premi.css" rel="stylesheet" type="text/css"/>
  <meta name="viewport" content="width=device-width, initial-scale=1">
</head>
<body>
  <header>
    <p id ="home"><a href="./home.html">&#x2190 HOME</a></p>
    <p id='back' onclick='history.back()'>&#x2190 BACK</p>&emsp;
  </header>
  <h2>PREMI DISPONIBILI</h2>
  <div id="optionView">View:
    <span class="view" id="boring" onclick="weep(0)">BORING</span>
    <span class="view" id="stunning" onclick="weep(1)">STUNNING</span>
  </div>
  <?php
  $count = 0;
  $select = "SELECT * FROM Premio ORDER BY Min_Punti ASC";
  if($result = $connessione->query($select)){
    if($result->num_rows >0){
      $premi = [];
      echo "<div id='premi'>";
      while($premio = $result->fetch_array(MYSQLI_ASSOC)){
        # vista immagini
        $d = "desc".$count;
        $t = "tit".$count;
        $p = "punti".$count;
        echo "<div class='premio' onmouseover='show(".$count.")' onmouseout='hide(".$count.")'>";
        echo "<img class='image'src='data:image/jpg;base64,".base64_encode($premio['Foto'])."'/>";
        echo "<p class='premioTitle' id='".$t."'>".$premio['Nome']."</p>";
        echo "<p class='punti' id='".$p."'>".$premio['Min_Punti']." PUNTI</p>";
        echo "<div class'descBox'><p class='description' id='".$d."'>".$premio['Descrizione']."<br>";
        echo "<br><small>".$premio['Administrator']."</small></p></div>";
        echo "</div>";
        $count++;
        array_push($premi, $premio);
      }
      echo "</div>";
    }
  }
  if($count != 0){
    echo "<table id='premiNoiosi'>";
    echo "<tr><th>Nome</th>";
    echo "<th style='flex-wrap: wrap;'>Descrizione</th>";
    echo "<th>Min_Punti</th>";
    echo "<th>Foto</th>";
    echo "<th>Administrator</th>";
    for($i = 0; $i<$count; $i++){
      echo "<tr><td>".$premi[$i]['Nome']."</td>";
      echo "<td><small>".$premi[$i]['Descrizione']."</small></td>";
      echo "<td>".$premi[$i]['Min_Punti']."</td>";
      echo "<td class='imgTd'><img class='imgBrutte' src='data:image/jpg;base64,".base64_encode($premi[$i]['Foto'])."'/></td>";
      echo "<td>".$premi[$i]['Administrator']."</td>";
      echo "</tr>";
    }
    echo "</table>";
  }else{
    echo "<p style='color:red;'><b>Nessun premio disponibile.</p>";
  }
   ?>
</body>
</html>
