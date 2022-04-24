<?php
define('DB_SERVER', 'db');
define('DB_USERNAME', 'userB10732018');
define('DB_PASSWORD', 'PnStDB10u732s018t');
define('DB_NAME', 'myDb');
 
$link = mysqli_connect(DB_SERVER, DB_USERNAME, DB_PASSWORD, DB_NAME);
 
if($link === false){
    die("ERROR: Could not connect. " . mysqli_connect_error());
}
?>
