<?php
require_once('checklogin.php');
if($login){
    $post_token = $_POST['token'];
    if($post_token != $_COOKIE['CSRF_token_img']){
        echo "CSRF_token don't match";
        echo '<script>
                function prepage(){
                    window.location.href="chat.php"
                }
                </script>
                <button onclick="prepage()"> 上一頁 </button><br>';
        exit;
    }else{
        echo "file: ".str_replace("<","&lt",str_replace(">","&gt",str_replace("&","&amp;",$_FILES['img']['name'])))."<br>";

        if ($_FILES['img']['error'] === UPLOAD_ERR_OK){
            $path = './images/';
            $file = $_FILES['img']['tmp_name'];
            $extension = pathinfo($_FILES['img']['name'], PATHINFO_EXTENSION);
            $dis = $path. $_COOKIE['id'].".".$extension;
            if(preg_match('/^(jpg|jpeg|png|gif|bmp)$/i',$extension,$matches) && in_array($_FILES['img']['type'], ["image/png", "image/jpeg", "image/jpg", "image/gif", "image/bmp"]) ){
                move_uploaded_file($file, $dis);
    
                include('config.php');
                $stmt = $link->prepare("UPDATE users SET img=? WHERE id=? and password = ?");
                $stmt->bind_param("sis", $dis, $_COOKIE['id'],$_COOKIE['password']);
                
                if($stmt->execute()){
                    echo "上傳成功<br>";
                    echo "<img src='".str_replace("<","&lt",str_replace(">","&gt",str_replace("&","&amp;",$dis)))."' style='height: 300px; '>";
                }
                else{
                    echo "sql error";
                }
                mysqli_close($link);
            }
            else{
                echo "請傳送jpg,jpeg,png,gif,bmp<br>";
            }
            
        } 
        else {
            echo '上傳失敗' . $_FILES['img']['error'];
        }
    }
    
}
?>

<script>
function prepage(){
    window.location.href="uploadimg.php"
}

</script>

<button onclick="prepage()"> 上一頁 </button>
