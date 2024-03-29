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

  if($moduleID == $parentID){
    $prereqQuery = "SELECT topic_id FROM tpl_tutorial_topic WHERE root=?;";
    $prereqStmt = $db->stmt_init();
    $prereqStmt->prepare($prereqQuery);
    $prereqStmt->bind_param("i", $moduleID);
    $prereqStmt->execute();
    $prereqResult = $prereqStmt->get_result();
  }else{
    $prereqQuery = "SELECT topic_id FROM tpl_tutorial_topic WHERE root=? AND lft<?;";
    $prereqStmt = $db->stmt_init();
    $prereqStmt->prepare($prereqQuery);
    $prereqStmt->bind_param("ii", $moduleID, $topicRelations['rgt']);
    $prereqStmt->execute();
    $prereqResult = $prereqStmt->get_result();
  }

  $rules = '';
  $messages = '';

  echo "<div class='row h-100 justify-content-center align-items-center'>";
    echo "<div class='card w-75 my-3'>";
    if($parentID == $moduleID){
      echo "<form class='p-3 card-body' method='POST' action='../PHP/createTopic2.php' id='topicCreateForm'>";
    }else{
      echo "<form class='p-3 card-body' method='POST' action='../PHP/createSubTopic2.php' id='topicCreateForm'>";
    }
        echo "<input type='hidden' id='parentID' value='".$parentID."'/>";
        echo "<input type='hidden' id='moduleID' value='".$moduleID."'/>";
        echo "<input type='hidden' id='parentTitle' value='".$parentTitle."'/>";
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
        echo "<div class='row justify-content-center align-items-center'>";
          echo "<div class='btn-group'><button class='btn btn-primary' type='submit'>Save Topic</button></div>";
        echo "</div>";
      echo "</form>";
    echo "</div>"; // Close card
  echo "</div>"; // Close row
  $editRules="topicTitle: 'required', topicContent: 'required', tokenField: 'required'";
  $editMessages = "topicTitle: 'Please enter a title for the module', topicContent: 'Please enter some Content for the Module!', tokenField: 'Please enter at least one token for the module!'";
  // Validation rules and messages for the edit tab

  echo "<script>";
  echo "var updateValidator = $('#topicCreateForm').validate({";
    echo "rules:{".$editRules."},";
    echo "messages:{".$editMessages."},";
    echo "errorPlacement: function(error, element){";
    echo "    if(element.attr('id') == 'tokenField'){";
    echo "        error.insertAfter(element.parent());";
    echo "    }else{";
    echo "        error.insertAfter(element.parent());";
    echo "    }";
    echo "},";
    echo "ignore: \":hidden:not(#topicContent),.note-editable.panel-body\"";
  echo "});";

  echo "var myElement = $('#topicContent');";
  echo "myElement.summernote({";
    echo "toolbar: [";
    echo "  ['style', ['bold', 'italic', 'underline']],";
    echo "  ['font', ['strikethrough', 'superscript', 'subscript']],";
    echo "  ['fontsize', ['fontsize']],";
    echo "  ['color', ['color']],";
    echo "  ['para', ['ul', 'ol', 'paragraph']],";
    // echo "  ['height', ['height']],";
    echo "  ['insert', ['link', 'video', 'table']],";
    echo "  ['misc', ['undo', 'redo', 'codeview', 'help']]";
    echo "],";
    echo "popover:[],";
    echo "callbacks: {";
      echo "onChange: function(contents, $editable) {";
        echo "myElement.val(myElement.summernote('isEmpty') ? \"\" : contents);";
        echo "updateValidator.element(myElement);";
      echo "}";
    echo "}";
  echo "});";
  echo "</script>";

?>
