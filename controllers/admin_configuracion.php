<?php defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Admin Blog Fields
 *
 * Manage custom blogs fields for
 * your blog.
 *
 * @author 		PyroCMS Dev Team
 * @package 	PyroCMS\Core\Modules\Users\Controllers
 */
class Admin_configuracion extends Admin_Controller {

	protected $section = 'configuracion';

	// --------------------------------------------------------------------------

	public function __construct()
	{
		parent::__construct();
        $this->lang->load('registro');
        $this->validation_rules = array(
 		             array(
						'field' => 'participante',
						'label' => 'Participante',
						'rules' => 'trim'
						),
                     array(
						'field' => 'auth',
						'label' => 'Autenticación',
						'rules' => 'trim|required'
						),
                     array(
						'field' => 'descripcion',
						'label' => 'Descripción',
						'rules' => 'trim|required'
						),
                      array(
						'field' => 'module',
						'label' => 'Módulo',
						'rules' => 'trim'
						),
                     array(
						'field' => 'module_id',
						'label' => 'Campo primario',
						'rules' => 'trim'
						),
                     array(
						'field' => 'group_by',
						'label' => 'Agrupado',
						'rules' => 'trim'
						),
                     array(
						'field' => 'campos',
						'label' => 'Campos',
						'rules' => ''
						),
                     array(
						'field' => 'acuse',
						'label' => 'Acuse',
						'rules' => 'trim'
						),
                     array(
						'field' => 'cerrado',
						'label' => 'Cerrado',
						'rules' => 'trim'
						),
                    array(
						'field' => 'disciplinas',
						'label' => 'Disciplinas',
						'rules' => 'trim'
						),
                     array(
						'field' => 'forced',
						'label' => 'Bloquear por autocomplete',
						'rules' => 'trim'
						),
                    array(
						'field' => 'autocomplete',
						'label' => 'Busqueda por autocomplete',
						'rules' => 'trim'
						),
                     array(
						'field' => 'autocomplete_display',
						'label' => 'Vista autocomplete',
						'rules' => 'trim'
						),
                     array(
						'field' => 'javascript',
						'label' => 'Javacript',
						'rules' => ''
						),
                    array(
						'field' => 'template',
						'label' => 'Plantilla email',
						'rules' => 'trim'
						),
                    array(
						'field' => 'fotografia',
						'label' => 'Fotografia',
						'rules' => 'trim'
						),
                   array(
						'field' => 'template_column',
						'label' => 'Plantilla de las columnas',
						'rules' => 'trim'
						),
                    
        );
        $this->load->model('configuracion_m');
    }
    
    function load($id)
    {
        $this->load->model('eventos/evento_m');
        $configuracion = $this->configuracion_m->get_by('id_evento',$id);
        $evento        = $this->evento_m->get($id) OR redirect('admin/registros');
        
        $this->form_validation->set_rules($this->validation_rules);        
        
        
		if($this->form_validation->run())        
        {
			unset($_POST['btnAction']);
            $data = array( 
                'id_evento'   => $id,
                'auth'        => $this->input->post('auth'),
                'titulo'      => $this->input->post('titulo'),
                'descripcion' => $this->input->post('descripcion'),
                'module'      => $this->input->post('module'),
                'module_id'   => $this->input->post('module_id'),
                'autocomplete'   => $this->input->post('autocomplete'),
                'autocomplete_display'   => $this->input->post('autocomplete_display'),
                'forced'        => $this->input->post('forced'),
                'disciplinas'   => $this->input->post('disciplinas'),
                'template'   => $this->input->post('template'),
                'template_cedula'   => $this->input->post('template_cedula'),
                'template_column'   => $this->input->post('template_column'),
                'participante'        => $this->input->post('participante'),
                
                'acuse'   => $this->input->post('acuse'),
                'cerrado'   => $this->input->post('cerrado'),
                
                'fotografia'   => $this->input->post('fotografia'),
                
                'javascript'   => $this->input->post('javascript'),
                'group_by'   => $this->input->post('group_by'),
                'campos'      => $this->input->post('campos')?json_encode($this->input->post('campos')):null,
            );
            
            
            if($this->configuracion_m->save($id,$data))
            {
				
				$this->session->set_flashdata('success',lang('configuracion:save_success'));
				
			}else{
				$this->session->set_flashdata('error',lang('configuracion:save_error'));
				
			}
			redirect($this->uri->uri_string());
        }
        
        if(!$configuracion)
        {   
            $configuracion = new StdClass();
            
            foreach ($this->validation_rules as $rule)
    		{
    			$configuracion->{$rule['field']} = $this->input->post($rule['field']);
    		}
        }
        
        if($_POST)
        {
            $configuracion = (object)$_POST;
        }
        $modules = array(
        
            array(
                'slug' => 'alumnos',
                'name' => 'Alumnos',
                'rows' => array()
            ),
            array(
                'slug' => 'egresados',
                'name' => 'Egresados',
                'rows' => array()
            )
        );//$this->db->select('slug')->where_in('slug',array('users'))
                     //   ->get('modules')->result();
        
        foreach($modules as &$module)
        {
            $module['rows'] = (array)$this->db->list_fields($module['slug']);
            
            foreach($module['rows'] as &$row)
            {
                //$row->visible = 1;
            }
            
        }
        
        
        
        $templates = $this->db->get('email_templates')->result();
        
        $this->template->title($this->module_details['name'],lang('configuracion:title'))//->set('id',$id)
                ->set('configuracion',$configuracion)
                ->set('evento',$evento)
                ->set('templates',array_for_select($templates,'slug','name'))
                ->append_js('module::registro.controller.js')
                ->append_metadata('<script type="text/javascript">var modules='.json_encode($modules).',rows_right='.($configuracion->campos?$configuracion->campos:'[]').'; </script>')
                ->build('admin/configuracion/index');
    }
 }
 ?>