<?php

  session_start();
  ini_set("default_charset", 'utf-8');
  $userid = $_SESSION['user_id'];
  $title = htmlspecialchars($_POST['moduleTitle']);
  $content = $_POST['moduleContent'];
  // No htmlspecialchars on content field as that causes the content in the topolor system to display HTML tags, not the correct formatting
  $tags = htmlspecialchars($_POST['tags']);

  $tagArray = explode(',',$tags);
  // Separate the tagId list into individual names so its frequency can be updated

  $db = new mysqli("localhost", "root", "topolor", "topolor");
  $db->set_charset("utf8");

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
        $result = $freqStmt->prepare($freqQuery);
        if($result === false){
            die('prepare() failed: ' . htmlspecialchars($freqStmt->error));
        }
        $result = $freqStmt->bind_param('s', $t);
        if($result === false){
            die('bind_param failed: ' . htmlspecialchars($freqStmt->error));
        }
        $result = $freqStmt->execute();
        if($result === false){
            die('execute() failed: ' . htmlspecialchars($freqStmt->error));
        }
    }else{
      $addNewTagQuery = "INSERT INTO tpl_tag VALUES (NULL, ?, ?, '1', CURRENT_TIMESTAMP, NULL);";
      $addNewTagStmt = $db->stmt_init();
      $addNewTagStmt->prepare($addNewTagQuery);
      $addNewTagStmt->bind_param("is", $userid, $t);
      $addNewTagStmt->execute();
      // Add the new tag to the database
    }
  }
  $query = "INSERT INTO tpl_post VALUES (NULL, ?, '0', '1', ?,?,?,NULL,NULL,CURRENT_TIMESTAMP,NULL);";
  // userid, title, content, tags
  $insertStmt = $db->stmt_init();
  $insertStmt->prepare($query);
  $insertStmt->bind_param("isss", $userid, $title, nl2br($content), $tags);
  $Result = $insertStmt->execute();
  echo $db->insert_id;
  // if($Result === true){
  //     header('location:../Pages/LessonOverview.php');
  // }else{
  //     echo "Error updating the data";
  //     echo mysqli_error($db);
  // }

?>
