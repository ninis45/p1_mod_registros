<?php defined('BASEPATH') OR exit('No direct script access allowed');
/**
 * Roles controller for the groups module
 *
 * @author		Phil Sturgeon
 * @author		PyroCMS Dev Team
 * @package	 PyroCMS\Core\Modules\Groups\Controllers
 *
 */
class Admin extends Admin_Controller
{

	/**
	 * Constructor method
	 */
	protected $section = 'registros';
	public function __construct()
	{
			parent::__construct();
            $this->load->model(array(
                        'registros/registro_m',
                        'medallero/disciplina_m',
                        'configuracion_m',
                        'centros/centro_m',
                        'files/file_folders_m'
                        
            ));
            $this->load->library('Registro');
            $this->lang->load('registro');
            $this->load->library('files/files');
            $this->validation_rules = array(
                'form'=>array(
        			'participante'=>array(
        				'field' => 'participante',
        				'label' => 'Participante',
        				'rules' => 'trim|required|callback__valid_participante'
        			),
          	        'module_id' => array(
        				'field' => 'module_id',
        				'label' => 'Module id',
        				'rules' => 'trim|callback__valid_module'
        			),
                    array(
        				'field' => 'activo',
        				'label' => 'Estatus',
        				'rules' => 'trim|required'
        			),
                ),
                'email' => array(
                
                     array(
        				'field' => 'subject',
        				'label' => 'Asunto',
        				'rules' => 'trim|required'
        			),
                     array(
        				'field' => 'body',
        				'label' => 'Mensaje',
        				'rules' => 'trim|required'
        			),
                )
                
    			
    		);
            
            
    }
    
    function index()
    {
        $eventos = $this->db->get('eventos')->result();
        
        
        
        $this->template
                ->set('eventos',$eventos)
                ->build('admin/init');
    }
    
    function _valid_module($field)
    {
        $registro = $this->registro_m->get_by(array(
            'id_evento'    => $this->input->post('evento'),
            'module_id' => trim($field),
            
        ));
        
        
       
        if($field && $registro && $registro->id != $this->input->post('id'))
        {
            $this->form_validation->set_message('_valid_module',lang('registros:error_duplicate'));
            
            return false;
        }
        return true;
    }
    function _valid_participante($field)
    {
        
        $registro = $this->registro_m->get_by(array(
            'id_evento'    => $this->input->post('evento'),
            'participante' => trim($field),
            
        ));
        
        
        
        if($registro && $registro->id != $this->input->post('id'))
        {
            $this->form_validation->set_message('_valid_participante',lang('registros:error_duplicate'));
            
            return false;
        }
        return true;
    }
    function report($id_evento)
    {
        
        $configuracion = $this->db->where('id_evento',$id_evento)->get('registro_configuracion')->row();
        
        $base_where = array(
            'registros.id_evento' => $id_evento,
            'inscrito'=> 1
        );
        $data = array();
        $head = array();
        
        $tipo   = $this->input->get('tipo');
        $centro = $this->input->get('centro');
        
        if($tipo)
        {
            $base_where['disciplinas.tipo'] = $tipo; 
        }
        
        if($centro)
        {
            $base_where['registros.id_centro'] = $centro; 
        }
        //centros.nombre AS nombre_centro,id_centro,
        
        
        if($configuracion->module)
        {
            $this->registro_m->join($configuracion->module,$configuracion->module.'.'.$configuracion->module_id.'=default_registros.module_id');
        }
        $registros = $this->registro_m->select('user_id,participante,disciplinas.nombre AS nombre_disciplina,registros.id_disciplina,registros.rama,disciplinas.tipo AS tipo_disciplina,registros.sexo,extra'.($configuracion->group_by?','.$configuracion->group_by:''))
                            ->join('disciplinas','disciplinas.id=registros.id_disciplina')
                            //->join('centros','centros.id=registros.id_centro')
                            ->where($base_where)
                            //->group_by('id_centro,registros.id_disciplina,sexo')
                            ->order_by('sexo')
                            ->get_all();
        
        
        foreach($registros as $registro)
        {
            //$registro->sexo = $registro->sexo == 1?'Hombre':($registro->sexo == 2?'Mujer':'Sin descripción');
            
            
            $registro->sexo = $registro->sexo?$registro->sexo:0;
            
            if(!array_key_exists($registro->id_disciplina,$head))
            {
                //$registro->nombre_disciplina
                $head[$registro->id_disciplina] = array(
                    'nombre' => $registro->nombre_disciplina,
                    'params' => array()
                );
                //$head[$registro->nombre_disciplina] = array();
                
            
            }
            if(!in_array($registro->sexo,$head[$registro->id_disciplina]['params']))
            {
               
                $head[$registro->id_disciplina]['params'][] = $registro->sexo;
                
            }
            
            /*if(!in_array($registro->id_disciplina,$head[$registro->nombre_disciplina]))
            {
                $head[$registro->nombre_disciplina][] = $registro->id_disciplina;
            }*/
            if(!isset($data[$registro->{$configuracion->group_by}]))
            {
               
               
                $data[$registro->{$configuracion->group_by}] = 
                    array(
                    //'rama'       => $registro->rama,
                    //'disciplina' => $registro->nombre_disciplina,
                    'asesores' =>  $this->db->where(array('user_id'=>$registro->user_id))->count_all_results('disciplina_asesores'),
                    'centro'         => $registro->nombre_centro,
                    'disciplinas'    => array(),
                    'personas'       => array()/*$this->db->select('participante')
                                                ->where('registros.id_centro',$registro->id_centro)
                                                ->where('registros.id_evento',$id_evento)
                                                ->where('tipo',$tipo)
                                                ->group_by('participante')
                                                ->join('disciplinas','disciplinas.id=registros.id_disciplina')
                                                ->get('default_registros')
                                                
                                                ->result()*/
                );
            }
            //Verificar asesores
            
            if(!in_array($registro->participante,$data[$registro->{$configuracion->group_by}]['personas']))
            {
                $data[$registro->{$configuracion->group_by}]['personas'][] = $registro->participante;
            }
            
            $registro->extra = json_decode($registro->extra);
            
            
            
            if(empty($registro->extra->asesor)== false && !in_array($registro->extra->asesor,$data[$registro->id_centro]['asesores']))
            {
                $data[$registro->{$configuracion->group_by}]['asesores'][] = $registro->extra->asesor;
                
            }
            /*if(in_array($registro->participante,$data[$registro->id_centro]['personas']) == false)
            {
                $data[$registro->id_centro]['personas'][] = $registro->participante;
            }*/
            $registro->rama = $registro->rama==1?'Varonil':'Femenil';
            
          
            
            /*if($registro->tipo_disciplina == 'deportivo'){
                $data[$registro->{$configuracion->group_by}]['disciplinas'][$registro->nombre_disciplina][$registro->rama] += 1;//= $registro->total;
            }
            else
            {
                $data[$registro->{$configuracion->group_by}]['disciplinas'][$registro->nombre_disciplina][$registro->sexo] += 1;//$registro->total;
                
            }*/
            //if($registro->sexo)
                $data[$registro->{$configuracion->group_by}]['disciplinas'][$registro->id_disciplina][$registro->sexo] += 1;
            //else
              //  $data[$registro->{$configuracion->group_by}]['disciplinas'][$registro->nombre_disciplina] += 1;
        }
        
        //print_r($data);
        //exit();
        $this->template->set('data',$data)
                    ->enable_parser(true)
                    ->set('head',$head)
                    ->set('tipo',$tipo)
                    ->set('id_evento',$id_evento)
                    ->set('centros',$this->centro_m->dropdown('id','nombre'))
                    ->build('admin/report');
       
    }
    
