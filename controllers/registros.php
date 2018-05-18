<?php defined('BASEPATH') OR exit('No direct script access allowed');
/**
 * The public controller for the Pages module.
 *
 * @author		PyroCMS Dev Team
 * @package		PyroCMS\Core\Modules\Pages\Controllers
 */
class Registros extends Public_Controller
{

	/**
	 * Constructor method
	 */
	public function __construct()
	{
		parent::__construct();
        $this->load->model(array('registro_m','files/file_folders_m','eventos/evento_m'));
        $this->lang->load('registro');
        $this->load->library(array('files/files','registros/registro'));
        $this->lang->load('calendar');
        $this->validation_rules = array(
			'disciplina'=>	array(
				'field' => 'disciplina',
				'label' => 'Disciplina',
				'rules' => 'trim'
				),
            'rama'=>	array(
				'field' => 'rama',
				'label' => 'Rama',
				'rules' => 'trim'
				),
			array(
				'field' => 'evento',
				'label' => 'Evento',
				'rules' => 'trim'
				),
            'participante'=>array(
				'field' => 'participante',
				'label' => 'Participante',
				'rules' => 'trim|required|callback__valid_participante'
				),
            /*array(
				'field' => 'extra[asesor]',
				'label' => 'Asesor',
				'rules' => 'trim|required'
				),
            array(
				'field' => 'extra[sexo_asesor]',
				'label' => 'Sexo del asesor',
				'rules' => 'trim|required'
				),
            array(
				'field' => 'extra[talla_asesor]',
				'label' => 'Talla del asesor',
				'rules' => 'trim|required'
				),
             array(
				'field' => 'extra[talla_participante]',
				'label' => 'Talla del participante',
				'rules' => 'trim|required'
				),
             array(
				'field' => 'disciplina',
				'label' => 'Disciplina',
				'rules' => 'trim|required'
				),
            array(
				'field' => 'sexo',
				'label' => 'Sexo',
				'rules' => 'trim'
				),
            array(
				'field' => 'email',
				'label' => 'Correo electrónico',
				'rules' => 'trim|required'
				),*/
        );
        
        if($this->current_user && $this->current_user->group != 'admin') {
            
            /*$this->load->model('directores/director_m');
        
        
            $director = $this->director_m
                    ->join('users','users.id=directores.user_id')->get_by('users.id',$this->current_user->id);
                    
            $_POST['centro'] = $director->id_centro;*/
            
            
                    
                    
        }
        $this->template->days_week = array(
             'Monday'   => 'Lunes',
             'Wednesday'   => 'Miercoles',
             'Friday'   => 'Viernes',
             'Saturday' => 'Sabado',
             'Sunday'   => 'Domingo',
             'Thursday'   => 'Jueves',
        );
        $this->template->append_js('handlebars.js')
                ->append_js('bloodhound.min.js')
                ->append_js('typeahead.bundle.min.js')
                ->append_js('typeahead.jquery.min.js')
                
                ->append_css('typehead.css')
                ->set_breadcrumb('Eventos','registros');
    }
    
    function _valid_participante($field)
    {
        
        $registro = $this->registro_m->get_by(array(
            'id_evento'    => $this->input->post('evento'),
            'participante' => trim($field),
            ///'id <> '       => $this->input->post('id')
            //'activo'       => '0'
        ));
        
        
        
        if($registro && $registro->id != $this->input->post('id'))
        {
            $this->form_validation->set_message('_valid_participante',lang('registros:error_duplicate'));
            
            return false;
        }
        return true;
    }
    function index()
    {
        $list = $this->db->join('eventos','eventos.id=registro_configuracion.id_evento')
                            ->where('cerrado',0)
                            ->get('registro_configuracion')
                            ->result();
        
        $this->template->title($this->module_details['name'],lang('registros:title'))
                    ->set('list',$list)
                    ->build('init');
    }
    
