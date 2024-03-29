<?php

  $topicID = $_POST['id'];
  $root = $_POST['root'];
  $db = new mysqli("localhost", "root", "topolor", "topolor");
  $db->set_charset("utf8");

  $topicDataQuery = "SELECT * FROM `tpl_post` WHERE `id` = ?;";
  $topicDataStmt = $db->stmt_init();
  $topicDataStmt->prepare($topicDataQuery);
  $topicDataStmt->bind_param("i", $topicID);
  $topicDataStmt->execute();
  $topicResult = $topicDataStmt->get_result()->fetch_assoc();

  $topicTitle = str_replace(" ", "",$topicResult['title']);

  if(strlen($topicResult['title'])<30){
    $titleExcerpt=$topicResult['title'];
  }else{
    $titleExcerpt = substr($topicResult['title'],0,27)."...";
  }

  $relationsQuery = "SELECT * FROM tpl_tutorial_topic WHERE topic_id=?;";
  $relationsStmt = $db->stmt_init();
  $relationsStmt->prepare($relationsQuery);
  $relationsStmt->bind_param("i", $topicID);
  $relationsStmt->execute();
  $relations = $relationsStmt->get_result()->fetch_assoc();

  $parentID=$_POST['parentID'];
  $topicCount = $_POST['topicCount'];

  echo "<div class='row ml-2 mb-2 w-100 topics".$parentID."' id='".$topicTitle."Row'>";
    echo "<div class='input-group pr-1'>";
      echo "<div class='input-group-prepend'>";
        echo "<button data-toggle='collapse' data-target='#".$topicTitle."TopicList' aria-expanded='false'  class = 'btn btn-sm collapseButton pull-left'><i class='fas fa-angle-right'></i></button>";
        echo "<input type='hidden' class='listTopicID".$parentID."' value='".$topicresult['id']."'/>";
      echo "</div>"; // Close input group prepend
      echo "<div class='input-group-btn btn-group-vertical'>
          <button type='button' class='btn btn-sm btn-secondary moveUpBtn' data-toggle='tooltip' data-placement='top' title='Move Topic Up' onclick='moveTopicUp(".$topicCount.",".($topicCount-1).", ".$parentID.");'><i class='fas fa-angle-up'></i></button>
          <button type='button' class='btn btn-sm btn-secondary moveDownBtn' data-toggle='tooltip' data-placement='bottom' title='Move Topic Down' onclick='moveTopicDown(".$topicCount.",".($topicCount+1).", ".$parentID.");'><i class='fas fa-angle-down'></i></button>
        </div>";
      echo "<div class='w-50 bg-light clickable pl-1 title' onclick='loadTopic(".$topicResult['id'].", ".$topicResult['category'].")'>".strip_tags($titleExcerpt)."</div>";
      echo "<div class='input-group-append'>";
        echo "<button class='btn btn-sm btn-success' id='".$topicTitle."AddTopicButton' onclick='return addTopic(".$topicResult['id'].",\"".$topicTitle."\", ".$root.");' data-toggle='tooltip' data-placement='top' title='Add A Sub-Topic'><i class='fas fa-plus'></i></button>";
        echo "<button class='btn btn-sm btn-danger' id='".$topicTitle."DeleteButton' ";
        // onclick='$.confirm({title: \"Are you sure?\",content: \"test\",buttons: {confirm: function() { location.href=\"../PHP/deletePost.php?m=".$root."&t=".$topicData['id']."\";},cancel: function() { $.alert(\"Cancelled\");}}});'
        echo "onclick='$.confirm({title: \"Are You Sure?\", content: \"Deletion can not been undone\", buttons: {confirm: function() {return deleteTopic(".$topicResult['id'].", \"".$topicTitle."\", ".$root.");}, cancel: function(){ $.alert(\"Cancelled\");}}});'";
        echo "data-toggle='tooltip' data-placement='top' title='Delete This Topic'><i class='fas fa-trash-alt'></i></button>";
      echo "</div>"; // Close input group append
    echo "</div>"; // Close input group
  echo "</div>"; // Close topic row Div
  echo "<div class='row collapse ml-3' id='".$topicTitle."TopicList' aria-expanded='false'>";
  echo "</div>"; // close moduleTitleTopicList Div.

?>
