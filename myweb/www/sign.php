<?php
if( !isset($_POST['username']) || !isset($_POST['password']) || $_POST['username']=="" || $_POST['password']=="" ){
    header("Location: index.php");
}
$username = $_POST['username'];
$password = $_POST['password'];

require_once('config.php');
$stmt = $link->prepare("SELECT * FROM users WHERE username = ?");
$stmt->bind_param("s", $username);
$stmt->execute();
$result = $stmt->get_result();
try {
    $row = mysqli_fetch_array($result);   
    
    if($row ){
        echo '重複帳號';
        echo '<button onclick="goback()"> 上一頁 </button>';
    }else{
        $stmt = $link->prepare("INSERT INTO users (username, password) VALUES (?, ?)");
	    $stmt->bind_param("ss", $username, $password);
	    $stmt->execute();
	    $result = $stmt->get_result();
        $result2=mysqli_query($link,"SELECT @@IDENTITY;");
        header("Set-Cookie: username=".urlencode($username)."; HttpOnly; Secure; SameSite=strict", false);
        header("Set-Cookie: password=".urlencode($password)."; HttpOnly; Secure; SameSite=strict", false);
        $id = mysqli_fetch_array($result2);
        header("Set-Cookie: id=".urlencode($id[0])."; HttpOnly; Secure; SameSite=strict", false);
        echo '註冊成功';
        echo '<button onclick="nextpage()"> 下一步 </button>';
    }
    mysqli_close($link);
}
catch (Exception $e) {
    mysqli_close($link);
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
