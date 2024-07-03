<?php
    $host = 'host';
    $username = 'nazwauzytkownika';
    $password = 'haslouzytkownika';
    $database = 'bazadanych';

    $conn=mysqli_connect($host, $username, $password, $database);
      if(!$conn){
          die('Could not Connect MySql Server:' .mysql_error());
        }
?>