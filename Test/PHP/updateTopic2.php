<?php

  session_start();
  ini_set("default_charset", 'utf-8');
  $userid = $_SESSION['user_id'];

  $topicID = $_GET['t'];
  $topicTitle = $_POST['topicTitle'];
  $topicContent = $_POST['topicContent'];
  $topicPrerequisite = $_POST['prereqs'];
  $topicOptional = $_POST['optional'];
  $newTagsString = $_POST['tags'];
  // echo "ID: ".$topicID."\nTitle: ".$topicTitle."\nContent: ".$topicContent."\nPrerequisite: ".$topicPrerequisite."\nOptional: ".$topicOptional."\nTags: ".$newTagsString;

  $db = new mysqli("localhost", "root", "topolor", "topolor");
  $db->set_charset("utf8");

  // Update Tags
    // Get Original Tags
    $originalTagsQuery = "SELECT tags FROM tpl_post WHERE id=?;";
    $originalTagsStmt = $db->stmt_init();
    $originalTagsStmt->prepare($originalTagsQuery);
    $originalTagsStmt->bind_param("i", $moduleID);
    $originalTagsStmt->execute();
    $originalTagsString = $originalTagsStmt->get_result()->fetch_assoc()['tags'];
    $originalTags = explode(', ', $originalTagsString);
    $newTags = explode(', ', $newTagsString);

    // Add brand new tags to tpl_tag
    foreach($newTags as $t){
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

    foreach($newTags as $nt){
      if(strpos($originalTagsString, $nt)!==true){
        // Doesn't exist therefore increment the new String
        $incrementTagQuery = "UPDATE tpl_tag SET frequency=frequency+1 WHERE name=?;";
        $incrementTagStmt = $db->stmt_init();
        $incrementTagStmt->prepare($incrementTagQuery);
        $incrementTagStmt->bind_param("s", $nt);
        $incrementTagStmt->execute();
      }
    }

    foreach($originalTags as $ot){
      if(strpos($newTagsString, $ot)!==true){
        // Doesn't exist therefore decrement the old string
        $decrementTagQuery = "UPDATE tpl_tag SET frequency=frequency-1 WHERE name=?;";
        $decrementTagStmt = $db->stmt_init();
        $decrementTagStmt->prepare($decrementTagQuery);
        $decrementTagStmt->bind_param("s", $ot);
        $decrementTagStmt->execute();
      }
    }

    // Actual Topic Update queries
    $updateQuery = "UPDATE tpl_post SET title=?, description=?, tags=?, update_at=CURRENT_TIMESTAMP WHERE id=?;";
    $updateStmt = $db->stmt_init();
    $updateStmt->prepare($updateQuery);
    $updateStmt->bind_param("sssi", $topicTitle, nl2br($topicContent), $newTagsString, $topicID);
    $updateStmt->execute();
    // Update title, desc, tags and update_at fields
    if($topicPrerequisite == -1){
        echo "inside1";
        $updateRelationQuery = "UPDATE tpl_tutorial_topic SET optional=?, prerequisite=NULL WHERE topic_id=?;";
        $updateRelationStmt = $db->stmt_init();
        $updateRelationStmt->prepare($updateRelationQuery);
        $updateRelationStmt->bind_param("ii", $topicOptional, $topicID);
        $updateRelationStmt->execute();
    }else{
        echo "inside2";
        echo $topicID;
        echo "<br>".$prereqs;
        echo "<br>".$opt;
        $updateRelationQuery = "UPDATE tpl_tutorial_topic SET optional=?, prerequisite=? WHERE topic_id=?;";
        $updateRelationStmt = $db->stmt_init();
        $updateRelationStmt->prepare($updateRelationQuery);
        $updateRelationStmt->bind_param("iii", $topicOptional, $topicPrerequisite, $topicID);
        $updateRelationStmt->execute();
    }
?>
