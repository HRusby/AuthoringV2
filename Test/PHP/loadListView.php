<?php
  session_start();
  ini_set("default_charset", 'utf-8');
  $userid = $_SESSION['user_id'];

  function printTopics($root, $level, $parentID){
    $db = new mysqli("localhost", "root", "topolor", "topolor");
    $db->set_charset("utf8");
    // Need to get parent relations
    if($parentID == $root){
        $topicIDQuery = "SELECT * FROM tpl_tutorial_topic WHERE root = ? AND level = ? ORDER BY lft ASC;";
        $topicIDStmt = $db->stmt_init();
        $topicIDStmt->prepare($topicIDQuery);
        $topicIDStmt->bind_param("ii", $root, $level);
        $topicIDStmt->execute();
        $IDResult = $topicIDStmt->get_result();
    }else{
        $parentRelationQuery = "SELECT * FROM tpl_tutorial_topic WHERE topic_id = ?;";
        $parentRelationStmt = $db->stmt_init();
        $parentRelationStmt->prepare($parentRelationQuery);
        $parentRelationStmt->bind_param("i", $parentID);
        $parentRelationStmt->execute();
        $parentRelationR = $parentRelationStmt->get_result();
        $parentRelationResult = $parentRelationR->fetch_assoc();
        $plft = $parentRelationResult['lft'];
        $prgt = $parentRelationResult['rgt'];
        $topicIDQuery = "SELECT * FROM tpl_tutorial_topic WHERE root = ? AND level = ? AND lft > ? AND rgt < ? ORDER BY lft ASC;";
        // Ordered by LFT to ensure the authoring tool reflects how the system will display the topics
        $topicIDStmt = $db->stmt_init();
        $topicIDStmt->prepare($topicIDQuery);
        $topicIDStmt->bind_param("iiii", $root, $level, $plft, $prgt);
        $topicIDStmt->execute();
        $IDResult = $topicIDStmt->get_result();
    }
    // Find the set of topic IDs
    $topicCount = 0;
    while($id = $IDResult->fetch_assoc()){

      $topicDataQuery = "SELECT * FROM `tpl_post` WHERE `id` = ?;";
      $topicDataStmt = $db->stmt_init();
      $topicDataStmt->prepare($topicDataQuery);
      $topicDataStmt->bind_param("i", $id['topic_id']);
      $topicDataStmt->execute();
      $topicResult = $topicDataStmt->get_result();

      while($topic = $topicResult->fetch_assoc()){
        $topicTitle = str_replace(" ", "",$topic['title']);

        if(strlen($topic['title'])<30){
          $titleExcerpt=$topic['title'];
        }else{
          $titleExcerpt = substr($topic['title'],0,27)."...";
        }

        echo "<div class='row ml-2 mb-2 w-100 topics".$parentID."' style='height:50px;' id='".$topicTitle."Row'>";
          echo "<div class='input-group pr-1'>";
            echo "<div class='input-group-prepend'>";
              echo "<button data-toggle='collapse' data-target='#".$topicTitle."TopicList' aria-expanded='false'  class = 'btn btn-sm collapseButton pull-left'><i class='fas fa-angle-right'></i></button>";
              echo "<input type='hidden' class='listTopicID".$parentID."' value='".$topic['id']."'/>";
            echo "</div>"; // Close input group prepend
            echo "<div class='input-group-btn btn-group-vertical'>
                <button type='button' class='btn btn-sm btn-secondary moveUpBtn' data-toggle='tooltip' data-placement='top' title='Move Topic Up' onclick='moveTopicUp(".$topicCount.",".($topicCount-1).", ".$parentID.");'><i class='fas fa-angle-up'></i></button>
                <button type='button' class='btn btn-sm btn-secondary moveDownBtn' data-toggle='tooltip' data-placement='bottom' title='Move Topic Down' onclick='moveTopicDown(".$topicCount.",".($topicCount+1).", ".$parentID.");'><i class='fas fa-angle-down'></i></button>
              </div>";
            echo "<div class='w-50 bg-light clickable pl-1 title' onclick='loadTopic(".$topic['id'].", ".$topic['category'].")'>".strip_tags($titleExcerpt)."</div>";
            echo "<div class='input-group-append btn-group' role='group'>";
              echo "<button class='btn btn-sm btn-success' id='".$topicTitle."AddTopicButton' onclick='return addTopic(".$topic['id'].",\"".$topicTitle."\", ".$root.");' data-toggle='tooltip' data-placement='top' title='Add A Sub-Topic'><i class='fas fa-plus'></i></button>";
              echo "<button class='btn btn-sm btn-danger' id='".$topicTitle."DeleteButton' ";
              // onclick='$.confirm({title: \"Are you sure?\",content: \"test\",buttons: {confirm: function() { location.href=\"../PHP/deletePost.php?m=".$root."&t=".$topicData['id']."\";},cancel: function() { $.alert(\"Cancelled\");}}});'
              echo "onclick='$.confirm({title: \"Are You Sure?\", content: \"Deletion can not been undone\", buttons: {confirm: function() {return deleteTopic(".$topic['id'].", \"".$topicTitle."\", ".$root.");}, cancel: function(){ $.alert(\"Cancelled\");}}});'";
              echo "data-toggle='tooltip' data-placement='top' title='Delete This Topic'><i class='fas fa-trash-alt'></i></button>";
            echo "</div>"; // Close input group append
          echo "</div>"; // Close input group
        echo "</div>"; // Close topic row Div
        echo "<div class='row collapse ml-3 topicLists".$parentID."' id='".$topicTitle."TopicList' aria-expanded='false'>";
          printTopics($root, $level+1, $topic['id']); // Prints out all of the modules topics.
        // Pass' the current ID, the level it's looking at (second level) and the parent ID as this is top level the parent ID is it's own ID
        echo "</div>"; // close moduleTitleTopicList Div.
        $topicCount++;
      }
    }
    $db->close();
  }

  $db = new mysqli("localhost", "root", "topolor", "topolor");
  $db->set_charset("utf8");
  $ModulesQuery = "SELECT * FROM tpl_post WHERE user_id=? AND category=0;";
  $ModulesStmt = $db->stmt_init();
  $ModulesStmt->prepare($ModulesQuery);
  $ModulesStmt->bind_param("i", $userid);
  $ModulesStmt->execute();
  $ModulesResult=$ModulesStmt->get_result();
  $moduleCount = 0;
  echo "\n<div class='mb-5 card h-100'>"; // Div to ensure padding around all modules/topics.
    echo "<div class='card-header text-center'>";
      echo "<button class='btn btn-success' id='addModuleButton' type='button' onclick='return addModule();' data-toggle='tooltip' data-placement='top' title='Create a New Module'><i class='fas fa-plus'></i> Create A New Module</button>";
    echo "</div>"; // Close card-header
    echo "<div id='allModules' class='ml-1 w-100 card-body'>";
      while($module = $ModulesResult->fetch_assoc()){
        // Create a row for this module, expand to load in all subtopics
        if(strlen($topic['title'])<30){
          $titleExcerpt=$module['title'];
        }else{
          $titleExcerpt = substr($module['title'],0,27)."...";
        }
        $moduleTitle = str_replace(" ", "",$module['title']);

        echo "<div class='row mb-2 w-100' style='height:50px;' id='".$moduleTitle."Row'>";
          echo "<div class='input-group pr-1'>";
            echo "<div class='input-group-prepend'>";
              echo "<button data-toggle='collapse' data-target='#".$moduleTitle."TopicList' aria-expanded='false'  class = 'btn btn-sm collapseButton pull-left'><i class='fas fa-angle-right'></i></button>";
            echo "</div>"; // Close input group prepend
            echo "<div class='w-50 bg-light clickable pl-1 title' onclick='loadTopic(".$module['id'].", ".$module['category'].")'>".strip_tags($titleExcerpt)."</div>";
            echo "<div class='input-group-append btn-group' role='group'>";
              echo "<button class='btn btn-sm btn-success' id='".$moduleTitle."AddTopicButton' onclick='return addTopic(".$module['id'].",\"".$moduleTitle."\", ".$module['id'].");' data-toggle='tooltip' data-placement='top' title='Add a Topic to this Module'><i class='fas fa-plus'></i></button>";
              echo "<button class='btn btn-sm btn-danger' id='".$moduleTitle."DeleteButton' ";
              //onclick='return deleteModule(".$module['id'].",\"".$moduleTitle."\", ".$moduleCount.");'
              echo "onclick='$.confirm({title: \"Are You Sure?\", content: \"Deletion can not been undone\", buttons: {confirm: function() {return deleteModule(".$module['id'].",\"".$moduleTitle."\");}, cancel: function(){ $.alert(\"Cancelled\");}}});'";
              echo "data-toggle='tooltip' data-placement='top' title='Delete this Module'><i class='fas fa-trash-alt'></i></button>";
            echo "</div>";
          echo "</div>"; // Close input group
        echo "</div>"; // Close module row Div
        echo "<div class='row collapse' id='".$moduleTitle."TopicList' aria-expanded='false'>";
          printTopics($module['id'], 2, $module['id']); // Prints out all of the modules topics.
        // Pass' the current ID, the level it's looking at (second level) and the parent ID as this is top level the parent ID is it's own ID
        echo "</div>"; // close moduleTitleTopicList Div.
        $moduleCount++;
      }
    echo "</div>"; // Close All Modules div
    // echo "<div class='card-footer'>";
    //   echo "<button class='btn btn-success id='addModuleButton' onclick='return addModule();' data-toggle='tooltip' data-placement='top' title='Create a New Module'><i class='fas fa-plus'></i></button>";
    // echo "</div>"; // close card-footer
  echo "</div>"; // Close padding Div
  echo "<script>$('[data-toggle=\"tooltip\"]').tooltip();</script>";
  $db->close();
?>
