<?php
require_once 'service/UploadService.php';
$service = new UploadService();
$id = $_GET['upload-id'];
if (isset($id)) {
    $service->getImage($id);
}
?>