    //Id resource nos define la importación de un evento a otro evento
    function import($id_evento,$id_resource='')
    {
       ini_set('max_execution_time', 0); 
        $eventos = $this->db//->where_not_in('id',array($id_evento))
                              ->get('eventos')->result();
                              
                              
        $configuracion = $this->db->where('id_evento',$id_evento)->get('registro_configuracion')->row();
        //$data    = array();
        
                    
        if($_POST)
        {
            $adds    = 0;
            $updates = 0;
            $result = array(
                'status'  => false,
                'message' => '',
                'data'    => array()
            );
            //Detectarmos si el recurso va a provenir de otro evento
            if($id_resource)
            {
                $clone_parts = $this->registro_m->where(array(
                                'id_evento'     => $this->input->post('id_evento'),
                                'id_centro'     => $this->input->post('id_centro') ,
                                'id_disciplina' => $this->input->post('id_disciplina'),
                            ))->get_all(); 
            }
            else{
                $groups = $this->input->post('groups');
                $rows_import = false;
                if($configuracion->group_by && $groups)
                {
                    $rows_import = $this->db->where_in($configuracion->group_by,$groups)
                                    ->get($configuracion->module)->result();
                }
                $segments = $configuracion?explode(',',$configuracion->participante) :array();
                if($rows_import)
                {
                    foreach($rows_import as $row)
                    {
                         
                         
                         
                         $data = array(
                            'id_evento'  => $id_evento,
                            'module_id'  => $row->{$configuracion->module_id},
                            'module'     => $configuracion->module,
                            //'created_on' => now(),
                            //'active'     => 1,
                            //'participante'     => ''
                         );
                         if($this->registro_m->get_by($data)==false){
                             //Construimos el nombre del participante
                             $data['participante'] = '';
                             $data['activo']       = 1;
                             $data['created_on']   = now();
                             $data['user_id']      = $this->input->post('user_id');
                             
                             $data['extra']        = json_encode($row); 
                             print_r($segments);
                             foreach($segments AS $segment)
                             {
                                 $data['participante'] .= $data['participante']==''?$row->{$segment}:' '.$row->{$segment};
                             }
                             
                             if($this->registro_m->insert($data))
                             {
                                $adds++;
                             }
                             
                         }
                    }
                }
               
                if($adds>0)
                {
                    $this->session->set_flashdata('success',sprintf(lang('registros:import_success'),$adds));
                    
                    $url_return = 'admin/registros/import/'.$id_evento.($id_resource==''?'':'/'.$id_resource);
                    redirect($url_return);
                }
            }
            /*$clone_parts = $this->registro_m->where(array(
                                'id_evento'     => $this->input->post('id_evento'),
                                'id_centro'     => $this->input->post('id_centro') ,
                                'id_disciplina' => $this->input->post('id_disciplina'),
                            ))->get_all(); 
            if($clone_parts)
            {
                $result['status'] = true;
                foreach($clone_parts as $part)
                {
                    $data_insert = (array)$part;
                    
                    $data_insert['id_evento'] = $id_evento;
                    $data_disciplina          = array(
                                                    'id_evento' => $id_evento,
                                                    'tipo'      => $this->input->post('tipo'),
                                                    'rama'      => $this->input->post('rama'),
                                                    'nombre'    => $this->input->post('disciplina')
                                                );
                                                
                    unset($data_insert['id']);
                    if(!$disciplina = $this->db->where($data_disciplina)
                                ->get('disciplinas')->row())
                    {
                        $data_insert['id_disciplina'] = $this->disciplina_m->insert($data_disciplina);
                    }
                    else
                    {
                         $data_insert['id_disciplina'] = $disciplina->id;
                    }
                    
                    $this->registro_m->insert($data_insert);
                }
                $result['data'] = $clone_parts;
            }
            
            return $this->template->build_json($result);*/
            
        }  
        $disciplinas = $this->disciplina_m->where('id_evento',$id_resource)
                            ->get_all();
                         
        foreach($disciplinas as &$disciplina)
        {
            $disciplina->participantes = $this->db->select('count(*) as total,centros.nombre,id_centro,id_disciplina,registros.id_evento,disciplinas.nombre AS disciplina,disciplinas.tipo AS tipo,registros.rama')
                                                ->where('id_disciplina',$disciplina->id)
                                                ->join('centros','centros.id=registros.id_centro')
                                                ->join('disciplinas','disciplinas.id=registros.id_disciplina')
                                                ->group_by('centros.nombre')
                                                ->get('registros')->result();
            
            $disciplina->rama = $disciplina->rama==1?'Varonil':($disciplina->rama==2?'Femenil':'Indistinto');
            
        }
        //Para agregar desde base de datos
        if($configuracion->group_by)
        {
            $groups = $this->db->group_by($configuracion->group_by)->get($configuracion->module)->result();
            
            $this->template->set('groups',array_for_select($groups,$configuracion->group_by,$configuracion->group_by));
        
        } 
        if($configuracion->auth)
        {
            $this->load->model('users/user_m');
            $users = $this->user_m->get_all();
            
            $this->template->set('users',array_for_select($users,'id','email'));
            
            
        }  
        $this->input->is_ajax_request()
        ?$this->template->set_layout(false)
                    ->set('id_evento',$id_evento)
                    ->set('id_resource',$id_resource)
                    ->set('eventos',$eventos)
                    ->build('admin/list_eventos')
        :$this->template->title($this->module_details['name'],lang('registros:import'))
                    ->append_js('module::registro.controller.js')
                    ->set('id_resource',$id_resource)
                    ->set('id_evento',$id_evento)
                    ->set('configuracion',$configuracion)
                    ->set('disciplinas_right',Registro::GetDisciplinas($id_evento))
                    ->append_metadata('<script type="text/javascript">var disciplinas_right='.json_encode(Registro::GetDisciplinas($id_evento)).', url_current=\''.base_url($this->uri->uri_string()).'\', disciplinas='.json_encode($disciplinas).';</script>')
                    ->build('admin/form_import');
    }
    function create($id_evento=0,$module_id=0)
    {
        $configuracion = $this->configuracion_m->get_by('id_evento',$id_evento);
        $registro   = new StdClass();  
        
        //$values     = Registro::GetResult($configuracion,$registro);
        
        $foreigns = $configuracion? 
                            $this->db->select('*,'.$configuracion->module_id.' AS module_id')
                            ->get($configuracion->module)->result()
                            :array();
                            
        if($configuracion->campos)
        {
            foreach(json_decode($configuracion->campos) as $campo)
            {
                $this->validation_rules['form'][] = array(
    				'field' => $campo->slug,
    				'label' => $campo->nombre,
    				'rules' => 'trim'.($campo->obligatorio?'|required':'').($campo->slug=='email'?'|valid_email':'')
    				); 
            }
        }
        
        //$this->validation_rules['participante']['rules'].='|callback__valid_participante';
        $this->validation_rules['form']['module_id']['label']=$configuracion->module_id;
        
        $this->form_validation->set_rules($this->validation_rules['form']);
        
         if($this->form_validation->run())
		 {
			unset($_POST['btnAction']);
            
            $folder = $this->file_folders_m->get_by_path('portadas') OR show_error('La carpeta aun no ha sido creada: Portadas');
            
            /*$file = Files::upload($folder->id,true,'fotografia',false,false,false,'gif|png|jpg|jpeg');
            
            $extra = $this->input->post('extra');
            if($file['status'] == true)
            {
                $extra['fotografia'] = $file['data']['id'];
            }*/
            $extra = array();
            
            foreach(json_decode($configuracion->campos) as $campo)
            {
                //if(!$_FILES['xml_file']['name'])
               // {
                if($campo->tipo == 'upload' && $_FILES[$campo->slug]['name'])
                {
                }
                else
                {
                    $extra[$campo->slug] = $this->input->post($campo->slug);
                }
            }
            $data = array(
                'module_id'    => $this->input->post('module_id'),
                'module'       => $configuracion->module,
                
                'id_evento'      => $id_evento,
                //'id_centro'      => $this->input->post('id_centro'),
                'participante'   => $this->input->post('participante'),
                'telefono'     => $this->input->post('telefono'),
                'email'        => $this->input->post('email'),
                //'id_disciplina'  => $this->input->post('id_disciplina'),
                'activo'        => $this->input->post('activo'),
                'extra'          => $extra?json_encode($extra):null,
               
            );
           
            $data['module_id'] = $this->input->post($configuracion->module_id);
            
            if($id = $this->registro_m->insert($data))
            {
                Registro::Save($this->input->post('table'),$configuracion,(Object)$data);
               	$this->session->set_flashdata('success',lang('global:save_success'));
                
                
            }
            else
            {
                $this->session->set_flashdata('error',lang('global:save_error'));
            }
            
            redirect($this->input->post('uri_redirect'));
        }
        foreach ($this->validation_rules['form'] as $rule)
  		{
    			$registro->{$rule['field']} = $this->input->post($rule['field']);
  		} 
        /*
       
        
        $disciplinas = $this->disciplina_m->where('id_evento',$id_evento)->get_all();
        $dropdown_disciplinas = array();
        foreach($disciplinas as $disc)
        {
            $disc->rama = $disc->rama==1?'Varonil':($disc->rama==2?'Femenil':'Indistinto');
            $dropdown_disciplinas[$disc->rama][$disc->id]= $disc->nombre;
        }*/
        
        
        $this->template->title($this->module_details['name'],lang('registros:create'))
            ->set('registro',$registro)
            ->set('configuracion',$configuracion)
            //->set('values',$values)
            ->set('id_evento',$id_evento)
           
            //->set('centros',$this->centro_m->dropdown('id','nombre'))
            ->append_js('module::registro.controller.js')
            ->append_metadata('<script type="text/javascript">var foreigns='.json_encode($foreigns).',url_current=\''.base_url($this->uri->uri_string()).'\';</script>')
            //->set('disciplinas',$dropdown_disciplinas)
            ->set('module_id',$module_id)
			->build('admin/form');
    }
    function edit($id=0)
    {
         $registro = $this->registro_m->get($id) OR show_404();
         
         
         $configuracion = $this->configuracion_m->get_by('id_evento',$registro->id_evento) or show_error('La configuracion es requerida');
         
        
         $foreigns = $configuracion? 
                            $this->db->select('*,'.$configuracion->module_id.' AS module_id')
                            ->get($configuracion->module)->result()
                            :array();
                            
         //Extraemos los valores iniciales del extra y la tabla foranea.
         $values        = Registro::GetResult($configuracion,$registro);
         
         //Lo metemos como si fuera un solo objeto principal
         $registro = (Object)array_merge($values,(array)$registro);
        
         
         $this->form_validation->set_rules($this->validation_rules['form']);
        
         if($this->form_validation->run())
		 {
			unset($_POST['btnAction']);
            $folder = $this->file_folders_m->get_by_path('portadas') OR show_error('La carpeta aun no ha sido creada: Portadas');
            
            /*$file = Files::upload($folder->id,true,'fotografia',false,false,false,'gif|png|jpg|jpeg');
            
            $extra = $this->input->post('extra');
            if($file['status'] == true)
            {
                $extra['fotografia'] = $file['data']['id'];
            }*/
            
            $extra = array();
            
            foreach(json_decode($configuracion->campos) as $campo)
            {
                //if(!$_FILES['xml_file']['name'])
               // {
                if($campo->tipo == 'upload' && $_FILES[$campo->slug]['name'])
                {
                }
                else
                {
                    $extra[$campo->slug] = $this->input->post($campo->slug);
                }
            }
            $data = array(
                'module_id'    => $this->input->post('module_id'),
                'module'       => $configuracion->module,
                //'id_centro'      => $this->input->post('id_centro'),
                'participante'   => $this->input->post('participante'),
                'telefono'     => $this->input->post('telefono'),
                'email'        => $this->input->post('email'),
                //'sexo'           => $this->input->post('sexo'),
                //'id_disciplina'  => $this->input->post('id_disciplina'),
                'activo'         => $this->input->post('activo'),
                'extra'          => $extra?json_encode($extra):null,
               
            );
            
            if($this->registro_m->update($id,$data))
            {
               
			     if($configuracion->module)
                    Registro::SaveResource($configuracion,$this->input->post('module_id'),$this->input->post());
				$this->session->set_flashdata('success',lang('global:save_success'));
				
			}
            else
            {
				$this->session->set_flashdata('error',lang('global:save_error'));
				
			}
            
            redirect($this->input->post('uri_redirect'));
         }
         
        $disciplinas = $this->disciplina_m->where('id_evento',$registro->id_evento)->get_all();
        $dropdown_disciplinas = array();
        foreach($disciplinas as $disc)
        {
            $disc->rama = $disc->rama==1?'Varonil':($disc->rama==2?'Femenil':'Indistinto');
            $dropdown_disciplinas[$disc->rama][$disc->id]= $disc->nombre;
        }
         $this->template->title($this->module_details['name'])
            ->set('registro',$registro)
            ->set('configuracion',$configuracion)
            ->set('values',$values)
            ->set('id_evento',$registro->id_evento)
            ->set('centros',$this->centro_m->dropdown('id','nombre'))
            ->append_js('module::registro.controller.js')
            ->append_metadata('<script type="text/javascript">var foreigns='.json_encode($foreigns).',url_current=\''.base_url($this->uri->uri_string()).'\';</script>')
            //->set('disciplinas',$dropdown_disciplinas)
            
			->build('admin/form');
    }
    
