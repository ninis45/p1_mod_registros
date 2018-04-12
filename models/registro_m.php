<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Registro_m extends MY_Model {

	private $folder;
   
	public function __construct()
	{
		parent::__construct();
		$this->_table = 'registros';
		
	}
    public function save($data)
    {
        $registro = $this->get_by(array(
        
            'id_evento' => $data['id_evento'],
            'module_id' => $data['module_id']
        ));
        
        if($data['module_id'] && $registro)
        {
            $data['updated_on'] = now();
            $this->update($registro->id,$data);
            
            return $registro->id;
        }
        else
        {
            $data['created_on'] = now();
            return $this->insert($data);
        }
    }
    
 }
 ?>