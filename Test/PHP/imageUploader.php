<?php
$allowed = array( 'png', 'jpg', 'gif' );
if( isset($_FILES['file']) && $_FILES['file']['error'] == 0 ) {
    $extension = pathinfo( $_FILES['file']['name'], PATHINFO_EXTENSION );
    if( !in_array( strtolower( $extension ), $allowed ) ) {
         echo 'AN ERROR OCCURED - INVALID IMAGE';
        exit;
    }
    if( move_uploaded_file( $_FILES['file']['tmp_name'], 'assets/images/'.$_FILES['file']['name'] ) ) {
        echo base_url().'assets/images/'.$_FILES['file']['name']; // echo absolute file_url
        exit;
    }
}
echo 'AN ERROR OCCURED';
exit;
?>
