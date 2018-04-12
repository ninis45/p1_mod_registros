<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Configuracion_m extends MY_Model {

	private $folder;
    private $update = array();
	public function __construct()
	{
		parent::__construct();
		$this->_table = 'registro_configuracion';
		
	}
    public function save($id,$data)
    {
        $result = $this->get_by('id_evento',$id);
        
        if($result)
        {
            $this->update($result->id,$data);
            return $result->id;
        }
        else
        {
            return  $this->insert($data);
        }
    }
 }
 ?>