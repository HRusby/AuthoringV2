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

  echo "<div class='row ml-3 my-2 w-100' id='".$topicTitle."Row'>";
    echo "<div class='input-group pr-1'>";
      echo "<div class='input-group-prepend'>";
        echo "<button data-toggle='collapse' data-target='#".$topicTitle."TopicList' aria-expanded='false'  class = 'btn btn-sm collapseButton pull-left'><i class='fas fa-angle-right'></i></button>";
      echo "</div>"; // Close input group prepend
      echo "<div class='w-50 bg-dark clickable pl-1' style='color:white;' onclick='loadTopic(".$topicResult['id'].", ".$topicResult['category'].")'>".strip_tags($topicResult['title'])."</div>";
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
