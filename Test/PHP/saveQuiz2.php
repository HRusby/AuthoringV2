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

    // Determine if there's at least one question for the topic, if not, set to draft
    // Theoretically would've stopped modules being seen in topolor, unfortunately doesn't seem to work

    // Get Module ID
    // $moduleIDQuery = "SELECT root FROM tpl_tutorial_topic WHERE topic_id=?;";
    // $moduleIDStmt = $db->stmt_init();
    // $moduleIDStmt->prepare($moduleIDQuery);
    // $moduleIDStmt->bind_param("i", $topicID);
    // $moduleIDStmt->execute();
    // $moduleID = $moduleIDStmt->get_result()->fetch_assoc();
    //
    // // Get all topic ID of that module
    // $topicIDsQuery = "SELECT topic_id FROM tpl_tutorial_topic WHERE root=?;";
    // $topicIDsStmt = $db->stmt_init();
    // $topicIDsStmt->prepare($topicIDsQuery);
    // $topicIDsStmt->bind_param("i", $moduleID);
    // $topicIDsStmt->execute();
    // $topicIDs = $topicIDsStmt->get_result();
    //
    // // Loop through Topic IDs, if it has at least 1 question break and set module to non-draft else set to draft
    // $atLeastOne = false;
    // while($id = $topicIDs->fetch_assoc()){
    //   $questionsQuery = "SELECT * FROM tpl_question WHERE topic_id=?;";
    //   $questionsStmt = $db->stmt_init();
    //   $questionsStmt->prepare($questionsQuery);
    //   $questionsStmt->bind_param("i", $id['topic_id']);
    //   $questionsStmt->execute();
    //   $questionsResult=$questionsStmt->get_result();
    //
    //   if($questionsResult->num_rows() > 0){
    //     $atLeastOne = true;
    //     break;
    //   }
    // }
    // if($atLeastOne){
    //   $updateDraftQuery = "UPDATE tpl_post SET status=3 WHERE id=?;";
    //   $updateDraftStmt = $db->stmt_init();
    //   $updateDraftStmt->prepare($updateDraftQuery);
    //   $updateDraftStmt->bind_param("i", $moduleID);
    //   $updateDraftStmt->execute();
    //   // Set module to category 0 (module)
    // }else{
    //   $updateDraftQuery = "UPDATE tpl_post SET status=1 WHERE id=?;";
    //   $updateDraftStmt = $db->stmt_init();
    //   $updateDraftStmt->prepare($updateDraftQuery);
    //   $updateDraftStmt->bind_param("i", $moduleID);
    //   $updateDraftStmt->execute();
    // }// Set module to being draft (category )
}

?>
