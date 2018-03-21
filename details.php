<?php defined('BASEPATH') or exit('No direct script access allowed');

/**
 * Groups module
 *
 * @author PyroCMS Dev Team
 * @package PyroCMS\Core\Modules\Groups
 */
 class Module_Registros extends Module
{

	public $version = '1.0';

	public function info()
	{
		$info= array(
			'name' => array(
				'en' => 'Registers',
				
				'es' => 'Registros',
				
			),
			'description' => array(
				'en' => 'N/A',
				
				'es' => 'Administra el registro de los participantes en las diferentes disciplinas y recursos',
				
			),
			'frontend' => false,
			'backend' => true,
			'menu' => 'admin',
            'roles' => array(
				
			),
            'sections'=>array(
                'registros'=>array(
                    'name'=>'registros:title',
                    //'ng-if'=>'hide_shortcuts',
                    'uri' => 'admin/registros/{{ id_evento }}',
        			'shortcuts' => array(
        				/*array(
        					'name' => 'registros:download',
        					'uri' => 'admin/registros/download/{{ id_evento }}?'.http_build_query($_GET),
        					'class' => 'btn btn-default',
                           
                            
        				),*/
                        	array(
        					'name' => 'registros:report',
        					'uri' => 'admin/registros/report/{{ id_evento }}',
        					'class' => 'btn btn-default',
                           
                            //'open-modal'=>'',
                            //'modal-title'=>'Disciplinas',
        				),
                        array(
        					'name' => 'registros:create',
        					'uri' => 'admin/registros/create/{{ id_evento }}',
        					'class' => 'btn btn-success',
                           
                            //'open-modal'=>'',
                            //'modal-title'=>'Disciplinas',
        				),
                        
                        array(
        					'name' => 'registros:import',
        					'uri' => 'admin/registros/import/{{ id_evento }}',
        					'class' => 'btn btn-success',
                           
                            'open-modal'=>'',
                            'modal-title'=>'Seleccionar origen'
        				),
                       
        			)
                )
           )
		);
        
        if (function_exists('group_has_role'))
		{
			if(group_has_role('fondo', 'admin_configuracion'))
			{
			    
				$info['sections']['configuracion'] = array(
							'name' 	=> 'configuracion:title',
							'uri' 	=> 'admin/registros/configuracion/{{ id_evento }}',
							'shortcuts' => array(
									/*'create' => array(
										'name' 	=> 'disciplinas:create',
										'uri' 	=> 'admin/medallero/disciplinas/create/{{ id }}',
										'class' => 'btn btn-success'
									)*/
							)
				);
			}
		}
        
        
        return $info;
	}

	public function install()
	{
	    $this->dbforge->drop_table('registros');
        $this->dbforge->drop_table('registro_configuracion');
        
		$tables = array(
		    'registros'=>array(
				'id'           => array('type' => 'INT', 'constraint' => 11, 'auto_increment' => true, 'primary' => true,),
                'id_evento'  => array('type' => 'INT', 'constraint' => 11,'null'=>true),
                'module_id'  => array('type' => 'VARCHAR', 'constraint' => 255,'null'=>true),
                'module'       => array('type' => 'VARCHAR','constraint' => 255, 'null' => true,),
                'participante' => array('type' => 'VARCHAR','constraint' => 255, 'null' => true,),
                'id_centro'  => array('type' => 'INT', 'constraint' => 11,'null'=>true),
                'id_disciplina'  => array('type' => 'INT', 'constraint' => 11,'null'=>true),
                'extra'       => array('type' => 'TEXT','null'=>true),
                
				
                
				
            ),
            
            'registro_configuracion'=>array(
            
                'id' => array('type' => 'INT', 'constraint' => 11, 'auto_increment' => true, 'primary' => true,),
                'id_evento' => array('type' => 'INT', 'constraint' => 11,'null'=>true),
                'module' => array('type' => 'VARCHAR','constraint' => 255, 'null' => true,),
                'titulo' => array('type' => 'VARCHAR','constraint' => 255, 'null' => true,),
                'descripcion' => array('type' => 'TEXT', 'null' => true,),
                'extra'       => array('type' => 'TEXT', 'null' => true,),
                
                'fecha_ini' => array('type' => 'DATE','null' => true,),
                'fecha_fin' => array('type' => 'DATE','null' => true,),
                'horario' => array('type' => 'VARCHAR','constraint' => 255, 'null' => true,),
                
            )
			
		);
        
        if ( ! $this->install_tables($tables))
		{
			return false;
		}

        return true;
        
		

		
	}

	public function uninstall()
	{
	  
        $this->dbforge->drop_table('registros');
        $this->dbforge->drop_table('registro_configuracion');
		return true;
	}

	public function upgrade($old_version)
	{
		return true;
	}

}
?>