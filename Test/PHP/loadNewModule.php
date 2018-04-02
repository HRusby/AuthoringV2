<?php

  $moduleID = $_POST['id'];

  $db = new mysqli("localhost", "root", "topolor", "topolor");
  $db->set_charset("utf8");
  $ModulesQuery = "SELECT * FROM tpl_post WHERE id=?;";
  $ModulesStmt = $db->stmt_init();
  $ModulesStmt->prepare($ModulesQuery);
  $ModulesStmt->bind_param("i", $moduleID);
  $ModulesStmt->execute();
  $ModulesResult=$ModulesStmt->get_result();
  $module = $ModulesResult->fetch_assoc();
  $moduleTitle = str_replace(" ", "",$module['title']);

  echo "<div class='row ml-3 my-2' id='".$moduleTitle."Row'>";
    echo "<div class='input-group pr-1'>";
      echo "<div class='input-group-prepend'>";
        echo "<button data-toggle='collapse' data-target='#".$moduleTitle."TopicList' aria-expanded='false'  class = 'btn btn-sm collapseButton pull-left'><i class='fas fa-angle-right'></i></button>";
      echo "</div>"; // Close input group prepend
      echo "<div class='w-50 bg-dark clickable pl-1' style='color:white;' onclick='loadTopic(".$module['id'].", ".$module['category'].")'>".strip_tags($module['title'])."</div>";
      echo "<div class='input-group-append'>";
        echo "<button class='btn btn-sm btn-success' id='".$moduleTitle."AddTopicButton' onclick='return addTopic(".$module['id'].",\"".$moduleTitle."\", ".$module['id'].");' data-toggle='tooltip' data-placement='top' title='Add a Topic to this Module'><i class='fas fa-plus'></i></button>";
        echo "<button class='btn btn-sm btn-danger' id='".$moduleTitle."DeleteButton' ";
        //onclick='return deleteModule(".$module['id'].",\"".$moduleTitle."\", ".$moduleCount.");'
        echo "onclick='$.confirm({title: \"Are You Sure?\", content: \"Deletion can not been undone\", buttons: {confirm: function() {return deleteModule(".$module['id'].",\"".$moduleTitle."\");}, cancel: function(){ $.alert(\"Cancelled\");}}});'";
        echo "data-toggle='tooltip' data-placement='top' title='Delete this Module'><i class='fas fa-trash-alt'></i></button>";
      echo "</div>";
    echo "</div>"; // Close input group
  echo "</div>"; // Close module row Div
  echo "<div class='row collapse ml-3' id='".$moduleTitle."TopicList' aria-expanded='false'>";
  echo "</div>"; // close moduleTitleTopicList Div.

?>
