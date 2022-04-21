<?php
require_once('checklogin.php');
if($login){
    $id = $_GET['id'];
    $token;
    if(!isset($_GET['token']) || $_GET['token']==""){
        $token = CSRFtokenGenerator();
    }
    else{
        $token = $_GET['token'];
        if(!preg_match('/^([0-9]|[a-z])*$/i',$token,$matches)){
            $token='';
        }
    }


    require('config.php');
    $stmt = $link->prepare("SELECT * FROM posts WHERE id = ?");
	$stmt->bind_param("i", $id);
	$stmt->execute();
	$result = $stmt->get_result();
    mysqli_close($link);

    echo '<body>';
    try {
        echo '<span id="btn"></span>';

        $row = mysqli_fetch_array($result);   
        if($row && is_numeric($id)){
            show($row,$token);
        }else{
            echo '沒有這則貼文';
        }
    }
    catch (Exception $e) {
        echo 'Caught exception: ', str_replace("&","&amp;",str_replace(">","&gt",str_replace("<","&lt",$e->getMessage()))), '<br>';
        echo 'Check credentials in config file at: ', $Mysql_config_location, '\n';
    }
    echo '</body>';
}

function CSRFtokenGenerator($len = 16){
    $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $token = '';
    for($i=0;$i<$len;$i++){
        $token .= $characters[rand(0, strlen($characters) - 1)];
    }
    header("Set-Cookie: CSRF_token=".urlencode($token)."; HttpOnly; Secure; SameSite=strict", false);
    return $token;
}

function show($post,$token){
    include('config.php');
    $stmt = $link->prepare("SELECT * FROM users WHERE id = ?");
	$stmt->bind_param("i", $post['user_id']);
	$stmt->execute();
	$result = $stmt->get_result();
    mysqli_close($link);

    try {
        $row = mysqli_fetch_array($result);   
        
        if($row){
            echo '
            <script>
            function deletepost(){
                window.location.href="delete.php?id='.$post['id'].'&token='.$token.'"
            }
            function prepage(){
    
                if ( window.self === window.top ) {
                    window.location.href="chat.php"; 
                } 
                else {
                    top.location.href="post.php?id='.$post['id'].'";
                }
            }
            </script>';
            echo "<h1><img src='".str_replace("&","&amp;",str_replace(">","&gt",str_replace("<","&lt",$row['img'])))."' style='height: 50px; '>";
            echo str_replace("&","&amp;",str_replace(">","&gt",str_replace("<","&lt",$row['username'])))."<br>";
            if($post['user_id'] == $_COOKIE['id']){
                echo '<button onclick="deletepost()"> 刪除 </button></h1><br>';
            }
            else{
                echo "</h1>";
            }
            BBcode($post['text']);

            if($post['file']){
                echo '<br><a href="download.php?id='.$_GET['id'].'" target="_blank">附件</a>';
            }
        }else{
            echo 'sql error';
        }
    }
    catch (Exception $e) {
        echo 'Caught exception: ', str_replace("&","&amp;",str_replace(">","&gt",str_replace("<","&lt",$e->getMessage()))), '<br>';
        echo 'Check credentials in config file at: ', $Mysql_config_location, '\n';
    }
}

function BBcode($text){
    //echo $text;
    $doc = new DOMDocument('1.0', 'UTF-8');
    $node = $doc->createElement('div');
    $node->setAttribute('type', '1');
    //$arr = RecursiveBBcode($doc,'[color  =   #FF0000    ]紅字[b]fff[/b][/color][b]aaa[i]ccc[/i][/b][u][img]http://upload.wikimedia.org/wikipedia/commons/thumb/6/63/Wikipedia-logo.png/72px-Wikipedia-logo.png[/img]bbb[/u]ddd[b][u]eee[/u][/b]',0,"","");
    $arr = RecursiveBBcode($doc,$text,0,"","");
    $node2 = $arr[0];
    $node->appendChild($node2);
    $doc->appendChild($node);
    echo $doc->saveHTML();
}

