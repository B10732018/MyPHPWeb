<?php
header("Location: index.php");
setcookie("username", "", time()-3600);
setcookie("password", "", time()-3600);
setcookie("id", "", time()-3600);

?>
