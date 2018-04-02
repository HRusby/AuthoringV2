<?php

ini_set("default_charset", 'utf-8');
session_start();
$userid = $_SESSION['user_id'];
$topicID = $_GET['t'];
$t = true;
echo $_POST['q1OptCorrect'];
$questionCount = 1;
while($t == true){
    if(isset($_POST['q'.$questionCount.'Title'])){
        // Establishes whether a question for this number exists
        if(isset($_POST['q'.$questionCount.'ID'])){
            // Update the question details
            // echo "existing question";
            $db = new mysqli("localhost", "root", "topolor", "topolor");
            $db->set_charset("utf8");
            $updateQQuery = "UPDATE tpl_question SET description=?, correct_answer=?, update_at=CURRENT_TIMESTAMP WHERE id=?;";
            $updateQStmt = $db->stmt_init();
            $updateQStmt->prepare($updateQQuery);
            $updateQStmt->bind_param("ssi", $_POST['q'.$questionCount.'Title'], $_POST['q'.$questionCount.'OptCorrect'], $_POST['q'.$questionCount.'ID']);
            $updateQStmt->execute();
            $options = true;
            $optionCount = 0;
            while($options){
                if(isset($_POST['q'.$questionCount."OptOrder".$optionCount])){
                    if(isset($_POST['q'.$questionCount.'Opt'.$optionCount.'ID'])){
                        $updateOQuery = "UPDATE tpl_question_option SET val=? WHERE id=?;";
                        $updateOStmt = $db->stmt_init();
                        $updateOStmt->prepare($updateOQuery);
                        $updateOStmt->bind_param("si", $_POST['q'.$questionCount.'Opt'.$optionCount.'Value'], $_POST['q'.$questionCount.'Opt'.$optionCount.'ID']);
                        $updateOStmt->execute();
                    }else{
                        $saveQuery = "INSERT INTO tpl_question_option VALUES (NULL, ?,?,?);";
                        $saveStmt = $db->stmt_init();
                        $saveStmt->prepare($saveQuery);
                        $saveStmt->bind_param("iss", $_POST['q'.$questionCount.'ID'], $_POST['q'.$questionCount.'OptOrder'.$optionCount], $_POST['q'.$questionCount.'Opt'.$optionCount.'Value']);
                        $saveStmt->execute();
                    }
                    // if it has an id update
                    // else store it

                }else{
                    $options = false;
                }
                $optionCount++;

           }
           $db->close();
        }else{
            // echo "new question\n";
            $db = new mysqli("localhost", "root", "topolor", "topolor");
            $db->set_charset("utf8");
            $saveQQuery = "INSERT INTO tpl_question VALUES (NULL, ?, ?, NULL, ?, ?, CURRENT_TIMESTAMP, NULL);";
            $saveQStmt = $db->stmt_init();
            $saveQStmt->prepare($saveQQuery);
            $correctAnswer = $_POST['q'.$questionCount.'OptCorrect'];
            $saveQStmt->bind_param("iiss", $userid, $topicID, $_POST['qTitle'.$questionCount], $correctAnswer);
            $saveQStmt->execute();
            $insertid = $db->insert_id;
            // echo $insertid;
            // First save the question
            $options = true;
            $optionCount = 0;
            while($options){
                if(isset($_POST['q'.$questionCount."OptOrder".$optionCount])){
                        // echo "adding option <br>";
                        $saveQuery = "INSERT INTO tpl_question_option VALUES (NULL, ?,?,?);";
                        $saveStmt = $db->stmt_init();
                        $saveStmt->prepare($saveQuery);

                        $saveStmt->bind_param("iss", $insertid, $_POST['q'.$questionCount.'OptOrder'.$optionCount], $_POST['q'.$questionCount.'Opt'.$optionCount.'Value']);
                        $saveStmt->execute();

                    // store each question option

                }else{
                    $options = false;
                }
                $optionCount++;

            }
            // Then save the question Options
            // Create the question details
        }
        $questionCount++; // Increment the questionCount for the next iteration
    }else{
        // If not the loop is stopped
        $t = false;
    }
}

?>
