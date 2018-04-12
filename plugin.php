<?php defined('BASEPATH') OR exit('No direct script access allowed');
/**
 * Search Plugin
 *
 * Use the search plugin to display search forms and content
 *
 * @author  PyroCMS Dev Team
 * @package PyroCMS\Core\Modules\Search\Plugins
 */
class Plugin_Registros extends Plugin
{
	public $version = '1.0.0';

	public $name = array(
		'en' => 'Search',
            'fa' => 'جستجو',
	);

	public $description = array(
		'en' => 'Create a search form and display search results.',
        'fa' => 'ایجاد فرم جستجو و نمایش نتایج',
	);
    public function __construct()
	{
	   $this->load->model(array('registros/registro_m','registros/alumno_m','medallero/disciplina_m','directores/director_m'));
       $this->template->append_metadata('<script type="text/javascript">var SITE_URL="'.base_url().'";</script>');
            //->append_css('typehead.css')
            //->append_js('handlebars.js')
            //->append_js('bloodhound.min.js')
            //->append_js('typeahead.bundle.min.js')
            //->append_metadata(Asset::js('modules/registros/index.js'));
            //->build('registros/metadata');
    }
    public function listing()
    {
        $limit  = $this->attribute('limit',6);
        $offset = $this->attribute('offset',0);
        
        $list  = $this->db->join('eventos','eventos.id=registro_configuracion.id_evento')
                            ->where('cerrado',0)
                            ->limit($limit,$offset)
                            ->get('registro_configuracion')
                            
                            ->result();
        
       
                    
        return $list;
    }
    public function load_form()
    {
        $base_where = array();
        
        if($this->current_user && $this->current_user->group == 'director')
        {
             
             $director = $this->director_m
                    ->join('users','users.id=directores.user_id')->get_by('users.id',$this->current_user->id);
                    
             $base_where['id_centro'] = $director->id_centro;
        }
        
        $id_evento = $this->attribute('evento');
        $module    = $this->attribute('module','alumnos');
        $disciplinas = $this->disciplina_m->where(array(
                            'id_evento' => $id_evento,
                            'activo'    => 1
                        ))->dropdown('id','nombre');
        $data = array();
        $ids_not = array();
        $agregados = array();
        
      
        
        $registros = $this->registro_m->select('*,disciplinas.nombre AS nombre_disciplina,registros.id AS id')
                            ->where($base_where)
                            ->where('registros.id_evento',$id_evento)
                            ->join('disciplinas','disciplinas.id=registros.id_disciplina')
                            ->get_all();
       
        foreach($registros as &$registro)
        {
            $registro->extra = json_decode($registro->extra);
            $registro->message = $registro->activo=='1'?'':'<div class="alert alert-warning">La disciplina ya no se encuentra disponible, solamente la podras cambiar por una que este activa.</div>';
            $agregados[$registro->id] = $registro;
            
            $ids_not[] = $registro->module_id;
        }
        
        /*if(count($ids_not)>0)
        {
            $this->alumno_m->where_not_in('matricula',$ids_not);
        }*/
        $alumnos = $this->alumno_m->where_not_in('grado',array('6'))
                    ->where($base_where)->get_all();
                    
        
        foreach($alumnos as $alumno)
        {
            $data[] = array(
                'tokens' => array($alumno->nombre,$alumno->apellido_paterno,$alumno->apellido_materno,$alumno->matricula),
                'nombre'=>$alumno->nombre.' '.$alumno->apellido_paterno.' '.$alumno->apellido_materno,
                'matricula' => $alumno->matricula
            );
        }
        
        $output = form_open($this->uri->uri_string(),array('method'=>'post'));
        $output .= '<div id="id="custom-templates"">';
        $output .=form_input('search','','class="form-control typeahead" id="text_auto" placeholder="Buscar alumno"');
        $output .= '</div><hr/><p text-align="right" class="text-right">Total participantes:'.count($registros).'</p>';
        $output .= '<div class="class="table-responsive">
                        <table class="table table-hover course-list-table tablesorter">
                            <thead>
                                <tr>
                                    <th>Nombre</th>
                                    <th width="14%">Disciplina</th>
                                    <th width="9%">Talla</th>
                                    <th width="14%">Hospedaje</th>
                                    <th width="16%">Documentos.</th>
                                    <th widt="2%">
                                </tr>
                            </thead>
                            <tbody id="list-content">
                                ';
            
                            
         $output .='          
                            </tbody>
                       </table>
           </div>';
       
        
        $output .= form_close(); 
        
        
        $output .='<div class="modal fade" id="modalRegistro" tabindex="-1">
                      <div class="modal-dialog" role="document">
                        <div class="modal-content">
                             <div class="modal-header">
                              <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                              <h4 class="modal-title" id="myModalLabel">Registro de participante</h4>
                            </div>
                            <div class="modal-body">
                            '.form_open_multipart($this->uri->uri_string(),array('method'=>'post','id'=>'form'),array('evento'=>$id_evento,'module'=>'alumnos')).'
                               <div id="notices-modal"></div>
                               <!-- Nav tabs -->
                              <ul class="nav nav-tabs" role="tablist">
                                <li role="presentation" class="active"><a href="#tab_participante" aria-controls="home" role="tab" data-toggle="tab">Participante</a></li>
                                <li role="presentation"><a href="#tab_asesor" aria-controls="profile" role="tab" data-toggle="tab">Asesor</a></li>
                                
                              </ul>
                            
                              <!-- Tab panes -->
                              <div class="tab-content">
                                <div role="tabpanel" class="tab-pane active" id="tab_participante">
                                    <div class="row">
                                
                                        <div class="col-md-3">
                                            
                                            <div class="avatar">
                                                <div class="hovereffect">
                                                    <input type="hidden" value="" name="extra[fotografia]" id="fotografia" />
                                                    <img class="img-responsive" id="img-avatar" src="{{ asset:image_url file="logo_mini.jpg" }}" />
                                                    
                                                    <div class="overlay">
                                                        
                                        				     
                                        					 <a href="javascript:;" title="Modificar imagen del avatar">
                                                                <i class="fa fa-edit"></i> 
                                                                <input type="file" id="fotografia_tmp" name="fotografia" class="upload" accept="image/*" />
                                                             </a>
                                        				    
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-9">
                                               <div class="form-group">
                                                  <label>Nombre del participante</label>
                                                  <input type="text" class="form-control" id="participante" name="participante"/>
                                                  <input type="hidden" id="matricula" class="form-control"  name="matricula"/>
                                               </div>
                                               <div class="form-group">
                                                  <label>Sexo del participante</label>
                                                  <select  class="form-control" id="sexo" name="sexo">
                                                    <option value=""> [ Elegir ] </option>
                                                    <option value="1">Hombre</option>
                                                    <option value="2">Mujer</option>
                                                  </select>
                                                  
                                               </div>
                                               <div class="form-group">
                                                  <label>Teléfono</label>
                                                  <input type="text" class="form-control" id="telefono_participante" name="extra[telefono_participante]"/>
                                               </div>
                                               
                                               <div class="form-group">
                                                  <label>Facebook</label>
                                                  <input type="text" class="form-control" id="facebook" name="extra[facebook]"/>
                                               </div>
                                               <div class="form-group">
                                                  <label>Disciplina</label>
                                                  '.form_dropdown('disciplina',array(''=>' [ Elegir ]')+$disciplinas,'','class="form-control" id="disciplina"').'
                                               </div>
                                               
                                               <div class="form-group">
                                                  <label>Talla</label>
                                                  '.form_dropdown('extra[talla_participante]',array(''=>' [ Elegir ]','CH'=>'Chica','M'=>'Mediana','G'=>'Grande','EG'=>'Extra grande'),'','class="form-control" id="talla_participante"').'
                                                  <p class="help-block">Talla del uniforme</p>
                                               </div>
                                               
                                               
                                               <hr/>
                                                <div class="form-group">
                                                  <label>Descripción</label>
                                                  <textarea class="form-control" id="descripcion" name="extra[descripcion]" placeholder="Ejemplo: Tema de las canciones en la disciplina de canto." ></textarea>
                                                  <p class="help-block">Describe acerca de la disciplina que escogiste</p>
                                               </div>
                                               <div class="form-group">
                                                  <label>Observaciones</label>
                                                  <textarea class="form-control" id="observaciones" name="extra[observaciones]" ></textarea>
                                                  <p class="help-block">Puedes colocar cualquier información relacionado con el participante</p>
                                               </div>
                                               <hr/>
                                               <div class="form-group">
                                                 
                                                  <label class="checkbox">
                                                       <input type="checkbox"  id="hospedaje_participante" name="extra[hospedaje_participante]" value="1"/>
                                                       Hospedaje para el participante
                                                  </label>
                                                  
                                                  <span class="help-inline">Marque la casilla si requiere hospedaje el participante</span>
                                               
                                               </div>      
                                        </div>
                                    </div>
                                
                                </div>
                                <div role="tabpanel" class="tab-pane" id="tab_asesor">
                                    <div class="form-group">
                                                  <label>Nombre del asesor</label>
                                                  <input type="text" class="form-control" id="asesor" name="extra[asesor]"/>
                                                 
                                    </div>
                                    <div class="form-group">
                                                  <label>Sexo del asesor</label>
                                                  '.form_dropdown('extra[sexo_asesor]',array(''=>' [ Elegir ]','H'=>'Hombre','M'=>'Mujer'),'','class="form-control" id="sexo_asesor"').'
                                                  
                                    </div>
                                    <div class="form-group">
                                                  <label>Teléfono</label>
                                                  <input type="text" class="form-control" id="telefono_asesor" name="extra[telefono_asesor]"/>
                                    </div>
                                    <div class="form-group">
                                                  <label>Talla</label>
                                                  '.form_dropdown('extra[talla_asesor]',array(''=>' [ Elegir ]','CH'=>'Chica','M'=>'Mediana','G'=>'Grande','EG'=>'Extra grande'),'','class="form-control" id="talla_asesor"').'
                                                  <p class="help-block">Talla del uniforme</p>
                                    </div>
                                    <div class="form-group">
                                       
                                                  <label class="checkbox">
                                                       <input type="checkbox"  id="hospedaje_asesor" name="extra[hospedaje_asesor]" value="1"/>
                                                       Hospedaje para el asesor
                                                  </label>
                                                  <span class="help-inline">Marque la casilla si requiere hospedaje el asesor</span>
                                     </div>      
                                    
                                </div>
                                
                              </div>
                              
                                
                                   
                               '.form_close().'
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-color-grey-light" data-dismiss="modal">Cerrar</button>
                                <button type="button" class="btn btn-success" id="btn-save"  >Guardar</button>
                              
                            </div>
                          
                          
                        </div>
                      </div>
                    </div>
                    ';
        
        $this->template->append_metadata('<script type="text/javascript">var registros='.json_encode($agregados).', action_redirect="'.base_url($this->uri->uri_string()).'", data = '.json_encode($data).'; var id_evento='.$id_evento.';</script>');
        return $output;
        
    }
    function list_cedula()
    {
        //COUNT(*),disciplinas.nombre
        $select     = $this->attribute('select','*');
        $group_by   = $this->attribute('group-by','disciplinas.id');
        $id_evento = $this->attribute('evento');
        $cedulas    = array();
        $base_where = array(
            'activo' => 0,
            'registros.id_evento' => $id_evento,
        );
       
        if($this->current_user && $this->current_user->group == 'director')
        {
             
             $director = $this->director_m
                    ->join('users','users.id=directores.user_id')->get_by('users.id',$this->current_user->id);
                    
             $base_where['id_centro'] = $director->id_centro;
        }
        $disciplinas = $this->disciplina_m->join('registros','registros.id_disciplina=disciplinas.id')
                            ->select($select)
                            ->where($base_where)
                            ->group_by($group_by)
                            ->get_all();
        /*foreach($disciplinas as &$disciplina)
        {
            $disciplina->extra = json_decode($disciplina->extra);
            if(isset($cedulas[$disciplina->id_disciplina][$disciplina->extra->asesor])== false)
            {
                
            }
        }*/
                            
        return $disciplinas;
    }
    function status()
    {
        if($this->current_user == FALSE) {
            
            return false;
        }
        
        $result = $this->director_m
                    ->join('users','users.id=directores.user_id')->get_by('users.id',$this->current_user->id);
                    
                    
        return (bool)$result;
    }
 }
 ?>