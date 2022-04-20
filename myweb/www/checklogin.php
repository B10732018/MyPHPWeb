<?php
$login=false;
if($_COOKIE['username'] && $_COOKIE['password'] && $_COOKIE['id']){
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
            if(!$row ){
                $login=false;
                header("Location: index.php");
            }
            else{
                $login=true;
            }
        }
        catch (Exception $e) {
            $login=false;
            header("Location: index.php");
        }
    }
    else{
        $login=false;
        header("Location: index.php");
    }
}
else{
    $login=false;
    header("Location: index.php");
}

?>
