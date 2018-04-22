<?php
// DONE
// POST VALUES: mID, tID, tTitle, tDesc, tags, prereqs, optional

    session_start();
    ini_set("default_charset", 'utf-8');
    $userid = $_SESSION['user_id'];
    $moduleID = $_POST['mID'];
    $topicID = $_POST['tID'];
    $title = $_POST['tTitle'];
    $content = $_POST['tDesc'];
    $tags = $_POST['tags'];
    $prereqs = $_POST['prereqs'];
    $optional = $_POST['optional'];
    if($optional == "on"){
        $opt = 1;
    }else{
        $opt = 0;
    }
    $newTagsArray = explode(',', $tags);
    $db = new mysqli("localhost", "root", "topolor", "topolor");
    $db->set_charset("utf8");
    $query = "SELECT tags FROM tpl_post WHERE id = ".$topicID.";";
    $Result = $db->query($query);
    $r = $Result->fetch_assoc();
    $oldTagsArray = explode(',',$r['tags']);
    $decrement = array();
    $increment = array();

    foreach($newTagsArray as $t){
      $tagExistsQuery = "SELECT * FROM tpl_tag WHERE name=?;";
      $tagExistsStmt = $db->stmt_init();
      $tagExistsStmt->prepare($tagExistsQuery);
      $tagExistsStmt->bind_param("s", $t);
      $tagExistsStmt->execute();
      $tagExistsResult = $tagExistsStmt->get_result();
      if(mysqli_num_rows($tagExistsResult) <= 0){
        $addNewTagQuery = "INSERT INTO tpl_tag VALUES (NULL, ?, ?, '0', CURRENT_TIMESTAMP, NULL);";
        $addNewTagStmt = $db->stmt_init();
        $addNewTagStmt->prepare($addNewTagQuery);
        $addNewTagStmt->bind_param("is", $userid, $t);
        $addNewTagStmt->execute();
      }
    }

    foreach($newTagsArray as $nt){
        foreach($oldTagsArray as $ot){
            if($nt == $ot){ // If the tag already exists in the record don't increment
                $increment[$nt] = false;
                $decrement[$ot] = false;
                break;
            }else{ // If the tag is new to the record then increment the tag frequency
                $increment[$nt] = true;
                $decrement[$ot] = true;
            }
            // Perform increment for $nt if $increment is true
        }
    }
    foreach($increment as $key=>$value){
        if($value == true){
            $incrementQuery = "UPDATE tpl_tag SET frequency=frequency+1 WHERE name=?;";
            $incrementStmt = $db->stmt_init();
            $incrementStmt->prepare($incrementQuery);
            $incrementStmt->bind_param("s", $key);
            $incrementStmt->execute();
        }
    }
    foreach($decrement as $key=>$value){
        if($value == true){
            $decrementQuery = "UPDATE tpl_tag SET frequency=frequency-1 WHERE name=?;";
            $decrementStmt = $db->stmt_init();
            $decrementStmt->prepare($decrementQuery);
            $decrementStmt->bind_param("s", $key);
            $decrementStmt->execute();
        }
    }
    // Perform Decrement for each $decrement that is true use counter to find what position $ot.
    $updateQuery = "UPDATE tpl_post SET title=?, description=?, tags=?, update_at=CURRENT_TIMESTAMP WHERE id=?;";
    $updateStmt = $db->stmt_init();
    $updateStmt->prepare($updateQuery);
    $updateStmt->bind_param("sssi", $title, nl2br($content), $tags, $topicID);
    $updateStmt->execute();
    // Update title, desc, tags and update_at fields
    if($prereqs == -1){
        echo "inside1";
        $updateRelationQuery = "UPDATE tpl_tutorial_topic SET optional=?, prerequisite=NULL WHERE topic_id=?;";
        $updateRelationStmt = $db->stmt_init();
        $updateRelationStmt->prepare($updateRelationQuery);
        $updateRelationStmt->bind_param("ii", $opt, $topicID);
        $updateRelationStmt->execute();
    }else{
        echo "inside2";
        echo $topicID;
        echo "<br>".$prereqs;
        echo "<br>".$opt;
        $updateRelationQuery = "UPDATE tpl_tutorial_topic SET optional=?, prerequisite=? WHERE topic_id=?;";
        $updateRelationStmt = $db->stmt_init();
        $updateRelationStmt->prepare($updateRelationQuery);
        $updateRelationStmt->bind_param("iii", $opt, $prereqs, $topicID);
        $updateRelationStmt->execute();
    }
    // Update tpl_tutorial_topic fields optional and prereq
    header('location:../Pages/LessonOverview.php');
?>
