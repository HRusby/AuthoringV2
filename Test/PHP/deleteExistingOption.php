<?php
    session_start();
    ini_set("default_charset", 'utf-8');
    // $questionID = $_GET['q'];
    $optionID = $_GET['o'];
    $db = new mysqli("localhost", "root", "topolor", "topolor");
    $db->set_charset("utf8");

    // Get all options for the question
    // If opt charcode is greater than opt for the deleting option, decrement, convert to charcode then update
    $findOptionQuery = "SELECT * FROM tpl_question_option WHERE id=?;";
    $findOptionStmt = $db->stmt_init();
    $findOptionStmt->prepare($findOptionQuery);
    $findOptionStmt->bind_param("i", $optionID);
    $findOptionStmt->execute();
    $currentOption = $findOptionStmt->get_result()->fetch_assoc();
    $currentOptionCharCode = ord($currentOption['opt']);
    // echo "Current charCode: "+$currentOptionCharCode;
    // echo "\nCurrent Question ID: ".$currentOption['question_id'];
    // echo "\nCurrent Option ID: ".$optionID;
    $getOptionsQuery = "SELECT * FROM tpl_question_option WHERE id != ? AND question_id = ?;";
    $getOptionsStmt = $db->stmt_init();
    $getOptionsStmt->prepare($getOptionsQuery);
    $getOptionsStmt->bind_param("ii", $optionID, $currentOption['question_id']);
    $getOptionsStmt->execute();
    $getOptionsResult = $getOptionsStmt->get_result();
    // echo "\nresult rows: ".$getOptionsResult->num_rows;
    while($option = $getOptionsResult->fetch_assoc()){
        // echo "Looping";
        $optionCharCode = ord($option['opt']);
        if($optionCharCode > $currentOptionCharCode){
          // Decrement charcode and update
          // echo "In if";
          $newCharCode = $optionCharCode-1;
          $newOpt = chr($newCharCode);
          // echo $newOpt;
          $updateOptQuery = "UPDATE tpl_question_option SET opt=? WHERE id=?;";
          $updateOptStmt = $db->stmt_init();
          $updateOptStmt->prepare($updateOptQuery);
          $updateOptStmt->bind_param("si", $newOpt, $option['id']);
          $updateOptStmt->execute();
        }
    }

    $deleteOptionQuery = "DELETE FROM tpl_question_option WHERE id = ?;";
    $deleteOptionStmt = $db->stmt_init();
    $deleteOptionStmt->prepare($deleteOptionQuery);
    $deleteOptionStmt->bind_param("i", $optionID);
    $deleteOptionStmt->execute();
    $db->close();
?>
