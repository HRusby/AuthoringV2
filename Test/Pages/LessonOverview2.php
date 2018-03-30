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
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css" integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/jquery-confirm/3.3.0/jquery-confirm.min.css">
    <link rel="stylesheet" type="text/css" href="../CSS/LessonOverview.css">
    <link rel="stylesheet" type="text/css" href="../JS/slipTreeTokenField/dist/css/bootstrap-tokenfield.css">
    <link rel="stylesheet" type="text/css" href="../JS/jquery-ui/jquery-ui.css">
    <script>
      $(document).ready(function(){
        $('[data-toggle="tooltip"]').tooltip();
      });

      $(document).on('submit', '#postForm', function(){
        // alert("Submitting");
        // alert("Action:"+$(this).attr('action'));
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
                alert($result);
                alert("Updated!");
            },
            error: function(xhr, textstatus, errorthrown){
              alert('Error\nState: '+xhr.readyState+'\nStatus: '+textstatus+'\nError: '+errorthrown);
            }
        });
        return false;
      });

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
                  // $('.nav-tabs a').click(function(){
                  //     $(this).tab('show');
                  // })
              }
          });
        }
      }

      function addOption($questionCount, $optionCount){
        if($optionCount > <?php echo $maxOptions; ?> ){
          alert("No More Options can be added!");
          return false;
        }else{
          $newOption = $optionCount+64;
          $optionLbl = String.fromCharCode($newOption);
          $("#q"+$questionCount+"Options").append("\
            <div class='input-group col-md-11 ml-5 mt-2' id='q"+$questionCount+"Opt"+$optionCount+"'>\
            <div class='input-group-prepend'>\
            <span class='input-group-text' id='q"+$questionCount+"Opt"+$optionCount+"Lbl'>"+$optionLbl+"</span>\
            <div class='input-group-text'><input type='radio' name='q"+$questionCount+"OptCorrect' id='q"+$questionCount+"Opt"+$optionCount+"RadButton'></div>\
            </div>\
            <input type='text' class='form-control' id='q"+$questionCount+"Opt"+$optionCount+"Value' name='q"+$questionCount+"Opt"+$optionCount+"Value' placeholder='Insert the Option'/>\
            <div class='input-group-append'><button class='btn btn-danger' id='q"+$questionCount+"Opt"+$optionCount+"DeleteButton' onclick='return deleteNewOption("+$questionCount+","+$optionCount+");'><i class='fas fa-trash-alt'></i></button></div>\
            </div>\
          ");
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
            <div class='input-group-append'><button class='btn btn-danger' id='q"+$questionCount+"DeleteButton' onclick='return deleteNewQuestion("+$questionCount+");'><i class='fas fa-trash-alt'></i></button></div>\
          </div>\
          <div class='form-group row w-100' id='q"+$questionCount+"Options'>\
          </div>\
          <div class='col-md-5'></div>\
          <button class='btn btn-success col-md-2' id='q"+$questionCount+"AddOptionButton' onClick='return addOption("+$questionCount+",1);'>Add an Option!</button>\
          <div class='col-md-5'></div>\
          </div>\
        ");
        $("#addQuestionButton").attr("onclick", "return addQuestion("+($questionCount+1)+");");
        return false;
      }

      function deleteOption($questionCount, $optionCount){
        return false;
      }

      function deleteNewOption($questionCount, $optionCount){
        $("#q"+$questionCount+"Opt"+$optionCount).html('');
        $("#q"+$questionCount+"Opt"+$optionCount).remove();
        updateOptionIndexes($questionCount, $optionCount);
        return false;
      }

      function deleteQuestion($questionCount){
        return false;
      }

      function deleteNewQuestion($questionCount){
        $("#q"+$questionCount).html('');
        $("#q"+$questionCount).remove();
        updateQuestionIndexes($questionCount);
        return false;
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
            $part1 = x.substr(0, x.indexOf('(')+1);
            $("#q"+$currentQuestion).find('#q'+$currentQuestion+'DeleteButton').attr('onclick', $part1+$newValue+');');
            $("#q"+$currentQuestion).find('#q'+$currentQuestion+'DeleteButton').attr('id', 'q'+$newValue+'DeleteButton');
            // change id=q1Title name=q1Title
            $("#q"+$currentQuestion).find("#q"+$currentQuestion+'Title').attr('name', 'q'+$newValue+'Title');
            $("#q"+$currentQuestion).find("#q"+$currentQuestion+'Title').attr('id', 'q'+$newValue+'Title');
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
            $("#q"+$currentQuestion+"Opt"+$currentOption).find('#q'+$currentQuestion+'Opt'+$currentOption+'DeleteButton').attr('onclick', $part1+$intendedQuestion+', '+$currentOption+');');
            // Change id: q1Opt1Value name: q1Opt1Value
            $("#q"+$currentQuestion+"Opt"+$currentOption).find('#q'+$currentQuestion+'Opt'+$currentOption+'Value').attr('name', 'q'+$intendedQuestion+'Opt'+$currentOption+'Value');
            $("#q"+$currentQuestion+"Opt"+$currentOption).find('#q'+$currentQuestion+'Opt'+$currentOption+'Value').attr('id', 'q'+$intendedQuestion+'Opt'+$currentOption+'Value');
            // Change q1Opt1RadButton
            $("#q"+$currentQuestion+"Opt"+$currentOption).find('#q'+$currentQuestion+'Opt'+$currentOption+'RadButton').attr('id', 'q'+$intendedQuestion+'Opt'+$currentOption+'RadButton');
            // Change q1Opt1Lbl Value as well
            $("#q"+$currentQuestion+"Opt"+$currentOption).find('#q'+$currentQuestion+'Opt'+$currentOption+'Lbl').html(String.fromCharCode($currentOption+64));
            $("#q"+$currentQuestion+"Opt"+$currentOption).find('#q'+$currentQuestion+'Opt'+$currentOption+'Lbl').attr('id', 'q'+$intendedQuestion+'Opt'+$currentOption+'Lbl');
            // change q1Opt1
            $("#q"+$currentQuestion+"Opt"+$currentOption).attr('id', 'q'+$intendedQuestion+'Opt'+$currentOption);
            $currentOption++;

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
            $part1 = x.substr(0, x.indexOf(',')+1);
            $("#q"+$questionCount+"Opt"+$currentOption).find('#q'+$questionCount+'Opt'+$currentOption+'DeleteButton').attr('onclick', $part1+$newValue+');');
            // Change id: q1Opt1Value name: q1Opt1Value
            $("#q"+$questionCount+"Opt"+$currentOption).find('#q'+$questionCount+'Opt'+$currentOption+'Value').attr('name', 'q'+$questionCount+'Opt'+$newValue+'Value');
            $("#q"+$questionCount+"Opt"+$currentOption).find('#q'+$questionCount+'Opt'+$currentOption+'Value').attr('id', 'q'+$questionCount+'Opt'+$newValue+'Value');
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
    <title>LessonOverview2</title>
  </head>
  <?php
    function printTopics($root, $level, $parentID){
      $db = new mysqli("localhost", "root", "topolor", "topolor");
      $db->set_charset("utf8");
      // Need to get parent relations
      if($parentID == $root){
          $topicIDQuery = "SELECT * FROM tpl_tutorial_topic WHERE root = ? AND level = ?;";
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
          $topicIDQuery = "SELECT * FROM tpl_tutorial_topic WHERE root = ? AND level = ? AND lft > ? AND rgt < ?;";
          $topicIDStmt = $db->stmt_init();
          $topicIDStmt->prepare($topicIDQuery);
          $topicIDStmt->bind_param("iiii", $root, $level, $plft, $prgt);
          $topicIDStmt->execute();
          $IDResult = $topicIDStmt->get_result();
      }
      // Find the set of topic IDs
      while($id = $IDResult->fetch_assoc()){

        $topicDataQuery = "SELECT * FROM `tpl_post` WHERE `id` = ?;";
        $topicDataStmt = $db->stmt_init();
        $topicDataStmt->prepare($topicDataQuery);
        $topicDataStmt->bind_param("i", $id['topic_id']);
        $topicDataStmt->execute();
        $topicResult = $topicDataStmt->get_result();

        while($topic = $topicResult->fetch_assoc()){
          $topicTitle = str_replace(" ", "",$topic['title']);

          echo "<div class='row ml-3 my-2 w-100' id='".$topicTitle."Row'>";
          echo "<button data-toggle='collapse' data-target='#".$topicTitle."TopicList' aria-expanded='false'  class = 'collapseButton pull-left'><i class='fas fa-angle-right'></i></button>";
          echo "<div class='w-75 bg-dark clickable' onclick='loadTopic(".$topic['id'].", ".$topic['category'].")'>".strip_tags($topic['title'])."</div>";
          echo "</div>"; // Close module row Div
          echo "<div class='row collapse ml-3' id='".$topicTitle."TopicList' aria-expanded='false'>";
          printTopics($root, $level+1, $topic['id']); // Prints out all of the modules topics.
          // Pass' the current ID, the level it's looking at (second level) and the parent ID as this is top level the parent ID is it's own ID
          echo "</div>"; // close moduleTitleTopicList Div.
        }
      }
      $db->close();
    }
  ?>

  <body>
    <nav class="navbar navbar-dark bg-dark fixed-top border-bottom border-light">
      <span class="navbar-brand mb-0 h1">Topolor</span>
    </nav>
    <div class='container-fluid' id='authoringContainer'>
      <div class='row' id='bodyContainer'>
        <div class='col-md-3 h-100 px-1 border-right border-dark bg-light position-fixed' id ='listView'>
          <?php
            $db = new mysqli("localhost", "root", "topolor", "topolor");
            $db->set_charset("utf8");
            $ModulesQuery = "SELECT * FROM tpl_post WHERE user_id=? AND category=0;";
            $ModulesStmt = $db->stmt_init();
            $ModulesStmt->prepare($ModulesQuery);
            $ModulesStmt->bind_param("i", $userid);
            $ModulesStmt->execute();
            $ModulesResult=$ModulesStmt->get_result();
            $moduleCount = 0;
            while($module = $ModulesResult->fetch_assoc()){
              // Create a row for this module, expand to load in all subtopics
              $moduleTitle = str_replace(" ", "",$module['title']);
              echo "<div class='row ml-3 my-2' id='module".$moduleCount."'>";
                echo "<button data-toggle='collapse' data-target='#".$moduleTitle."TopicList' aria-expanded='false'  class = 'collapseButton pull-left'><i class='fas fa-angle-right'></i></button>";
                echo "<div class='w-75 bg-dark clickable' onclick='loadTopic(".$module['id'].", ".$module['category'].")'>".strip_tags($module['title'])."</div>";
              echo "</div>"; // Close module row Div
              echo "<div class='row collapse ml-3' id='".$moduleTitle."TopicList' aria-expanded='false'>";
              printTopics($module['id'], 2, $module['id']); // Prints out all of the modules topics.
              // Pass' the current ID, the level it's looking at (second level) and the parent ID as this is top level the parent ID is it's own ID
              echo "</div>"; // close moduleTitleTopicList Div.
              $moduleCount++;
            }
            $db->close();
          ?>
        </div>

        <div class='col-md-9 offset-md-3' id='actionPanel'>

        </div>
      </div>
    </div>
  </body>
</html>
