<?php
require_once('checklogin.php');
if($login){
    $token = CSRFtokenGenerator();
    echo '
    <button onclick="prepage()"> 上一頁 </button><br>
    <h4>透過url上傳</h4>
    <form method="POST" action="urlimg.php">
        <input id="urlupload" placeholder="URL" required="" autofocus="" type="text" name="url">
        <input type="hidden" name="token" value="'.$token.'"/>
        <button  type="submit">上傳</button>
    </form>

    <h4>透過file上傳</h4>
    <form method="POST" action="fileimg.php" enctype="multipart/form-data">
        <input id="file" required="" type="file" name="img">
        <input type="hidden" name="token" value="'.$token.'"/>
        <button  type="submit">上傳</button>
    </form>
    ';
}

function CSRFtokenGenerator($len = 16){
    $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $token = '';
    for($i=0;$i<$len;$i++){
        $token .= $characters[rand(0, strlen($characters) - 1)];
    }
    header("Set-Cookie: CSRF_token_img=".urlencode($token)."; HttpOnly; Secure; SameSite=strict", false);
    return $token;
}
?>

<script>
function prepage(){
    window.location.href="chat.php"
}
</script>