function RecursiveBBcode($doc,$text,$start,$tag,$para){
    $node;
    if($tag==""){
        $node = $doc->createElement('div');
    }
    else if($tag=="img"){
        $node = $doc->createElement($tag);
        $k = strpos($text,'[/img]',$start);
        if($k){
            $node->setAttribute('src', substr($text,$start,$k-$start));
        }else{
            $node->setAttribute('src', substr($text,$start,strlen($text)-$start));
            $k = strlen($text);
        }
        return [$node,$k+6];
    }
    else if($tag=="color"){
        $node = $doc->createElement("span");
        $node->setAttribute('style', "color: ".$para.";");
    }
    else{
        $node = $doc->createElement($tag);
    }


    for($i=$start;$i<strlen($text);$i++){
        if($text[$i]=='['){
            //echo 'func: '.$text.' '.$start.' '.$tag.'<br>';
            if(strpos($text,'[/'.$tag.']',$i)===$i){
                //echo 'tag: [/'.$tag.']<br>';
                if($start < $i){
                    $node2 = $doc->createTextNode(substr($text,$start,$i-$start));
                    $node->appendChild($node2);
                    //echo "sub: ".substr($text,$start,$i-$start)."<br>";
                }
                return [$node,$i+strlen('[/'.$tag.']')];

            }else if(strpos($text,'[b]',$i)===$i){
                if($start < $i){
                    $node2 = $doc->createTextNode(substr($text,$start,$i-$start));
                    $node->appendChild($node2);
                }
                $arr = RecursiveBBcode($doc,$text,$i+3,"b","");
                $node->appendChild($arr[0]);
                $start = $arr[1];
                $i = $start-1;
            }
            else if(strpos($text,'[u]',$i)===$i){
                if($start < $i){
                    $node2 = $doc->createTextNode(substr($text,$start,$i-$start));
                    $node->appendChild($node2);
                }
                $arr = RecursiveBBcode($doc,$text,$i+3,"u","");
                $node->appendChild($arr[0]);
                $start = $arr[1];
                $i = $start-1;
            }
            else if(strpos($text,'[i]',$i)===$i){
                if($start < $i){
                    $node2 = $doc->createTextNode(substr($text,$start,$i-$start));
                    $node->appendChild($node2);
                }
                $arr = RecursiveBBcode($doc,$text,$i+3,"i","");
                $node->appendChild($arr[0]);
                $start = $arr[1];
                $i = $start-1;
            }
            else if(strpos($text,'[img]',$i)===$i){
                if($start < $i){
                    $node2 = $doc->createTextNode(substr($text,$start,$i-$start));
                    $node->appendChild($node2);
                }
                $arr = RecursiveBBcode($doc,$text,$i+5,"img","");
                $node->appendChild($arr[0]);
                $start = $arr[1];
                $i = $start-1;
            }
            else if(strpos($text,'[color',$i)===$i){
                $flag=false;
                $s;
                $e=0;
                for($s=$i+6;$s<strlen($text);$s++){
                    if($text[$s]==' '){
                        continue;
                    }
                    else if($text[$s]=='='){
                        $flag=true;
                        break;
                    }else if($text[$s]==']'){
                        $flag=false;
                        $para='';
                        $e=$s;
                        break;
                    }else{
                        $flag=false;
                        break;
                    }
                }
                if($flag){
                    for($s=$s+1;$s<strlen($text);$s++){
                        if($text[$s]==' '){
                            continue;
                        }else{
                            break;
                        }
                    }
                    if($text[$s]==']'){
                        $para='';
                        $e=$s;
                    }else{
                        for($e=$s;$e<strlen($text);$e++){
                            if($text[$e]==']'){
                                $para=substr($text,$s,$e-$s);
                                break;
                            }else if($text[$e]!=' '){
                                continue;
                            }else{
                                $para=substr($text,$s,$e-$s);
                                break;
                            }
                        }
                        if($e==strlen($text)){
                            continue;
                        }else if($text[$e]==' '){
                            for($e=$e;$e<strlen($text);$e++){
                                if($text[$e]==' '){
                                    continue;
                                }else{
                                    break;
                                }
                            }
                            if($text[$e]!=']'){
                                continue;
                            }
                        }
                    }
                }else if($e!=$s){
                    continue;
                }




                if($start < $i){
                    $node2 = $doc->createTextNode(substr($text,$start,$i-$start));
                    $node->appendChild($node2);
                }
                $arr = RecursiveBBcode($doc,$text,$e+1,"color",$para);
                $node->appendChild($arr[0]);
                $start = $arr[1];
                $i = $start-1;
            }

            
        }
    }

    if($start < strlen($text)){
        $node2 = $doc->createTextNode(substr($text,$start,strlen($text)-$start));
        $node->appendChild($node2);
    }
    return [$node,strlen($text)];


    /*$node = $doc->createElement('div');
    $node2 = $doc->createTextNode($text);
    $node->appendChild($node2);
    return $node;*/
}
?>


<script>
window.onload=showbutton;
function showbutton(){
    if ( window.self === window.top ){
        document.getElementById('btn').innerHTML='<button onclick="prepage()"> 上一頁 </button>';
    }
    else{
        document.getElementById('btn').innerHTML='<button onclick="prepage()"> 查看 </button>';
    }
}
</script>
