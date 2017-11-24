<?php
require_once 'service/AlbumService.php';
$service = new AlbumService();
$id = $_GET['album-id'];
if (isset($id)) {
    $service->getImage($id);
}
?>