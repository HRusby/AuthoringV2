<?php
    session_start();
    ini_set("default_charset", 'utf-8');
    $postID = $_GET['t'];
    // $query = "DELETE FROM tpl_post WHERE id=".$postID.";";
    $db = new mysqli("localhost", "root", "topolor", "topolor");
    $db->set_charset("utf8");

    // $db->query($query);
    // header('location: ../Pages/LessonOverview.php');
    // NEEDS HEAVY EDITING to include sub topics and various dependencies

    $postQuery = "SELECT * FROM tpl_post WHERE id = ?;";
    $postStmt = $db->stmt_init();
    $postStmt->prepare($postQuery);
    $postStmt->bind_param("i", $postID);
    $postStmt->execute();
    $postData = $postStmt->get_result()->fetch_assoc();
    // Find all the data related to this topic in tpl_post

    if($postData['category'] == 0){
      //  Find all dependencies (get rgt and lft then find relations where rgt and lft are between this with the same root)
      $findRelationsQuery = "SELECT * FROM tpl_tutorial_topic WHERE root = ?;";
      $findRelationsStmt = $db->stmt_init();
      $findRelationsStmt->prepare($findRelationsQuery);
      $findRelationsStmt->bind_param("i", $postID);
      $findRelationsStmt->execute();
      $findRelationsData = $findRelationsStmt->get_result();
      while($row = $findRelationsData->fetch_assoc()){
        //  Delete all dependencies and corresponding posts
          $curID = $row['topic_id'];

          $getTagsQuery = "SELECT tags FROM tpl_post WHERE id=?;";
          $getTagsStmt = $db->stmt_init();
          $getTagsStmt->prepare($getTagsQuery);
          $getTagsStmt->bind_param("i", $curID);
          $getTagsStmt->execute();
          $tagData = $getTagsStmt->get_result()->fetch_assoc();
          // Fetch the tags for each post that's being deleted
          $tagArray = explode(',',$tagData['tags']);
          // Separate the tagId list into individual names so its frequency can be updated
          foreach($tagArray as $t){
              $freqQuery = "UPDATE tpl_tag SET frequency=frequency-1 WHERE name=?;";
              // For each tag id, find the name and increment its frequency
              $freqStmt = $db->stmt_init();
              $freqStmt->prepare($freqQuery);
              $freqStmt->bind_param('s', $t);
              $freqStmt->execute();
              // Decrement the tag frequency for each tag
          }

          $deletePostQuery = "DELETE FROM tpl_post WHERE id = ?;";
          $deletePostStmt = $db->stmt_init();
          $deletePostStmt->prepare($deletePostQuery);
          $deletePostStmt->bind_param("i", $curID);
          $deletePostStmt->execute();
          $deleteRelationQuery = "DELETE FROM tpl_tutorial_topic WHERE topic_id = ?;";
          $deleteRelationStmt = $db->stmt_init();
          $deleteRelationStmt->prepare($deleteRelationQuery);
          $deleteRelationStmt->bind_param("i", $curID);
          $deleteRelationStmt->execute();
      }

    }else if($postData['category'] == 1){
      $postRelationQuery = "SELECT * FROM tpl_tutorial_topic WHERE topic_id = ?;";
      $postRelationStmt = $db->stmt_init();
      $postRelationStmt->prepare($postRelationQuery);
      $postRelationStmt->bind_param("i", $postID);
      $postRelationStmt->execute();
      $postRelationData = $postRelationStmt->get_result()->fetch_assoc();
      // Find all the data related to this topic in tpl_tutorial_topic

      $relationDifference = $postRelationData['rgt'] - $postRelationData['lft'];
      echo $relationDifference;
      //  Find relationDifference to update subsequent relations rgt and lft
      if($relationDifference == 1){
        // Then it's a root node, just delete this post relations, tpl_post deleted later
        $deleteRelationQuery = "DELETE FROM tpl_tutorial_topic WHERE topic_id = ?;";
        $deleteRelationStmt = $db->stmt_init();
        $deleteRelationStmt->prepare($deleteRelationQuery);
        $deleteRelationStmt->bind_param("i", $postID);
        $deleteRelationStmt->execute();
      }else{
        $findRelationsQuery = "SELECT * FROM tpl_tutorial_topic WHERE lft >= ? AND rgt <= ? AND root = ?;";
        $findRelationsStmt = $db->stmt_init();
        $findRelationsStmt->prepare($findRelationsQuery);
        $findRelationsStmt->bind_param("iii", $postRelationData['lft'], $postRelationData['rgt'], $postRelationData['root']);
        $findRelationsStmt->execute();
        $findRelationsData = $findRelationsStmt->get_result();
        //  Find all dependencies between this.rgt and this.lft
        while($row = $findRelationsData->fetch_assoc()){
          //  Delete all dependencies and corresponding posts
          $curID = $row['topic_id'];
          $deleteRelationQuery = "DELETE FROM tpl_tutorial_topic WHERE topic_id = ?;";
          $deleteRelationQueryStmt = $db->stmt_init();
          $deleteRelationQueryStmt->prepare($deleteRelationQuery);
          $deleteRelationQueryStmt->bind_param("i", $curID);
          $deleteRelationQueryStmt->execute();
          // Relations deleted

          $getTagsQuery = "SELECT tags FROM tpl_post WHERE id=?;";
          $getTagsStmt = $db->stmt_init();
          $getTagsStmt->prepare($getTagsQuery);
          $getTagsStmt->bind_param("i", $curID);
          $getTagsStmt->execute();
          $tagData = $getTagsStmt->get_result()->fetch_assoc();
          // Fetch the tags for each post that's being deleted
          $tagArray = explode(',',$tagData['tags']);
          // Separate the tagId list into individual names so its frequency can be updated
          foreach($tagArray as $t){
              $freqQuery = "UPDATE tpl_tag SET frequency=frequency-1 WHERE name=?;";
              // For each tag id, find the name and increment its frequency
              $freqStmt = $db->stmt_init();
              $freqStmt->prepare($freqQuery);
              $freqStmt->bind_param('s', $t);
              $freqStmt->execute();
              // Decrement the tag frequency for each tag
          }

          $deletePostQuery = "DELETE FROM tpl_post WHERE id = ?;";
          $deletePostStmt = $db->stmt_init();
          $deletePostStmt->prepare($deletePostQuery);
          $deletePostStmt->bind_param("i", $curID);
          $deletePostStmt->execute();
          // Delete the post
        }
      }

      $findUpdatesQuery = "SELECT * FROM tpl_tutorial_topic WHERE rgt > ? AND root = ?";
      $findUpdatesStmt = $db->stmt_init();
      $findUpdatesStmt->prepare($findUpdatesQuery);
      $findUpdatesStmt->bind_param("ii", $postRelationData['rgt'], $postRelationData['root']);
      $findUpdatesStmt->execute();
      $findUpdatesData = $findUpdatesStmt->get_result();
      // Find all relations that need to be edited
      while($row = $findUpdatesData->fetch_assoc()){
        $decrease = $relationDifference + 1;
        if($row['lft'] < $postRelationData['lft']){
          $updateQuery = "UPDATE tpl_tutorial_topic SET rgt = rgt - ? WHERE topic_id = ?;";
          $updateStmt = $db->stmt_init();
          $updateStmt->prepare($updateQuery);
          $decrease = $relationDifference + 1;
          $updateStmt->bind_param("ii", $decrease, $row['topic_id']);
          $updateStmt->execute();
        }else if($row['lft'] > $postRelationData['rgt']){
          $updateQuery = "UPDATE tpl_tutorial_topic SET rgt = rgt - ?, lft = lft - ? WHERE topic_id = ?";
          $updateStmt = $db->stmt_init();
          $updateStmt->prepare($updateQuery);
          $updateStmt->bind_param("iii", $decrease, $decrease, $row['topic_id']);
          $updateStmt->execute();
        }
      }
      //  Update all relation dependencies where rgt >= this.rgt and lft >= this.rgt and root = this.root
      //      Set rgt -= relationDifference, lft -= relationDifference
      //  Update all relation dependencies where rgt >= this.rgt and lft <= this.lft and root = this.root
      //      Set rgt -= relationDifference
    }

    $tagArray = explode(',',$postData['tags']);
    // Separate the tagId list into individual names so its frequency can be updated

    foreach($tagArray as $t){
        $freqQuery = "UPDATE tpl_tag SET frequency=frequency-1 WHERE name=?;";
        // For each tag id, find the name and increment its frequency
        $freqStmt = $db->stmt_init();
        $freqStmt->prepare($freqQuery);
        $freqStmt->bind_param('s', $t);
        $freqStmt->execute();
    }

    $deletePostQuery = "DELETE FROM tpl_post WHERE id = ?;";
    $deletePostStmt = $db->stmt_init();
    $deletePostStmt->prepare($deletePostQuery);
    $deletePostStmt->bind_param("i", $postID);
    $deletePostStmt->execute();

    header('location:../Pages/LessonOverview.php');

    //  Update all relation dependencies where rgt >= this.rgt and lft >= this.rgt and root = this.root
    //      Set rgt -= relationDifference, lft -= relationDifference
    //  Update all relation dependencies where rgt >= this.rgt and lft <= this.lft and root = this.root
    //      Set rgt -= relationDifference
?>
