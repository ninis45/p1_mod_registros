<?php defined('BASEPATH') OR exit('No direct script access allowed');
/**
 * PyroCMS File library. 
 *
 * This handles all file manipulation 
 * both locally and in the cloud
 * 
 * @author		Jerel Unruh - PyroCMS Dev Team
 * @package		PyroCMS\Core\Modules\Files\Libraries
 */
class Registro
{
    public function __construct()
	{
	   ci()->load->model(array(
       
            'medallero/disciplina_m'
       ));
    }
    ///Nuevo agregado el 02 Octubre 2017, permite modificar  y actualizar la tabla vinculada.
    public static function SaveResource($configuracion,$id_resource,$input)
    {
        if(empty($configuracion->campos)== false)
        {
            
            $values_table = ci()->db->where($configuracion->module_id,$id_resource)->get($configuracion->module)->row();
            
           
            $data_update = array(
            
            );
            if($values_table){
                foreach($configuracion->campos as $field)
                {
                        if(array_key_exists($field->slug,$values_table)&& $field->grupo=='table')
                        {
                            $data_update[$field->slug] = $input[$field->slug];
                        }
                }
                
                if($data_update)
                {
                    return ci()->db->where($configuracion->module_id,$id_resource)
                            ->set($data_update)
                            ->update($configuracion->module);
                }
            }
           
            return false;
        }
       
    }
    public static function Save($input,$configuracion,$registro)
    {
        if($registro->module_id)
        {
            $values_table = ci()->db->where($configuracion->module_id,$registro->module_id)->get($configuracion->module)->row();
            $data_input   = array();
            $data_extra   = array();
            
            ///Realiza un recorrido de los campos habilitados para este registro
            foreach(json_decode($configuracion->campos) as $campo)
            {
               
                if(array_key_exists($campo->slug,$values_table)&& $campo->grupo=='table')
                {
                    $data_input[$campo->slug] = $input[$campo->slug];
                }
            }
            
            if($data_input)
            {
                
                ci()->db->where($configuracion->module_id,$registro->module_id)
                        ->set($data_input)
                        ->update($configuracion->module);
            }
        }
    }
    public static function Uploads($configuracion,$registro)
    {
         if($configuracion){
            foreach(json_decode($configuracion->campos) as $campo)
            {
                if($campo->tipo == 'upload')
                {
                    
                }
            }
         }
    }
    ///Remover no tiene caso
    public static function GetDisciplinas($id_evento=0)
    {
         $disciplinas = ci()->disciplina_m->where('id_evento',$id_evento)
                            ->get_all();
                            
        foreach($disciplinas as &$disciplina)
        {
            $disciplina->rama          = $disciplina->rama==1?'Varonil':($disciplina->rama==2?'Femenil':'Indistinto');
            $disciplina->participantes = ci()->db->select('count(*) as total,centros.nombre,id_disciplina,registros.id_evento,id_centro,registros.id_evento,disciplinas.nombre AS disciplina,disciplinas.tipo AS tipo,registros.rama')
                                                ->where('id_disciplina',$disciplina->id)
                                                ->join('centros','centros.id=registros.id_centro')
                                                ->join('disciplinas','disciplinas.id=registros.id_disciplina')
                                                ->group_by('centros.nombre')
                                                ->get('registros')->result();
        }
        
        return $disciplinas;
    }
    
    public static function SendEmail($id,$configuracion,$evento)
    {
        //$data['id']                 = $id;  
                            $data['evento']             = $evento; 
                            $data['registro']           = $this->registro_m->get($id); 
                            $data['nombre']             = strtoupper($this->input->post('participante'));
                        	$data['slug'] 				= $configuracion->template;
                       		$data['to'] 				= $data['email'];
                       		$data['from'] 				= Settings::get('server_email');
                       		$data['name']				= Settings::get('site_name');
                       		$data['reply-to']			= Settings::get('contact_email');
                             Events::trigger('email', $data, 'array');
    }
    public static function GetResult($configuracion,$registro)
    {
        
        $values_table  = false;
        
        
        empty($registro->module_id)== false AND  $values_table  = ci()->db->where($configuracion->module_id,$registro->module_id)->get($configuracion->module)->row();
       
        $values_custom = $registro->extra?json_decode($registro->extra):array();
        $values        = array();
        
        
        if($configuracion){
            foreach(json_decode($configuracion->campos) as $campo)
            {
                $values[$campo->slug] = '';
                if($values_table && array_key_exists($campo->slug,$values_table))
                {
                    $values[$campo->slug] =   $values_table->{$campo->slug};
                }
                
                if($values_custom && array_key_exists($campo->slug,$values_custom))
                {
                    $values[$campo->slug] = $values_custom->{$campo->slug};
                }
                
            }
        }
        return $values;
        //print_r($configuracion);
        
    }
    
}