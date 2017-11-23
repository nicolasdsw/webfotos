<?php
class Upload {
	public $id;
	public $file;
	public $subtitle;
	public $id_album;

	public function __construct($row) {
		if ($row != NULL) {
	    	$this->id 	= $row['id_upload'];
	    	$this->file = $row['file'];
	    	$this->subtitle = $row['subtitle'];
	    	$this->id_album = $row['id_album'];
		}  
	}

	public function getId() {
		return $this->id;
	}

	public function setId($id){
		$this->id = $id;
	}

	public function toString() {
	 	return "Id: ".$this->id." Legenda: ".$this->subtitle;
	}
}
?>