    function load($id_evento=0)
    {
        $registro = new StdClass();
        
        $data_autocomplete     = array();
      
        
        
        
        $evento        = $this->evento_m->get($id_evento) OR show_404();
        
        $evento->date_countdown = format_date($evento->fecha,'M d,Y H:i:s');
        $configuracion = $this->db->where('id_evento',$id_evento)->get('registro_configuracion')->row();
        
        $disciplinas = array();
        
        $base_where    = array(
            'registros.id_evento' => $id_evento
        );
        
        
        if($configuracion->auth)
        {
            
            
            $base_where['user_id'] = $this->current_user->id;
        }
        
        //Redireccion de la busqueda
        if($this->input->get('participante'))
        {
            $participante = $this->registro_m->get_by(array(
                'participante' => $this->input->get('participante'),
                'id_evento'    => $id_evento
            ));
            
            if(!$participante)
            {
                redirect('registros/crear/'.$id_evento.'?'.http_build_query($_GET));
            }
            else
            {
                redirect('registros/editar/'.$id_evento.'?'.http_build_query($_GET));
            }
            
        }
        
        
        
        
        if($configuracion->autocomplete == 1)
        {
            $registros = $this->registro_m
                                ->where($base_where)
                                //->where('module_id <> "" ',null)
                                //->where('activo','1')
                                ->get_all();
             //print_r($registros);                  
            foreach($registros as $reg)
            {
                $data_autocomplete[] = array(
                    //'tokens' =>array($reg->participante),//array($alumno->nombre,$alumno->apellido_paterno,$alumno->apellido_materno,$alumno->matricula),
                    'id'            => $reg->id, 
                    'participante'  => $reg->participante,
                    'module_id' => $reg->module_id?$reg->module_id:'N/A',
                    'extra'     => json_decode($reg->extra)
                    //'inscrito'  => 0
                );
            }
        }
         $base_where['inscrito'] = 1;
         if($configuracion->auth)
         {
              if(!$this->current_user)
                redirect('users/login/registros/'.$id_evento);
                
              $base_where['user_id'] = $this->current_user->id;
              
              if($configuracion->disciplinas)
                  $this->registro_m->select('*,disciplinas.nombre AS disciplina,registros.id AS id')->join('disciplinas','disciplinas.id=registros.id_disciplina');
              $inscritos = $this->registro_m->where($base_where)->get_all(); 
              $this->template->set('inscritos',$inscritos); 
              
              
             
              
         }
         if($configuracion->disciplinas)
         {
             //$inscritos = $this->registro_m->where($base_where); 
               
              $result = $this->db->where('id_evento',$id_evento)
                        //->where('user_id',$this->current_user->id)
                        ->select('*,disciplinas.id AS id')
                        //->join('disciplina_asesores','disciplina_asesores.id_disciplina=disciplinas.id','LEFT')
                        ->get('disciplinas')->result();
             
              foreach($result as $re)
              {
                  if(!array_key_exists($re->id,$disciplinas))
                  {
                       $disciplinas[$re->id] = array(
                          'nombre'   => $re->nombre,
                          'cantidad' => 0,
                          'asesor'   => $this->db->where(array('id_disciplina'=>$re->id,'user_id'=>$this->current_user->id))->get('disciplina_asesores')->row(),
                          'activo'   => $re->activo 
                       );
                   }
              }
              foreach($inscritos as $inscrito)
              {
                //if(array_key_exists($inscrito->id_disciplina,$disciplinas))
                    $disciplinas[$inscrito->id_disciplina]['cantidad']++;
              }
              /*foreach($inscritos as $inscrito)
              {
                  if(array_key_exists($inscrito->id_disciplina,$disciplinas_activos) == false)
                  {
                      $disciplinas_activos [$inscrito->id_disciplina] = 0;
                  }
                  
                  $disciplinas_activos [$inscrito->id_disciplina]++;
                  
              }*/
         }
        
         
         $this->template->set('disciplinas',$disciplinas);
        // print_r($configuracion);
         //exit();
         $this->template->title($this->module_details['name'],$evento->titulo)
                    ->set_breadcrumb($evento->titulo)
                   
                    ->append_js('spin.min.js')
                    ->append_js('module::front/form.js')
                    ->append_metadata('<script type="text/javascript">var display_autocomplete=\''.$configuracion->autocomplete_display.'\', text_empty=\''.lang('registros:empty_'.($configuracion->forced==1?'forced':'free')).'\', lng='.($evento->map_longitud?$evento->map_longitud:-90.5467695763607).',lat='.($evento->map_latitud?$evento->map_latitud:19.833932192097134).',zoom='.($evento->map_zoom?$evento->map_zoom:10).', url_current=\''.base_url($this->uri->uri_string()).'\', data ='.json_encode($data_autocomplete).'; '.$configuracion->javascript.'</script>')
                    ->set('action',$action)
                    ->set('evento',$evento)
                    ->set('registro',$registro)
                    
                    ->set('configuracion',$configuracion)
                    ->build('index');
    }
    function _valid_disciplina($field)
    {
        $disciplina    = $this->db->where('id',$field)
                        ->get('disciplinas')->row();
                        
                        
        $configuracion = $this->db->where('id_evento',$this->input->post('evento'))
                                ->get('registro_configuracion')->row();
        
        $base_where = array(
            'rama'          => $this->input->post('rama')?$this->input->post('rama'):0,
            'id_disciplina' => $field,
            'id_evento'     => $this->input->post('evento'),
            'inscrito'      => 1,
            'id <> '        => $this->input->post('id'),
          
        );
        
        if($configuracion->auth)
        {
            $base_where['user_id'] = $this->current_user->id;
        }
                       
        $registros = $this->registro_m->count_by($base_where);
        
        
        if(($registros+1) > $disciplina->max)
        {
            $this->form_validation->set_message('_valid_disciplina', lang('registros:limit_max'));
            
            return false;
        }
        
        
        return true;
    }
    function _valid_part($participante)
    {
        $id_evento     = $this->input->post('evento');
        $id_disciplina = $this->input->post('disciplina');
        $tipo          = false;
        $valid         = true;
        $message       = '';
        
        $disciplina = $this->db->where('id',$id_disciplina)->get('disciplinas')->row();
        
        $registros  = $this->registro_m->join('disciplinas','disciplinas.id=registros.id_disciplina')
                            ->get_many_by(array(
                                    'registros.id_evento'    => $id_evento,
                                    'participante' => $participante,
                                    
                                    
                          )); 
                      
        if($disciplina->tipo == 'deportivo')
        {
            $tipo = $disciplina->tipo;
        }
        
        foreach($registros as $registro_row)
        {
            //El participante no se puede inscribir dos veces en este tipo de disciplina.
            if($registro_row->tipo === $tipo)
            {
                $valid   = false;
                $message = sprintf(lang('registros:banned_disciplina'),$disciplina->nombre); 
                break;
            }
            
            if($registro_row->id_disciplina == $disciplina->id)
            {
                $valid   = false;
                $message = lang('registros:duplicate_disciplina');
            }
        }
        
       
        
        if($valid == false)
        {
            $this->form_validation->set_message('_valid_part', $message);
            
            return false;
        }
        
        return true;
    }
    function delete($id=0)
    {
        $this->load->library('files/files');
        $status  = 'success';
        if($registro = $this->registro_m->get($id))
        {
            $registro->extra = json_decode($registro->extra);
           
            if(empty($registro->extra->fotografia) == false)
            {
                Files::delete_file($registro->extra->fotografia);
            }
            $this->registro_m->where('id_centro',$this->input->post('centro'))->delete($id);
        }
    }
    function edit($id_evento)
    {
        if($configuracion->auth)
        {
              if(!$this->current_user)
                redirect('users/login/registros/'.$id_evento);
        }
        $registro = new StdClass();
        $data_autocomplete     = array();
        
        $evento        = $this->evento_m->get($id_evento) OR show_404();
        
        $evento->date_countdown = format_date($evento->fecha,'M d,Y H:i:s');
        $configuracion = $this->db->where('id_evento',$id_evento)->get('registro_configuracion')->row();
       
        //Verificar que no encuentra cerrado
        
        if($configuracion->cerrado)
        {
            $this->session->set_flashdata('error',lang('registros:inactive'));
            redirect('registros/'.$id_evento);
        }
        
        //Verificar funcionalidad
        if($this->input->get('participante'))
        {
            $participante = $this->registro_m->select('*,id_disciplina AS disciplina')->get_by(array(
                                    'registros.id_evento'    => $id_evento,
                                    'participante' => $this->input->get('participante'),
                                    
                                    
                          ));
            
            if(!$participante)
            {
                redirect('registros/crear/'.$id_evento.'?'.http_build_query($_GET));
            }
            
            $_GET['id'] = $participante->id;
        }
        
        
        if($id = $this->input->get('id'))
        {
            $registro = $this->registro_m->select('*,id_disciplina AS disciplina')->get($this->input->get('id'));
            
            //Extraemos los valores iniciales del extra y la tabla foranea.
            $values        = Registro::GetResult($configuracion,$registro);
         
            //Lo metemos como si fuera un solo objeto principal
            $registro = (Object)array_merge($values,(array)$registro);
            
            
           
        }
        
        
        ///Eliminar 
        if($configuracion->autocomplete == 1)
        {
            $registros = $this->registro_m
                                ->where('id_evento',$id_evento)
                                //->where('module_id <> "" ',null)
                                //->where('activo','1')
                                ->get_all();
                                
            foreach($registros as $reg)
            {
                $data_autocomplete[] = array(
                    //'tokens' =>array($reg->participante),//array($alumno->nombre,$alumno->apellido_paterno,$alumno->apellido_materno,$alumno->matricula),
                    'id'            => $reg->id, 
                    'participante'  => $reg->participante,
                    'module_id' => $reg->module_id?$reg->module_id:'N/A'
                );
            }
        }
        
        
        $campos = json_decode($configuracion->campos);
        
        unset($configuracion->campos);
        
        foreach($campos as $campo)
        {
            $this->validation_rules[] = array(
				'field' => $campo->slug,
				'label' => $campo->nombre,
				'rules' => 'trim'.($campo->obligatorio?'|required':'').($campo->slug=='email'?'|valid_email':'')
				); 
        }
        
        //Habilitar disciplina
        if($configuracion->disciplinas)
        {
            $this->validation_rules['disciplina']['rules'].='|required|callback__valid_disciplina';
            $disciplinas = $this->db->where(array(
                                'id_evento' => $id_evento,
                                '(activo=1 OR id='.($registro->disciplina?$registro->disciplina:0).')'    => null
                            ))->get('disciplinas')->result();
            
            if($disciplinas)
                $this->template->set('disciplinas',array_for_select($disciplinas,'id','nombre'));
        }
       
        if($this->input->post('disciplina'))
        {
             $disciplina    = $this->db->where('id',$this->input->post('disciplina'))
                                ->get('disciplinas')->row();
            // print_r($disciplina);               
            if($disciplina->rama > 0)                   
                $this->validation_rules['rama']['rules'].='|required';
            
            
        }
        
        $this->form_validation->set_rules($this->validation_rules);	
        
       	if($this->form_validation->run())
		{
            unset($_POST['btnAction']);
            $folder = $this->file_folders_m->get_by_path('alumnos') OR show_error('La carpeta aun no ha sido creada: Alumnos');
            $extra = array();
            
            foreach($campos as $campo)
            {
                //if(!$_FILES['xml_file']['name'])
               // {
                if($campo->tipo == 'legend')continue;
                    
                if($campo->tipo == 'upload' && $_FILES[$campo->slug]['name'])
                {
                    $result_file = Files::upload($folder->id,false,$campo->slug);
                    
                    if($result_file['status'])
                    {
                        $extra[$campo->slug]  = $result_file['data']['id'];
                    }
                }
                else
                {
                    $extra[$campo->slug] = $this->input->post($campo->slug);
                }
            }
             $data = array(
                'module_id'    => $this->input->post('module_id'),
                'module'       => $configuracion->module,
                'id_evento'    => $id_evento,
                'participante' => strtoupper($this->input->post('participante')),
                'telefono'     => $this->input->post('telefono'),
                'email'        => $this->input->post('email'),
                'rama'         => $this->input->post('rama')?$this->input->post('rama'):0,
                'updated_on'   => now(),
                'id_disciplina' => $this->input->post('disciplina')?$this->input->post('disciplina'):null,
                'fotografia'    => $this->input->post('fotografia'),
                //'id_centro' => $this->input->post('centro'),
                'sexo'     => $this->input->post('sexo')?$this->input->post('sexo'):null,
                'inscrito' => 1,
                //'created_on' => now(),
                'extra' => json_encode($extra)
            );
            
            $fotografia = Files::upload($folder->id,false,'fotografia',false,false,false,'jpg|png|jpeg|gif');
                    
            if($fotografia['status'])
            {
                        $data['fotografia']  = $fotografia['data']['id'];
            }
           
            if($this->registro_m->update($id,$data))
            {
                
                    if($configuracion->module)
                        Registro::SaveResource($configuracion,$this->input->post('module_id'),$this->input->post());
                    //print_r($participante);
                    //print_r($data);
                   
                    //        print_r($data);
                            
                            
                    if( $configuracion->template && $data['email'] && $participante->send_email < 1)
                    {
                            
                            //$data['id']                 = $id;  
                            $data['disciplina'] = isset($disciplina)?$disciplina:false;
                            $data['evento']             = $evento; 
                            $data['registro']           = $this->registro_m->get($id); 
                            $data['nombre']             = strtoupper($this->input->post('participante'));
                        	$data['slug'] 				= $configuracion->template;
                       		$data['to'] 				= $data['email'];
                       		$data['from'] 				= Settings::get('server_email');
                       		$data['name']				= Settings::get('site_name');
                       		$data['reply-to']			= 'educacion.continua@cobacam.edu.mx';//Settings::get('contact_email');
                            Events::trigger('email', $data, 'array');
                             
                            $this->registro_m->update($id,array(
                              'send_email'=> $participante->send_email+1
                            ));  
                         $this->session->set_flashdata('success',sprintf(lang('registros:add_thanks_email'),$data['email'],$configuracion->acuse,$id));
                    }
                    else
                    {
                        $this->session->set_flashdata('success',lang('registros:add_thanks'));
                    }
                        
                 redirect('registros/detalles/'.$id_evento.'/'.$id);
            }
            else
            {
                 $this->session->set_flashdata('error',lang('registros:save_error'));
                 redirect('registros/editar/'.$id_evento);
            }
        }
       
        if($_POST)
        {
            $registro = (Object)$_POST;
        }
        $this->template->title($this->module_details['name'],'Inscripción al evento')
                ->set_layout('basic.html')
                ->append_js('spin.min.js')
                ->append_js('module::front/form.js')
                ->append_metadata('<script type="text/javascript">var value_rama=\''.$registro->rama.'\', display_autocomplete=false, disciplinas='.(isset($disciplinas)?json_encode($disciplinas):'[]').', text_empty=\'\', lng='.($evento->map_longitud?$evento->map_longitud:-90.5467695763607).',lat='.($evento->map_latitud?$evento->map_latitud:19.833932192097134).',zoom='.($evento->map_zoom?$evento->map_zoom:10).', url_current=\''.base_url($this->uri->uri_string()).'\', data ='.json_encode($data_autocomplete).'; '.$configuracion->javascript.'</script>')
                ->set('evento',$evento)
                ->set('registro',$registro)
                ->set('configuracion',$configuracion)
                ->set('campos',$campos)
                //->enable_parser(false)
                //->set('list',$list)//Para el autocomplete
                ->build('form');
        
      
        
    }
    function remove($id=0)
    {
        $registro = $this->registro_m->get($id) OR show_404();
        
        if($this->registro_m->update($registro->id,array(
        
            'inscrito' => 0
        )))
        {
            $this->session->set_flashdata('success',lang('global:delete_success'));
        }
        
        redirect('registros/'.$registro->id_evento);
    }
    function details($id_evento='',$id)
    {
        $registro = $this->registro_m->select('*,id_disciplina AS disciplina')->get($id) OR show_404();
         
        
         
        $evento        = $this->evento_m->get($id_evento) OR show_404();
        
        $evento->date_countdown = format_date($evento->fecha,'M d,Y H:i:s');
        $configuracion = $this->db->where('id_evento',$id_evento)->get('registro_configuracion')->row();
        
        
        $configuracion->autocomplete = false;
        
        
        $values        = Registro::GetResult($configuracion,$registro);
        
            //Lo metemos como si fuera un solo objeto principal
        $registro = (Object)array_merge($values,(array)$registro); 
        
        $configuracion->campos = json_decode($configuracion->campos);
         //Habilitar disciplina
        if($configuracion->disciplinas)
        {
            $disciplinas = $this->db->where('id_evento',$id_evento)->get('disciplinas')->result();
            
            if($disciplinas)
                $this->template->set('disciplinas',array_for_select($disciplinas,'id','nombre'));
        }
          $this->template->title($this->module_details['name'],'Detalles del registro')
                ->append_js('spin.min.js')
                ->append_js('module::front/form.js')
                ->append_metadata('<script type="text/javascript">var value_rama=\''.$registro->rama.'\',display_autocomplete=false,disciplinas='.(isset($disciplinas)?json_encode($disciplinas):'[]').', lng='.($evento->map_longitud?$evento->map_longitud:-90.5467695763607).',lat='.($evento->map_latitud?$evento->map_latitud:19.833932192097134).',zoom='.($evento->map_zoom?$evento->map_zoom:10).', url_current=\''.base_url($this->uri->uri_string()).'\', data =[]; '.$configuracion->javascript.'</script>')
                ->set('evento',$evento)
                ->set('registro',$registro)
                ->set('configuracion',$configuracion)
                ->set('campos',$configuracion->campos)
                 ->set_layout('basic.html')
                //->enable_parser(false)
                //->set('list',$list)//Para el autocomplete
                ->build('form');
    }
    function create($id_evento='')
    {
        
        
        $registro = new StdClass();
       
        $data_autocomplete     = array();
        
        $evento        = $this->evento_m->get($id_evento) OR show_404();
        
        $evento->date_countdown = format_date($evento->fecha,'M d,Y H:i:s');
        $configuracion = $this->db->where('id_evento',$id_evento)->get('registro_configuracion')->row();
        
        if($configuracion->forced)
        {
            redirect('registros/'.$id_evento);
        }
        if($configuracion->auth && $this->current_user == FALSE) {
        
            redirect('users/login/registros/crear/'.$id_evento);
        }
       
        if($configuracion->autocomplete == 1)
        {
            $registros = $this->registro_m
                                ->where('id_evento',$id_evento)
                                ->where('module_id <> "" ',null)
                                ->where('activo','1')
                                ->get_all();
                                
            foreach($registros as $reg)
            {
                $data_autocomplete[] = array(
                    //'tokens' =>array($reg->participante),//array($alumno->nombre,$alumno->apellido_paterno,$alumno->apellido_materno,$alumno->matricula),
                    'participante'  => $reg->participante,
                    'module_id' => $reg->module_id
                );
            }
        }
        
        
        $configuracion->campos = json_decode($configuracion->campos);
        
        foreach($configuracion->campos as $campo)
        {
            $this->validation_rules[] = array(
				'field' => $campo->slug,
				'label' => $campo->nombre,
				'rules' => 'trim'.($campo->obligatorio?'|required':'').($campo->slug=='email'?'|valid_email':'')
				); 
        }
        
        //Habilitar disciplina
        if($configuracion->disciplinas)
        {
            $disciplinas = $this->db->where('id_evento',$id_evento)->get('disciplinas')->result();
            
            if($disciplinas)
                $this->template->set('disciplinas',array_for_select($disciplinas,'id','nombre'));
        }
        $this->form_validation->set_rules($this->validation_rules);	
        
       	if($this->form_validation->run())
		{
            unset($_POST['btnAction']);
            $folder = $this->file_folders_m->get_by_path('portadas') OR show_error('La carpeta aun no ha sido creada: Portadas');
            $extra = array();
            
            foreach($configuracion->campos as $campo)
            {
                //if(!$_FILES['xml_file']['name'])
               // {
                if($campo->tipo == 'legend')continue;
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
                'id_evento'    => $id_evento,
                'participante' => strtoupper($this->input->post('participante')),
                'telefono'     => $this->input->post('telefono'),
                'email'        => $this->input->post('email'),
                'id_disciplina' => $this->input->post('id_disciplina')?$this->input->post('id_disciplina'):null,
                //'id_centro' => $this->input->post('centro'),
                //'sexo'      => $this->input->post('sexo'),
                'inscrito' => 1,
                //'created_on' => now(),
                'extra' => json_encode($extra)
            );
            
            if($this->current_user)
            {
                $data['user_id'] = $this->current_user->id;
            }
           
            if($id=$this->registro_m->save($data))
            {
                
                if($configuracion->module)
                    Registro::SaveResource($configuracion,$this->input->post('module_id'),$this->input->post());
                    
                if($configuracion->template && $data['email'])
                {
                        
                        $data['id']                 = $id;  
                        $data['evento']             = $evento; 
                        $data['registro']           = $this->registro_m->get($id); 
                        $data['nombre']             = strtoupper($this->input->post('participante'));
                    	$data['slug'] 				= $configuracion->template;
                   		$data['to'] 				= $data['email'];
                   		$data['from'] 				= Settings::get('server_email');
                   		$data['name']				= Settings::get('site_name');
                   		$data['reply-to']			= 'chicaychico.cobacam2018@cobacam.edu.mx';//Settings::get('contact_email');
                         Events::trigger('email', $data, 'array');
                         
                         
                     $this->session->set_flashdata('success',sprintf(lang('registros:add_thanks_email'),$data['email'],$configuracion->acuse,$id));
                }
                else
                {
                    $this->session->set_flashdata('success',lang('registros:add_thanks'));
                }
                
                redirect('registros/detalles/'.$id_evento.'/'.$id);
            }
            else
            {
                $this->session->set_flashdata('error',lang('registros:save_error'));
                redirect('registros/crear/'.$id_evento);
            }
            
            
       
       }
       
        foreach ($this->validation_rules as $rule)
  		{
    			$registro->{$rule['field']} = $this->input->post($rule['field'])?$this->input->post($rule['field']):$this->input->get($rule['field']);
  		} 
		
        
        
       
        $this->template->title($this->module_details['name'],'Inscripción al evento')
                ->append_js('spin.min.js')
                ->append_js('module::front/form.js')
                ->append_metadata('<script type="text/javascript">var lng='.($evento->map_longitud?$evento->map_longitud:-90.5467695763607).',lat='.($evento->map_latitud?$evento->map_latitud:19.833932192097134).',zoom='.($evento->map_zoom?$evento->map_zoom:10).', url_current=\''.base_url($this->uri->uri_string()).'\', data ='.json_encode($data_autocomplete).'; '.$configuracion->javascript.'</script>')
                ->set('evento',$evento)
                ->set('registro',$registro)
                ->set('configuracion',$configuracion)
                 ->set_layout('basic.html')
                //->enable_parser(false)
                ->set('list',$list)//Para el autocomplete
                ->build('form');
        
        
    }
   
