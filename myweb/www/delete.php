<?php
require_once('checklogin.php');
if($login){
    $id = $_GET['id'];
    $token = $_GET['token'];

    if( ($token != $_COOKIE['CSRF_token_del']) && $token != $_COOKIE['CSRF_token_chat'] ){
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
        require('config.php');
        $stmt = $link->prepare("SELECT * FROM posts WHERE id = ?");
	    $stmt->bind_param("i", $id);
	    $stmt->execute();
	    $result = $stmt->get_result();
        mysqli_close($link);

        try {
            $row = mysqli_fetch_array($result);   
            
            if($row && is_numeric($id)){
                deletepost($row);
            }else{
                echo '沒有這則貼文';
            }
        }
        catch (Exception $e) {
            echo 'Caught exception: ', str_replace("<","&lt",str_replace(">","&gt",str_replace("&","&amp;",$e->getMessage()))), '<br>';
            echo 'Check credentials in config file at: ', $Mysql_config_location, '\n';
        }
    }

}

function deletepost($post){
    include('config.php');
    $stmt = $link->prepare("SELECT * FROM users WHERE id = ?");
	$stmt->bind_param("i", $post['user_id']);
	$stmt->execute();
	$result = $stmt->get_result();
    mysqli_close($link);

    try {
        $row = mysqli_fetch_array($result);   
        
        if($row){
            if($row['id']==$_COOKIE['id']){
                include('config.php');
                $stmt = $link->prepare("DELETE FROM posts WHERE id = ?");
	            $stmt->bind_param("i", $post['id']);
	            $stmt->execute();
                mysqli_close($link);

                echo " <script   language = 'javascript' 
                type = 'text/javascript'> "; 
                echo " top.location.href = 'chat.php' "; 
                echo " </script > "; 
            }
            else{
                echo 'no permission';
            }
        }else{
            echo 'sql error';
        }
    }
    catch (Exception $e) {
        echo 'Caught exception: ', str_replace("<","&lt",str_replace(">","&gt",str_replace("&","&amp;",$e->getMessage()))), '<br>';
        echo 'Check credentials in config file at: ', $Mysql_config_location, '\n';
    }
}
?>
