<?php
    session_start();
    ini_set("default_charset", 'utf-8');
    if(!isset($_SESSION['user_id'])){
        header('location:./login.php');
    }else{
        $userid=$_SESSION['user_id'];
    }
    $moduleID = $_GET['m'];
    $topicID = $_GET['t'];
?>
<!DOCTYPE html>
<html>
	<head>
		<meta charset="utf-8">
		<title>Quiz Designer</title>
		<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
        <script type='text/javascript' src='../JS/jquery-validation-1.17.0/dist/jquery.validate.js'></script>
        <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
        <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
		<script>

    		  function addOption(question, option) {
            		// A equals 65
            		// Therefore option + 65 equals character
                  // $("#q"+question+"Options").append("<div class='col-md-1'></div>");
                  $("#q"+question+"Options").append("\
                      <div class='row form-group' id='q"+question+"o"+option+"'>"+
                       // Formatting and open formgroup
                          "<input type = 'radio' name = 'q"+question+"OptCorrect' id='q"+question+"OptRad"+option+"' value='"+String.fromCharCode(option+65)+"' class='form-check-input position-static col-md-1' required/>\
      	                <label for = 'q"+question+"Opt"+option+"' class='col-md-1' id='q"+question+"Opt"+option+"Lbl'>"+
                              String.fromCharCode(option+65)+
                          "</label>\
      	                <input type='hidden' name='q"+question+"OptOrder"+option+"' value = "+String.fromCharCode(option+65)+"/>\
            				<input type = 'text' name = 'q"+question+"Opt"+option+"' id='q"+question+"Opt"+option+"' class='form-control-inline col-md-8'/>\
                          <button type='button' class='btn-small btn-danger col-md-1' onclick='deleteNewOption("+question+","+option+");' id='q"+question+"Opt"+option+"DeleteButton'>\
                              <span class='glyphicon glyphicon-remove-sign'></span>\
                          </button>\
                      </div>");
                  // Close form group

                  $("#q"+question+"Opt"+option).rules("add", {required: true, messages: {required: "Please enter an option, or delete the option!"}});
                  // $("#q"+question+"OptCorrect").rules("add", {required: true, messages: {required: "Please select a correct option!"}});
      				if((option+65) != (65+25)){
      					$("#q"+question+"AddOptionButton").attr("onclick", "addOption("+question+", "+(option + 1)+")");
      				}

      				// If reaches Z will only output Z's onwards
      		}

      		function addQuestion(questionNo){

                  $("#Questions").append("\
                      <div class='row' id = 'q"+questionNo+"'>\
                          <div class='row form-group'>\
      	                   <label for = 'qTitle"+questionNo+"' class='col-md-1' id='qTitle"+questionNo+"Lbl'>"+questionNo+"</label>\
                             <input type = 'text' id = 'qTitle"+questionNo+"' class='qTitle form-control-inline col-md-9' name='qTitle"+questionNo+"' />\
                             <button type='button' class='btn-small btn-danger col-md-1' id='q"+questionNo+"DeleteQuestionButton' onclick='deleteNewQuestion("+questionNo+");'>\
                                  <span class='glyphicon glyphicon-remove-sign'></span>\
                             </button>\
                          </div>"+
                          /* End q# row*/
                          "<div class='col-md-1'></div>"+
                          /* Formatting to indent question options*/
      	                "<div id = 'q"+questionNo+"Options' class = 'row form-group col-md-11'>\
      	                </div> "+
                          /*Close Option Group*/
                          "<div class='row form-group'> "+
                          /* Open divs for formatting*/
                              "<div class='col-md-10'></div>\
                              <button type='button' class='btn-small btn-success col-md-1' id='q"+questionNo+"AddOptionButton' onClick='addOption("+questionNo+",0);'><span class='glyphicon glyphicon-plus-sign'></span></button> \
                          </div><br> "+
                          /* Close option button row*/
                      "</div> ");
                      /*Close Question Div*/
                  $("#qTitle"+questionNo).rules("add", {required: true, messages: {required: "Please enter a title (or delete the question!)"}});
                  $("#addQuestionButton").attr("onclick", 'addQuestion('+(questionNo+1)+')');
            }

            function deleteNewQuestion(questionNo){
                $("#q"+questionNo).html('');
                $("#q"+questionNo).remove();
                // Add in correcting index and id etc.
                // Only need to consider new questions and not existing as at no point will an existing question appear after a new one
                var t = true;
                var curQuestion = questionNo;
                while(t==true){
                    curQuestion += 1;
                    // alert("Question: " + curQuestion);
                    if($("#qTitle"+curQuestion+"Lbl").length){
                        var newNo = curQuestion-1;
                        // alert("NewNo: "+newNo)
                        $("#qTitle"+curQuestion+"Lbl").html(newNo);
                        $("#qTitle"+curQuestion+"Lbl").attr('for', "qTitle"+newNo);
                        $("#qTitle"+curQuestion+"Lbl").attr('id', "qTitle"+newNo+"Lbl");

                        $("#q"+curQuestion).attr('id', 'q'+newNo);

                        $("[name=qID"+curQuestion+"]").attr('name', 'qID'+newNo);

                        $("#qTitle"+curQuestion).attr('name', 'qTitle'+newNo);
                        $("#qTitle"+curQuestion).attr('id', 'qTitle'+newNo);

                        $("#qTitle"+curQuestion+"lbl").attr('for', "#qTitle"+newNo);
                        $("#qTitle"+curQuestion+"lbl").attr('id', "#qTitle"+newNo+"lbl");

                        // Change id and name of title attributes to accurately reflect model

                        var bOnClick = $("#q"+curQuestion+"DeleteQuestionButton").attr('onclick');
                        // alert("bOnClick substr: "+bOnClick.substring(0,17));
                        if(bOnClick.substring(0,17) == 'deleteNewQuestion'){
                            // alert("Changing new button");
                            $("#q"+curQuestion+"DeleteQuestionButton").attr('onclick', "deleteNewQuestion("+newNo+");");
                        }else{
                            // alert("Changing existing button");
                            var part2 = bOnClick.substring(bOnClick.indexOf(","));
                            $("#q"+curQuestion+"DeleteQuestionButton").attr('onclick', "deleteNewQuestion("+newNo+part2);
                            // For existing question
                        }
                        $("#q"+curQuestion+"DeleteQuestionButton").attr('id', "q"+newNo+"DeleteQuestionButton");

                        // alert("Starting Options");

                        $("#q"+curQuestion+"Options").find('input:radio').each(function(){
                            var eID = $(this).attr('id');
                            var part2 = eID.substring(eID.indexOf('O'));
                            $(this).attr('id', 'q'+newNo+part2);
                            var eID = $(this).attr('name');
                            part2 = $(this).attr('name').substring(eID.indexOf('O'));
                            $(this).attr('name', 'q'+newNo+part2);
                        });

                        $("#q"+curQuestion+"Options").find('label').each(function(){
                            var eID = $(this).attr('id');
                            var part2 = eID.substring(eID.indexOf('O'));
                            $(this).attr('id', 'q'+newNo+part2);
                            var eID = $(this).attr('for');
                            part2 = $(this).attr('for').substring(eID.indexOf('O'));
                            $(this).attr('for', 'q'+newNo+part2);
                        });

                        $("#q"+curQuestion+"Options").find('input:hidden').each(function(){
                            var eID = $(this).attr('name');
                            var part2 = eID.substring(eID.indexOf('O'));
                            $(this).attr('name', 'q'+newNo+part2);
                        });

                        $("#q"+curQuestion+"Options").find('input:text').each(function(){
                            var eID = $(this).attr('id');
                            var part2 = eID.substring(eID.indexOf('O'));
                            $(this).attr('id', 'q'+newNo+part2);
                            $(this).attr('name', 'q'+newNo+part2);
                        });

                        $("#q"+curQuestion+"Options").find('button').each(function(){
                            var onC = $(this).attr('onclick');
                            part1 = onC.slice(0, onC.indexOf('(')+1);
                            // alert("Part1: " + part1);
                            if(part1 == 'deleteExistingOption('){
                                part2 = onC.slice(onC.indexOf(','));
                                $(this).attr('onclick', part1+newNo+part2);
                            }else{
                                part2 = onC.slice(onC.indexOf(')'));
                                $(this).attr('onclick', part1+newNo+part2);
                            }
                            var part2 = $(this).attr('id').substring($(this).attr('id').indexOf('O')); // Get the id after the q+count
                            $(this).attr('id', 'q'+newNo+part2);
                        });

                        $("#q"+curQuestion+"Options").find('div').each(function(){
                            var eID = $(this).attr('id');
                            var part2 = eID.substring(eID.indexOf('o'));
                            $(this).attr('name', 'q'+newNo+part2);
                            $(this).attr('id', 'q'+newNo+part2);
                        });

                        // Change div id
                        $("#q"+curQuestion+"Options").attr('id', 'q'+newNo+'Options');
                        // alert("curQuestion:" + curQuestion);
                        var eOC = $("#q"+curQuestion+"AddOptionButton").attr('onclick')
                        // Existing OnClick
                        var part2 = eOC.substring(eOC.indexOf(','));
                        $("#q"+curQuestion+"AddOptionButton").attr('onclick', "addOption("+(curQuestion-1)+part2);
                        $("#q"+curQuestion+"AddOptionButton").attr('id', "q"+(curQuestion-1)+"AddOptionButton");
                    }else{
                        t = false;
                    }
                }


                // Change add question on click to act on curQuestion
                $("#addQuestionButton").attr('onclick', "addQuestion("+(curQuestion-1)+");");
            }

            function deleteExistingQuestion(questionNo, questionID){
                $.ajax({
                    url:"../PHP/deleteExistingQuestion.php?q="+questionID,
                    success:function(result){
                        // alert(result);
                        $("#q"+questionNo).html('');
                        $("#q"+questionNo).remove();
                        // $("#q"+questionNo+"AddOptionButton").attr("value", "Hello");
                    }
                });
                var t = true;
                var curQuestion = questionNo;
                while(t==true){
                    curQuestion += 1;
                    // alert("Question: " + curQuestion);
                    if($("#qTitle"+curQuestion+"Lbl").length){
                        var newNo = curQuestion-1;
                        // alert("NewNo: "+newNo)
                        $("#qTitle"+curQuestion+"Lbl").html(newNo);
                        $("#qTitle"+curQuestion+"Lbl").attr('for', "qTitle"+newNo);
                        $("#qTitle"+curQuestion+"Lbl").attr('id', "qTitle"+newNo+"Lbl");

                        $("#q"+curQuestion).attr('id', 'q'+newNo);

                        $("[name=qID"+curQuestion+"]").attr('name', 'qID'+newNo);

                        $("#qTitle"+curQuestion).attr('name', 'qTitle'+newNo);
                        $("#qTitle"+curQuestion).attr('id', 'qTitle'+newNo);

                        $("#qTitle"+curQuestion+"lbl").attr('for', "#qTitle"+newNo);
                        $("#qTitle"+curQuestion+"lbl").attr('id', "#qTitle"+newNo+"lbl");

                        // Change id and name of title attributes to accurately reflect model

                        var bOnClick = $("#q"+curQuestion+"DeleteQuestionButton").attr('onclick');
                        // alert("bOnClick substr: "+bOnClick.substring(0,17));
                        if(bOnClick.substring(0,17) == 'deleteNewQuestion'){
                            // alert("Changing new button");
                            $("#q"+curQuestion+"DeleteQuestionButton").attr('onclick', "deleteNewQuestion("+newNo+");");
                        }else{
                            // alert("Changing existing button");
                            var part2 = bOnClick.substring(bOnClick.indexOf(","));
                            $("#q"+curQuestion+"DeleteQuestionButton").attr('onclick', "deleteNewQuestion("+newNo+part2);
                            // For existing question
                        }
                        $("#q"+curQuestion+"DeleteQuestionButton").attr('id', "q"+newNo+"DeleteQuestionButton");

                        // alert("Starting Options");

                        $("#q"+curQuestion+"Options").find('input:radio').each(function(){
                            var eID = $(this).attr('id');
                            var part2 = eID.substring(eID.indexOf('O'));
                            $(this).attr('id', 'q'+newNo+part2);
                            var eID = $(this).attr('name');
                            part2 = $(this).attr('name').substring(eID.indexOf('O'));
                            $(this).attr('name', 'q'+newNo+part2);
                        });

                        $("#q"+curQuestion+"Options").find('label').each(function(){
                            var eID = $(this).attr('id');
                            var part2 = eID.substring(eID.indexOf('O'));
                            $(this).attr('id', 'q'+newNo+part2);
                            var eID = $(this).attr('for');
                            part2 = $(this).attr('for').substring(eID.indexOf('O'));
                            $(this).attr('for', 'q'+newNo+part2);
                        });

                        $("#q"+curQuestion+"Options").find('input:hidden').each(function(){
                            var eID = $(this).attr('name');
                            var part2 = eID.substring(eID.indexOf('O'));
                            $(this).attr('name', 'q'+newNo+part2);
                        });

                        $("#q"+curQuestion+"Options").find('input:text').each(function(){
                            var eID = $(this).attr('id');
                            var part2 = eID.substring(eID.indexOf('O'));
                            $(this).attr('id', 'q'+newNo+part2);
                            $(this).attr('name', 'q'+newNo+part2);
                        });

                        $("#q"+curQuestion+"Options").find('button').each(function(){
                            var onC = $(this).attr('onclick');
                            part1 = onC.slice(0, onC.indexOf('(')+1);
                            // alert("Part1: " + part1);
                            if(part1 == 'deleteExistingOption('){
                                part2 = onC.slice(onC.indexOf(','));
                                $(this).attr('onclick', part1+newNo+part2);
                            }else{
                                part2 = onC.slice(onC.indexOf(','));
                                $(this).attr('onclick', part1+newNo+part2);
                                //deleteNewOption(4,2);
                            }
                            var part2 = $(this).attr('id').substring($(this).attr('id').indexOf('O')); // Get the id after the q+count
                            $(this).attr('id', 'q'+newNo+part2);
                        });

                        $("#q"+curQuestion+"Options").find('div').each(function(){
                            var eID = $(this).attr('id');
                            var part2 = eID.substring(eID.indexOf('o'));
                            $(this).attr('name', 'q'+newNo+part2);
                            $(this).attr('id', 'q'+newNo+part2);
                        });

                        // Change div id
                        $("#q"+curQuestion+"Options").attr('id', 'q'+newNo+'Options');
                        // alert("curQuestion:" + curQuestion);
                        var eOC = $("#q"+curQuestion+"AddOptionButton").attr('onclick')
                        // Existing OnClick
                        var part2 = eOC.substring(eOC.indexOf(','));
                        $("#q"+curQuestion+"AddOptionButton").attr('onclick', "addOption("+(curQuestion-1)+part2);
                        $("#q"+curQuestion+"AddOptionButton").attr('id', "q"+(curQuestion-1)+"AddOptionButton");
                    }else{
                        t = false;
                    }
                }

                // Change add question on click to act on curQuestion
                $("#addQuestionButton").attr('onclick', "addQuestion("+(curQuestion-1)+");");
              // To fix numbers: get every item where questionNo > its $questionCount
              //                  decrement the index
              //                id='qTitle".$questionCount."Lbl'
            }

            function deleteNewOption(questionNo, optionID){
                $("#q"+questionNo+"o"+optionID).html('');
                $("#q"+questionNo+"o"+optionID).remove();
                updateOptionIndexes(questionNo, optionID);
            }

            function deleteExistingOption(questionNo, optionID, optionCount){
                $.ajax({
                    url:"../PHP/deleteExistingOption.php?o="+optionID,
                    success:function(result){
                        $("#q"+questionNo+"o"+optionCount).html('');
                        $("#q"+questionNo+"o"+optionCount).remove();
                    }
                });
                updateOptionIndexes(questionNo, optionCount);
                // Change add option button id = q3AddOptionButton onclick = addOption(question,option);
            }

            function updateOptionIndexes(questionNo, optionCount){
              var t = true;
              var curOption = optionCount;
              while(t){
                curOption = curOption+1;

                if($("#q"+questionNo+"o"+curOption).length){
                  var newNo = curOption - 1;

                  $("#q"+questionNo+"o"+curOption).find('input:radio').each(function(){
                      var eID = $(this).attr('id');
                      var part2 = eID.charCodeAt(eID.indexOf('d')+1);
                      part2 = String.fromCharCode(part2-1);
                      $(this).attr('value', part2);
                      $(this).attr('id', 'q'+questionNo+'OptRad'+part2);
                  });
                  // type = radio id - q1OptRadC value - C

                  $("#q"+questionNo+"o"+curOption).find('label').each(function(){
                      var eID = $(this).attr('id');
                      var part2 = eID.substring(eID.indexOf('t')+1, eID.indexOf('Lbl')); // Current Number
                      $(this).html(String.fromCharCode(parseInt(part2)+64));
                      $(this).attr('id', 'q'+questionNo+'Opt'+newNo+'Lbl');
                      $(this).attr('for', 'q'+questionNo+'Opt'+newNo);
                  });
                  // type = label id - q1Opt1Lbl html()

                  $("#q"+questionNo+"o"+curOption).find('input:hidden').each(function(){
                      var eID = $(this).attr('name');
                      if(eID.substring(0, eID.indexOf('D')+1) == 'q'+questionNo+'OptID'){
                        $(this).attr('name', eID.substring(0, eID.indexOf('D')+1)+newNo);
                        // type = hidden name - q1OptID1
                      }else{
                        $(this).attr('name', eID.substring(0, eID.indexOf('Order')+5)+newNo);
                        var newVal = String.fromCharCode($(this).attr('value').charCodeAt(0)-1);
                        $(this).attr('Value', newVal);
                        // type = hidden name - q1OptOrder1 Value - B
                      }
                  });

                  $("#q"+questionNo+"o"+curOption).find('input:text').each(function(){
                      var eID = $(this).attr('id');
                      var part2 = eID.substring(eID.indexOf('Opt')+3) - 1; // Current Number
                      $(this).attr('id', 'q'+questionNo+'Opt'+part2);
                      $(this).attr('name', 'q'+questionNo+'Opt'+part2);
                  });
                  // type = text name - q1Opt1 id - q1Opt1

                  bOnC = $('#q'+questionNo+'Opt'+curOption+"DeleteButton").attr('onclick');
                  if(bOnC.substring(0,bOnC.indexOf('('))=='deleteExistingOption'){
                    $('#q'+questionNo+'Opt'+curOption+"DeleteButton").attr('onclick', bOnC.substring(0, bOnC.indexOf(', ')+2)+newNo+');');
                  }else{
                    $('#q'+questionNo+'Opt'+curOption+"DeleteButton").attr('onclick', bOnC.substring(0, bOnC.indexOf(',')+1)+newNo+');');
                  }
                  $('#q'+questionNo+'Opt'+curOption+"DeleteButton").attr('id', 'q'+questionNo+'Opt'+newNo+"DeleteButton");
                  // Changes delete button // type = button onclick - deleteExistingOption(1,10098, 1); id - q1Opt1DeleteButton //
                  $("#q"+questionNo+"o"+curOption).attr('id', 'q'+questionNo+'o'+newNo);
                  // Changes div id as last to ensure anything reliant on the current div id is correct.
                  // type = div id - q1o1 //
                }else{
                  t = false;
                }
              }
              var bOnC = $('#q'+questionNo+'AddOptionButton').attr('onclick');
              var part1 = bOnC.substring(0, bOnC.indexOf(',')+1);
              $('#q'+questionNo+'AddOptionButton').attr('onclick', part1+(newNo+1)+');');
            }

        </script>

	</head>
	<body>
		<!-- Topic Name: <input type="text" list="Modules" name="moduleChoice" id="moduleChoice" /></br> -->
        <br>
        <div class='container'>
          <div class='row'>
            <div class='col-md-3'></div>
            <div class='col-md-6'>
              <?php
                $db = new mysqli("localhost", "root", "topolor", "topolor");
                $db->set_charset("utf8");
                $topicNameQuery = "SELECT * FROM tpl_post WHERE id = ?;";
                $topicNameStmt = $db->stmt_init();
                $topicNameStmt->prepare($topicNameQuery);
                $topicNameStmt->bind_param("i", $topicID);
                $topicNameStmt->execute();
                $topicNameResult = $topicNameStmt->get_result();
                $tN = $topicNameResult->fetch_assoc();
                echo "<h1>".$tN['title']."</h1>";
              ?>
            </div>
            <div class='col-md-3'></div>
          </div>
            <div class='col-md-4'></div>
            <div class='col-md-4'>
                <form action="../PHP/saveQuiz.php" method="post" id='quizForm'>


    			       <div id = "Questions">
        				<?php
                            $rules = '';
                            $messages = '';
                            $errorPlacement = '';
                            echo "<input type='hidden' name='topicID' value='".$topicID."'/>";
                            $db = new mysqli("localhost", "root", "topolor", "topolor");
                            $db->set_charset("utf8");
                            $questionsQuery = "SELECT * FROM tpl_question WHERE topic_id = ?;";
                            $questionsStmt = $db->stmt_init();
                            $questionsStmt->prepare($questionsQuery);
                            $questionsStmt->bind_param("i", $topicID);
                            $questionsStmt->execute();
                            $questionsResult = $questionsStmt->get_result();
                            // Selects all questions for this topic from the database
                            $questionCount = 1;
                            // Creates a variable to store the number of questions (allowing accurate indexing)
                    	    while($question = $questionsResult->fetch_assoc()){
                               echo "<div id = 'q".$questionCount."' class='row'>\n"; // Create a div to encapsulate each question
                               echo "<div class='row form-group'>\n";
                               // echo "<div class='form-group'>";
                    	       echo "<label for = 'qTitle".$questionCount."' class='col-md-1' id='qTitle".$questionCount."Lbl'>".$questionCount."</label>\n";
                    	       echo "<input type = 'hidden' name = 'qID".$questionCount."' value='".$question['id']."'/>";
                    	       echo "<input type = 'text' id = 'qTitle".$questionCount."' class = 'qTitle form-control-inline col-md-9' name ='qTitle".$questionCount."' value='".$question['description']."'/>\n";
                               echo "<button type='button' class='btn-small btn-danger col-md-1' id='q".$questionCount."DeleteQuestionButton' onclick='deleteExistingQuestion(".$questionCount.", ".$question['id'].");'>\n<span class='glyphicon glyphicon-remove-sign'></span>\n</button>\n";
                               if($rules == ''){
                                   $rules .= "qTitle".$questionCount.": 'required'";
                               }else{
                                   $rules .= ",\nqTitle".$questionCount.": 'required'";
                               }
                               if($messages == ''){
                                   $messages .= "qTitle".$questionCount.": 'Please enter a title (or delete the question!)'";
                               }else{
                                   $messages .= ", \nqTitle".$questionCount.": 'Please enter a title (or delete the question!)'";
                               } // Input new validation rule and message for each question title
                               // For new validation rules, snippet vQ

                               echo "</div>\n"; // End Question Title Row

                    	       $optionsQuery = "SELECT * FROM tpl_question_option WHERE question_id = ?;";
                    	       $optionsStmt = $db->stmt_init();
                    	       $optionsStmt->prepare($optionsQuery);
                    	       $optionsStmt->bind_param("i", $question['id']);
                    	       $optionsStmt->execute();
                    	       $optionsResult = $optionsStmt->get_result();
                               // Query to get all options for the current question

                    	       $optionCount = 0;
                               // Set option count to 0 to accuractely get the next letter for the option indexing

                               echo "<div class = 'col-md-1'></div>\n"; // Formatting column - Makes option group ~ tab in from question title
                    	       echo "<div id = 'q".$questionCount."Options' class = 'row form-group col-md-11'>\n";
                    	       while($option = $optionsResult->fetch_assoc()){
                                   echo "<div class='row form-group' id='q".$questionCount."o".$optionCount."'>\n"; // Open option form group
                                   if($question['correct_answer'] == $option['opt']){
                                       echo "<input type ='radio' name='q".$questionCount."OptCorrect' id='q".$questionCount."OptRad".$option['opt']."'   value='".$option['opt']."' checked class = 'form-check-input position-static col-md-1' required/>\n";
                                   }else{
                                       echo "<input type ='radio' name='q".$questionCount."OptCorrect' id='q".$questionCount."OptRad".$option['opt']."'   value='".$option['opt']."' class = 'form-check-input position-static col-md-1' required/>\n";
                                   }

                                   $rules .= ",\nq".$questionCount."OptCorrect: 'required'";
                                   $messages .= ",\nq".$questionCount."OptCorrect: 'Select a radiobutton to indicate the correct answer'";
                    	           echo "<label for = 'q".$questionCount."Opt".$optionCount."' class='col-md-1' id='q".$questionCount."Opt".$optionCount."Lbl'>".$option['opt']."</label>\n";
                    	           echo "<input type='hidden' name='q".$questionCount."OptID".$optionCount."' value=".$option['id']."/>\n";
                    	           echo "<input type='hidden' name='q".$questionCount."OptOrder".$optionCount."' value = ".$option['opt']."/>\n";
                    	           echo "<input type = 'text' name = 'q".$questionCount."Opt".$optionCount."' id='q".$questionCount."Opt".$optionCount."' value = '".$option['val']."' class='form-control-inline col-md-8'/>\n";
                    	           echo "<button type='button' class='btn-small btn-danger col-md-1' onclick='deleteExistingOption(".$questionCount.",".$option['id'].", ".$optionCount.");' id='q".$questionCount."Opt".$optionCount."DeleteButton'>\n<span class='glyphicon glyphicon-remove-sign'></span>\n</button>\n";

                                   $rules .= ",\nq".$questionCount."Opt".$optionCount.": 'required'";
                                   $messages .= ",\nq".$questionCount."Opt".$optionCount.": 'Please enter an option, or delete it!'";
                    	           $optionCount++;
                                   echo "</div>\n"; // Close row and form group
                    	       }
                               echo "</div>\n"; // Close Option group
                               echo "<div class='row form-group'>\n";
                               echo "<div class='col-md-10'></div>\n";
                               echo "<button type='button' class='btn-small btn-success col-md-1' id='q".$questionCount."AddOptionButton' onClick='addOption(".$questionCount.",".$optionCount.");'>\n<span class='glyphicon glyphicon-plus-sign'></span>\n</button>\n";
                               echo "</div><br>\n"; //Close row
                             $questionCount++;
                             echo "</div>\n"; // Close question div
                    	   }
                    	   // If questionCount is still 0 then there are no questions and a blank question form can be made
                    	   if($questionCount == 1){
                               echo "<div class='row'>\n"; // Div to encapsulate question
                               echo "<div class='row form-group'>\n";
                    	       echo "<label for = 'qTitle".$questionCount."' class='col-md-1'>".$questionCount."</label>\n";
                    	       echo "<input type = 'text' id = 'qTitle".$questionCount."' class='qTitle form-control-inline col-md-8' name ='qTitle".$questionCount."' value='".$question['description']."'/>\n";
                               echo "<button type='button' class='btn-small btn-danger col-md-1' id='q".$questionCount."DeleteQuestionButton' onclick='deleteNewQuestion(".$questionCount.");'>\n<span class='glyphicon glyphicon-remove-sign'></span>\n</button>\n";
                               echo "</div>"; // Close q1 title row

                               if($rules == ''){
                                   $rules .= "qTitle".$questionCount.": 'required'";
                               }else{
                                   $rules .= ",\nqTitle".$questionCount.": 'required'";
                               }
                               if($messages == ''){
                                   $messages .= "qTitle".$questionCount.": 'Please enter a title (or delete the question!)'";
                               }else{
                                   $messages .= ", \nqTitle".$questionCount.": 'Please enter a title (or delete the question!)'";
                               } // Input new validation rule and message for each question title

                               echo "<div id = 'col-md-1'></div>\n";
                    	       echo "<div id = 'q".$questionCount."Options' class = 'row form-group col-md-11'>\n";
                    	       echo "</div>\n";
                               echo "<div class='row form-group'>\n";
                               echo "<div class='col-md-10'></div>\n";
                    	       echo "<button type='button' class='btn-small btn-success col-md-1' id='q".$questionCount."AddOptionButton' onClick='addOption(".$questionCount.",0);'>\n<span class='glyphicon glyphicon-plus-sign'></span>\n</button>\n";
                               echo "</div><br>\n"; // Close option button row
                               $rules = "qTitle1: 'required',\n";
                               $messages = "qTitle1: 'This field is'";

                               echo "</div>\n"; // End question encapsulation
                               $questionCount++;
                    	   }
                    	   $db->close();
                        // TODO: create php code to save new questions / edited ones to the database
                        // Will need to consider whether it is editing or if it's creating anew
                        echo "<script>\n";
                        echo "$().ready(function(){
                                var validator = $('#quizForm').validate({
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
                                });
                            });";
                        echo "$.validator.messages.required = 'Select a correct answer!';";
                        echo "</script>\n";
                        // TODO validation for Radio Buttons, add to errorPlacement if type = radio then insert after element.parent().parent()
            	    ?>
                    </div><!-- Close Questions-->
                    <div class='row'>
		                   <button type='button' class='btn-sm btn-success col-md-4 form-group' id='addQuestionButton' onclick='addQuestion(<?php echo ($questionCount); ?>)'>
                            <span class='glyphicon glyphicon-plus-sign'>Question</span>
                        </button>
	                    <input type="submit" class='btn-sm btn-success col-md-4 form-group' value="Save This Quiz!"/>
                      <button class='btn-sm btn-warning col-md-4' onclick="location.href='../Pages/LessonOverview.php';">Cancel</button>
                    </div>
        		</form>
            </div><!--Close column-->
            <div class='col-md-4'></div> <!-- Formatting column for right side of screen -->
        </div><!--Close Container-->
   </body>
</html>
