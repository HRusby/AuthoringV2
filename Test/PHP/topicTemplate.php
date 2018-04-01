<?php

  // $topicID = $_GET['t'];
  $db = new mysqli("localhost", "root", "topolor", "topolor");
  $db->set_charset("utf8");
  $parentID = $_POST['parentID'];
  $parentTitle = $_POST['parentTitle'];
  $moduleID = $_POST['moduleID'];

  $tagsQuery = "SELECT name FROM tpl_tag;";
  $tagsStmt = $db->stmt_init();
  $tagsStmt->prepare($tagsQuery);
  $tagsStmt->execute();
  $tagsResult = $tagsStmt->get_result();
  $tags = "";
  while($tag = $tagsResult->fetch_assoc()){
    if($tags == ""){
      $tags = "'".htmlspecialchars($tag['name'])."'";
    }else{
      $tags = $tags.",'".htmlspecialchars($tag['name'])."'";
    }
  }

  $topicRelationsQuery = "SELECT * FROM tpl_tutorial_topic WHERE topic_id = ?;";
  $topicRelationsStmt = $db->stmt_init();
  $topicRelationsStmt->prepare($topicRelationsQuery);
  $topicRelationsStmt->bind_param("i", $parentID);
  $topicRelationsStmt->execute();
  $topicRelations = $topicRelationsStmt->get_result()->fetch_assoc();

  $prereqQuery = "SELECT topic_id FROM tpl_tutorial_topic WHERE root=? AND lft<?;";
  $prereqStmt = $db->stmt_init();
  $prereqStmt->prepare($prereqQuery);
  $prereqStmt->bind_param("ii", $moduleID, $topicRelations['rgt']);
  $prereqStmt->execute();
  $prereqResult = $prereqStmt->get_result();

  echo "<div class='row h-100 justify-content-center align-items-center my-3'>";
  if($parentID == $moduleID){
    echo "<form class='w-75 bg-white border border-light p-3' method='POST' action='../PHP/createTopic2.php' id='topicCreateForm'>";
  }else{
    echo "<form class='w-75 bg-white border border-light p-3' method='POST' action='../PHP/createSubTopic2.php' id='topicCreateForm'>";
  }
      echo "<input type='hidden' id='parentID' value='".$parentID."'/>";
      echo "<input type='hidden' id='moduleID' value='".$moduleID."'/>";
      echo "<div class='form-group'><label for='topicTitle'>Topic Title:</label><input type='text' class ='form-control bg-light' placeholder='Enter a Title!' id='topicTitle' name='topicTitle'/></div>";
      echo "<div class='form-group'><label for='topicContent'>Topic Content:</label><textarea class='form-control bg-light' id='topicContent' name='topicContent' placeholder='Add Some Content!' style='resize:vertical;'></textarea></div>";
      echo "<div class='form-group'><label for='tokenField'>Tags:</label><input type='text' placeholder='Select an item or type some text and hit enter!' class='form-control bg-light' id='tokenField' name='tokenField' value='' /></div>";
      echo "<script>$('#tokenField').tokenfield({autocomplete: {source: [".$tags."], delay: 100}, showAutocompleteOnFocus: true})</script>";
      echo "<div class='form-group'><label for='prerequisites'>Prerequisite?</label><select class='form-control bg-light' name='prereqs' id ='prereqs'>";
        echo "<option value = '-1'>None</option>";
        while($prereq = $prereqResult->fetch_assoc()){
          $optionDataQuery = "SELECT * FROM tpl_post WHERE id=?;";
          $optionDataStmt = $db->stmt_init();
          $optionDataStmt->prepare($optionDataQuery);
          $optionDataStmt->bind_param("i", $prereq['topic_id']);
          $optionDataStmt->execute();
          $optionData = $optionDataStmt->get_result()->fetch_assoc();
          if($prereq['topic_id'] == $topicPreReq){
            echo "<option value='".$prereq['topic_id']."' selected>".$optionData['title']."</option>";
          }else{
            echo "<option value='".$prereq['topic_id']."'>".$optionData['title']."</option>";
          }
        }
      echo "</select></div>";
      echo "<div class='form-check'><input class='form-check-input' name='optional' id='optional' type='checkbox'/><label class='form-check-label' for='optional'>Is this topic optional?</label></div>";
      echo "<div class='btn-group'><button class='btn btn-primary' type='submit'>Create Topic</button></div>";
    echo "</form>";
  echo "</div>"; // Close row container

?>
