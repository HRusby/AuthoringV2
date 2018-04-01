<!DOCTYPE html>
<html>
	<head>
		<meta charset="utf-8">
		<title>Topic Designer</title>

		<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
    	<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
    	<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>

        <script type='text/javascript' src='../JS/jquery-validation-1.17.0/dist/jquery.validate.js'></script>

		<script type="text/javascript" src="../JS/jquery-tokeninput-master/src/jquery.tokeninput.js"></script>
    	<link rel="stylesheet" href="../JS/jquery-tokeninput-master/styles/token-input.css" type="text/css" />
    	<link rel="stylesheet" href="../JS/jquery-tokeninput-master/styles/token-input-facebook.css" type="text/css" />

		<script type="text/javascript">
			function changeAction(m){
				if(m == "sT"){
					$('#lessonForm').prop('action', "../PHP/createTopic.php");
				}else if (m == "uT"){
					$('#lessonForm').prop('action', "../PHP/updateTopic.php");
				}else if (m == "sM"){
					$('#lessonForm').prop('action', "../PHP/createModule.php");
				}else if (m == "uM"){
					$('#lessonForm').prop('action', '../PHP/updateModule.php');
				}else if(m=="sST"){
					$('#lessonForm').prop('action', '../PHP/createSubTopic.php');
				}
				return true;
			} // Changes the form action to the appropriate action based on passed data,
				// if sT (Save Topic) is sent the action is createTopic.php
				// if uT (update Topic) is sent the action is updateTopic.php
				// if sM (save Module) is sent the action is createModule.php
				// if uM (update Module) is sent the action is updateModule.php
				// if sST (save SubTopic) is sent the action is createSubTopic.php
				// a uST is not needed as a subtopic can be edited the same as a standard topic.

				// ChangeAction occurs first so can use form action to determine type of validation needed
				function validateForm(){
					action = $('#lessonForm').prop('action');
					action = action.substr(action.indexOf('/PHP/')+5);
					if (action == 'createModule.php'){
						if($('#mTitle').val() == ""){
							alert('Please enter a Module Name!');
							return false;
						}else if ($('#mDesc').val() == ""){
							alert('Please enter some content!');
							return false;
						}else if ($('#tags').val() == ""){
							alert('Please enter at least one tag');
							return false;
						}
						// If mTitle is empty
						// If mDesc is empty
						// If tags is empty
					}else if (action == 'createTopic.php'){
						if($('#tTitle').val() == ""){
							alert('Please enter a Topic Name!');
							return false;
						}else if ($('#tDesc').val() == ""){
							alert('Please enter some content!');
							return false;
						}else if ($('#tags').val() == ""){
							alert('Please enter at least one tag');
							return false;
						}
					}else if (action == 'updateTopic.php'){
						if($('#tTitle').val() == ""){
							alert('Please enter a Topic Name!');
							return false;
						}else if ($('#tDesc').val() == ""){
							alert('Please enter some content!');
							return false;
						}else if ($('#tags').val() == ""){
							alert('Please enter at least one tag');
							return false;
						}
					}else if (action == 'updateModule.php'){
						if($('#mTitle').val() == ""){
							alert('Please enter a Module Name!');
							return false;
						}else if ($('#mDesc').val() == ""){
							alert('Please enter some content!');
							return false;
						}else if ($('#tags').val() == ""){
							alert('Please enter at least one tag');
							return false;
						}
					}else if(action == 'createSubTopic.php'){
						if($('#stTitle').val() == ""){
							alert('Please enter a Sub-Topic Name!');
							return false;
						}else if ($('#stDesc').val() == ""){
							alert('Please enter some content!');
							return false;
						}else if ($('#tags').val() == ""){
							alert('Please enter at least one tag');
							return false;
						}
					}
					return true;
				}

		</script>

		<?php
		  ini_set("default_charset", 'utf-8');
		  session_start();
		  if(!isset($_SESSION['user_id'])){
		      header('location:./login.php');
		  }else{
		      $userid=$_SESSION['user_id'];
		  }
		  if(isset($_GET['m'])){
		      $moduleID = $_GET['m'];
		  }
		  if(isset($_GET['t'])){
		      $topicID = $_GET['t'];
		      $db = new mysqli("localhost", "root", "topolor", "topolor");
		      $db->set_charset("utf8");
		      $currentDataQuery = "SELECT * FROM tpl_tutorial_topic WHERE topic_id = ?;";
		      $currentDataStmt = $db->stmt_init();
		      $currentDataStmt->prepare($currentDataQuery);
		      $currentDataStmt->bind_param("i", $topicID);
		      $currentDataStmt->execute();
		      $currentDataTemp = $currentDataStmt->get_result();
		      $currentDataResult = $currentDataTemp->fetch_assoc();
		      $currentPrereq = $currentDataResult['prerequisite'];
		      $currentOptional = $currentDataResult['optional'];
		      $currentDataStmt->close();
		      $db->close();
		  }
		  if(isset($_GET['p'])){
		      $parentID = $_GET['p'];
		  }
		?>

		<?php
            //open connection to mysql db
            $connection =new mysqli("localhost", "root", "topolor", "topolor") or die("Error " . mysqli_error($connection));
            $s = $_GET["q"];
            //fetch table rows from mysql db
            $sql = "SELECT `id`, `name` FROM `tpl_tag`;";
            $result = mysqli_query($connection, $sql) or die("Error in Selecting " . mysqli_error($connection));
            $currentTagJSON = array();
            //create an array
            $emparray = array();
            while($row =mysqli_fetch_assoc($result))
            {
                $emparray[] = $row;
            }
            //close the db connection
            mysqli_close($connection);
     ?>
	</head>
	<body>
		<div class = 'container'>
			<div class='row'>
				<div class='col-md-3'></div>
				<div class='col-md-6'><!-- Initial set-up of the page, divided into quarters where the main area takes half the page -->
					<form action="" method="post" id = "lessonForm" >
						<?php
							$rules = '';
							$messages = '';
						     if(isset($moduleID)){
						         $db = new mysqli("localhost", "root", "topolor", "topolor");
						         $db->set_charset("utf8");
						         $moduleQuery = "SELECT * FROM `tpl_post` WHERE `id` = ".$moduleID.";";
						         $moduleData = $db->query($moduleQuery);
						         while($row = $moduleData->fetch_assoc()){
						             $moduleTitle = $row['title'];
						             $moduleDesc = $row['description'];
						             $moduleTags = $row['tags'];
						         }
						         if(isset($topicID) && ($topicID != '-1' || $topicID != '-2')){
						             $topicQuery = "SELECT * FROM `tpl_post` WHERE `id` = ".$topicID.";";
						             $topicData = $db->query($topicQuery);
						             while($trow = $topicData->fetch_assoc()){
						                 $topicTitle = $trow['title'];
						                 $topicDesc = $trow['description'];
						                 $topicTags = $trow['tags'];
						             }
						         }
						         // Loads both module and topic data where appropriate from the database
						         $db->close();

						         echo "<input type='hidden' name = 'mID' value = '".$moduleID."'/>";
						         // Displays the module name and stores the module id (mID) to be sent on submit
						         // TODO: module title shouldn't be editable here
						         if(isset($topicTitle)){
										 		 echo "<h1>Module: ".$moduleTitle."</h1>";
						             echo "<div class='form-group'><label for='tTitle'>Topic Title:</label><input name = 'tTitle' class='form-control' id = 'tTitle' value = '".$topicTitle."' /></div>";

                         if($rules == ''){
                             $rules .= "tTitle: 'required'";
                         }else{
                             $rules .= ",\ntTitle: 'required'";
                         }
                         if($messages == ''){
                             $messages .= "tTitle: 'Please input a Title for this Topic'";
                         }else{
                             $messages .= ",\ntTitle: 'Please input a Title for this Topic'";
                         } // Input new validation rule and message for each question title

						             echo "<div class='form-group'><label for='tDesc'>Topic Content:</label><textarea class='form-control' name = 'tDesc' id='tDesc' style='resize:vertical;'>".$topicDesc."</textarea></div>";

									     	$rules .= ",\ntDesc: 'required'";

									     	$messages .= ",\ntDesc: 'Please enter some content!'";
									 	 		// Input new validation rule and message for each question title

						             echo "<div class='form-group'><label for = 'tags'>Tags:</label><input class='form-control' name = 'tags' id = 'tags'/></div>";
												 $rules .= ",tags: 'required'";
												 $messages .= ",tags: 'Please enter at least one Tag!'";
						             echo "<input type='hidden' name = 'tID' value='".$topicID."'/>";
						             // Code for building the page when an existing topic is being edited.
						             // Page knows this as the topic title has been stored.
						         }else{
						             if(isset($topicID) && $topicID == '-1'){
													 	 echo "<h1>Module: ".$moduleTitle."</h1>";
						                 echo "<div class='form-group'><label for='tTitle'>Topic Title:</label><input class='form-control' name = 'tTitle' id='tTitle' /></div>";
														 if($rules == ''){
		                             $rules .= "tTitle: 'required'";
		                         }else{
		                             $rules .= ",\ntTitle: 'required'";
		                         }
		                         if($messages == ''){
		                             $messages .= "tTitle: 'Please input a Title for this Topic'";
		                         }else{
		                             $messages .= ",\ntTitle: 'Please input a Title for this Topic'";
		                         } // Input new validation rule and message for each question title
														 echo "<div class='form-group'><label for='tDesc'>Topic Content:</label><textarea class='form-control' name = 'tDesc' id='tDesc' style='resize:vertical;' ></textarea></div>";
														 $rules .= ",tDesc: 'required'";
														 $messages .= ",tDesc: 'Please enter some content!'";
														 echo "<div class='form-group'><label for = 'tags'>Tags:</label><input class='form-control' name = 'tags' id='tags'/></div>";
														 $rules .= ",tags: 'required'";
														 $messages .= ",tags: 'Please enter at least one Tag!'";
						                 // Code for building the page if a new topic is being added to a module
						             }else if(isset($topicID) && $topicID == '-2'){
														 $db = new mysqli("localhost", "root", "topolor", "topolor");
										         $db->set_charset("utf8");
														 $parentNameQuery = "SELECT * FROM `tpl_post` WHERE `id` = ?;";
														 $parentNameStmt = $db->stmt_init();
														 $parentNameStmt->prepare($parentNameQuery);
														 $parentNameStmt->bind_param("i", $_GET['p']);
														 $parentNameStmt->execute();
														 $nameResult = $parentNameStmt->get_result();
														 $r = $nameResult->fetch_assoc();
														 echo "<h1>Parent:</h1>";
														 echo "<h3>".$r['title']."</h3>";
						                 echo "<div class='form-group'><label for='stTitle'>Sub-Topic Title:</label><input class='form-control' name = 'stTitle' id='stTitle' /></div>";
														 if($rules == ''){
		                             $rules .= "stTitle: 'required'";
		                         }else{
		                             $rules .= ",\nstTitle: 'required'";
		                         }
		                         if($messages == ''){
		                             $messages .= "stTitle: 'Please input a Title for this Topic'";
		                         }else{
		                             $messages .= ",\nstTitle: 'Please input a Title for this Topic'";
		                         } // Input new validation rule and message for each question title
														 echo "<div class='form-group'><label for='stDesc'>Sub-Topic Content:</label><textarea class='form-control' name = 'stDesc' id='stDesc' style='resize:vertical;' ></textarea></div>";
														 $rules .= ",stDesc: 'required'";
														 $messages .= ",stDesc: 'Please enter some content!'";
														 echo "<div class='form-group'><label for = 'tags'>Tags:</label><input class='form-control' name = 'tags' id='tags'/></div>";
														 $rules .= ",tags: 'required'";
														 $messages .= ",tags: 'Please enter at least one Tag!'";
														 echo "<input type='hidden' name='ptID' value='".$_GET['p']."'/>";
						                 // Code for building the page if a new sub-topic is being added
						                 // Stores the parent topic id (ptID) as a a hidden value to be sent on submit
						             }else{
													   echo "<div class='form-group'><label for='mTitle'>Module Title:</label><input class='form-control' name = 'mTitle' id = 'mTitle' value = '".$moduleTitle."' /></div>";
														 if($rules == ''){
		                             $rules .= "mTitle: 'required'";
		                         }else{
		                             $rules .= ",\nmTitle: 'required'";
		                         }
		                         if($messages == ''){
		                             $messages .= "mTitle: 'Please input a Title for this Topic'";
		                         }else{
		                             $messages .= ",\nmTitle: 'Please input a Title for this Topic'";
		                         } // Input new validation rule and message for each question title
														 echo "<div class='form-group'><label for='mDesc'>Module Content:</label><textarea class='form-control' name = 'mDesc' id='mDesc' style='resize:vertical;' >".$moduleDesc."</textarea></div>";
														 $rules .= ",mDesc: 'required'";
														 $messages .= ",mDesc: 'Please enter some content!'";
														 echo "<div class='form-group'><label for = 'tags'>Tags:</label><input name = 'tags' id='tags'/></div>";
														 $rules .= ",tags: 'required'";
														 $messages .= ",tags: 'Please enter at least one Tag!'";
						             } // Code for building the page if the module itself is being edited
						         }
						     }else{
						         echo "<div class='form-group'><label for='mTitle'>Module Name: </label><input class='form-control' name ='mTitle' id='mTitle' /></div>";
										 if($rules == ''){
												 $rules .= "mTitle: 'required'";
										 }else{
												 $rules .= ",\nmTitle: 'required'";
										 }
										 if($messages == ''){
												 $messages .= "mTitle: 'Please input a Title for this Module'";
										 }else{
												 $messages .= ",\nmTitle: 'Please input a Title for this Module'";
										 } // Input new validation rule and message for each question title
										 echo "<div class='form-group'><label for='mDesc'>Module Content:</label><textarea class='form-control' name = 'mDesc' id='mDesc' style='resize:vertical;'></textarea></div>";
										 $rules .= ",mDesc: 'required'";
										 $messages .= ",mDesc: 'Please enter some content!'";
										 echo "<div class='form-group'><label for = 'tags'>Tags:</label><input class='form-control' name = 'tags' id='tags' value='".$moduleTags."'/></div>";
										 $rules .= ",tags: 'required'";
										 $messages .= ",tags: 'Please enter at least one Tag!'";
										 // Code for building the page when a new module is created
						     }
						      # TODO: Add functionality for t = -2 where that is adding a subtopic

						      // update range of tpl_tutorial_post for all predecessors
						     if(isset($topicID) && $topicID == '-1'){
						         echo "<div class='form-group'><label for='prereqs'>Prerequisite: </label><select class='form-control' name='prereqs' id ='prereqs'>";
						         echo "<option value = '-1'>None</option>";
						         $preReqQuery = "SELECT topic_id FROM tpl_tutorial_topic WHERE root=? AND level = 2;";
						         $db = new mysqli("localhost", "root", "topolor", "topolor");
						         $db->set_charset("utf8");
						         $preReqStmt = $db->stmt_init();
						         $preReqStmt->prepare($preReqQuery);
						         $preReqStmt->bind_param("i", $moduleID);
						         $preReqStmt->execute();
						         $preReqResult = $preReqStmt->get_result();
						         while($preReq = $preReqResult->fetch_assoc()){
						             // Topic id fetched need to get tpl_post data then output
						             $postQuery = "SELECT title FROM tpl_post WHERE id=?;";
						             $postStmt = $db->stmt_init();
						             $db->set_charset("utf8");
						             $postStmt->prepare($postQuery);
						             $postStmt->bind_param("i", $preReq['topic_id']);
						             $postStmt->execute();
						             $postResult=$postStmt->get_result();
						             $postData = $postResult->fetch_assoc();
						             echo "<option value = ".$preReq['topic_id'].">".$postData['title']."</option>";
						         }

						         // Select lft,rgt and root from tpl_tutorial_topic where topic id = $topic id and level = 1
						         // create options for all topics with lft or rgt between the returned values
						         $db->close();
						         echo "</select><br>";
										 echo "</div>";// Close form-group

						         echo "<div class='form-check'><label class='form-check-label' for='optional'>Is this topic optional</label> <input class='form-check-input' name='optional' id='optional' type='checkbox'/></div>";
										 echo "<div class='row'>";
										 echo "<div class='col-md-3'></div>";
										 echo "<input class='btn-sm btn-success col-md-3' type='Submit' value='Save New Topic' onclick='return changeAction(\"sT\");'/>";
										 echo "<button class='btn-sm btn-warning col-md-3' onclick='location.href=\"../Pages/LessonOverview.php\";'>Cancel</button>";
										 echo "<div class='col-md-3'></div>";
										 echo "</div>";

						         // Save a new topic to a particular module
						     }elseif(isset($topicID) && $topicID =='-2'){
						         echo "<div class='form-group'><label for='prereqs'>Prerequisite</label><select class='form-control' name='prereqs' id ='prereqs'>";
						         echo "<option value = '-1'>None</option>";

						         $boundQuery = "SELECT lft,rgt,root,level FROM tpl_tutorial_topic WHERE topic_id=?;";
						         $db = new mysqli("localhost", "root", "topolor", "topolor");
						         $db->set_charset("utf8");
						         $boundStmt = $db->stmt_init();
						         echo $parentID;
						         $boundStmt->prepare($boundQuery);
						         $boundStmt->bind_param("i", $parentID);

						         $boundStmt->execute();
						         $boundResult = $boundStmt->get_result();
						         $bounds = $boundResult->fetch_assoc();
						         $lft = $bounds['lft'];
						         $rgt = $bounds['rgt'];
						         $root = $bounds['root'];
						         $nextLevel = $bounds['level'] + 1;
						         // Above gets the bounds that prereq topic ids will be within
						         $idQuery = "SELECT topic_id FROM tpl_tutorial_topic WHERE lft > ? AND rgt < ? AND root = ? AND level = ?;";
						         $idStmt = $db->stmt_init();
						         $idStmt->prepare($idQuery);
						         $idStmt->bind_param("iiii", $lft, $rgt, $root, $nextLevel);
						         $idStmt->execute();
						         $idResult = $idStmt->get_result();
						         // Gets all possible pre-requisite topic_ids
						         while($id = $idResult->fetch_assoc()){
						             $postQuery = "SELECT title FROM tpl_post WHERE id=?;";
						             $postStmt = $db->stmt_init();
						             $postStmt->prepare($postQuery);
						             $postStmt->bind_param("i", $id['topic_id']);
						             $postStmt->execute();
						             $postResult=$postStmt->get_result();
						             $postData = $postResult->fetch_assoc();
						             echo "<option value = ".$id['topic_id'].">".$postData['title']."</option>";
						             // gets the title from tpl_post for each possible pre-requisite and creates an option field
						         }
						         $db->close();
						         // Select lft,rgt and root from tpl_tutorial_topic where topic id = $topic id
						         // create options for all topics with lft or rgt between the returned values
						         echo "</select></div>";
										 echo "<div class='form-check'><label class='form-check-label' for='optional'>Is this sub-topic optional</label> <input class='form-check-input' name='optional' id='optional' type='checkbox'/></div>";
						         // echo "<label for='optional'>Is this sub-topic optional</label><input name='optional' id='optional' type='checkbox'/><br>";
										 echo "<div class='row'>";
										 echo "<div class='col-md-3'></div>";
										 echo "<input class='btn-sm btn-success col-md-3 form-group' type='Submit' value='Save Sub-Topic' onclick='return changeAction(\"sST\");'/>";
										 echo "<button class='btn-sm btn-warning col-md-3' onclick='location.href=\"../Pages/LessonOverview.php\";'>Cancel</button>";
										 echo "<div class='col-md-3'></div>";
										 echo "</div>";
						         // Save the new subtopic to a particular topic
						     }elseif(isset($topicID)){
						         echo "<div class='form-group'><label for='prereqs'>Prerequisite</label><select class='form-control' name='prereqs' id ='prereqs'>";
						         echo "<option value='-1'>None</option>";
						         $db = new mysqli("localhost", "root", "topolor", "topolor");
						         $db->set_charset("utf8");
						         $levelQuery = "SELECT level FROM tpl_tutorial_topic WHERE topic_id = ?;";
						         $levelStmt = $db->stmt_init();
						         $levelStmt->prepare($levelQuery);
						         $levelStmt->bind_param("i", $topicID);
						         $levelStmt->execute();
						         $levelResult = $levelStmt->get_result();
						         $levelData = $levelResult->fetch_assoc();
						         $newLevel = $levelData['level'];


						         $preReqQuery = "SELECT topic_id FROM tpl_tutorial_topic WHERE root=? AND level = ? AND topic_id !=?;";
						         $preReqStmt = $db->stmt_init();
						         $preReqStmt->prepare($preReqQuery);
						         $preReqStmt->bind_param("iii", $moduleID, $newLevel, $topicID);
						         $preReqStmt->execute();
						         $preReqResult = $preReqStmt->get_result();
						         while($preReq = $preReqResult->fetch_assoc()){
						             // Topic id fetched need to get tpl_post data then output
						             $postQuery = "SELECT title FROM tpl_post WHERE id=?;";
						             $postStmt = $db->stmt_init();
						             $postStmt->prepare($postQuery);
						             $postStmt->bind_param("i", $preReq['topic_id']);
						             $postStmt->execute();
						             $postResult=$postStmt->get_result();
						             $postData = $postResult->fetch_assoc();
						             if($preReq['topic_id'] == $currentPrereq){
						                 echo "<option selected value = ".$preReq['topic_id'].">".$postData['title']."</option>";
						             }else{
						                 echo "<option value = ".$preReq['topic_id'].">".$postData['title']."</option>";
						             }
						         }
						         // Select lft from tpl_tutorial_topic
						         // Select topic_id and root from tpl_tutorial_topic where root = $moduleID and level = 1 and lft < lft from prev query
						         // create options for all topics with lft or rgt between the returned values
						         echo "</select></div>";
						         if($currentOptional == 1){
											  echo "<div class='form-check'><label class='form-check-label' for='optional'>Is this topic optional</label> <input class='form-check-input' name='optional' id='optional' type='checkbox' checked/></div>";
						             // echo "<label for='optional'>Is this topic optional</label><input name='optional' id='optional' type='checkbox' checked/><br>";
						         }else{
											  echo "<div class='form-check'><label class='form-check-label' for='optional'>Is this topic optional</label> <input class='form-check-input' name='optional' id='optional' type='checkbox'/></div>";
						             // echo "<label for='optional'>Is this topic optional? </label><input name='optional' id='optional' type='checkbox'/><br>";
						         }
										 echo "<div class='row'>";
										 echo "<div class='col-md-3'></div>";
						         echo "<input class='btn-sm btn-success col-md-3 form-group' type='Submit' value='Update Topic' onclick='return changeAction(\"uT\");'/>";
										 echo "<button class='btn-sm btn-warning col-md-3' onclick='location.href=\"../Pages/LessonOverview.php\";'>Cancel</button>";
										 echo "<div class='col-md-3'></div>";
										 echo "</div>";
										 // Update the currently loaded topic
					         }elseif(isset($moduleID) && !isset($topicID)){
					             // Modules have no field to be optional nor store pre-reqs
											 echo "<div class='row'>";
											 echo "<div class='col-md-3'></div>";
	                     echo "<input class='btn-sm btn-success col-md-3 form-group' type='Submit' value='Update Module' onclick='return changeAction(\"uM\");'/>";
											 echo "<button class='btn-sm btn-warning col-md-3' onclick='location.href=\"../Pages/LessonOverview.php\";'>Cancel</button>";
											 echo "<div class='col-md-3'></div>";
											 echo "</div>";
											 // Update the current module
					         }else{
					             // Modules have no field to be optional nor store pre-reqs
					             echo "<div class='row'>";
											 echo "<div class='col-md-3'></div>";
											 echo "<input class='btn-sm btn-success col-md-3 form-group' type='Submit' value='Save New Module' onclick='return changeAction(\"sM\");'/>";
											 echo "<button class='btn-sm btn-warning col-md-3' onclick='location.href=\"../Pages/LessonOverview.php\";'>Cancel</button>";
											 echo "<div class='col-md-3'></div>";
											 echo "</div>";
					         }// Create a new module

						?>

					</form>
				</div>
				<div class='col-md-3'></div>
			</div>
		</div><!-- Close Container -->
		<?php
		  echo "<script type=\"text/javascript\">";
          echo "$(document).ready(function() {";
          if(isset($topicTags)){
            $currentTagList = explode(",", $topicTags);
          }else{
            $currentTagList = explode(",", $moduleTags);
          }
          if((isset($moduleTags) && !isset($topicID)) && $moduleTags != "" || isset($topicTags) && $topicTags != ""){

              echo "$(\"#tags\").tokenInput(".json_encode($emparray).", {prePopulate: [";
              $count = 0;
              foreach($currentTagList as $ct){
                  $db = new mysqli("localhost", "root", "topolor", "topolor");
                  $db->set_charset("utf8");
                  $tagQuery = "SELECT id,name FROM tpl_tag WHERE name = ?;";
                  $tagStmt = $db->stmt_init();
                  $tagStmt->prepare($tagQuery);
                  $tagStmt->bind_param('s', $ct);
                  $tagStmt->execute();
                  $tagTemp = $tagStmt->get_result();
                  $tagResult = $tagTemp->fetch_assoc();
                  if($count == 0){
                      echo "{id: ".$tagResult['id'].", name: \"".$tagResult['name']."\"}";
                  }else{
                      echo ",{id: ".$tagResult['id'].", name: \"".$tagResult['name']."\"}";
                  }
                  $count++;
              }

              echo "]});";

          }else{
              echo "$(\"#tags\").tokenInput(".json_encode($emparray).");";
          }


						echo "var validator = $('#lessonForm').validate({
								ignore:'',
								rules: {
										".$rules."
								},
								messages: {
										".$messages."
								},
								errorPlacement: function(error, element){
										if(element.is('input:radio')){
												error.insertAfter(element.parent().parent());
										}else{
												error.insertAfter(element.parent());
										}
								}
						});";
						echo "$.validator.messages.required = 'Select a correct answer!';";
						echo "});";
    	  echo "</script>";
    	  // Search google for token-input (should be by loopj) for documentation on this code
		?>

	</body>

	<datalist id="tags">
				<?php
				    $db = new mysqli("localhost", "root", "topolor", "topolor");
				    $db->set_charset("utf8");
				    $tagResult = $db->query("SELECT `name` FROM `tpl_tag`;");
				    while($row = $tagResult->fetch_assoc()){
				        echo "<option value=".$row["name"].">";
				    }
				    $db->close();
				?>
	</datalist>

 <!-- Needs an input to tpl_tutorial_topic to note what level each topic is on and what module it corresponds to -->

</html>
