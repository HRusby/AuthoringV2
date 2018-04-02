<?php

  $topicID = $_GET['t'];
  // echo "Module: ".$_GET['m'];
  $db = new mysqli("localhost", "root", "topolor", "topolor");
  $db->set_charset("utf8");

  $postDetailsQuery = "SELECT * FROM tpl_post WHERE id=?;";
  $postDetailsStmt = $db->stmt_init();
  $postDetailsStmt->prepare($postDetailsQuery);
  $postDetailsStmt->bind_param("i", $topicID);
  $postDetailsStmt->execute();
  $topic = $postDetailsStmt->get_result()->fetch_assoc();

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
  // Select this topics relations
  $topicRelationsQuery = "SELECT * FROM tpl_tutorial_topic WHERE topic_id = ?;";
  $topicRelationsStmt = $db->stmt_init();
  $topicRelationsStmt->prepare($topicRelationsQuery);
  $topicRelationsStmt->bind_param("i", $topicID);
  $topicRelationsStmt->execute();
  $topicRelations = $topicRelationsStmt->get_result()->fetch_assoc();
  $topicLFT = $topicRelations['lft'];
  $topicPreReq = $topicRelations['prerequisite'];
  // Select all ids of topics before the current topic.
  $prereqQuery = "SELECT topic_id FROM tpl_tutorial_topic WHERE root=? AND lft<?";
  $prereqStmt = $db->stmt_init();
  $prereqStmt->prepare($prereqQuery);
  $prereqStmt->bind_param("ii", $topicRelations['root'], $topicRelations['lft']);
  $prereqStmt->execute();
  $prereqResult = $prereqStmt->get_result();

  // echo "<div class='row'>";
  echo "<ul class='nav nav-tabs' id='questionContentTabs' role='tablist'>";
  echo "<li class='nav-item'>";
  echo "<a class='nav-link active' id='editTab' data-toggle='tab' href='#edit' role='tab' aria-controls='edit' aria-selected='true'>Edit Content</a>";
  echo "</li>";

  echo "<li class='nav-item'>";
  echo "<a class='nav-link' id='quizTab' data-toggle='tab' href='#quiz' role='tab' aria-controls='quiz' aria-selected='false'>Edit Quiz</a>";
  echo "</li>";
  echo "</ul>";// Close tab list

  echo "<div class='tab-content' id='tabContent'>";
    echo "<div class='tab-pane fade show active' id='edit' role='tabpanel' aria-labelledby='editTab'>";
      echo "<div class='row h-100 justify-content-center align-items-center my-3'>";
        echo "<form class='w-75 bg-white border border-light p-3' method='POST' action='../PHP/updateTopic2.php?t=".$topicID."' id='topicUpdateForm'>";
          echo "<div class='form-group'><label for='topicTitle'>Topic Title:</label><input type='text' class ='form-control bg-light' value='".$topic['title']."' id='topicTitle' name='topicTitle'/></div>";
          echo "<div class='form-group'><label for='topicContent'>Topic Content:</label><textarea class='form-control bg-light' id='topicContent' name='topicContent' style='resize:vertical;'>".$topic['description']."</textarea></div>";
          echo "<div class='form-group'><label for='tokenField'>Tags:</label><input type='text' class='form-control bg-light' id='tokenField' name='tokenField' value='".$topic['tags']."' /></div>";
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
          echo "<div class='btn-group'><button class='btn btn-primary' type='submit'>Update Topic</button></div>";
        echo "</form>";
      echo "</div>"; // Close row container
    echo "</div>"; // Close editTab div

    $quizRules='';
    $quizMessages='';

    echo "<div class='tab-pane fade' id='quiz' role='tabpanel' aria-labelledby='quizTab'>";
      echo "<div class='row h-100 justify-content-center align-items-center my-3'>";
        echo "<form class='w-75 bg-white border border-light p-4' method='POST' action='../PHP/saveQuiz2.php?t=".$topicID."' id='quizForm'>";
          echo "<h2>".$topic['title']."</h2>";
          // Load all questions for this topic
          $topicQuestionQuery = "SELECT * FROM tpl_question WHERE topic_id=?;";
          $topicQuestionStmt = $db->stmt_init();
          $topicQuestionStmt->prepare($topicQuestionQuery);
          $topicQuestionStmt->bind_param("i", $topic['id']);
          $topicQuestionStmt->execute();
          $topicQuestionResult = $topicQuestionStmt->get_result();
          $questionCount = 1;
          echo "<div class = 'row' id='allQuestions'>";
          while($question = $topicQuestionResult->fetch_assoc()){
            $questionOptionQuery = "SELECT * FROM tpl_question_option WHERE question_id = ?;";
            $questionOptionStmt = $db->stmt_init();
            $questionOptionStmt->prepare($questionOptionQuery);
            $questionOptionStmt->bind_param("i", $question['id']);
            $questionOptionStmt->execute();
            $questionOptionResult = $questionOptionStmt->get_result();

            echo "<div class='form-group row w-100' id='q".$questionCount."'>";
              echo "<div class='input-group col-md-12'>";
                // Add a prepended label for the question
                echo "<div class='input-group-prepend'><span class='input-group-text' id='q".$questionCount."Lbl'>".$questionCount.".</span></div>";
                echo "<input type='text' class='form-control' id='q".$questionCount."Title' name='q".$questionCount."Title' value='".$question['description']."' />";
                if($quizRules == ''){
                  $quizRules .= "q".$questionCount."Title: 'required'";
                  $quizMessages .= "q".$questionCount."Title: 'Please enter a title for the question!'";
                }else{
                  $quizRules .= ", q".$questionCount."Title: 'required'";
                  $quizMessages .= ", q".$questionCount."Title: 'Please enter a title for the question!'";
                }
                // Add an appended delete button for the question.
                echo "<div class='input-group-append'><button type='button' class='btn btn-danger' id='q".$questionCount."DeleteButton' ";
                echo "onclick='$.confirm({title: \"Are You Sure?\", content: \"Deletion can not been undone\", buttons: {confirm: function() {return deleteQuestion(".$questionCount.");}, cancel: function(){ $.alert(\"Cancelled\");}}});'";
                echo "><i class='fas fa-trash-alt'></i></button></div>";
                echo "<input type='hidden' id='q".$questionCount."ID' name='q".$questionCount."ID' value='".$question['id']."'/>";
              echo "</div>"; // Close Title Input Group
              echo "<div class='form-group row w-100' id='q".$questionCount."Options'>";
              $optionCount = 1;
                while($option = $questionOptionResult->fetch_assoc()){
                  echo "<div class='input-group col-md-11 col-md-offset-1 ml-5 mt-2' id='q".$questionCount."Opt".$optionCount."'>";
                    echo "<div class='input-group-prepend'>";
                      echo "<span class='input-group-text' id='q".$questionCount."Opt".$optionCount."Lbl'>".$option['opt']."</span>";
                      echo "<div class='input-group-text'><input type='radio' name='q".$questionCount."OptCorrect' id='q".$questionCount."Opt".$optionCount."RadButton' value='".$option['opt']."'";
                      if($question['correct_answer'] == $option['opt']){
                        echo "checked='checked'";
                      }
                      echo "></div>";
                      echo "<input type='hidden' id='q".$questionCount."OptOrder".$optionCount."' name='q".$questionCount."OptOrder".$optionCount."' value='".$option['opt']."'/>";
                    echo "</div>";
                    echo "<input type='text' class='form-control' id='q".$questionCount."Opt".$optionCount."Value' name='q".$questionCount."Opt".$optionCount."Value' value='".$option['val']."' />";

                    $quizRules .= ", q".$questionCount."Opt".$optionCount."Value: 'required'";
                    $quizMessages .= ", q".$questionCount."Opt".$optionCount."Value: 'Please enter a title for the Option!'";
                    // Add validation rule and message for the current option

                    echo "<div class='input-group-append'>";
                      echo "<button type='button' class='btn btn-sm btn-danger' id='q".$questionCount."Opt".$optionCount."DeleteButton' ";
                      echo "onclick='$.confirm({title: \"Are You Sure?\", content: \"Deletion can not been undone\", buttons: {confirm: function() {return deleteOption(".$questionCount.",".$optionCount.");}, cancel: function(){ $.alert(\"Cancelled\");}}});'";
                      echo "><i class='fas fa-trash-alt'></i></button>";
                    echo "</div>";
                    echo "<input type='hidden' id='q".$questionCount."Opt".$optionCount."ID' name='q".$questionCount."Opt".$optionCount."ID' value='".$option['id']."'/>";
                    // echo "<input type='hidden' id='q".$questionCount."Opt".$optionCount."'/>";
                  echo "</div>"; // Close option input group
                  $optionCount++;
                }
                $quizRules .= ", q".$questionCount."OptCorrect: 'required'";
                $quizMessages .= ", q".$questionCount."OptCorrect: 'Please Select a Correct Answer for the Question'";
                // Add validation rule and message for the radiobutton set of this questions options
              echo "</div>"; // Close question Options Div
              echo "<div class='col-md-5'></div>";
              echo "<button type='button' class='btn btn-success col-md-2' id='q".$questionCount."AddOptionButton' onClick='return addOption(".$questionCount.",".$optionCount.");'>Add an Option!</button>";
              echo "<div class='col-md-5'></div>";
            echo "</div>"; // Close form-group row
            $questionCount++;
          }
          echo "</div>"; // Close allQuestions div

          echo "<div class='form-group text-center'>";
            echo "<button class='btn btn-success' type='button' id='addQuestionButton' onclick='return addQuestion(".$questionCount.");'>Add a Question!</button>";
            echo "<button class='btn btn-success' type='submit' id='saveQuizButton'>Save Quiz</button>";
          echo "</div>";
        echo "</form>";
      echo "</div>";
    echo "</div>";
  echo "</div>"; // Close tabContent div

  $editRules="topicTitle: 'required', topicContent: 'required', tokenField: 'required'";
  $editMessages = "topicTitle: 'Please enter a title for the module', topicContent: 'Please enter some Content for the Module!', tokenField: 'Please enter at least one token for the module!'";
  // Validation rules and messages for the edit tab

  echo "<script>";
  echo "var updateValidator = $('#topicUpdateForm').validate({";
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

  echo "$('#quizForm').validate({";
    echo "rules:{".$quizRules."},";
    echo "messages:{".$quizMessages."},";
    echo "errorPlacement: function(error, element){";
    echo "    error.addClass('form-group ml-2');";
    echo "    if(element.attr('id') == 'tokenField'){";
    echo "        error.insertAfter(element.parent());";
    echo "    }else if(element.is('input:radio')){";
    echo "        error.insertAfter(element.parent().parent().parent().parent().parent());";
    echo "    }else{";
    echo "        error.insertAfter(element.parent());";
    echo "    }";
    echo "}";
  echo "});";

  echo "var myElement = $('#topicContent');";
  echo "myElement.summernote({";
    echo "callbacks: {";
      echo "onChange: function(contents, $editable) {";
        echo "myElement.val(myElement.summernote('isEmpty') ? \"\" : contents);";
        echo "updateValidator.element(myElement);";
      echo "}";
    echo "}";
  echo "});";
  echo "</script>";

  $db->close();
?>
