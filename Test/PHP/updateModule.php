<?php
//  DONE
    // POST values: mID, tags, mTitle, mDesc
    session_start();
    ini_set("default_charset", 'utf-8');
    $userid = $_SESSION['user_id'];
    $id = $_POST['mID'];
    $title = $_POST['mTitle'];
    $content = $_POST['mDesc'];
    $tags = $_POST['tags'];
    $newTagsArray = explode(',', $tags);
    $db = new mysqli("localhost", "root", "topolor", "topolor");
    $db->set_charset("utf8");
    $query = "SELECT tags FROM tpl_post WHERE id = ".$id.";";
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
        $addNewTagQuery = "INSERT INTO tpl_tag VALUES (NULL, ?, ?, '1', CURRENT_TIMESTAMP, NULL);";
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
    $updateStmt->bind_param("sssi", $title, nl2br($content), $tags, $id);
    $updateStmt->execute();
    // Update title, desc, tags and update_at fields
    header('location:../Pages/LessonOverview.php');
?>
