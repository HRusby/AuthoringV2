<?php

  $db = new mysqli("localhost", "root", "topolor", "topolor");
  $db->set_charset("utf8");
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

  echo "<div class='row justify-content-center align-items-center my-3'>";
    echo "<div class='w-75 card'>";
      echo "<form class='p-3 card-body' method='POST' action='../PHP/createModule2.php' id='moduleCreateForm'>";
        echo "<div class='form-group'><label for='moduleTitle'>Module Title:</label><input type='text' class ='form-control' placeholder='Module Title' id='moduleTitle' name='moduleTitle'/></div>";
        echo "<div class='form-group'><label for='moduleContent'>Module Content:</label><textarea class='form-control' id='moduleContent' name='moduleContent' placeholder='Module Description' style='resize:vertical;'></textarea></div>";
        echo "<div class='form-group'><label for='tokenField'>Tags:</label><input type='text' class='form-control' id='tokenField' name='tokenField' placeholder='Select an item or type some text and hit enter!' /></div>";
          echo "<script>$('#tokenField').tokenfield({autocomplete: {source: [".$tags."], delay: 100}, showAutocompleteOnFocus: true})</script>";
        echo "<div class='row justify-content-center align-items-center'>";
          echo "<div class='btn-group'><input class='btn btn-primary' type='submit' value='Save Module'/></div>";
        echo "</div>";
      echo "</form>";
    echo "</div>"; // Close row container
  echo "</div>";

  echo "<script>";
  echo "var updateValidator = $('#moduleCreateForm').validate({";
    echo "rules:{";
    echo "  moduleTitle: 'required',";
    echo "  moduleContent: 'required',";
    echo "  tokenField: 'required'";
    echo "},";
    echo "messages:{";
    echo "  moduleTitle: 'Please enter a title for the module',";
    echo "  moduleContent: 'Please enter some Content for the Module!',";
    echo "  tokenField: 'Please enter at least one token for the module!'";
    echo "},";
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
