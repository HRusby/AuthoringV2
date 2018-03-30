<?php
    $db = new mysqli("localhost", "root", "topolor", "topolor");
    $query = "";
    $Result = $db->query($query);
    while($row = $Result->fetch_assoc()){
        
        // Action on data by using $row['DBcolumnName']
    }
    $db->close();
?>