    function download($doc='',$id=0)
    {
        ini_set('max_execution_time', 300);
        $this->load->library(array('pdf'));
        
        $html2pdf = new HTML2PDF('P', 'A4', 'fr');
        
       
        $output = ''; 
        $name_doc = '';    
        //$this->current_user OR redirect('users/login');           
        $director = $this->db->select('*,centros.nombre AS nombre_centro')
                            ->join('users','users.id=directores.user_id')
                            ->join('centros','centros.id=directores.id_centro')
                            ->where('user_id',$this->current_user->id)
                            ->get('directores')->row();
                       
        $base_where = array(
            //'registros.user_id' => $director->user_id,
            'inscrito'          => 1
        );
       
        switch($doc)
        {
            case 'tutor':
                 $registro = $this->registro_m->select('*,centros.nombre AS nombre_centro')
                        ->join('centros','centros.id=registros.id_centro')
                        ->join('eventos','eventos.id=registros.id_evento')
                        ->get_by('registros.id',$id) OR show_404();
                 if($registro->extra)
                {
                    $registro->extra = json_decode($registro->extra);
                }
                $output=$this->template->set_layout(false)
          //              ->title('Reporte ')
                        ->enable_parser(true)
						->build('templates/pdf_'.$doc,array('actividad'=>$registro->titulo,'director'=>$director->nombre,'participante'=>$registro->participante,'centro'=>$registro->nombre_centro),true);
                $doc = 'autorizacion_'.$doc;
         //echo $output;   
            break;
            
            case 'asesor':
                $registro = $this->registro_m->select('*,centros.nombre AS nombre_centro')
                        ->join('centros','centros.id=registros.id_centro')
                        ->join('eventos','eventos.id=registros.id_evento')
                        ->get_by('registros.id',$id) OR show_404();
                 if($registro->extra)
                {
                    $registro->extra = json_decode($registro->extra);
                }
                $output=$this->template->set_layout(false)
          //              ->title('Reporte ')
                        ->enable_parser(true)
						->build('templates/pdf_'.$doc,array('actividad'=>$registro->titulo,'asesor'=>$registro->extra->asesor, 'director'=>$director->nombre,'participante'=>$registro->participante,'centro'=>$registro->nombre_centro),true);
                $doc = 'autorizacion_'.$doc;
            break;
            case 'cedula':
             
                $disciplina = $this->db->where('id',$id)->get('disciplinas')->row();
                
                $base_where['disciplinas.id'] = $id;
                $registros = $this->registro_m->select('*')
                                ->join('disciplinas','disciplinas.id = registros.id_disciplina')
                                //->join('alumnos','registros.module_id = alumnos.matricula','LEFT')
                               
                                ->where($base_where)
                                ->get_all();
                
                
                $index = 1; 
                $table = '<table border="0">';   
                
                
                ///print_r($base_where);
                //exit();      
                foreach($registros as &$reg)
                {
                    $reg->extra = json_decode($reg->extra);
                    //$reg->sexo  = $reg->sexo == '1'?'HOMBRE':'MUJER';
                    //$reg->open_tr = ($index%4)==1?true:false;
                    //$reg->close_tr = ($index%4)==0?true:false;
                    $table .= ($index%4)==1?'<tr>':'';
                    if(isset($reg->extra->fotografia) && $reg->extra->fotografia)
                    {
                        $reg->extra->fotografia = '<img src="{{url:base}}files/cloud_thumb/'.$reg->extra->fotografia.'/100/100" style="margin:2px 4px;" />';
                    }
                    else
                    {
                        $reg->extra->fotografia = '{{asset:image file="no_photo.png" style="margin:2px 4px;width:100px;height:100px;"}}';
                    }
                    $reg->alumno = $this->db->where('matricula',$reg->module_id)->get('alumnos')->row();
                    
                    if($reg->alumno)
                    {
                        $reg->alumno->sexo = $reg->alumno->sexo==1?'HOMBRE':'MUJER';
                    }
                    $table .='<td width="25%" align="center">'.$reg->extra->fotografia.' <br /><small>'.resume($reg->participante,20,false).'</small></td>';
                    
                    $table .= ($index%4)==0 || count($registros)== $index?'</tr>':'';
                    
                    
                    
                    
                    $index++;
                }
                //print_r($registros);
                $table .='</table>';
                
                $output=$this->template->set_layout(false)
          //              ->title('Reporte ')
                        ->enable_parser(true)
						->build('templates/pdf_'.$doc,array('table'=>$table, 'registros'=>$registros,'total_rows'=>count($registros),'disciplina'=>$disciplina->nombre,'centro'=>$director->nombre_centro),true);
                //$doc = 'autorizacion_'.$doc;
            break;
             case 'lista':
             
                $disciplina = $this->db->where(array(
                                      'disciplinas.id' => $id,
                                      'user_id'        => $director->user_id
                                ))
                                ->join('disciplina_asesores','disciplina_asesores.id_disciplina=disciplinas.id','LEFT')
                                ->get('disciplinas')->row();
                
                $configuracion = $this->db->where('id_evento',$disciplina->id_evento)
                                            ->get('registro_configuracion')->row();
                
                
                $base_where['disciplinas.id'] = $id;
                $this->registro_m->select('*')
                                ->join('disciplinas','disciplinas.id = registros.id_disciplina');
                                
                if($configuracion->module)
                {
                    $this->registro_m->join($configuracion->module,$configuracion->module.'.'.$configuracion->module_id.'=registros.module_id');
                }
                  
                 $registros =  $this->registro_m->where($base_where)->get_all();
                
                
                $index = 1; 
               
                foreach($registros as &$reg)
                {
                    $reg->extra = json_decode($reg->extra);
                    //$reg->sexo  = $reg->sexo == '1'?'HOMBRE':'MUJER';
                    //$reg->open_tr = ($index%4)==1?true:false;
                    //$reg->close_tr = ($index%4)==0?true:false;
                   
                    if(isset($reg->extra->fotografia) && $reg->extra->fotografia)
                    {
                        $reg->extra->fotografia = '<img src="{{url:base}}files/cloud_thumb/'.$reg->extra->fotografia.'/100/100" style="margin:2px 4px;" />';
                    }
                    else
                    {
                        $reg->extra->fotografia = '{{asset:image file="no_photo.png" style="margin:2px 4px;width:100px;height:100px;"}}';
                    }
                    $reg->alumno = $this->db->where('matricula',$reg->module_id)->get('alumnos')->row();
                    
                    if($reg->alumno)
                    {
                        $reg->alumno->sexo = $reg->alumno->sexo==1?'HOMBRE':'MUJER';
                    }
                    
                   
                    
                    
                    
                    
                    $index++;
                }
                //print_r($registros);
              
                
                $output=$this->template->set_layout(false)
          //              ->title('Reporte ')
                        ->enable_parser(true)
						->build('templates/pdf_'.$doc,array( 'registros'=>$registros,'total_rows'=>count($registros),'disciplina'=>$disciplina,'centro'=>$director->nombre_centro),true);
                //$doc = 'autorizacion_'.$doc;
            break;
            default:
                show_error('El documento no existe');
            break;
        }
        //print_r($registros);
         // echo $output;                     
        //exit();
        $html2pdf->writeHTML($output);
        $html2pdf->Output($doc.'_'.now().'.pdf','I');
    }
    
