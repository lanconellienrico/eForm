<?php

require_once('config.php');

$Email = $connessione->real_escape_string($_POST['Email']);
$CF = $connessione->real_escape_string($_POST['CF']);

if($_SERVER["REQUEST_METHOD"] === "POST"){

  $sql_select = "SELECT * FROM Azienda WHERE Codice_Fiscale = '$CF'";
  if($result = $connessione->query($sql_select)){
      if($result->num_rows == 1){
        $row = $result->fetch_array(MYSQLI_ASSOC);
        session_start();
        if($row['Email'] == $Email){
          $_SESSION['log'] = true;
          $_SESSION['human'] = false;
          $_SESSION['azienda'] = $row;
          header("location: privateAreaAz.php");
        }else{
          header("location: nomailAz.html");
        }
      } else{
        header("location: nomailAz.html");
      }
    } else{
      $error = "Oooopss, a Login Error has occured ._.";
      echo $error;
  }
}
?>
