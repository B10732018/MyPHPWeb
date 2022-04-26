<?php
require_once('checklogin.php');
if($login){
    $token = CSRFtokenGenerator();
    
    include('config.php');
    $stmt = $link->prepare("SELECT head FROM title");
    $stmt->execute();
    $result = $stmt->get_result();
    mysqli_close($link);
    $row = mysqli_fetch_array($result);
    echo "<h1 style='font-size:50px'>".str_replace("<","&lt",str_replace(">","&gt",str_replace("&","&amp;",$row['head'])))."</h1>";

    include('config.php');
    $stmt = $link->prepare("SELECT img,admin FROM users WHERE username = ? and password = ?");
    $stmt->bind_param("ss", $_COOKIE['username'], $_COOKIE['password']);
    $stmt->execute();
    $result = $stmt->get_result();
    mysqli_close($link);
    $row = mysqli_fetch_array($result);
    
    echo "<h1><img src='".str_replace("<","&lt",str_replace(">","&gt",str_replace("&","&amp;",$row['img'])))."' style='height: 50px; '>";

    echo ' Hi '.str_replace("<","&lt",str_replace(">","&gt",str_replace("&","&amp;",$_COOKIE['username'])));
    echo '<button onclick="uploadimg()"> 上傳圖片 </button>';
    if($row['admin']==1){
        echo '<button onclick="adminpage()"> 管理頁面 </button>';
    }
    echo '<button onclick="logout()"> 登出 </button></h1><br>';
    
    echo '
    <h4>留言</h4>
    <form method="POST" action="chat.php"  enctype="multipart/form-data">
        <textarea placeholder="上限三百字" name="text" rows="6" cols="40" maxlength="300" required></textarea>
        <input id="file" type="file" name="file">
        <input type="hidden" name="token" value="'.$token.'"/>
        <br>
        <button  type="submit">留言</button>
    </form>';
    echo '<br><br><button onclick="refresh()"> refresh </button></h1><br>';
    
    if( !(!isset($_POST['text']) || $_POST['text']=="") ){
        $post_token = $_POST['token'];
        if($post_token != $_COOKIE['CSRF_token']){
            echo "CSRF_token don't match"; 
            echo '<script>
            function logout(){
                window.location.href="logout.php"
            }
            
            function uploadimg(){
                window.location.href="uploadimg.php"
            }
            
            function refresh(){
                window.location.href="chat.php"
            }
            
            function adminpage(){
                window.location.href="admin.php"
            }
            </script>';
            exit;
        }
        else{
            
            $text = $_POST['text'];
            echo "code".$_FILES['file']['error']." ";
            if($_FILES['file']['error'] === UPLOAD_ERR_OK){
                $path = './files/';
                $file = $_FILES['file']['tmp_name'];
                $extension = pathinfo($_FILES['file']['name'], PATHINFO_EXTENSION);
                
                include('config.php');
                $stmt = $link->prepare("INSERT INTO posts (text, file, user_id) VALUES (?, ?, ?)");
	            $stmt->bind_param("ssi", $text,$extension,$_COOKIE['id']);
	            $stmt->execute();
                $result2=mysqli_query($link,"SELECT @@IDENTITY;");
                mysqli_close($link);
                
                if($stmt->affected_rows > 0){
                    if($result2){
                        $id = mysqli_fetch_array($result2);
                        $dis = $path.$id[0].".".$extension;
                        move_uploaded_file($file, $dis);

                        include('config.php');
                        $stmt = $link->prepare("UPDATE posts SET file=? WHERE id=?");
	                    $stmt->bind_param("si", $dis, $id[0]);
                        
                        if($stmt->execute()){
                            echo "留言with file成功<br>";
                        }
                        else{
                            echo "sql with filepath error";
                        }
                        mysqli_close($link);
                    }
                    else{
                        echo "sql with file error";
                    }
                    
                }
                else{
                    echo "sql error";
                }
            }else{
                include('config.php');
                $stmt = $link->prepare("INSERT INTO posts (text, user_id) VALUES (?, ?)");
	            $stmt->bind_param("si", $text, $_COOKIE['id']);
	            $stmt->execute();
                mysqli_close($link);

                if($stmt->affected_rows > 0){
                    echo "留言成功<br>";
                }
                else{
                    echo "sql error";
                }
            }
        }

    }
    

    include('config.php');
    $stmt = $link->prepare("SELECT id FROM posts");
	$stmt->execute();
	$result = $stmt->get_result();
    mysqli_close($link);
    $count=0;
    while ($row = mysqli_fetch_row($result)) {
        if(!is_numeric($row[0])){
            continue;
        }
        echo "
        <iframe src='https://demo.b10732018.works/post.php?id=".$row[0]."&token=$token'
        height='400px' width='600px'>    
        </iframe><br>";
        $count++;
        
    }
    if($count==0){
        echo "no post";
    }

    

}


function CSRFtokenGenerator($len = 16){
    $date=new DateTime();
    if(strtotime($date->format('Y-m-d H:i:s'))-$_COOKIE['CSRF_refresh_time']<0){
        return $_COOKIE['CSRF_token'];
    }

    $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $token = '';
    for($i=0;$i<$len;$i++){
        $token .= $characters[rand(0, strlen($characters) - 1)];
    }
    //echo $token."<br>";
    header("Set-Cookie: CSRF_token=".urlencode($token)."; HttpOnly; Secure; SameSite=strict", false);

    $date=new DateTime();
    $rdate = strtotime($date->format('Y-m-d H:i:s'))+60;
    header("Set-Cookie: CSRF_refresh_time=".urlencode($rdate)."; HttpOnly; Secure; SameSite=strict", false);

    return $token;
}


?>

<script>
function logout(){
    window.location.href="logout.php"
}

function uploadimg(){
    window.location.href="uploadimg.php"
}

function refresh(){
    window.location.href="chat.php"
}

function adminpage(){
    window.location.href="admin.php"
}
</script>
