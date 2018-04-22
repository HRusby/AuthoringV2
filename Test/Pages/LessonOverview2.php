<!doctype html>
<html lang="en">
  <head>
    <?php
        session_start();
        ini_set("default_charset", 'utf-8');
        if(!isset($_SESSION['user_id'])){
            header('location:./login.php');
        }else{
            $userid=$_SESSION['user_id'];
        }
        $maxOptions = 6;
    ?>

    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <!-- Bootstrap CSS -->
    <script src="../JS/jquery-3.3.1.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.9/umd/popper.min.js" integrity="sha384-ApNbgh9B+Y1QKtv3Rn7W3mgPxhU9K/ScQsAP7hUibX39j7fakFPskvXusvfa0b4Q" crossorigin="anonymous"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js" integrity="sha384-JZR6Spejh4U02d8jOt6vLEHfe/JQGiRRSQQxSfFWpi1MquVdAyjUar5+76PVCmYl" crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-confirm/3.3.0/jquery-confirm.min.js"></script>
    <script defer src="https://use.fontawesome.com/releases/v5.0.9/js/all.js" integrity="sha384-8iPTk2s/jMVj81dnzb/iFR2sdA7u06vHJyyLlAd4snFpCl/SnyUjRrbdJsw1pGIl" crossorigin="anonymous"></script>
    <script src="../JS/slipTreeTokenField/dist/bootstrap-tokenfield.js"></script>
    <script src="../JS/jquery-ui/jquery-ui.js"></script>
    <script type='text/javascript' src='../JS/jquery-validation-1.17.0/dist/jquery.validate.js'></script>
    <link href="http://cdnjs.cloudflare.com/ajax/libs/summernote/0.8.9/summernote.css" rel="stylesheet">
    <script src="http://cdnjs.cloudflare.com/ajax/libs/summernote/0.8.9/summernote.js"></script>

    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css" integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/jquery-confirm/3.3.0/jquery-confirm.min.css">

    <link rel="stylesheet" type="text/css" href="../JS/slipTreeTokenField/dist/css/bootstrap-tokenfield.css">
    <link rel="stylesheet" type="text/css" href="../JS/jquery-ui/jquery-ui.css">

    <link rel="stylesheet" type="text/css" href="../CSS/LessonOverview.css">
    <script>
      $(document).ready(function(){
        $('[data-toggle="tooltip"]').tooltip();
      });

      $(document).on('submit', '#moduleUpdateForm', function(){
        $newModuleTitle = $(this).find('#moduleTitle').val();
        $newModuleContent = $(this).find('#moduleContent').val();
        $tags = $(this).find('#tokenField').val();
        $dataString = 'moduleTitle='+$newModuleTitle+'&moduleContent='+$newModuleContent+'&tags='+$tags;
        // alert($dataString);
        $.ajax({
            url:$(this).attr('action'),
            type:$(this).attr('method'),
            data: $dataString,
            success:function($result){
                // alert($result);
                $.alert("Module Updated!");
                // $('#actionPanel').html('');
            },
            error: function(xhr, textstatus, errorthrown){
              alert('Error\nState: '+xhr.readyState+'\nStatus: '+textstatus+'\nError: '+errorthrown);
            }
        });
        return false;
      });

      $(document).on('submit', '#topicUpdateForm', function(){
        $newTopicTitle = $(this).find('#topicTitle').val();
        $newTopicContent = $(this).find('#topicContent').val();
        $tags = $(this).find('#tokenField').val();
        if($(this).find("#optional").is(":checked")){
          $optional = 1;
        }else{
          $optional = 0;
        }
        $prerequisite = $(this).find('#prereqs').val();
        $dataString = 'topicTitle='+$newTopicTitle+'&topicContent='+$newTopicContent+'&tags='+$tags+'&prereqs='+$prerequisite+'&optional='+$optional;
        // alert($dataString);
        $.ajax({
            url:$(this).attr('action'),
            type:$(this).attr('method'),
            data: $dataString,
            success:function($result){
                // alert($result);
                $.alert("Updated Topic!");
            },
            error: function(xhr, textstatus, errorthrown){
              alert('Error\nState: '+xhr.readyState+'\nStatus: '+textstatus+'\nError: '+errorthrown);
            }
        });
        return false;
      });

      $(document).on('submit', '#topicCreateForm', function(){
        $topicTitle = $(this).find('#topicTitle').val();
        $newTopicContent = $(this).find('#topicContent').val();
        $tags = $(this).find('#tokenField').val();
        $parentID = $(this).find('#parentID').val();
        $parentTitle = $(this).find('#parentTitle').val();
        $root = $(this).find('#moduleID').val();
        if($(this).find("#optional").is(":checked")){
          $optional = 1;
        }else{
          $optional = 0;
        }
        $prerequisite = $(this).find('#prereqs').val();
        $dataString = "topicTitle="+$topicTitle+"&topicContent="+$newTopicContent+"&tags="+$tags+"&prereqs="+$prerequisite+"&optional="+$optional+"&parentID="+$parentID+"&root="+$root;
        // alert($dataString);
        $('#actionPanel').html('');
        $.ajax({
            url:$(this).attr('action'),
            type:$(this).attr('method'),
            data: $dataString,
            success:function($result){
                $.alert("New Topic Saved!");
                // $.alert($result);
                $.ajax({
                  url:"../PHP/loadNewTopic.php",
                  type:'POST',
                  data: 'id='+$result+'&root='+$root+'&parentID='+$parentID+'&topicCount='+$('.topics'+$parentID).length,
                  success: function($topicDisplay){
                    $('#'+$parentTitle+'TopicList').append($topicDisplay);
                    loadTopic($result, 1);
                  },
                  error: function(xhr, textstatus, errorthrown){
                    alert('Error\nState: '+xhr.readyState+'\nStatus: '+textstatus+'\nError: '+errorthrown);
                  }
                });
            },
            error: function(xhr, textstatus, errorthrown){
              alert('Error\nState: '+xhr.readyState+'\nStatus: '+textstatus+'\nError: '+errorthrown);
            }
        });
        return false;
      });

      $(document).on('submit', '#moduleCreateForm', function(){
        $newModuleTitle = $(this).find('#moduleTitle').val();
        $newModuleContent = $(this).find('#moduleContent').val();
        $tags = $(this).find('#tokenField').val();
        $dataString = 'moduleTitle='+$newModuleTitle+'&moduleContent='+$newModuleContent+'&tags='+$tags;
        $('#actionPanel').html('');
        // Empty panel to prevent user from spamming the create button
        $.ajax({
            url:$(this).attr('action'),
            type:$(this).attr('method'),
            data: $dataString,
            success:function($result){
                $.alert("Module Created!");
                loadTopic($result, 0);
                // Empty the actionPanel
                // alert($result);
                $.ajax({
                  url:"../PHP/loadNewModule.php",
                  type:'POST',
                  data: 'id='+$result,
                  success: function($topicDisplay){
                    $('#allModules').append($topicDisplay);
                  },
                  error: function(xhr, textstatus, errorthrown){
                    alert('Error\nState: '+xhr.readyState+'\nStatus: '+textstatus+'\nError: '+errorthrown);
                  }
                });
            },
            error: function(xhr, textstatus, errorthrown){
              alert('Error\nState: '+xhr.readyState+'\nStatus: '+textstatus+'\nError: '+errorthrown);
            }
        });

        return false;
      });

      $(document).on('submit', '#quizForm', function(){
        // $.alert('Submitting Quiz');
        $.ajax({
            url:$(this).attr('action'),
            type:$(this).attr('method'),
            data: "x=0&"+$(this).serialize()+"&y=0",
            success:function($result){
                $.alert("Quiz Saved!");
                // $.alert($result);

            },
            error: function(xhr, textstatus, errorthrown){
              alert('Error\nState: '+xhr.readyState+'\nStatus: '+textstatus+'\nError: '+errorthrown);
            }
        });

        return false;
      });

      function moveTopicUp($currentIndex, $intendedIndex, $parentID){
        if($intendedIndex == -1){
          $.alert("Topic cannot be moved any further up!");
          return;
        }else if($intendedIndex == ($('.topics'+$parentID).length)){
          $.alert("Topic cannot be moved any further down!");
          return;
        }
        // $.alert('In Move Topic\nTopics Length: '+$('.topics'+$parentID).length);
        $movingTopic = $($('.topics'+$parentID).get($currentIndex)); // The topic the user wants to move
        $movingTopicList = $($('.topicLists'+$parentID).get($currentIndex));
        $movingID = $movingTopic.find('.listTopicID'+$parentID).val();
        // $.alert('Moving ID: '+$movingID);
        $positionTopic = $($('.topics'+$parentID).get($intendedIndex)); // The topic in the position the user wants to move to
        $positionTopicList = $($('.topicLists'+$parentID).get($intendedIndex));
        $positionID = $positionTopic.find('.listTopicID'+$parentID).val();
        // $.alert('position ID: '+$positionID);

        $movingTopic.insertBefore($positionTopic);
        $positionTopicList.insertAfter($positionTopic);
        $movingTopicList.insertAfter($movingTopic);
        // Change button onClick
        $movingTopic.find('.moveUpBtn').attr('onclick', 'moveTopicUp('+($currentIndex-1)+','+($intendedIndex-1)+','+$parentID+');');
        $positionTopic.find('.moveUpBtn').attr('onclick', 'moveTopicUp('+($currentIndex)+','+($intendedIndex)+','+$parentID+');');

        $movingTopic.find('.moveDownBtn').attr('onclick', 'moveTopicDown('+($currentIndex-1)+','+($currentIndex)+','+$parentID+');');
        $positionTopic.find('.moveDownBtn').attr('onclick', 'moveTopicDown('+($currentIndex)+','+($currentIndex+1)+','+$parentID+');');
        // AJAX request to update lft and rgt values

        $.ajax({
            url:'../PHP/movePositionsUp.php',
            type:'POST',
            data: 'moveID='+$movingID+'&positionID='+$positionID,
            success:function($result){
              // $.alert($result);
                $.alert("Positions Saved!");
            },
            error: function(xhr, textstatus, errorthrown){
              alert('Error\nState: '+xhr.readyState+'\nStatus: '+textstatus+'\nError: '+errorthrown);
            }
        });

      }

      function moveTopicDown($currentIndex, $intendedIndex, $parentID){
        if($intendedIndex == -1){
          $.alert("Topic cannot be moved any further up!");
          return;
        }else if($intendedIndex == ($('.topics'+$parentID).length)){
          $.alert("Topic cannot be moved any further down!");
          return;
        }
        // $.alert('In Move Topic\nTopics Length: '+$('.topics'+$parentID).length);
        $movingTopic = $($('.topics'+$parentID).get($currentIndex)); // The topic the user wants to move
        $movingTopicList = $($('.topicLists'+$parentID).get($currentIndex));
        $movingID = $movingTopic.find('.listTopicID'+$parentID).val();
        // $.alert('Moving ID: '+$movingID);
        $positionTopic = $($('.topics'+$parentID).get($intendedIndex)); // The topic in the position the user wants to move to
        $positionTopicList = $($('.topicLists'+$parentID).get($intendedIndex));
        $positionID = $positionTopic.find('.listTopicID'+$parentID).val();
        // $.alert('position ID: '+$positionID);

        $movingTopic.insertAfter($positionTopic);
        $positionTopicList.insertAfter($positionTopic);
        $movingTopicList.insertAfter($movingTopic);
        // Change button onClick
        $movingTopic.find('.moveDownBtn').attr('onclick', 'moveTopicDown('+($currentIndex+1)+','+($intendedIndex+1)+','+$parentID+');');
        $positionTopic.find('.moveDownBtn').attr('onclick', 'moveTopicDown('+($currentIndex)+','+($intendedIndex)+','+$parentID+');');

        $movingTopic.find('.moveUpBtn').attr('onclick', 'moveTopicUp('+($currentIndex+1)+','+($currentIndex)+','+$parentID+');');
        $positionTopic.find('.moveUpBtn').attr('onclick', 'moveTopicUp('+($currentIndex)+','+($currentIndex-1)+','+$parentID+');');

        // AJAX request to update lft and rgt values
        $.ajax({
            url:'../PHP/movePositionsDown.php',
            type:'POST',
            data: 'moveID='+$movingID+'&positionID='+$positionID,
            success:function($result){
              // $.alert($result);
                $.alert("Positions Saved!");
            },
            error: function(xhr, textstatus, errorthrown){
              alert('Error\nState: '+xhr.readyState+'\nStatus: '+textstatus+'\nError: '+errorthrown);
            }
        });

      }

      function loadTopic($id, $category){
        if($category == 0){
          // Load module
          $.ajax({
              url:"../PHP/displayModule.php?m="+$id,
              success:function($result){
                  $("#actionPanel").html($result);
              }
          });
        }else if($category == 1){
          // Load Topic/Sub-Topic
          $.ajax({
              url:"../PHP/displayTopic.php?t="+$id,
              success:function($result){
                  $("#actionPanel").html($result);
              }
          });
        }
      }

      function addModule(){
        $.ajax({
            url:"../PHP/moduleTemplate.php",
            success:function($result){
                $("#actionPanel").html($result);
            },
            error: function(xhr, textstatus, errorthrown){
              alert('Error\nState: '+xhr.readyState+'\nStatus: '+textstatus+'\nError: '+errorthrown);
            }
        });
      }

      function addTopic($parentID, $parentTitle, $moduleID, $parentCount){
        $dataString = 'parentID='+$parentID+'&parentTitle='+$parentTitle+'&moduleID='+$moduleID;
        $.ajax({
            url:"../PHP/topicTemplate.php",
            type: 'POST',
            data: $dataString,
            success:function($result){
                $("#actionPanel").html($result);
                $('#topicCreateForm').validate({
                  rules:{
                    topicTitle: 'required',
                    topicContent: 'required',
                    tokenField: 'required'
                  },
                  messages:{
                    topicTitle: 'Please enter a title for the module',
                    topicContent: 'Please enter some Content for the Module!',
                    tokenField: 'Please enter at least one token for the module!'
                  },
                  errorPlacement: function(error, element){
  										if(element.attr('id') == 'tokenField'){
  												error.insertAfter(element.parent());
  										}else{
  												error.insertAfter(element.parent());
  										}
  								}
                });
            },
            error: function(xhr, textstatus, errorthrown){
              alert('Error\nState: '+xhr.readyState+'\nStatus: '+textstatus+'\nError: '+errorthrown);
            }
        });
      }

      function deleteTopic($topicID, $topicTitle, $moduleID){
        $dataString='id='+$topicID;
        $('#actionPanel').html('');
        $.ajax({
            url:"../PHP/deletePost.php",
            type: 'POST',
            data: $dataString,
            success:function($result){
                $("#"+$topicTitle+"Row").html('');
                $("#"+$topicTitle+"Row").remove();
                $("#"+$topicTitle+"TopicList").html('');
                $("#"+$topicTitle+"TopicList").remove();
                $.alert("Topic Deleted!");
            },
            error: function(xhr, textstatus, errorthrown){
              alert('Error\nState: '+xhr.readyState+'\nStatus: '+textstatus+'\nError: '+errorthrown);
            }
        });
      }

      function deleteModule($moduleID, $moduleTitle){
        $dataString='id='+$moduleID;
        $('#actionPanel').html('');
        $.ajax({
            url:"../PHP/deletePost.php",
            type: 'POST',
            data: $dataString,
            success:function($result){
                $('#listView').find("#"+$moduleTitle+"Row").html('');
                $('#listView').find("#"+$moduleTitle+"Row").remove();
                $('#listView').find("#"+$moduleTitle+"TopicList").html('');
                $('#listView').find("#"+$moduleTitle+"TopicList").remove();
                $.alert("Module Deleted!");
            },
            error: function(xhr, textstatus, errorthrown){
              alert('Error\nState: '+xhr.readyState+'\nStatus: '+textstatus+'\nError: '+errorthrown);
            }
        });
      }

      function addOption($questionCount, $optionCount){
        if($optionCount > <?php echo $maxOptions; ?> ){
          $.alert("No More Options can be added!");
          return false;
        }else{
          $newOption = $optionCount+64;
          $optionLbl = String.fromCharCode($newOption);
          $("#q"+$questionCount+"Options").append("\
            <div class='input-group col-md-11 ml-5 mt-2' id='q"+$questionCount+"Opt"+$optionCount+"'>\
            <div class='input-group-prepend'>\
            <span class='input-group-text' id='q"+$questionCount+"Opt"+$optionCount+"Lbl'>"+$optionLbl+"</span>\
            <div class='input-group-text'><input type='radio' name='q"+$questionCount+"OptCorrect' id='q"+$questionCount+"Opt"+$optionCount+"RadButton' value='"+$optionLbl+"'></div>\
            </div>\
            <input type='text' class='form-control' id='q"+$questionCount+"Opt"+$optionCount+"Value' name='q"+$questionCount+"Opt"+$optionCount+"Value' placeholder='Insert the Option'/>\
            <div class='input-group-append'><button type='button' class='btn btn-sm btn-danger' id='q"+$questionCount+"Opt"+$optionCount+"DeleteButton' \
            onclick=\'$.confirm({title: \"Are You Sure?\", content: \"Deletion can not been undone\", buttons: {Yes: function() {deleteNewOption("+$questionCount+","+$optionCount+");}, No: function(){}}});'>\
            <i class='fas fa-trash-alt'></i></button></div>\
            </div>\
          ");
          $("#q"+$questionCount+"Opt"+$optionCount+"Value").rules("add", {required: true, messages: {required: "Please enter an option, or delete the option!"}});
          $("#q"+$questionCount+"OptCorrect").rules("add", {required: true, messages: {required: "Please Select a correct answer for the option!"}});
          $("#q"+$questionCount+"AddOptionButton").attr("onclick", "return addOption("+$questionCount+", "+($optionCount + 1)+");");
          return false;
        }

      }

      function addQuestion($questionCount){
        $("#allQuestions").append("\
        <div class='form-group row w-100' id='q"+$questionCount+"'>\
          <div class='input-group col-md-12'>\
            <div class='input-group-prepend'><span class='input-group-text' id='q"+$questionCount+"Lbl'>"+$questionCount+".</span></div>\
            <input type='text' class='form-control' id='q"+$questionCount+"Title' name='q"+$questionCount+"Title' placeholder='Enter the Question!'/>\
            <div class='input-group-append'><button type='button' class='btn btn-danger' id='q"+$questionCount+"DeleteButton' \
            onclick=\'$.confirm({title: \"Are You Sure?\", content: \"Deletion can not been undone\", buttons: {confirm: function() {deleteNewQuestion("+$questionCount+");}, cancel: function(){ $.alert(\"Cancelled\");}}});'>\
            <i class='fas fa-trash-alt'></i></button></div>\
          </div>\
          <div class='form-group row w-100' id='q"+$questionCount+"Options'>\
          </div>\
          <div class='col-md-5'></div>\
          <button class='btn btn-success col-md-2' id='q"+$questionCount+"AddOptionButton' onClick='return addOption("+$questionCount+",1);'>Add an Option!</button>\
          <div class='col-md-5'></div>\
          </div>\
        ");
        $("#q"+$questionCount+"Title").rules("add", {required: true, messages: {required: "Please enter a question, or delete it!"}});
        $("#addQuestionButton").attr("onclick", "return addQuestion("+($questionCount+1)+");");
        return false;
      }

      function deleteOption($questionCount, $optionCount){

        $optionID = $("#q"+$questionCount+"Opt"+$optionCount).find("#q"+$questionCount+"Opt"+$optionCount+"ID").attr('value');
        // alert("OptionID: "+$optionID);
        // run delete script via AJAX
        $.ajax({
            url:"../PHP/deleteExistingOption.php?o="+$optionID,
            success:function($result){
                $.alert("Option has been successfully deleted!");
                // alert($result);
                $("#q"+$questionCount+"Opt"+$optionCount).html('');
                $("#q"+$questionCount+"Opt"+$optionCount).remove();
                updateOptionIndexes($questionCount, $optionCount);
                return;
            },
            error: function(xhr, textstatus, errorthrown){
              alert('Error\nState: '+xhr.readyState+'\nStatus: '+textstatus+'\nError: '+errorthrown);
              return;
            }
        });

        return;
      }

      function deleteNewOption($questionCount, $optionCount){
        $("#q"+$questionCount+"Opt"+$optionCount).html('');
        $("#q"+$questionCount+"Opt"+$optionCount).remove();
        updateOptionIndexes($questionCount, $optionCount);
        $.alert('Option has been successfully Deleted');
        return;
      }

      function deleteQuestion($questionCount){

        $questionID = $("#q"+$questionCount).find("#q"+$questionCount+"ID").attr('value');
        // alert("questionID: "+$questionID);
        // run delete script via AJAX
        $.ajax({
            url:"../PHP/deleteExistingQuestion.php?q="+$questionID,
            success:function($result){
                $.alert("Question has been successfully deleted!");
                // alert($result);
                $("#q"+$questionCount).html('');
                $("#q"+$questionCount).remove();
                updateQuestionIndexes($questionCount);
                return;
            },
            error: function(xhr, textstatus, errorthrown){
              alert('Error\nState: '+xhr.readyState+'\nStatus: '+textstatus+'\nError: '+errorthrown);
              return;
            }
        });
        return;
      }

      function deleteNewQuestion($questionCount){
        $("#q"+$questionCount).html('');
        $("#q"+$questionCount).remove();
        updateQuestionIndexes($questionCount);
        $.alert("Question has been successfully deleted!");
        return;
      }

      function updateQuestionIndexes($questionCount){
        // $questionCount is the question that was deleted
        $questionsExist = true;
        $newValue = $questionCount; // First id becomes the deleted ID after which $newValue is incremented
        $currentQuestion = $questionCount+1;
        while($questionsExist){
          if($('#q'+$currentQuestion).length){
            // change every value in q1Options
            updateOptionQuestion($currentQuestion, $newValue);

            var y = $("#q"+$currentQuestion).find('#q'+$currentQuestion+'AddOptionButton').attr('onclick');
            $part2 = y.substr(y.indexOf($currentQuestion)+1);
            $("#q"+$currentQuestion+"AddOptionButton").attr("onclick", "return addOption("+$newValue+$part2);
            $("#q"+$currentQuestion+"AddOptionButton").attr("id", "q"+$newValue+"AddOptionButton");
            // change id=q1Options
            $("#q"+$currentQuestion).find("#q"+$currentQuestion+'Options').attr('id', 'q'+$newValue+'Options');
            // change id=q1DeleteButton onclick=return deleteQuestion(1);
            var x = $("#q"+$currentQuestion).find('#q'+$currentQuestion+'DeleteButton').attr('onclick');
            $part1 = x.substr(0, x.indexOf('deleteQuestion(')+15);
            $part2 = x.substr(x.indexOf(');')+2);
            $("#q"+$currentQuestion).find('#q'+$currentQuestion+'DeleteButton').attr('onclick', $part1+$newValue+');'+$part2);
            $("#q"+$currentQuestion).find('#q'+$currentQuestion+'DeleteButton').attr('id', 'q'+$newValue+'DeleteButton');
            // change id=q1Title name=q1Title
            $("#q"+$currentQuestion).find("#q"+$currentQuestion+'Title').attr('name', 'q'+$newValue+'Title');
            $("#q"+$currentQuestion).find("#q"+$currentQuestion+'Title').attr('id', 'q'+$newValue+'Title');
            // change q1ID
            $("#q"+$currentQuestion).find("#q"+$currentQuestion+"ID").attr('name', 'q'+$newValue+'Title');
            $("#q"+$currentQuestion).find("#q"+$currentQuestion+"ID").attr('id', 'q'+$newValue+'Title');
            // change id=q1Lbl and value
            $("#q"+$currentQuestion).find("#q"+$currentQuestion+'Lbl').html($newValue);
            $("#q"+$currentQuestion).find("#q"+$currentQuestion+'Lbl').attr('id', 'q'+$newValue+'Lbl');
            // change id=q1
            $("#q"+$currentQuestion).attr('id', 'q'+$newValue);

            $newValue++;
            $currentQuestion++;
          }else{
            $questionsExist=false;
          }
          // change addNewQuestionButton
          $("#addQuestionButton").attr("onclick", "return addQuestion("+($newValue)+");");
        }
      }

      function updateOptionQuestion($currentQuestion, $intendedQuestion){
        $optionsExist = true;
        $currentOption = 1;
        while($optionsExist){
          if($("#q"+$currentQuestion+"Opt"+$currentOption).length){
            // Change id: q1Opt1DeleteButton onClick = return deleteOption(1,1);
            var x = $("#q"+$currentQuestion+"Opt"+$currentOption).find('#q'+$currentQuestion+'Opt'+$currentOption+'DeleteButton').attr('onclick');
            $part1 = x.substr(0, x.indexOf($currentQuestion));
            $part2 = x.substr(x.indexOf(');')+2);
            $("#q"+$currentQuestion+"Opt"+$currentOption).find('#q'+$currentQuestion+'Opt'+$currentOption+'DeleteButton').attr('onclick', $part1+$intendedQuestion+', '+$currentOption+');'+$part2);
            $("#q"+$currentQuestion+"Opt"+$currentOption).find('#q'+$currentQuestion+'Opt'+$currentOption+'DeleteButton').attr('id', 'q'+$intendedQuestion+'Opt'+$currentOption+'DeleteButton');
            // Change id: q1Opt1Value name: q1Opt1Value
            $("#q"+$currentQuestion+"Opt"+$currentOption).find('#q'+$currentQuestion+'Opt'+$currentOption+'Value').attr('name', 'q'+$intendedQuestion+'Opt'+$currentOption+'Value');
            $("#q"+$currentQuestion+"Opt"+$currentOption).find('#q'+$currentQuestion+'Opt'+$currentOption+'Value').attr('id', 'q'+$intendedQuestion+'Opt'+$currentOption+'Value');
            // Update q1Opt1ID
            $("#q"+$currentQuestion+"Opt"+$currentOption).find('#q'+$currentQuestion+'Opt'+$currentOption+'ID').attr('name', 'q'+$intendedQuestion+'Opt'+$currentOption+'ID');
            $("#q"+$currentQuestion+"Opt"+$currentOption).find('#q'+$currentQuestion+'Opt'+$currentOption+'ID').attr('id', 'q'+$intendedQuestion+'Opt'+$currentOption+'ID');
            // Change q1Opt1RadButton
            $("#q"+$currentQuestion+"Opt"+$currentOption).find('#q'+$currentQuestion+'Opt'+$currentOption+'RadButton').attr('id', 'q'+$intendedQuestion+'Opt'+$currentOption+'RadButton');
            // Change q1Opt1Lbl Value as well
            $("#q"+$currentQuestion+"Opt"+$currentOption).find('#q'+$currentQuestion+'Opt'+$currentOption+'Lbl').html(String.fromCharCode($currentOption+64));
            $("#q"+$currentQuestion+"Opt"+$currentOption).find('#q'+$currentQuestion+'Opt'+$currentOption+'Lbl').attr('id', 'q'+$intendedQuestion+'Opt'+$currentOption+'Lbl');
            // change q1Opt1
            $("#q"+$currentQuestion+"Opt"+$currentOption).attr('id', 'q'+$intendedQuestion+'Opt'+$currentOption);
            $currentOption++;
            // q".$questionCount."Opt".$optionCount."ID

          }else{
            $optionsExist = false;
          }
        }
      }

      function updateOptionIndexes($questionCount, $optionCount){
        // $questionCount = The question whose options are being updated, $optionCount = the option that was deleted
        $optionsExist = true;
        $newValue = $optionCount; // First id becomes the deleted ID after which $newValue is incremented
        $currentOption = $optionCount+1;
        while($optionsExist){
          if($("#q"+$questionCount+"Opt"+$currentOption).length){
            // Change id: q1Opt1DeleteButton onClick = return deleteOption(1,1);
            var x = $("#q"+$questionCount+"Opt"+$currentOption).find('#q'+$questionCount+'Opt'+$currentOption+'DeleteButton').attr('onclick');
            $part1 = x.substr(0, x.indexOf('deleteOption('+$questionCount+',')+15);
            $part2 = x.substr(x.indexOf(');')+2);
            $("#q"+$questionCount+"Opt"+$currentOption).find('#q'+$questionCount+'Opt'+$currentOption+'DeleteButton').attr('onclick', $part1+$newValue+');'+$part2);
            $("#q"+$questionCount+"Opt"+$currentOption).find('#q'+$questionCount+'Opt'+$currentOption+'DeleteButton').attr('id', 'q'+$questionCount+'Opt'+$newValue+'DeleteButton');
            // Change id: q1Opt1Value name: q1Opt1Value
            $("#q"+$questionCount+"Opt"+$currentOption).find('#q'+$questionCount+'Opt'+$currentOption+'Value').attr('name', 'q'+$questionCount+'Opt'+$newValue+'Value');
            $("#q"+$questionCount+"Opt"+$currentOption).find('#q'+$questionCount+'Opt'+$currentOption+'Value').attr('id', 'q'+$questionCount+'Opt'+$newValue+'Value');
            // Update q1Opt1ID
            $("#q"+$questionCount+"Opt"+$currentOption).find('#q'+$questionCount+'Opt'+$currentOption+'ID').attr('name', 'q'+$questionCount+'Opt'+$newValue+'ID');
            $("#q"+$questionCount+"Opt"+$currentOption).find('#q'+$questionCount+'Opt'+$currentOption+'ID').attr('id', 'q'+$questionCount+'Opt'+$newValue+'ID');
            // Change q1Opt1RadButton
            $("#q"+$questionCount+"Opt"+$currentOption).find('#q'+$questionCount+'Opt'+$currentOption+'RadButton').attr('id', 'q'+$questionCount+'Opt'+$newValue+'RadButton');
            // Change q1Opt1Lbl Value as well
            $("#q"+$questionCount+"Opt"+$currentOption).find('#q'+$questionCount+'Opt'+$currentOption+'Lbl').html(String.fromCharCode($newValue+64));
            $("#q"+$questionCount+"Opt"+$currentOption).find('#q'+$questionCount+'Opt'+$currentOption+'Lbl').attr('id', 'q'+$questionCount+'Opt'+$newValue+'Lbl')
            // change q1Opt1
            $("#q"+$questionCount+"Opt"+$currentOption).attr('id', 'q'+$questionCount+'Opt'+$newValue);

            $newValue++;
            $currentOption++;
            $("#q"+$questionCount+"AddOptionButton").attr("onclick", "return addOption("+$questionCount+", "+$newValue+");");
          }else{
            $optionsExist = false;
          }

        }
      }

    </script>
    <title>Topolor Authoring</title>
  </head>
  <body>
    <nav class="navbar bg-light mb-0 fixed-top border-bottom border-light" style='box-shadow:1px 1px 1px grey;'>
      <span class="navbar-brand mb-0 py-1"><a href="../../php/"><img src='../../php/images/logo.png' alt='Topolor'></a></span>
    </nav>
    <div class='container-fluid' id='authoringContainer'>
      <div class='row w-100 h-100' id='bodyContainer'>
        <div class='col-md-3 pb-5 h-100 w-100 border-right border-dark bg-light position-fixed' id ='listView'>
          <script>
            $loadURL = "../PHP/loadListView.php?u="+<?php echo $userid; ?>;
            $.ajax({
                url:$loadURL,
                success:function($result){
                    $("#listView").html($result);
                },
                error: function(xhr, textstatus, errorthrown){
                  alert('Error\nState: '+xhr.readyState+'\nStatus: '+textstatus+'\nError: '+errorthrown);
                }
            });
          </script>

        </div>

        <div class='col-md-9 offset-md-3 w-100' id='actionPanel'>

        </div>
      </div>
    </div>

  </body>
</html>
