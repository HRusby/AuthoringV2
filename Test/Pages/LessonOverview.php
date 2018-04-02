<?php
    session_start();
    ini_set("default_charset", 'utf-8');
    if(!isset($_SESSION['user_id'])){
        header('location:./login.php');
    }else{
        $userid=$_SESSION['user_id'];
    }
    $moduleWordCount = 200;
    $topicWordCount = 150;
    $subTopicWordCount = 100;
    $firstLevel = 2;
?>
<?php
    function printT($root, $level, $parentID){
        $topicWordCount = 50;
        $subTopicWordCount = 25;
        $firstLevel = 2;
        $db = new mysqli("localhost", "root", "topolor", "topolor");
        $db->set_charset("utf8");
        //Set up a new database connection
       // $fLevel = 2;
        if ($db->connect_error) {
            die("Connection Failure: ".$db->connect_error);
        }

        $parentNameQuery = "SELECT `title` FROM `tpl_post` WHERE id = ?;";
        $parentNameStmt = $db->stmt_init();
        $parentNameStmt->prepare($parentNameQuery);
        $parentNameStmt->bind_param("i", $parentID);
        $parentNameStmt->execute();
        $parentNameResult = $parentNameStmt->get_result();
        while($pName = $parentNameResult->fetch_assoc()){
            $parentName = $pName['title'];
        }
        //Find the parent module/topic name to allow for unique id names

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

        //echo "<button class='btn-minimize' data-value = '".str_replace(" ", "", $parentName)."TopicList' ></button>\n";
        echo "<div id='".str_replace(" ", "", $parentName)."TopicList' aria-expanded='false' class='collapse'>\n";
        while($topicID = $IDResult->fetch_assoc()){

            $topicDataQuery = "SELECT * FROM `tpl_post` WHERE `id` = ?;";
            $topicDataStmt = $db->stmt_init();
            $topicDataStmt->prepare($topicDataQuery);
            $topicDataStmt->bind_param("i", $topicID['topic_id']);
            $topicDataStmt->execute();
            $topicResult = $topicDataStmt->get_result();
            while($topicData = $topicResult->fetch_assoc()){
                echo "<div class='row'>\n";
                echo "<div class ='col-md-1'></div>\n";


                echo "<div class='col-md-10 TopicListLevel tll".$level." outlined'>\n";
                echo "<button data-toggle='collapse' data-target='#".str_replace(" ", "", $topicData['title'])."TopicList' aria-expanded='false'  class = 'collapseButton pull-left'><span class='glyphicon glyphicon-chevron-down'></span></button>";
                echo "<h".$level.">\n";
                $subLevel = $level - $firstLevel;
                for($k=0; $k<$level-2; $k++){
                    echo "Sub-";
                }
                // Print the appropriate sub section name
                echo "Topic\n</h".$level.">\n";

                $titleHLevel = $level + 1;
                echo "<h".$titleHLevel." class ='topicTitleLevel ttl".$level."'>".htmlspecialchars(strip_tags($topicData['title']))."\n";
                echo "<button type = 'button' class='btn-sm btn-danger pull-right' onclick='$.confirm({title: \"Are you sure?\",content: \"test\",buttons: {confirm: function() { location.href=\"../PHP/deletePost.php?m=".$root."&t=".$topicData['id']."\";},cancel: function() { $.alert(\"Cancelled\");}}});'>\n";
                echo "<span class='glyphicon glyphicon-trash'></span>\n";
                echo "</button>\n</h".$titleHLevel.">\n";
                if(strlen($topicData['description'])<$topicWordCount){
                    $tExcerpt = $topicData['description'];
                }else{
                    $tExcerpt = substr($topicData['description'],0,$topicWordCount)."...";
                }
                // Displays the entire description if less than 50 characters else displays a portion of the description
                echo "<p class = 'topicDescriptionLevel tdl".$level."'>".htmlspecialchars(strip_tags($tExcerpt))."</p>\n";
                if($topicID['rgt'] != $topicID['lft']+1){
                    printT($root, $level+1, $topicID['topic_id']);
                }
                $tagHLevel = $level+2;
                echo "<strong class='topicTagsLabel'> Topic Tags:</strong>\n";
                echo "<p class = 'tagsList tl".$level."'>".htmlspecialchars($topicData['tags'])."</p>\n";
                // Displays all tags TODO separate and format tags by commas.
                echo "<div class='row buttonRow'>";
                echo "<div class='text-center'>";
                echo "<div class = 'row buttonRow btn-group' role='group'>";

                echo "<button type ='button' class='btn btn-sm' onclick='location.href=\"../Pages/topicDesign.php?m=".$root."&t=".$topicData['id']."\"'>Edit Topic Data</button>\n";
                echo "<button type = 'button' class='btn btn-sm' onclick='location.href=\"../Pages/quizDesigner.php?m=".$root."&t=".$topicData['id']."\"'>Edit Topic Quiz</button>\n";
                echo "<button type = 'button' class='btn btn-sm' onclick='location.href=\"../Pages/topicDesign.php?m=".$root."&t=-2&p=".$topicData['id']."\"'>Add a sub-topic</button>\n";
                echo "</div>"; // End button row div
                echo "</div>";
                echo "</div>";
                // Buttons to edit this specific topic
            }
            echo "</div><!--close TopicList$level-->\n";// close TopicList$level
            echo "<div class='col-md-1'></div><!--Formatting Column-->\n";// close row
            echo "</div><!--close T row-->";//Close Row
        }
        echo "</div>"; // Closes topic list div
        $db->close();
        return;
    }
