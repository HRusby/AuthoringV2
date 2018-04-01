<?php
    session_start();
    ini_set("default_charset", 'utf-8');
    $questionID = $_GET['q'];
    echo $questionID;
    $db = new mysqli("localhost", "root", "topolor", "topolor");
    $db->set_charset("utf8");
    $deleteOptionsQuery = "DELETE FROM tpl_question_option WHERE question_id = ?;";
    $deleteOptionsStmt = $db->stmt_init();
    $deleteOptionsStmt->prepare($deleteOptionsQuery);
    $deleteOptionsStmt->bind_param("i", $questionID);
    $deleteOptionsStmt->execute();

    $DeleteQuestionQuery = "DELETE FROM tpl_question WHERE id = ?;";
    $DeleteQuestionStmt = $db->stmt_init();
    $DeleteQuestionStmt->prepare($DeleteQuestionQuery);
    $DeleteQuestionStmt->bind_param("i", $questionID);
    $DeleteQuestionStmt->execute();

    echo "DELETED QUESTION: ".$questionID;
?>
