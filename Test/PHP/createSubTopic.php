<?php
// DONE
// POST VALUES: mID, stTitle, stDesc, ptID, tags, prereqs, optional
    session_start();
    ini_set("default_charset", 'utf-8');
    $userid = $_SESSION['user_id'];
    $moduleID = $_POST['mID'];
    $title = htmlspecialchars($_POST['stTitle']);
    $description = htmlspecialchars($_POST['stDesc']);
    $parentID = $_POST['ptID'];
    $tags = htmlspecialchars($_POST['tags']);
    $prereqs = $_POST['prereqs'];
    $optional = $_POST['optional'];
    if($optional == "on"){
        $opt = 1;
    }else{
        $opt = 0;
    }
    // Store all the post variables in local variables

    $db = new mysqli("localhost", "root", "topolor", "topolor");
    $db->set_charset("utf8");
    $tagArray = explode(',',$tags);
    // Separate the tagId list into individual names so its frequency can be updated

    foreach($tagArray as $t){
      $tagExistsQuery = "SELECT * FROM tpl_tag WHERE name=?;";
      $tagExistsStmt = $db->stmt_init();
      $tagExistsStmt->prepare($tagExistsQuery);
      $tagExistsStmt->bind_param("s", $t);
      $tagExistsStmt->execute();
      $tagExistsResult = $tagExistsStmt->get_result();
      if(mysqli_num_rows($tagExistsResult) > 0){
        $freqQuery = "UPDATE tpl_tag SET frequency=frequency+1 WHERE name=?;";
        // For each tag id, find the name and increment its frequency
        $freqStmt = $db->stmt_init();
        $freqStmt->prepare($freqQuery);
        $freqStmt->bind_param('s', $t);
        $freqStmt->execute();
      }else{
        echo "Tag doesn't exist";
        echo "\n tag: ".$t;
        $addNewTagQuery = "INSERT INTO tpl_tag VALUES (NULL, ?, ?, '1', CURRENT_TIMESTAMP, NULL);";
        $addNewTagStmt = $db->stmt_init();
        $addNewTagStmt->prepare($addNewTagQuery);
        $addNewTagStmt->bind_param("is", $userid, $t);
        $addNewTagStmt->execute();
      }
    }
    $db->close();
    // Increment all tags that are given to the post argument

    $db = new mysqli("localhost", "root", "topolor", "topolor");
    $db->set_charset("utf8");
    $insertQuery = "INSERT INTO tpl_post VALUES (NULL, ?, '1', '1', ?,?,?,NULL,NULL,CURRENT_TIMESTAMP,NULL);";
    $insertStmt = $db->stmt_init();
    $insertStmt->prepare($insertQuery);
    $insertStmt->bind_param("isss", $userid, $title, nl2br($description), $tags);
    $insertStmt->execute();
    $insertID = $db->insert_id;
    $db->close();
    // New entry to tpl_post for the topic record

    $db = new mysqli("localhost", "root", "topolor", "topolor");
    $db->set_charset("utf8");
    $parentQuery = "SELECT * FROM tpl_tutorial_topic WHERE topic_id = ?;";
    $parentStmt = $db->stmt_init();
    $parentStmt->prepare($parentQuery);
    $parentStmt->bind_param("i", $parentID);
    $parentStmt->execute();
    $parentTemp = $parentStmt->get_result();
    $parentResult = $parentTemp->fetch_assoc();

    $rootID = $parentResult['root'];
    $lft = $parentResult['rgt'];
    $rgt = $parentResult['rgt'] + 1;
    $prgt = $parentResult['rgt'];
    $plft = $parentResult['lft'];
    $parentLevel = $parentResult['level'];
    // Find the parents details
    $parentStmt->close();

    $selectUpdateQuery = "SELECT * FROM tpl_tutorial_topic WHERE rgt >= ?;";
    $selectUpdateStmt = $db->stmt_init();
    $selectUpdateStmt->prepare($selectUpdateQuery);
    $selectUpdateStmt->bind_param("i", $prgt);
    $selectUpdateStmt->execute();
    $selectUpdateResult = $selectUpdateStmt->get_result();
    while($row = $selectUpdateResult->fetch_assoc()){
        if($row['lft'] <= $plft && $row['rgt'] >= $prgt){
            // update rgt to rgt+2
            $updateContainersQuery = "UPDATE tpl_tutorial_topic SET rgt = rgt+2 WHERE topic_id = ?;";
            $updateContainerStmt = $db->stmt_init();
            $updateContainerStmt->prepare($updateContainersQuery);
            $updateContainerStmt->bind_param("i", $row['topic_id']);
            $updateContainerStmt->execute();
        }else if ($row['lft'] >= $plft && $row['rgt'] >= $prgt){
            // lft+=2 rgt +=2
            $updateContainersQuery = "UPDATE tpl_tutorial_topic SET rgt = rgt+2, lft = lft+2 WHERE topic_id = ?;";
            $updateContainerStmt = $db->stmt_init();
            $updateContainerStmt->prepare($updateContainersQuery);
            $updateContainerStmt->bind_param("i", $row['topic_id']);
            $updateContainerStmt->execute();
        }
    }

