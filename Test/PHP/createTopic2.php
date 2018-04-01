<?php

  session_start();
  ini_set("default_charset", 'utf-8');
  $userid = $_SESSION['user_id'];
  $moduleID = $_POST['root'];
  $title = htmlspecialchars($_POST['topicTitle']);
  $description = htmlspecialchars($_POST['topicDesc']);
  $tags = htmlspecialchars($_POST['tags']);
  $prereqs = $_POST['prereqs'];
  $optional = $_POST['optional']; // either on or null
  $parent=$_POST['parentID'];

  $db = new mysqli("localhost", "root", "topolor", "topolor");
  $db->set_charset("utf8");

  $existQuery = "SELECT * FROM tpl_tutorial_topic WHERE root = ?;";
  $existStmt = $db->stmt_init();
  $existStmt->prepare($existQuery);
  $existStmt->bind_param("i", $moduleID);
  $existStmt->execute();
  if($existStmt->get_result()->num_rows == 0){
      $lft = 0;
      $rgt = 1;
      // echo "<script>alert(\"num_rows = 0\");</script>";
  }else{
      $maxQuery = "SELECT MAX(rgt) AS rgt FROM tpl_tutorial_topic WHERE root = ?;";
      $maxStmt = $db->stmt_init();
      $maxStmt->prepare($maxQuery);
      $maxStmt->bind_param("i", $moduleID);
      $maxStmt->execute();
      $maxResult = $maxStmt->get_result();
      $max = $maxResult->fetch_assoc();
      $lft = $max['rgt']+1;
      $rgt = $max['rgt']+2;
      // Find max rgt where root = module id then lft = max +1 rgt = max+2
  }
  // Finds what the lft and rgt values should be of this topic
  if($optional == "on"){
      $opt = 1;
  }else{
      $opt = 0;
  }
  // echo "opt: ".$opt."<br>";
  // echo "lft: ".$lft."<br>";
  // echo "rgt: ".$rgt."<br>";
  // echo "prerequirements: ".$prereqs."<br>";


  $tagArray = explode(', ',$tags);
  // Separate the tagId list into individual names so its frequency can be updated

  foreach($tagArray as $t){
      $tagExistsQuery = "SELECT * FROM tpl_tag WHERE name=?;";
      $tagExistsStmt = $db->stmt_init();
      $tagExistsStmt->prepare($tagExistsQuery);
      $tagExistsStmt->bind_param("s", $t);
      $tagExistsStmt->execute();
      $tagExistsResult = $tagExistsStmt->get_result();
      echo "After TagExists";
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
  // Updates frequency of all tags that were input

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

  if($prereqs == '-1'){
      echo "is -1<br>";
      $db = new mysqli("localhost", "root", "topolor", "topolor");
      $db->set_charset("utf8");
      $relationQuery = "INSERT INTO tpl_tutorial_topic VALUES (?, ?, ?, ?, '2', ?, NULL);";
      if(!($relationStmt = $db->stmt_init())){
          echo "init failed: (".$db->errno.") ".$db->error;
      }
      if(!$relationStmt->prepare($relationQuery)){
          echo "prepare failed: (".$db->errno.") ".$db->error;
      }
      if(!$relationStmt->bind_param("iiiii",$insertID, $moduleID, $lft, $rgt, $opt)){
          echo "bind failed: (".$db->errno.") ".$db->error;
      }
      if(!$relationStmt->execute()){
          echo "execute failed: (".$db->errno.") ".$db->error;
      }
      $db->close();
      // If no prereqs then enter a record into tpl_tutorial_topic with the prereqs as NULL
  }else{
      echo "not -1<br>";
      $db = new mysqli("localhost", "root", "topolor", "topolor");
      $db->set_charset("utf8");
      $relationQuery = "INSERT INTO tpl_tutorial_topic VALUES (?, ?, ?, ?, '2', ?, ?);";
      if(!($relationStmt = $db->stmt_init())){
          echo "init failed: (".$db->errno.") ".$db->error;
      }
      if(!$relationStmt->prepare($relationQuery)){
          echo "prepare failed: (".$db->errno.") ".$db->error;
      }
      if(!$relationStmt->bind_param("iiiiii",$insertID, $moduleID, $lft, $rgt, $opt, $prereqs)){
          echo "bind failed: (".$db->errno.") ".$db->error;
      }
      if(!$relationStmt->execute()){
          echo "execute failed: (".$db->errno.") ".$db->error;
      }
      $db->close();
      // If there is a prerequisite then parse that to the query
  }

?>
