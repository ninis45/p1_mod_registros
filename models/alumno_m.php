<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Alumno_m extends MY_Model {

	private $folder;
    private $update = array();
	public function __construct()
	{
		parent::__construct();
		$this->_table = 'alumnos';
		
	}
 }
 ?>