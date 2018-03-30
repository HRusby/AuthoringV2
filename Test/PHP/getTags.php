<?php
    //open connection to mysql db
ini_set("default_charset", 'utf-8');
    $db = new mysqli("localhost", "root", "topolor", "topolor") or die("Error " . mysqli_error($db));
    
    $query = "SELECT id, name FROM tpl_tag WHERE name LIKE ? ORDER BY frequency";
    $stmt = $db->stmt_init();
    $stmt->prepare($query);
    $stmt->bind_param("s", $_GET['q']);
    $stmt->execute();
    $result = $stmt->get_result();
    $arr = array();
    while($row = $result->fetch_assoc()){
        $arr[] = $row;
    }
    
    $json_response = json_encode($arr);
    echo $json_response;
    $db->close();
    
?>