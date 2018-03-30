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
