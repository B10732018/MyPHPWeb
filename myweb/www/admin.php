<?php
require_once('checklogin.php');
if($login){
    $token = CSRFtokenGenerator();

    include('config.php');
    $stmt = $link->prepare("SELECT admin FROM users WHERE username = ? and password = ?");
    $stmt->bind_param("ss", $_COOKIE['username'], $_COOKIE['password']);
    $stmt->execute();
    $result = $stmt->get_result();
    mysqli_close($link);
    $row = mysqli_fetch_array($result);
    

    if($row['admin']==1){
        if( !(!isset($_POST['title']) || $_POST['title']=="") ){
            $post_token = $_POST['token'];
            if($post_token != $_COOKIE['CSRF_token_admin']){
                echo "CSRF_token don't match"; 
                echo '<script>
                function prepage(){
                    window.location.href="chat.php"
                }
                </script>
                <button onclick="prepage()"> 上一頁 </button><br>';
                exit;
            }
            else{
                $title = $_POST['title'];

                include('config.php');
                $stmt = $link->prepare("UPDATE title SET head=?");
                $stmt->bind_param("s", $title);
                $stmt->execute();
                $result = $stmt->get_result();
                mysqli_close($link);
                $row = mysqli_fetch_array($result);
                echo '更改成功<br>';
            }
        }
    }
    else{
        echo "no permission"; 
        exit;
    }

    echo'  
    <button onclick="prepage()"> 上一頁 </button><br>
    <h4>更換主頁標題</h4>
    <form method="POST" action="admin.php">
       <input id="title" placeholder="title" required="" autofocus="" type="text" name="title">
       <input type="hidden" name="token" value="'.$token.'"/>
       <button  type="submit">更改</button>
    </form>
    ';
}

function CSRFtokenGenerator($len = 16){
    $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $token = '';
    for($i=0;$i<$len;$i++){
        $token .= $characters[rand(0, strlen($characters) - 1)];
    }
    header("Set-Cookie: CSRF_token_admin=".urlencode($token)."; HttpOnly; Secure; SameSite=strict", false);
    return $token;
}

?>


<script>
function prepage(){
    window.location.href="chat.php"
}
</script>
