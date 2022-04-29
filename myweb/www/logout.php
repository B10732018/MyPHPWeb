<?php
$token = $_GET['token'];

if($token != $_COOKIE['CSRF_token']){
    echo "CSRF_token don't match";
    echo '<script>
            function prepage(){
                window.location.href="chat.php"
            }
            </script>
            <button onclick="prepage()"> 上一頁 </button><br>';
    exit;
}

header("Location: index.php");
setcookie("username", "", time()-3600);
setcookie("password", "", time()-3600);
setcookie("id", "", time()-3600);
setcookie("CSRF_token", "", time()-3600);
setcookie("CSRF_refresh_time", "", time()-3600);

?>
