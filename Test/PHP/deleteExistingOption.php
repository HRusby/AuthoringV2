<?php
    session_start();
    ini_set("default_charset", 'utf-8');
    // $questionID = $_GET['q'];
    $optionID = $_GET['o'];
    echo $optionID;
    $db = new mysqli("localhost", "root", "topolor", "topolor");
    $db->set_charset("utf8");
    $deleteOptionQuery = "DELETE FROM tpl_question_option WHERE id = ?;";
    $deleteOptionStmt = $db->stmt_init();
    $deleteOptionStmt->prepare($deleteOptionQuery);
    $deleteOptionStmt->bind_param("i", $optionID);
    $deleteOptionStmt->execute();

    echo "DELETED Option: ".$optionID;
?>
