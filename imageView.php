<?php
require_once 'service/AlbumService.php';
$service = new AlbumService();
$id = $_GET['album-id'];
if (isset($id)) {
    $obj = $service->getById($id);
    header("Content-type: image/jpg");
    echo $obj->image;
}
?>
