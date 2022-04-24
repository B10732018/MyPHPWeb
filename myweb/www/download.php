<?php 
require_once('checklogin.php');
if($login){
    $id = $_GET['id'];

    require('config.php');
    $stmt = $link->prepare("SELECT * FROM posts WHERE id = ?");
	$stmt->bind_param("i", $id);
	$stmt->execute();
	$result = $stmt->get_result();
    mysqli_close($link);

    try {
        $row = mysqli_fetch_array($result);   
        if($row && is_numeric($id)){
            downloadfile($row);
        }else{
            echo '沒有這則貼文';
        }
    }
    catch (Exception $e) {
        echo 'Caught exception: ', str_replace("<","&lt",str_replace(">","&gt",str_replace("&","&amp;",$e->getMessage()))), '<br>';
        echo 'Check credentials in config file at: ', $Mysql_config_location, '\n';
    }
}

function downloadfile($post){
    if($post['file']){
        $extension = pathinfo($post['file'], PATHINFO_EXTENSION);
        $saveasname = $post['id'].".".$extension;
        header('Content-Type: application/octet-stream');
        header('Content-Disposition: attachment; Filename="'.$saveasname.'"');
        readfile($post['file']);
    }
    else{
        echo '沒有這個檔案';
    }
}


?>
