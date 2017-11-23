<?php
class Usuario {
	public $id;
	public $login;
	public $password;
	public $email;
	public $superuser;
	public $salvarSenha;
	public $novaSenha;

	public function __construct($row) {
		if ($row != NULL) {
	    	$this->id 	= $row['id_user'];
	    	$this->login = $row['login'];
	    	$this->password = $row['password'];
	    	$this->email = $row['email'];
	    	$this->superuser = $row['superuser'];
	    	$this->salvarSenha = FALSE;
	    	$this->novaSenha = NULL;
		}  
	}

	public function getId() {
		return $this->id;
	}

	public function setId($id){
		$this->id = $id;
	}

	public function toString() {
	 	return "Id: ".$this->id." Login: ".$this->login;
	}
}
?>