    function acuse($template='',$id)
    {
          ini_set('max_execution_time', 300);
        $this->load->library(array('pdf','parser'));
        $this->load->model('templates/email_templates_m');
        
                
        /*$director = $this->db->select('*,centros.nombre AS nombre_centro')
                            ->join('users','users.id=directores.user_id')
                            ->join('centros','centros.id=directores.id_centro')
                            ->where('user_id',$this->current_user->id)
                            ->get('directores')->row();*/
        
        
        $html2pdf = new HTML2PDF('P', 'A4', 'fr');
        
        $email_template = (array)$this->email_templates_m->get_by('slug',$template);
        
        if(!$email_template)
        {
            echo 'Ha habido un problema al procesar la petición, te pedimos paciencia pues estamos trabajando en ello.';
        } 
        else{
            $output = ''; 
            $name_doc = $this->input->get('file_name')?$this->input->get('file_name'):'doc';   
            
            $registro = $this->registro_m->select('*,registros.id AS id,eventos.fecha AS fecha')
                                    ->join('eventos','eventos.id=registros.id_evento')
                                ->get_by('registros.id',$id) OR show_404();
                                
                             
            $disciplina = false;
            
            $registro->extra = json_decode($registro->extra);
            
            if($registro->id_disciplina)
            {
                $disciplina = $this->db->where(array(
                                      'disciplinas.id' => $registro->id_disciplina,
                                      
                                ))
                                
                                ->get('disciplinas')->row();
                                
                
            }
            if($registro)
            {
                $output = $this->parser->parse_string($email_template['body'],array_merge($_GET,array('registro'=>$registro,'disciplina'=>$disciplina)),true);
           
                $html2pdf->writeHTML($output);
                $html2pdf->Output($name_doc.'_'.now().'.pdf','I');
             }
        }
    }
    function asesores($id_disciplina=0)
    {
        
        $disciplina = $this->db->where('id',$id_disciplina)->get('disciplinas')->row();
        
        
        $asesor = $this->db->where(array(
                    'user_id' => $this->current_user->id,
                    'id_disciplina' => $id_disciplina
                ))
                //->join('disciplinas','disciplinas.id=disciplina_asesores.id_disciplina')
                ->get('disciplina_asesores')->row();
                
        if(!$asesor)
            $asesor = new StdClass();
        $this->load->model('empleados/empleado_m');
        $base_where = array(
            'activo' => 1,
        );
        $result = array(
            'status'  => false,
            'message' => '',
            'data'    => false
        );
        $validation_rules = array(
            array(
				'field' => 'asesor',
				'label' => 'Asesor',
				'rules' => 'trim|required'
				),
        );
        $this->form_validation->set_rules($validation_rules);	
        
       	if($this->form_validation->run())
		{
            unset($_POST['btnAction']);
            
            $data = array(
                
                'user_id' => $this->current_user->id,
                'id_disciplina' => $id_disciplina,
                //'observaciones' => $this->input->post('observaciones'),
            );
            
            
           
            $asesor = $this->db->where($data)->get('disciplina_asesores')->row();
            
            $data['observaciones'] = $this->input->post('observaciones');
            
            $data['asesor']        = $this->input->post('asesor');
            if($asesor)
            {
                $this->db->set($data)->where('id',$asesor->id)->update('disciplina_asesores');
            }
            else
            {
                
                $this->db->set($data)->insert('disciplina_asesores');
            }
            
            return $this->template->build_json(array(
            
                'status'  => true,
                'message' => lang('global:save_success'),
                'data'    => $data
            ));
           
        }
        elseif(validation_errors())
        {
       	    $message	= '<div class="alert alert-danger">'.validation_errors().'</div>';
            
            return $this->template->build_json(array(
            
                'status'  => false,
                'message' => $message
            ));
        }
        if($this->current_user && $this->current_user->group == 'director')
        {
            $director = $this->db
                            ->where('user_id',$this->current_user->id)->get('directores')->row();
            
            /*if($director)
            {
                $base_where['id_centro'] = $director->id_centro;
            }*/
            $base_where['id_centro <> 40 '] = null;
        }
        $empleados = $this->empleado_m->where($base_where)->order_by('nombre')
                                ->get_all(); 
                                
        foreach($empleados as &$empleado)
        {
            $empleado->nombre = $empleado->nombre.' '.$empleado->apellido_paterno.' '.$empleado->apellido_materno;  
        }                    
       
        return $this->template->set_layout(false)
                        ->set('asesor',$asesor)
                        ->set('disciplina',$disciplina)
                        ->set('empleados',array_for_select($empleados,'nombre','nombre'))
                        ->build('asesores/form');
    }
    
   
}
?>