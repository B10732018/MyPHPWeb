<?php
require_once('checklogin.php');
if($login){
    $post_token = $_POST['token'];
    if($post_token != $_COOKIE['CSRF_token']){
        echo " <script   language = 'javascript' 
        type = 'text/javascript'> "; 
        echo " top.location.href = 'chat.php' "; 
        echo " </script > "; 
        exit;
    }else{
        $url = $_POST['url'];

        $url = str_replace("<","%3c",$url);
        $url = str_replace(">","%3e",$url);
        $url = str_replace("&","%26",$url);
        $url = str_replace("#","%23",$url);
        $url = str_replace(";","%3b",$url);

        echo "url: ".$url."<br>";

        $extension = pathinfo($url, PATHINFO_EXTENSION);
        if(preg_match('/^(jpg|jpeg|png|gif|bmp)$/i',$extension,$matches)){
            if(strpos($url,'http',0)===0){
                download($url);
            }
            else{
                echo "請使用http或https";
            }
            
        }
        else{
            echo "請傳送jpg,jpeg,png,gif,bmp<br>";
        }

        
    }

}

function download($url, $path = './images/')
    {
        $ch=curl_init();
        $timeout=5; 
        curl_setopt($ch,CURLOPT_URL,$url);
        curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);
        curl_setopt($ch,CURLOPT_CONNECTTIMEOUT,$timeout);
        curl_setopt($ch,CURLOPT_USERAGENT,'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/100.0.4896.127 Safari/537.36');
    
        $img=curl_exec($ch);
        $httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
    
        if($httpcode=='200'){
            $extension = pathinfo($url, PATHINFO_EXTENSION);
            if(preg_match('/^(jpg|jpeg|png|gif|bmp)$/i',$extension,$matches)){
                $resource = fopen($path . $_COOKIE['id'].".".$extension, 'w');
                fwrite($resource, $img);
                fclose($resource);
                
                include('config.php');
                $dis = $path.$_COOKIE['id'].".".$extension;
                $stmt = $link->prepare("UPDATE users SET img=? WHERE id=? and password = ?");
	            $stmt->bind_param("sis", $dis, $_COOKIE['id'], $_COOKIE['password']);
                
                if($stmt->execute()){
                    echo "上傳成功<br>";
                    echo "<img src='".$path . $_COOKIE['id'].".".$extension."' style='height: 300px; '>";
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
        else{
            echo "error ".$httpcode;
        }
        
        
        
        
    }

?>

<script>
function prepage(){
    window.location.href="uploadimg.php"
}

</script>

<button onclick="prepage()"> 上一頁 </button>
