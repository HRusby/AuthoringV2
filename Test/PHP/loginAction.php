<?php
    session_start();
    ini_set("default_charset", 'utf-8');
    $db = new mysqli("localhost", "root", "topolor", "topolor");
    $username = $_POST["username"];
    $password = $_POST["md5Password"];
    $query = "SELECT * FROM tpl_user WHERE username = \"".$username."\" AND password = \"".$password."\";";
    $Result = $db->query($query);
    while($row = $Result->fetch_assoc()){
        $_SESSION['user_id'] = $row['id'];
    }
    echo "session check user id = ".$_SESSION['user_id'];
    $db->close();
    header("location: ../Pages/LessonOverview2.php"); 
?>