//     $rgtQuery = "UPDATE tpl_tutorial_topic SET rgt = ? WHERE topic_id = ?;";
//     $rgtStmt = $db->stmt_init();
//     $rgtStmt->prepare($rgtQuery);
//     $rgtStmt->bind_param("ii", $prgt, $parentID);
//     $rgtStmt->execute();
//     $rgtStmt->close();
//     // Update the parent rgt value

//     // Get level 2 topic lft and rgt values
//     $baseQuery = "SELECT * FROM tpl_tutorial_topic WHERE root = ? AND level = 2 AND lft < ? AND rgt >= ?;";
//     // Finds the base topic (level 2) that this subtopic will belong to (i.e. its lft and rgt values are between the topic lft and rgt)
//     $baseStmt = $db->stmt_init();
//     $baseStmt->prepare($baseQuery);
//     $baseStmt->bind_param("iii", $rootID, $lft, $rgt);
//     $baseStmt->execute();
//     $baseTemp = $baseStmt->get_result();
//     $baseResult = $baseTemp->fetch_assoc();
//     $baseID = $baseResult['topic_id'];
//     $baseRgt = $baseResult['rgt'] + 2; // Accomodates for the two new fields
//     $baseStmt->close();
//     // Find the base topic this subtopic belongs to


//     $updateContainersQuery = "UPDATE tpl_tutorial_topic SET rgt = rgt+2 WHERE lft < ? AND rgt >= ? AND topic_id != ?;";
//     $updateContainerStmt = $db->stmt_init();
//     $updateContainerStmt->prepare($updateContainersQuery);
//     $updateContainerStmt->bind_param("iii", $lft, $lft, $parentID);
//     $updateContainerStmt->execute();
//     // Select all records where lft < this.lft and rgt >= this.lft and make rgt +=2

//     $updateOtherQuery = "UPDATE tpl_tutorial_topic SET rgt = rgt+2, lft = lft+2 WHERE lft >= ? and rgt > ?;";
//     $updateOtherStmt = $db->stmt_init();
//     $updateOtherStmt->prepare($updateOtherQuery);
//     $updateOtherStmt->bind_param("ii", $rgt, $rgt);
//     $updateOtherStmt->execute();
//     // Select all records where lft >= this.rgt and rgt > this.rgt and make rgt += 2 and lft += 2

//     // Starts from sub topic rgt as this may clash with a pre-existing value

    $db->close();
    $newLevel = $parentLevel+1;

    if($prereqs == '-1'){

        $db = new mysqli("localhost", "root", "topolor", "topolor");
        $db->set_charset("utf8");
        $relationQuery = "INSERT INTO tpl_tutorial_topic VALUES (?, ?, ?, ?, ?, ?, NULL);";
        if(!($relationStmt = $db->stmt_init())){
            echo "init failed: (".$db->errno.") ".$db->error;
        }
        if(!$relationStmt->prepare($relationQuery)){
            echo "prepare failed: (".$db->errno.") ".$db->error;
        }

        if(!$relationStmt->bind_param("iiiiii",$insertID, $moduleID, $lft, $rgt, $newLevel, $opt)){
            echo "bind failed: (".$db->errno.") ".$db->error;
        }
        if(!$relationStmt->execute()){
            echo "execute failed: (".$db->errno.") ".$db->error;
        }
        $db->close();
        // If no prereqs then enter a record into tpl_tutorial_topic with the prereqs as NULL
    }else{

        $db = new mysqli("localhost", "root", "topolor", "topolor");
        $db->set_charset("utf8");
        $relationQuery = "INSERT INTO tpl_tutorial_topic VALUES (?, ?, ?, ?, ?, ?, ?);";
        if(!($relationStmt = $db->stmt_init())){
            echo "init failed: (".$db->errno.") ".$db->error;
        }
        if(!$relationStmt->prepare($relationQuery)){
            echo "prepare failed: (".$db->errno.") ".$db->error;
        }
        if(!$relationStmt->bind_param("iiiiiii",$insertID, $moduleID, $lft, $rgt, $newLevel,$opt, $prereqs)){
            echo "bind failed: (".$db->errno.") ".$db->error;
        }
        if(!$relationStmt->execute()){
            echo "execute failed: (".$db->errno.") ".$db->error;
        }
        $db->close();
        // If there is a prerequisite then parse that to the query
    }
    // Insertion query to tpl_tutorial_topic for this subtopic


    header("location: ../Pages/LessonOverview.php");
    // NEED TESTING AND LOGIC TEST


    // INCREMENT TAGS ... DONE
    // NEW POST RECORD ... DONE
    // NEW tpl_tutorial_post RECORD ... DONE
        // Get parent record from tpl_tutorial_topic to give size of lft and rgt DONE
            // new input has lft = parent rgt and rgt = parent rgt + 1
            // parent rgt then = rgt +2 DONE
        // Get root topic record from tpl_tutorial_topic i.e. record where lft is < than this.lft AND rgt > this.rgt DONE
        // If any record whose root is the same has a lft value = to parent rgt - 1
            // then
            // Increment*2 the lft value that = parent rgt - 1
            // Increment*2 the lft / rgt value that = parent rgt and topic id != parentID
            // NOW all records where lft or rgt > parent rgt + 2 need to be incremented*2 DONE
    // ENSURE lft AND rgt VALUES ARE ALL ACCURATE ...
?>
