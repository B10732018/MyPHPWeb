<?php
$login=false;
if($_COOKIE['username'] && $_COOKIE['password'] && $_COOKIE['id']){
    if( !(!isset($_COOKIE['username']) || !isset($_COOKIE['password']) || $_COOKIE['username']=="" || $_COOKIE['password']=="" || !isset($_COOKIE['id']) || $_COOKIE['id']=="") ){
        $username = $_COOKIE['username'];
        $password = $_COOKIE['password'];
        $id = $_COOKIE['id'];

        include('config.php');
        $stmt = $link->prepare("SELECT * FROM users WHERE username = ?");
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $result = $stmt->get_result();
        mysqli_close($link);
        $row = mysqli_fetch_array($result);
        if($row['unlocktime']){
            $date=new DateTime();
            $second=strtotime($row['unlocktime']) - strtotime($date->format('Y-m-d H:i:s'));
            if( $second > 0 ){
                echo '請'.$second.'秒後再試<br>';
                $login=false;
                exit;
            }
        }

        include('config.php');
        $stmt = $link->prepare("SELECT * FROM users WHERE username = ? and password = ? and id = ?");
        $stmt->bind_param("ssi", $username, $password, $id);
        $stmt->execute();
        $result = $stmt->get_result();
        mysqli_close($link);
        try {
            $row = mysqli_fetch_array($result);   
            if(!$row ){
                $login=false;

                include('config.php');
                $stmt = $link->prepare("UPDATE users SET unlocktime = date_add(now(), interval 10 second) WHERE username = ?");
                $stmt->bind_param("s", $username);
                $stmt->execute();
                mysqli_close($link);

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