    function load($id=0)
    {
        $evento        = $this->db->where('id',$id)->get('eventos')->row() OR redirect('admin');
        $configuracion = $this->db->where('id_evento',$id)->get('registro_configuracion')->row();
        
        $this->load->model(array(
            'centros/centro_m',
            'registro_m'
        ));
        
        $base_where = array(
        
            'registros.id_evento' => $evento->id
        );
        
        $group      = $this->input->get('group');
        $f_keywords = $this->input->get('f_keywords');
        $inscrito   = $this->input->get('inscrito');
        $disciplina   = $this->input->get('disciplina');
        $tables_ids  = array();
        //Si se necesita agrupar
        
        if($configuracion && $configuracion->group_by)
        {
            $groups = $this->db->get($configuracion->module)->result();
            
            $this->template->set('groups',array_for_select($groups,$configuracion->group_by,$configuracion->group_by));
            
            if($group)
            {
                $result_group = $this->db->where($configuracion->group_by,$group)
                                            ->get($configuracion->module)->result();
                $tables_ids = array_for_select($result_group,$configuracion->module_id,$configuracion->module_id);
                
                $base_where['module_id IN(\''.implode('\',\'',$tables_ids).'\')'] = null;
                 //$this->registro_m->where_in('module_id',$tables_ids);
            }
        }  
        
        
        
        if($f_keywords)
        {
            $base_where[' (participante LIKE "%'.$f_keywords.'%" OR id LIKE "%'.$f_keywords.'%")'] = NULL;
        }
        
        if(is_numeric($f_estatus))
        {
            $base_where['registros.activo'] = $f_estatus;
        }
        
        if(is_numeric($inscrito))
        {
            $base_where['inscrito'] = $inscrito;
        }
        if(is_numeric($disciplina))
        {
            $base_where['id_disciplina'] = $disciplina;
        }
        
        
        
        $total_rows =  $this->registro_m->select('*')
                            ->count_by($base_where);
                            
        $pagination = create_pagination('admin/registros/'.$id, $total_rows,NULL);
                            
                            
        $registros = $this->registro_m->select('*')
                            ->where($base_where)
                            
                            ->limit($pagination['limit'],$pagination['offset'])
                            ->get_all();
                            
        //Extraemos todos los correos para envio masivo
        
        $emails = $this->registro_m->where(array_merge($base_where,array('email IS NOT NULL'=>null)))->dropdown('email','email');
                            
        foreach($registros as $registro)
        {
            $registro->extra = json_decode($registro->extra);
        }
             
             
        $templates = $this->db->get('email_templates')->result();  
        
        
        if($configuracion->disciplinas==1)
        {
            $disciplinas = $this->db->where('id_evento',$evento->id)->get('disciplinas')->result();
            
            $this->template->set('disciplinas',array_for_select($disciplinas,'id','nombre'));
        }
        
                   
        $this->template->title($evento->titulo)//->append_js('module::medallero.controller.js')
                ->enable_parser(true)
                //->set('configuracion',$configuracion)
                ->set('registros',$registros)   
                ->set('evento',$evento)
                ->set('centros',$this->centro_m->dropdown('id','nombre'))
                ->set('id_evento',$evento->id)
                ->set('pagination',$pagination)
                
                ->append_metadata('<script type="text/javascript"> var evento='.$id.',emails='.json_encode(array_keys($emails)).', templates ='.json_encode($templates).';</script>')
                ->append_js('module::registro.controller.js')
                ->build('admin/index');
    }
    function delete($id=0,$id_evento='')
    {
         role_or_die($this->section, 'delete');
        
        
		$ids = ($id) ?array(0=>$id) : $this->input->post('action_to');
		$url_return  = $this->input->post('url_return');
        $deletes = array();
        if ( ! empty($ids))
		{
		  
   	        foreach ($ids as $id)
			{
                
                
                if($registro = $this->registro_m->get($id))
                {
                    $this->registro_m->delete($id);
                    
                    $deletes[] = $registro->participante;
                }
            }
        }
        
        if ( ! empty($deletes))
		{
		    $this->session->set_flashdata('success', sprintf(lang('registros:delete_success'), implode('", "', $deletes)));
        }
        else
        {
            $this->session->set_flashdata('error',lang('global:delete_error'));
        }
        /*if ($registro && $this->registro_m->delete($id))
		{
		    
			

			$this->session->set_flashdata('success', lang('global:delete_success'));
		}
		else
		{
			$this->session->set_flashdata('error', lang('global:delete_error'));
		}*/

		redirect($url_return?$url_return:'admin/registros/'.$id_evento);
    }
    function asesores($id=0)
    {
         $this->load->library(array('Factory'));
    
         error_reporting(E_ALL);
        ini_set('display_errors', TRUE);
        ini_set('display_startup_errors', TRUE);
        
        define('EOL',(PHP_SAPI == 'cli') ? PHP_EOL : '<br />');
        
        date_default_timezone_set('Europe/London');
        
        $data       = array();
        $tipo       = $this->input->get('tipo');
        $centro     = $this->input->get('centro');
        $base_where = array(
            'registros.id_evento'=>$id
        );
        
        if($tipo)
        {
            $base_where['disciplinas.tipo'] = $tipo;
        }
        if($centro)
        {
            $base_where['registros.id_centro'] = $centro;
        }
        $registros = $this->registro_m->select('*,centros.nombre AS nombre_centro,disciplinas.nombre AS nombre_disciplina')
                            ->join('disciplinas','disciplinas.id=registros.id_disciplina')
                            ->join('centros','centros.id=registros.id_centro')
                            //->join('alumnos','alumnos.matricula=registros.module_id','LEFT')
                            
                            ->where($base_where)
                            ->order_by('centros.ordering_count')
                            ->get_all();
                            
        foreach($registros AS &$registro)
        {
            $registro->extra = json_decode($registro->extra);
            $registro->nombre_disciplina = $registro->nombre_disciplina.'('.($registro->rama==1?'Varonil':($registro->rama == 2? 'Femenil':'Indistinto')).')';
            if(!isset($data[$registro->id_centro]))
            {
                
                $data[$registro->id_centro] = array(
                
                    'centro' => $registro->nombre_centro,
                    'disciplinas'  => array()
                );
            }
            
            if(!isset($data[$registro->id_centro]['disciplinas'][$registro->nombre_disciplina]))
            {
                $data[$registro->id_centro]['disciplinas'][$registro->nombre_disciplina] = array();
            }
            
            if(empty($registro->extra->asesor)== false && array_key_exists($registro->extra->asesor,$data[$registro->id_centro]['disciplinas'][$registro->nombre_disciplina])== false)
            {
                $data[$registro->id_centro]['disciplinas'][$registro->nombre_disciplina][$registro->extra->asesor] = array(
                    'nombre'    => $registro->extra->asesor,
                    'talla'     => $registro->extra->talla_asesor,
                    'sexo'      => $registro->extra->sexo_asesor,
                    'hospedaje' => $registro->extra->hospedaje_asesor,
                    'telefono'  => $registro->extra->telefono_asesor  
                );
            }
            //$data[$registro->id_centro]['lista'][] = $registro;
        }
        
        //print_r($data);
        //exit();
         error_reporting(E_ALL);
        ini_set('display_errors', TRUE);
        ini_set('display_startup_errors', TRUE);
        
        define('EOL',(PHP_SAPI == 'cli') ? PHP_EOL : '<br />');
        
        date_default_timezone_set('Europe/London');
        
        $this->excel = factory::getTemplate('registros_asesores.xlsx');
        
        // Set document properties
        $this->excel->getProperties()->setCreator("Colegio de Bachilleres del Estado de Campeche")
							 ->setLastModifiedBy("Colegio de Bachilleres del Estado de Campeche")
							 ->setTitle("Office 2007 XLSX Test Document")
							 ->setSubject("Office 2007 XLSX Test Document")
							 ->setDescription("Test document for Office 2007 XLSX, generated using PHP classes.")
							 ->setKeywords("office 2007 openxml php")
							 ->setCategory("Test result file");




// Rename worksheet
        $this->excel->getActiveSheet()->setTitle('Asesores_'.now());

        $active_sheet = $this->excel->getActiveSheet();
        $position = 2;
        
        foreach($data as $data_centro)
        {
            foreach($data_centro['disciplinas'] as $data_disciplina=>$data_asesores)
            {
                foreach($data_asesores as $asesor=>$data_asesor)
                {
                    $this->excel->getActiveSheet()->insertNewRowBefore($position+1,1);
                    $this->excel->getActiveSheet()->setCellValue('A'.$position, $data_centro['centro']);
                    $this->excel->getActiveSheet()->setCellValue('B'.$position, $data_disciplina);
                    $this->excel->getActiveSheet()->setCellValue('C'.$position, $data_asesor['talla']);
                    $this->excel->getActiveSheet()->setCellValue('D'.$position, $data_asesor['sexo']);
                    $this->excel->getActiveSheet()->setCellValue('E'.$position, $data_asesor['hospedaje']);
                    $this->excel->getActiveSheet()->setCellValue('F'.$position, $data_asesor['telefono']);
                    $this->excel->getActiveSheet()->setCellValue('G'.$position, $asesor);
                
                    $position++;
                }
            }
        }
        
        
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        //header('Content-Type: application/vnd.ms-excel');
        header('Content-Disposition: attachment;filename="asesores_'.$id.'_'.now().'.xlsx"');
        header('Cache-Control: max-age=0');
        // If you're serving to IE 9, then the following may be needed
        header('Cache-Control: max-age=1');
        
        // If you're serving to IE over SSL, then the following may be needed
        header ('Expires: Mon, 26 Jul 1997 05:00:00 GMT'); // Date in the past
        header ('Last-Modified: '.gmdate('D, d M Y H:i:s').' GMT'); // always modified
        header ('Cache-Control: cache, must-revalidate'); // HTTP/1.1
        header ('Pragma: public'); // HTTP/1.0
        //exit();
        $objWriter = PHPExcel_IOFactory::createWriter($this->excel, 'Excel2007');
        $objWriter->setIncludeCharts(TRUE);
        $objWriter->save('php://output');
        
    }
    function download($id=0)
    {
         $this->load->library(array('Excel'));
    
         error_reporting(E_ALL);
        ini_set('display_errors', TRUE);
        ini_set('display_startup_errors', TRUE);
        
        define('EOL',(PHP_SAPI == 'cli') ? PHP_EOL : '<br />');
        
        date_default_timezone_set('Europe/London');
        
        $data = array();
        
        $inscrito   = $this->input->get('inscrito');
        $disciplina = $this->input->get('disciplina');
        $group      = $this->input->get('group');
        $sexo       = $this->input->get('param');
        
        $base_where = array(
            'registros.id_evento' => $id,
            'inscrito'            => 1
        );
        
        $configuracion = $this->configuracion_m->get_by('id_evento',$id);
        if(!$configuracion)
        {
            $this->session->set_flashdata('error',lang('registros:config_error'));
            redirect('admin/registros/configuracion/'.$id);
        }
        
        
        
        $f_keywords = $this->input->get('f_keywords');
        $f_estatus  = $this->input->get('f_estatus');
        
        
        
        if(is_numeric($disciplina))
        {
            $base_where['registros.id_disciplina'] = $disciplina;
        }
        
        if(is_numeric($inscrito))
        {
            $base_where['registros.inscrito'] = $inscrito;
        }
        if(is_numeric($f_estatus))
        {
            $base_where['registros.activo'] = $f_estatus;
        }
        if(is_numeric($sexo))
        {
            $base_where['registros.sexo'] = $sexo;
        }
         if($group && $configuracion->group_by)
        {
            $base_where[$configuracion->module.'.'.$configuracion->group_by] = $group;
        }
        
        if($f_keywords)
        {
            $base_where[' (participante LIKE "%'.$f_keywords.'%" OR id LIKE "%'.$f_keywords.'%")'] = NULL;
        }
      
        $configuracion->campos = json_decode($configuracion->campos);
        
       
       $this->excel->setActiveSheetIndex(0);
           
       $this->excel->getActiveSheet()->setTitle(date('d M Y'));
       $this->excel->getActiveSheet()->setCellValue('A1','ID');
       $this->excel->getActiveSheet()->setCellValue('C1','Participante');
        $pos_x = 3;
       if($configuracion->disciplinas)
       {
            $this->excel->getActiveSheet()->setCellValue('D1','Disciplina');
            $pos_x++;
       }
       
       $this->excel->getActiveSheet()->setCellValue('B1',ucfirst($configuracion->module_id));
       
      
       
       $columns = array('A','B','C','D','E','F','G','H','I','J','K','L','M','N','O','P','Q','R','S','T','U','V','W','X','Y','Z');
        foreach($configuracion->campos as $campo)
        {
            $this->excel->getActiveSheet()->setCellValue($columns[$pos_x].'1',$campo->nombre);
            $pos_x++;
        }
        
        
         if($configuracion->module)
        {
            $this->registro_m->join($configuracion->module,$configuracion->module.'.'.$configuracion->module_id.'=default_registros.module_id');
        }
        
        $registros = $this->registro_m->select('*')
                            //->join('disciplinas','disciplinas.id=registros.id_disciplina')
                            //->join('centros','centros.id=registros.id_centro')
                            //->join('alumnos','alumnos.matricula=registros.module_id','LEFT')
                            ->where($base_where)->get_all();
                         
        foreach($registros as $index=>$registro)
        {
        
            $pos_x = 3;   
            
            if($configuracion->disciplinas)
            {
                
                 $disciplina = $this->db->where('id',$registro->id_disciplina)->get('disciplinas')->row();
                 $this->excel->getActiveSheet()->setCellValue('D'.($index+2),$disciplina->nombre);
                 $pos_x ++;
            }
            $this->excel->getActiveSheet()->setCellValue('A'.($index+2),$registro->id);
            $this->excel->getActiveSheet()->setCellValue('B'.($index+2),$registro->module_id);
            $this->excel->getActiveSheet()->setCellValue('C'.($index+2),$registro->participante);
            
            
            
            $registro->extra = json_decode($registro->extra);
            foreach($configuracion->campos as $campo)
            {
                $this->excel->getActiveSheet()->setCellValue($columns[$pos_x].($index+2),$registro->extra->{$campo->slug});
                $pos_x++;
            }
        }
        
         header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        
        header('Content-Disposition: attachment;filename="registros_'.$id.'_'.now().'.xlsx"');
        header('Cache-Control: max-age=0');
        // If you're serving to IE 9, then the following may be needed
        header('Cache-Control: max-age=1');
        
        // If you're serving to IE over SSL, then the following may be needed
        header ('Expires: Mon, 26 Jul 1997 05:00:00 GMT'); // Date in the past
        header ('Last-Modified: '.gmdate('D, d M Y H:i:s').' GMT'); // always modified
        header ('Cache-Control: cache, must-revalidate'); // HTTP/1.1
        header ('Pragma: public'); // HTTP/1.0
      
        $objWriter = PHPExcel_IOFactory::createWriter($this->excel, 'Excel2007');
        $objWriter->setIncludeCharts(TRUE);
        $objWriter->save('php://output');
    }
    public function action()
	{
		switch ($this->input->post('btnAction'))
		{
			
			case 'delete':
				$this->delete();
				break;

			default:
				redirect('admin/fondo');
				break;
		}
	}
    
