<?php
class Album {
	public $id;
	public $name;
	public $description;
	public $image;
	public $image_type;
	public $id_user;
	public $uploads;

	public function __construct($row) {
		if ($row != NULL) {
	    	$this->id 	= $row['id_album'];
	    	$this->name = $row['name'];
	    	$this->description = $row['description'];
	    	$this->image = $row['image'];
	    	$this->image_type = $row['image_type'];
	    	$this->id_user = $row['id_user'];
		}  
	}

	public function getId() {
		return $this->id;
	}

	public function setId($id){
		$this->id = $id;
	}

	public function toString() {
	 	return "Id: ".$this->id." Name: ".$this->name;
	}
}
?>
