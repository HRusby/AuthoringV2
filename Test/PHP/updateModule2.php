<?php
  // echo $_POST['tags'];
  session_start();
  ini_set("default_charset", 'utf-8');
  $userid = $_SESSION['user_id'];

  $moduleID = $_GET['m'];
  $moduleTitle = $_POST['moduleTitle'];
  $moduleContent = $_POST['moduleContent'];
  $newTagsString = $_POST['tags'];
  // echo $newTagsString;
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
        // echo $t;
        // echo "\nNew Tag Found\n";
        $addNewTagQuery = "INSERT INTO tpl_tag VALUES (NULL, ?, ?, '1', CURRENT_TIMESTAMP, NULL);";
        $addNewTagStmt = $db->stmt_init();
        $addNewTagStmt->prepare($addNewTagQuery);
        $addNewTagStmt->bind_param("is", $userid, $t);
        $addNewTagStmt->execute();
      }
    }

    foreach($newTags as $nt){
      if(strpos($originalTagsString, $nt)!==true){
        // Doesn't exist therefore increment the new String
        echo "\nIncrementing: ".$nt;
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
        echo "\nDecrementing: ".$ot;
        $decrementTagQuery = "UPDATE tpl_tag SET frequency=frequency-1 WHERE name=?;";
        $decrementTagStmt = $db->stmt_init();
        $decrementTagStmt->prepare($decrementTagQuery);
        $decrementTagStmt->bind_param("s", $ot);
        $decrementTagStmt->execute();
      }
    }

    // Increment/Decrement tags where necessary
    // $decrement = array();
    // $increment = array();
    // foreach($newTags as $nt){
    //     foreach($originalTags as $ot){
    //         if($nt == $ot){ // If the tag already exists in the record don't increment
    //             echo "Tag ".$nt." Exists Already\n";
    //             $increment[$nt] = false;
    //             $decrement[$ot] = false;
    //             break; // Break as it's already been found, further iterations are pointless
    //         }else{ // If the tag is new to the record then increment the tag frequency
    //             $increment[$nt] = true;
    //             $decrement[$ot] = true;
    //         }
    //         // Perform increment for $nt if $increment is true
    //     }
    // }
    // foreach($increment as $key=>$value){
    //     if($value == true){
    //         echo "Incrementing Tag: ".$key."\n";
    //         $incrementQuery = "UPDATE tpl_tag SET frequency=frequency+1 WHERE name=?;";
    //         $incrementStmt = $db->stmt_init();
    //         $incrementStmt->prepare($incrementQuery);
    //         $incrementStmt->bind_param("s", $key);
    //         $incrementStmt->execute();
    //     }
    // }
    // foreach($decrement as $key=>$value){
    //     if($value == true){
    //         echo "Decrementing Tag: ".$key."\n";
    //         $decrementQuery = "UPDATE tpl_tag SET frequency=frequency-1 WHERE name=?;";
    //         $decrementStmt = $db->stmt_init();
    //         $decrementStmt->prepare($decrementQuery);
    //         $decrementStmt->bind_param("s", $key);
    //         $decrementStmt->execute();
    //     }
    // }
    // Perform Decrement for each $decrement that is true use counter to find what position $ot.
  // End Tag Update

  // Update the actual Module data
  // $db = new mysqli("localhost", "root", "topolor", "topolor");
  // $db->set_charset("utf8");
  $updateQuery = "UPDATE tpl_post SET title=?, description=?, tags=?, update_at=CURRENT_TIMESTAMP WHERE id=?;";
  $updateStmt = $db->stmt_init();
  $updateStmt->prepare($updateQuery);
  $updateStmt->bind_param("sssi", $moduleTitle, nl2br($moduleContent), $newTagsString, $moduleID);
  $updateStmt->execute();
  $db->close();

?>