    function distinct()
    {
        $alumnos = $this->db->where('grado',2)->get('alumnos')->result();
        
        
        foreach($alumnos as $alumno)
        {
            $alumno->nombre_completo = $alumno->nombre;
            $exists = $this->registro_m->join('centros','centros.id=registros.id_centro')
                            ->join('disciplinas','disciplinas.id=registros.id_disciplina')
                            ->where('registros.id_evento',6)
                            ->like('participante',$alumno->nombre_completo)->get_all();
            
            if($exists)
            {
                print_r($exists).'</br>';
            }
        }
    }
    
    function import_module($id_evento,$concat)
    {
        $result = array(
            'status'  => true,
            'message' => ''
            
        );
        
        $concat = explode('.',$concat);
        
        $configuracion = $this->db->where('id_evento',$id_evento)->get('registro_configuracion')->row() OR show_error('La configuración es requerida.');
        $registros     = $configuracion->module_id?$this->registro_m->where('id_evento',$id_evento)
                                ->dropdown('module_id','participante')
                                :$this->registro_m->where('id_evento',$id_evento)
                                ->dropdown('id','participante');
        $count_adds = 0;
        if($configuracion->module)
        {
            $rows_module = $this->db->get($configuracion->module)->result();
            
            foreach($rows_module as $row)
            {
                $participante = '';
                if($configuracion->module_id)
                {
                    
                    if($row->{$configuracion->module_id} && array_key_exists($row->{$configuracion->module_id},$registros)== false)
                    {
                        
                        foreach($concat as $c)
                        {
                            $participante .= !$participante?$row->{$c}:' '.$row->{$c};
                        }
                            
                        $data_insert = array(
                            'participante' => $participante,
                            'created_on' => now(),
                            'activo'     => 1,
                            'id_evento'  => $id_evento,
                            'module_id'  => $row->{$configuracion->module_id}
                        );
                        if($this->db->set($data_insert)->insert('registros'))
                        {
                            $count_adds++;
                        }
                        else
                        {
                            echo 'Datos no insertados:<br/>';
                            print_r($data_insert);
                            echo '<br/>';
                            
                        }
                        
                    }
                }
                else
                {
                    
                }
                
            }
            $result['message']  = 'Se han agregado '.$count_adds.' de '.count($rows_module).' registros.';
        }
        else
        {
            $result['status']  = false;
            $result['message'] = 'El módulo '.$configuracion->module.' no cargo correctamente ';
        }
    
        return $this->template->build_json($result);
    
    }
    function cedula()
    {
        
    }
    function send_email($id_evento='')
    {
        $this->load->library(array(
            'parser',
            'email'
        ));
        
        $this->load->model('eventos/evento_m');
        $result=array(
            'status' => true,
            'message' => false
        );
        
        $this->form_validation->set_rules($this->validation_rules['email']);
        
         if($this->form_validation->run())
		 {
            $post = $this->input->post();
            
            $subject = $post['subject'];
            $body    = $post['body'];
            
            $count_sends = 0;
            
            if(count($post['emails']) > 0)
            {
                $template = $this->db->where('slug',$post['template'])->get('email_templates')->row();
                
                if($template)
                {
                     $subject = $template->subject;
                     $body    = $template->body;
                }
                $data = array(
                    'evento'            => $this->evento_m->get($post['evento'])
                    //$data['registro']           = $this->registro_m->get($post['evento']);
                    
                );
                
                $registros = $this->registro_m->where_in('email',$post['emails'])->get_all();
                
                //$body = $this->parser->parse_string($body, $data, true);
                
               
                
                foreach($registros as $registro)
                {
                    if($registro->email)
                    {    
                        
                        
                        
                        
                        $data['registro'] = $registro;
                        $body_send = $this->parser->parse_string($body, $data, true);
                        $this->email->from(Settings::get('server_email'), Settings::get('site_name'));
                        $this->email->reply_to(Settings::get('server_email'));
                        $this->email->subject($subject);
                        $this->email->message($body_send);
                    
                        $this->email->to($registro->email);
                   
                    
                    
                        if($this->email->send())                
                            $count_sends ++;
                            
                        //$result['message'][]= array('message'=>'Enviado a '.$registro->email.' con participante '.$registro->participante,'body'=>$body_send);
                    }
                }
            }
            //$result['message'] = sprintf(lang('registros:send_email_success'),$count_sends,count($post['emails']));
        }
        elseif (validation_errors())
	    {
	        $result['status']	= false;
			$result['message']	= validation_errors();
        }
        //$data['id']                 = $post['evento'];  
        /*$data['evento']             = $evento; 
        $data['registro']           = $this->registro_m->get($post['evento']); 
        $data['nombre']             = strtoupper($this->input->post('participante'));
       	$data['slug'] 				= $configuracion->template;
  		$data['to'] 				= $data['email'];
  		$data['from'] 				= Settings::get('server_email');
  		$data['name']				= Settings::get('site_name');
  		$data['reply-to']			= 'transparencia@cobacam.edu.mx';//Settings::get('contact_email');
        Events::trigger('email', $data, 'array');*/
        
         
         return $this->template
                ->build_json($result);
         
    }
    
