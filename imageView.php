<?php
require_once 'service/AlbumService.php';
$service = new AlbumService();
$id = $_GET['album-id'];
if (isset($id)) {
    /* $obj = $service->getImage($id);
   	header("Content-type: image/jpeg");
    echo $obj; */
    $service->getImage($id);
}
?>
