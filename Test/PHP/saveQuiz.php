<?php
// Post values: qTitle(questionCount), qID(questionCount) -- won't exist for some counts as new ids won't have it
//              q(questionCount)OptOrder(optionCount), q(Count)Opt(optionCount), q(count)OptCorrect
//              q(questionCount)OptID(optionCount)
    ini_set("default_charset", 'utf-8');
    session_start();
    $userid = $_SESSION['user_id'];
    $t = true;
    $questionCount = 1;
    echo $_POST['q1OptCorrect'];
    while($t == true){
        if(isset($_POST['qTitle'.$questionCount])){
            // Establishes whether a question for this number exists
            if(isset($_POST['qID'.$questionCount])){
                // Update the question details
                // echo "existing question";
                $db = new mysqli("localhost", "root", "topolor", "topolor");
                $db->set_charset("utf8");
                $updateQQuery = "UPDATE tpl_question SET description=?, correct_answer=?, update_at=CURRENT_TIMESTAMP WHERE id=?;";
                $updateQStmt = $db->stmt_init();
                $updateQStmt->prepare($updateQQuery);
                $updateQStmt->bind_param("ssi", $_POST['qTitle'.$questionCount], $_POST['q'.$questionCount.'OptCorrect'], $_POST['qID'.$questionCount]);
                $updateQStmt->execute();
                $options = true;
                $optionCount = 0;
                while($options){
                    if(isset($_POST['q'.$questionCount."OptOrder".$optionCount])){
                        if(isset($_POST['q'.$questionCount.'OptID'.$optionCount])){
                            $updateOQuery = "UPDATE tpl_question_option SET val=? WHERE id=?;";
                            $updateOStmt = $db->stmt_init();
                            $updateOStmt->prepare($updateOQuery);
                            $updateOStmt->bind_param("si", $_POST['q'.$questionCount.'Opt'.$optionCount], $_POST['q'.$questionCount.'OptID'.$optionCount]);
                            $updateOStmt->execute();
                        }else{
                            $saveQuery = "INSERT INTO tpl_question_option VALUES (NULL, ?,?,?);";
                            $saveStmt = $db->stmt_init();
                            $saveStmt->prepare($saveQuery);
                            $saveStmt->bind_param("iss", $_POST['qID'.$questionCount], $_POST['q'.$questionCount.'OptOrder'.$optionCount], $_POST['q'.$questionCount.'Opt'.$optionCount]);
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
                $saveQStmt->bind_param("iiss", $userid, $_POST['topicID'], $_POST['qTitle'.$questionCount], $correctAnswer);
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

                            $saveStmt->bind_param("iss", $insertid, $_POST['q'.$questionCount.'OptOrder'.$optionCount], $_POST['q'.$questionCount.'Opt'.$optionCount]);
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
    // header('location:../Pages/LessonOverview.php');
    // TODO: for each question determine if it's new or to be updated (if qID(count) isset)
    // TODO: For each question determine the number of options and store/update them.
    //              update if they exist already (will need a select query)
?>
