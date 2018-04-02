<?php

  session_start();
  ini_set("default_charset", 'utf-8');
  $userid = $_SESSION['user_id'];
  $moduleID = $_POST['root'];
  $title = htmlspecialchars($_POST['topicTitle']);
  $description = htmlspecialchars($_POST['topicContent']);
  $parentID = $_POST['parentID'];
  $tags = htmlspecialchars($_POST['tags']);
  $prereqs = $_POST['prereqs'];
  $opt = $_POST['optional'];

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
  echo $db->insert_id;
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

?>
