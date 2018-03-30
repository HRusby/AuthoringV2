<?php
    $moduleID = strval($_GET['v']);
    ini_set("default_charset", 'utf-8');
    $db = new mysqli("localhost", "root", "topolor", "topolor");
    $Result = $db->query("SELECT topic_id FROM tpl_tutorial_topic WHERE root=".$moduleID.";");
    while($row = $Result->fetch_assoc()){
        $Result2 = $db->query("SELECT title FROM tpl_post WHERE id = ".$row["topic_id"].";");
        while($row2=$Result2->fetch_assoc()){
            echo "<option value=".$row2["title"]." data-value=".$row["topic_id"].">";
        }
//         echo "<option value=".$row["title"].">";
    }
    $db->close();
?>