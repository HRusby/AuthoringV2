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

// echo "<div class='row'>";
echo "<div class='row h-100 justify-content-center align-items-center my-3'>";
echo "<form class='w-75 bg-white border border-light p-3' method='POST' action='../PHP/createModule2.php' id='moduleUpdateForm'>";
echo "<div class='form-group'><label for='moduleTitle'>Module Title:</label><input type='text' class ='form-control' placeholder='Module Title' id='moduleTitle' name='moduleTitle'/></div>";
echo "<div class='form-group'><label for='moduleContent'>Module Content:</label><textarea class='form-control' id='moduleContent' name='moduleContent' placeholder='Module Description' style='resize:vertical;'></textarea></div>";
echo "<div class='form-group'><label for='tokenField'>Tags:</label><input type='text' class='form-control' id='tokenField' name='tokenField' placeholder='Select an item or type some text and hit enter!' /></div>";
echo "<script>$('#tokenField').tokenfield({autocomplete: {source: [".$tags."], delay: 100}, showAutocompleteOnFocus: true})</script>";
echo "<div class='btn-group'><button class='btn btn-primary' type='submit'>Update Module</button></div>";
echo "</form>";
echo "</div>"; // Close row container

?>
