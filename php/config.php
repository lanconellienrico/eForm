<?php

$connessione = new mysqli("127.0.0.1", "root", "", "eForm");

if($connessione === false){
  die("Connection Error: " . $connessione->connect_error);
}

/*echo "Connection established" . $connessione->host_info;*/
?>
