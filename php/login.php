<?php

require_once('config.php');

$Email = $connessione->real_escape_string($_POST['Email']);
$usertype = $_POST['selectType'];
$userInfo =null;

$select = "SELECT * FROM Utente WHERE Email = '$Email'";
if($result = $connessione->query($select)){
  if($result->num_rows == 1){
    $userInfo = $result->fetch_array(MYSQLI_ASSOC);
  }else{
    header("location: login.html");
  }
}

if(!is_null($userInfo)){
  if($usertype === 'user'){
    session_start();
    $_SESSION['log'] = true;
    $_SESSION['human'] = true;
    $_SESSION['user'] = $userInfo;
    header("location: privateArea.php");
  }else if($usertype === 'premium'){
    $premium_select = " SELECT * FROM Utente INNER JOIN Premium ON Utente.Email = Premium.Utente WHERE Utente.Email = '$Email'";
    if($premium_result = $connessione->query($premium_select)){
      if($premium_result->num_rows == 1){
       $premium_row = $premium_result->fetch_array(MYSQLI_ASSOC);
       session_start();
       $_SESSION['log'] = true;
       $_SESSION['human'] = true;
       $_SESSION['user'] = $premium_row;
       $_SESSION['premium'] = true;
       header("location: premiumArea.php");
      }else{
       header("location: login.html");
      }
    }
  }else if($usertype === 'admin'){
    $select = "SELECT * FROM Utente INNER JOIN Administrator ON Utente.Email = Administrator.Utente WHERE Email = '$Email'";
    if($result = $connessione->query($select)){
      if($result->num_rows == 1){
        session_start();
        $_SESSION['log'] = true;
        $_SESSION['human'] = true;
        $_SESSION['user'] = $userInfo;
        header("location: adminArea.php");
      }else{
        header("location: login.html");
      }
    }
  }
}

?>
