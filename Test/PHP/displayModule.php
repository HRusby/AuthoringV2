<?php
  $moduleID = $_GET['m'];
  // echo "Module: ".$_GET['m'];
  $db = new mysqli("localhost", "root", "topolor", "topolor");
  $db->set_charset("utf8");

  $postDetailsQuery = "SELECT * FROM tpl_post WHERE id=?;";
  $postDetailsStmt = $db->stmt_init();
  $postDetailsStmt->prepare($postDetailsQuery);
  $postDetailsStmt->bind_param("i", $moduleID);
  $postDetailsStmt->execute();
  $module = $postDetailsStmt->get_result()->fetch_assoc();

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
  // Prepare all tags to be added to tokenField as potential tags

  $db->close();

  $rules = '';
  $messages = '';

  // echo "<div class='row'>";
  $moduleTitle = str_replace(" ", "",$module['title']);
  echo "<div class='row h-100 justify-content-center align-items-center my-3'>";
  echo "<form class='w-75 bg-white border border-light p-3' method='POST' action='../PHP/updateModule2.php?m=".$moduleID."' id='moduleUpdateForm'>";
  echo "<div class='form-group'><label for='moduleTitle' class='w-100'>Module Title:";
  echo "<button type='button' class='btn btn-danger float-right'";
  echo "onclick='$.confirm({title: \"Are You Sure?\", content: \"Deletion can not been undone\", buttons: {confirm: function() {deleteModule(".$module['id'].",\"".$moduleTitle."\"); $(\"#actionPanel\").html(\"\");}, cancel: function(){ $.alert(\"Cancelled\");}}});'";
  echo "</button><i class='fas fa-trash-alt'></i></div>";
  echo "</label><input type='text' class ='form-control' value='".$module['title']."' id='moduleTitle' name='moduleTitle'/>";

  echo "<div class='form-group'><label for='moduleContent'>Module Content:</label><textarea class='form-control' id='moduleContent' name='moduleContent' style='resize:vertical;'>".$module['description']."</textarea></div>";
  echo "<div class='form-group'><label for='tokenField'>Tags:</label><input type='text' class='form-control' id='tokenField' name='tokenField' value='".$module['tags']."' /></div>";
  echo "<script>$('#tokenField').tokenfield({autocomplete: {source: [".$tags."], delay: 100}, showAutocompleteOnFocus: true})</script>";
  echo "<div class='btn-group'><button class='btn btn-primary' type='submit'>Update Module</button></div>";
  echo "</form>";
  echo "</div>"; // Close row container

  $rules .= "moduleTitle: 'required'";
  $messages .= "moduleTitle: 'Please input a Title for this Module'";

  $rules .= ", moduleContent: 'required'";
  $messages .= ", moduleContent: 'Please input some Content for this Module'";

  $rules .= ", tokenField: 'required'";
  $messages .= ", tokenField: 'Please input at least One Tag for this Module'";

  echo "<script>";
  echo "var updateValidator = $('#moduleUpdateForm').validate({";
    echo "rules:{".$rules."},";
    echo "messages:{".$messages."},";
    echo "errorPlacement: function(error, element){";
    echo "    if(element.attr('id') == 'tokenField'){";
    echo "        error.insertAfter(element.parent());";
    echo "    }else{";
    echo "        error.insertAfter(element.parent());";
    echo "    }";
    echo "},";
    echo "ignore: \":hidden:not(#moduleContent),.note-editable.panel-body\"";
  echo "});";

  echo "var myElement = $('#moduleContent');";
  echo "myElement.summernote({";
    echo "toolbar: [";
    echo "  ['style', ['bold', 'italic', 'underline']],";
    echo "  ['font', ['strikethrough', 'superscript', 'subscript']],";
    echo "  ['fontsize', ['fontsize']],";
    echo "  ['color', ['color']],";
    echo "  ['para', ['ul', 'ol', 'paragraph']],";
    // echo "  ['height', ['height']],";
    echo "  ['insert', ['picture', 'link', 'video', 'table']],";
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