?>
<!DOCTYPE html>
<html>
    <head>
    	<meta charset="UTF-8">
      <meta name="viewport" content="width=device-width, initial-scale=1">
    	<title>Lesson Overview</title>
    	<script type="text/javascript" src="../JS/jquery-3.2.1.min.js"></script>
      <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
      <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
      <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
      <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/jquery-confirm/3.3.0/jquery-confirm.min.css">
      <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-confirm/3.3.0/jquery-confirm.min.js"></script>
    	<link rel="stylesheet" type="text/css" href="../CSS/BasicLessonOverview.css">
    	<script>
          $(document).ready(function(){
            $('[data-toggle="tooltip"]').tooltip();
        	});
		</script>
    </head>
	  <body>
        <div class='container'>
      		<?php
          		$db = new mysqli("localhost", "root", "topolor", "topolor");
          		$db->set_charset("utf8");
          		$query = "SELECT * FROM `tpl_post` WHERE `user_id` = ".$userid." AND `category` = 0;";
          		$Result = $db->query($query);
          		// Query to return all details of tutorials or topics for a particular user
          		$moduleCount = 0;
          		// Count of the number of modules this user made.

          		while($row = $Result->fetch_assoc()){
                  // echo "<button data-toggle='collapse' data-target='#UM".$moduleCount."'>C</button>";
                  // echo strip_tags($row['title']);
                  $parentTitle = str_replace(" ", "", strip_tags($row['title']));
          		    echo "<div class = 'row userModules outlined' id = 'UM".$moduleCount."'>\n";
                  echo "<button data-toggle='collapse' data-target='#".$parentTitle."TopicList' aria-expanded='false'  class = 'collapseButton pull-left'><span class='glyphicon glyphicon-chevron-down'></span></button>";
                  // echo "<div class = 'col-md-2'><button data-toggle='collapse' data-target='#".$parentTitle."TopicList'  class = 'collapseButton'><span class='glyphicon glyphicon-chevron-down'></span></button></div>\n";
                  echo "<div class = 'text-center'>\n";
          		    // Creates a unique div for each module to contain all that modules displayed details

          		    echo "<h1 class='postTitle'>".strip_tags($row['title'])."\n";
                  echo "<button type = 'button' class='btn btn-danger pull-right' onclick='$.confirm({title: \"Are you sure?\",content: \"test\",buttons: {confirm: function() { location.href=\"../PHP/deletePost.php?t=".$row['id']."\";},cancel: function() { $.alert(\"Cancelled\");}}});'>\n";
                  echo "<span class='glyphicon glyphicon-trash'></span>\n";
                  echo "</button>\n</h1>\n";

          		    if(strlen($row['description']) < $moduleWordCount){
          		        $excerpt = $row['description'];
          		    }else{
          		        $excerpt = substr($row['description'],0,$moduleWordCount)."...";
          		    }
          		    // Displays the entire description if it's less than 150 characters otherwise displays a portion of the description
          		    echo "<p class='moduleDescription'>".strip_tags($excerpt)."</p>\n";
          		    printT($row['id'], $firstLevel, $row['id']);
          		    echo "<strong class='moduleTagsLabel'>Module Tags:</strong>\n";
          		    echo "<p>".$row['tags']."</p>\n";
          		    // Displays all tags, TODO separate and format tags by commas
          		    // Buttons need to be made dynamic
          		    $moduleCount++;
                  echo "</div><!--// Close module column-->\n";// Close module column
                  // echo "<div class='col-md-2'></div><!-- Formatting column-->\n"; // Formatting column

                  echo "<div class='row buttonRow modButtonRow'>";
                  echo "<div class='text-center'>";
                  echo "<div class='row buttonRow btn-group' role='group'>";
      		        echo "<button type ='button' class='btn btn-sm' onclick='location.href=\"./topicDesign.php?m=".$row['id']."&t=-1\"' data-toggle='tooltip' data-placement='top' title='Add A Topic'><span class='glyphicon glyphicon-plus-sign'></span></button>\n";
      		        echo "<button type ='button' class='btn btn-sm' onclick='location.href=\"./topicDesign.php?m=".$row['id']."\"' data-toggle='tooltip' data-placement='top' title='Edit Module Data'><span class='glyphicon glyphicon-console'></span></button>\n";
                  echo "</div>"; // End Button group
                  echo "</div>"; // end formatting div
                  echo "</div>"; //end buttonRow


                  // echo "<div class='row buttonRow'>";
                  // echo "<div class='text-center'>";
                  // echo "<div class = 'row buttonRow btn-group' role='group'>";
                  //
                  // echo "<button type ='button' class='btn btn-sm' onclick='location.href=\"../Pages/topicDesign.php?m=".$root."&t=".$topicData['id']."\"'>Edit Topic Data</button>\n";
                  // echo "<button type = 'button' class='btn btn-sm' onclick='location.href=\"../Pages/quizDesigner.php?m=".$root."&t=".$topicData['id']."\"'>Edit Topic Quiz</button>\n";
                  // echo "<button type = 'button' class='btn btn-sm' onclick='location.href=\"../Pages/topicDesign.php?m=".$root."&t=-2&p=".$topicData['id']."\"'>Add a sub-topic</button>\n";
                  // echo "</div>"; // End button row div
                  // echo "</div>";
                  // echo "</div>";

                  echo "</div><!--CloseRow-->\n";// Close Row
          		}
          		$db->close();

      		?>
        <!-- </div> -->
          <div class='row'>
            <!-- <div class='col-md-4'></div> -->
            <div class='text-center'>
              <button type='button' class='btn btn-default' onclick='location.href="./topicDesign.php"'>Create a new Module!</button>
            </div>
            <!-- <div class='col-md-4'></div> -->
          </div>
        </div> <!--Close Container -->

	</body>
</html>
<!-- Button footer #666666 -->
<!-- Title Green #18B193 -->
<!-- Button Positive #3498DB -->
<!-- Button Negative #E74C3C -->
<!-- Area Background #FEFEFE -->
