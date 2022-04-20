<?php
echo '<br> B10732018<br>';
echo htmlspecialchars(date('G:i'));

if($_COOKIE['username'] && $_COOKIE['password']){
    if( !(!isset($_COOKIE['username']) || !isset($_COOKIE['password']) || $_COOKIE['username']=="" || $_COOKIE['password']=="" || !isset($_COOKIE['id']) || $_COOKIE['id']=="") ){
        $username = $_COOKIE['username'];
        $password = $_COOKIE['password'];
        $id = $_COOKIE['id'];

        require_once('config.php');
        $stmt = $link->prepare("SELECT * FROM users WHERE username = ? and password = ? and id = ?");
	    $stmt->bind_param("ssi", $username, $password, $id);
	    $stmt->execute();
	    $result = $stmt->get_result();
        mysqli_close($link);
        try {
            $row = mysqli_fetch_array($result);   
    
            if($row ){
                $url  =  "chat.php" ; 
                echo " <script   language = 'javascript' 
                type = 'text/javascript'> "; 
                echo " window.location.href = '$url' "; 
                echo " </script > "; 
            }
        }
        catch (Exception $e) {
            echo 'Caught exception: ', str_replace(">","&gt",str_replace("<","&lt",$e->getMessage())), '<br>';
            echo 'Check credentials in config file at: ', $Mysql_config_location, '\n';
        }
    }
}

?>

<form method="POST" action="login.php">

    <input id="username" placeholder="Username" required="" autofocus="" type="text" name="username">
    <input id="password" placeholder="Password" required="" type="password" name="password">
    <button  type="submit">登入</button>
</form>

<form method="POST" action="sign.php">

    <input id="username" placeholder="Username" required="" autofocus="" type="text" name="username">
    <input id="password" placeholder="Password" required="" type="password" name="password">
    <button  type="submit">註冊</button>
</form>