    private function _send_email($data,$email)
    {
        
    }
    
    function extraupdate($id_evento=0)
    {
        $registros = $this->db->where(array(
                'id_evento'=>$id_evento,
                'inscrito' => 1,
               
                
            ))
            ->join('alumnos','alumnos.idalum=registros.module_id')
            ->get('registros')
            ->result();
            
        foreach($registros as $registro)
        {
            
          
            $registro->extra = json_decode($registro->extra);
            
             $registro->extra->sexo= $registro->sexo;
             $registro->extra->escuela= $registro->escuela;
             
            
            $this->registro_m->update( $registro->id,array('fotografia'=>$registro->extra->fotografia,'sexo'=>$registro->sexo, 'extra'=>json_encode($registro->extra)));
        }
    }
    function img($id_evento)
    {
        ini_set('max_execution_time', 0);
        $this->load->helper('file');
        $this->load->library('files/files');
        $this->load->model('files/file_folders_m');
        $this->load->model('files/file_m');
        $this->load->library('Upload');
        
        $centro = $this->input->get('centro');
        $group  = $this->input->get('group');
        $centro_s = explode(' ',$centro);
        $registros = $this->db->where(array(
                'id_evento'=>$id_evento,
                'inscrito' => 1,
                'escuela'  => trim($centro_s[1])
                
            ))
            ->join('alumnos','alumnos.idalum=registros.module_id')
            ->get('registros')
            ->result();
            
        
            
        $files   = get_filenames(__dir__.'/img/'.$centro);
        
       
        
        $alumnos = $this->db->where('escuela',$centro_s[1])->get('alumnos')->result();
        echo 'Total registros: '.count($registros).'  | Total imagenes: '.count($files).'<br/>';
        foreach($registros as $registro)
        {
            $registro->extra = json_decode($registro->extra);
            
            
            $matricula = explode('-',$registro->extra->matricula);
           // print_r($matricula);
          
           //print_r($registro->grupo.'<br/>');
           
            if(!$registro->extra->fotografia && in_array($matricula[1].'.jpg',$files))
            {
                /// echo $matricula[1].'.jpg <br/>';
                
                //$properties = get_file_info(__dir__.'/img/'. $matricula[1].'.jpg');
               // print_r($matricula[1].'.jpg<br/>');
               $registro->extra->fotografia = $this->_move_file($centro.'/'.$group,$matricula[1].'.jpg');
                 ///echo $matricula[1].'<br/>';
                
                
                $this->registro_m->update($registro->id,array('extra'=>json_encode($registro->extra)));
                //echo $registro->escuela.' '.$registro->participante.' '.$matricula[1].'.jpg<br/>';
            }
            else{
                echo 'Registro actualizado anteriormente:'.$registro->participante.'<br/>';
            }
            /*while($registro->extra->fotografia)
            {
                $registro->extra->fotografia = 
            *}*/
            
            
        }
       // print_r($files);
        /*foreach($alumnos as $alumno)
        {
            $foto = explode('-',$alumno->matricula);
            
            print_r($foto);
        }*/
        /*foreach($registros AS $registro)
        {
            $extra = json_decode($registro->campos);
            
            if(!$extra['fotografia'])
            {
                
            }
        }*/
        
        
    }
    function _move_file($group='',$file)
    {
        ini_set('max_execution_time', 0);
        $this->load->helper('file');
        $this->load->library('files/files');
        $this->load->model('files/file_folders_m');
        $this->load->model('files/file_m');
        $this->load->library('Upload');
        
        $folder = $this->file_folders_m->get_by_path('alumnos') or show_error('Falta la carpeta de empleados');
        
         
        $image_bd = $this->file_m->get_by('name',$file);
            
        if($image_bd)
        {
                echo 'Imagen ya existe en la BD '.$image_bd->path.'<br/>';
                //$data[$file]['name'] = $file;
                //$data[$file]['id'] = $image_bd->id;
                
                return $image_bd->id;
        }
        else{
            $path = config_item('files:path');
            $data = array();
            if(file_exists(__dir__.'/img/'.$group.'/'. $file))
            {
            
                $exist = true;
                $this->upload->encrypt_name = true;
                $properties = get_file_info(__dir__.'/img/'.$group.'/'. $file);
                $contents = file_get_contents(__dir__.'/img/'.$group.'/'.$file);
                $ext = explode('.',$file);
                $ext[1] = strtolower($ext[1]);
                 $imagesize  = getimagesize(__dir__.'/img/'.$group.'/'.$file);
                $data[$file]['width'] = $imagesize[0]; 
                $data[$file]['height'] = $imagesize[1]; 
                //$data[$file]['properties'] = $properties; 
                $data[$file]['date_added'] = $properties['date']; 
                
                $data[$file]['extension'] = '.'.$ext[1];
                //$ext		= pathinfo($_FILES[$field]['name'], PATHINFO_EXTENSION);
                $data[$file]['name']       = $file;
                $data[$file]['filesize']   = $properties['size'];
               
                //$data[$file]['id']       = substr(md5(microtime()+$data['filename']), 0, 15);
                $data[$file]['mimetype'] = $mime = get_mime_by_extension(__dir__.'/img/'.$group.'/'.$file);
                
                
                
                $data[$file]['user_id'] = 1;
                $data[$file]['sort'] = 666;
                $data[$file]['description'] = '';
                $data[$file]['folder_id'] = $folder->id;
                $data[$file]['type'] = 'i';
                
                 //Esta funcion valida si hay una misma ID dentro los registros
                while($exist)
                {
                    $data[$file]['filename']   = $this->upload->set_filename(__dir__.'/img/'.$group.'/'.$file).'.'.$ext[1];
                    $data[$file]['id'] = substr(md5(microtime()+$data[$file]['filename']), 0, 15);
                    if($this->file_m->get_by('id',$data[$file]['id'])== false)
                    {
                        $exist = false;
                        //$data[$file]['id'] = substr(md5(microtime()+$data[$file]['filename']), 0, 15);
                        //$exist = false;
                    }
                    
                    
                    
                    
                }
                $data[$file]['path'] = '{{ url:site }}files/large/'.$data[$file]['filename'];
                $temp_file = $path.$data[$file]['filename'];
                
                if(write_file($temp_file, $contents, 'wb')== false)
                {
                    echo 'Archivo no creado:'.$temp_file.'<br/>';
                }
                
                 if($this->file_m->insert($data[$file]) == true)
                 {
                    return $data[$file]['id'];
                        ///echo 'Archivo  agregado:'.$temp_file.'<br/>';
                 }
            }
            else
            {
                echo 'El archivo no existe fisicamente: '.__dir__.'/img/'.$group.'/'. $file.'<br/>';
            }
            return false;
        }
        
        
    }
 }
 ?>