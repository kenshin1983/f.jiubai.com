<?php 

class Gq_Models {
	protected $_db;
	public function __construct($conn)
    {
    	$this->_db = $conn;
    }
}
?>