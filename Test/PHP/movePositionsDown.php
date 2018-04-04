<?php
  echo "\nMove ID: ".$_POST['moveID'];
  echo "\nPositionID: ".$_POST['positionID'];

  //as moving up move lft/rgt > position lft/rgt
  // Therefore change all values of move topics to lft/rgt - difference between move lft and position lft

  // Load lft and rgt values of move and position

  $db = new mysqli("localhost", "root", "topolor", "topolor");
  $db->set_charset("utf8");

  $moveRelationQuery = "SELECT * FROM tpl_tutorial_topic WHERE topic_id=?;";
  $moveRelationStmt = $db->stmt_init();
  $moveRelationStmt->prepare($moveRelationQuery);
  $moveRelationStmt->bind_param("i", $_POST['moveID']);
  $moveRelationStmt->execute();
  $moveRelation = $moveRelationStmt->get_result()->fetch_assoc();

  $positionRelationQuery = "SELECT * FROM tpl_tutorial_topic WHERE topic_id=?;";
  $positionRelationStmt = $db->stmt_init();
  $positionRelationStmt->prepare($positionRelationQuery);
  $positionRelationStmt->bind_param("i", $_POST['positionID']);
  $positionRelationStmt->execute();
  $positionRelation = $positionRelationStmt->get_result()->fetch_assoc();

  // Load all topics between the respective lft and rgt values
  $moveTopicRelationsQuery = "SELECT * FROM tpl_tutorial_topic WHERE lft > ? AND rgt < ?;";
  $moveTopicRelationsStmt = $db->stmt_init();
  $moveTopicRelationsStmt->prepare($moveTopicRelationsQuery);
  $moveTopicRelationsStmt->bind_param("ii", $moveRelation['lft'], $moveRelation['rgt']);
  $moveTopicRelationsStmt->execute();
  $moveTopicRelationsResult = $moveTopicRelationsStmt->get_result();

  $positionTopicRelationsQuery = "SELECT * FROM tpl_tutorial_topic WHERE lft > ? AND rgt < ?;";
  $positionTopicRelationsStmt = $db->stmt_init();
  $positionTopicRelationsStmt->prepare($positionTopicRelationsQuery);
  $positionTopicRelationsStmt->bind_param("ii", $positionRelation['lft'], $positionRelation['rgt']);
  $positionTopicRelationsStmt->execute();
  $positionTopicRelationsResult = $positionTopicRelationsStmt->get_result();

  // Find Difference between move and position lfts
  $positionDifference =  $positionRelation['lft'] - $moveRelation['lft'];
  echo "\nPositionDifference: ".$positionDifference;

  // Then update each topics lft rgt values
  $updateRelationsQuery = "UPDATE tpl_tutorial_topic SET lft=(lft+".$positionDifference."), rgt=(rgt+".$positionDifference.") WHERE topic_id=?;";
  $updateRelationsStmt = $db->stmt_init();
  $updateRelationsStmt->prepare($updateRelationsQuery);
  while($relation = $moveTopicRelationsResult->fetch_assoc()){
    echo "\nMoveTopicRelations TopicID: ".$relation['topic_id'];
    $updateRelationsStmt->bind_param("i", $relation['topic_id']);
    $updateRelationsStmt->execute();
  }

  $updateRelationsQuery = "UPDATE tpl_tutorial_topic SET lft=(lft-".$positionDifference."), rgt=(rgt-".$positionDifference.") WHERE topic_id=?;";
  $updateRelationsStmt = $db->stmt_init();
  $updateRelationsStmt->prepare($updateRelationsQuery);
  while($relation = $positionTopicRelationsResult->fetch_assoc()){
    echo "\nPositionTopicRelations TopicID: ".$relation['topic_id'];
    $updateRelationsStmt->bind_param("i", $relation['topic_id']);
    $updateRelationsStmt->execute();
  }

  // Update the root nodes
  $updateRelationsQuery = "UPDATE tpl_tutorial_topic SET lft=(lft+".$positionDifference."), rgt=(rgt+".$positionDifference.") WHERE topic_id=?;";
  echo "\nmoveUpdateQuery: ".$updateRelationsQuery;
  $updateRelationsStmt = $db->stmt_init();
  $updateRelationsStmt->prepare($updateRelationsQuery);
  $updateRelationsStmt->bind_param("i", $_POST['moveID']);
  $updateRelationsStmt->execute();

  $updateRelationsQuery = "UPDATE tpl_tutorial_topic SET lft=(lft-".$positionDifference."), rgt=(rgt-".$positionDifference.") WHERE topic_id=?;";
  $updateRelationsStmt = $db->stmt_init();
  $updateRelationsStmt->prepare($updateRelationsQuery);
  $updateRelationsStmt->bind_param("i", $_POST['positionID']);
  $updateRelationsStmt->execute();

  $db->close();
?>
