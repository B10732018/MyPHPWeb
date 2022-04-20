<?php
if( !isset($_POST['username']) || !isset($_POST['password']) || $_POST['username']=="" || $_POST['password']=="" ){
    header("Location: index.php");
}
$username = $_POST['username'];
$password = $_POST['password'];

require_once('config.php');
$stmt = $link->prepare("SELECT * FROM users WHERE username = ? and password = ?");
$stmt->bind_param("ss", $username, $password);
$stmt->execute();
$result = $stmt->get_result();
mysqli_close($link);
try {
    $row = mysqli_fetch_array($result);   
    
    if($row ){
        //setcookie("username","$username");
        header("Set-Cookie: username=".urlencode($username)."; HttpOnly; Secure; SameSite=strict", false);
        header("Set-Cookie: password=".urlencode($password)."; HttpOnly; Secure; SameSite=strict", false);
        header("Set-Cookie: id=".urlencode($row['id'])."; HttpOnly; Secure; SameSite=strict", false);
        //setcookie("password","$password");
        //setcookie("id",$row['id']);
        echo '登入成功';
        echo '<button onclick="nextpage()"> 下一步 </button>';
    }else{
        echo '登入失敗';
        echo '<button onclick="goback()"> 上一頁 </button>';
    }
}
catch (Exception $e) {
    echo 'Caught exception: ', str_replace(">","&gt",str_replace("<","&lt",$e->getMessage())), '<br>';
    echo 'Check credentials in config file at: ', $Mysql_config_location, '\n';
}

?>

<script>
function nextpage(){
    window.location.href="chat.php"
}
function goback(){
    window.location.href="index.php"
}
</script>
