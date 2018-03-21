<section ng-controller="IndexCtrl">
    <div class="lead text-success"><?=lang('registros:title')?> del evento "<?=$evento->titulo?>"</div>
    <?php echo form_open($this->uri->uri_string(), 'class="form-inline" method="get" ') ?>
    		<div class="row" >
    			
               
                
                 
    			<div class="form-group col-md-4">
    				
    				<?php echo form_input('f_keywords', '', ' class="form-control" placeholder="Participante o disciplina" style="width:90%;"') ?>
    			</div>
                <div class="col-md-8">
        			<button class="md-raised btn btn-primary"><i class="fa fa-search"></i> Buscar</button>
                    <a href="#" ng-click="open_busqueda()"  class="btn btn-default" ><i class="fa fa-search"></i> Búsqueda avanzada</a>
        			<?php if($_GET):?>
                    <a href="<?=base_url($this->uri->uri_string())?>" class="btn btn-default"><i class="fa fa-refresh"></i> Mostrar todos</a>
                    <?php endif;?>
                    
                     
                     
                    
                      <div class="btn-group" uib-dropdown is-open="status.isopen1">
                                <button ui-wave type="button" class="btn btn-raised btn-w-lg btn-primary dropdown-toggle" uib-dropdown-toggle ng-disabled="disabled"> Acciones <span class="caret"></span> </button>
                                <ul class="dropdown-menu" role="menu">
                                    <li><a href="javascript:;" ng-click="open_modal()"><i class="fa fa-envelope"></i> Enviar mensaje</a></li>
                                    <li><a href="<?=base_url('admin/registros/cedula/')?>">Descargar cédula</a></li>
                                    <!--li class="divider"></li>
                                    <li><a href="javascript:;">Separated link</a></li-->
                                </ul>
                      </div>
                </div>
    			
    	</div>	
    	<?php echo form_close() ?>
        <hr />
        <p class="text-right text-muted">Total registros: <?=$pagination['total_rows']?> </p>
        <?php echo form_open('admin/registros/action') ?>
        <table class="table">
                                        <thead>
                                            <tr>
                                                 <th width="3%">
                                                    <label>
                                                    <?php echo  form_checkbox(array(
                                                                
                                                                'class'=>'check-all',
                                                                'ng-model'=>'checked_all'
                                                                ));?>
                                                            
                                                    </label>
                                               </th>
                                                <th width="2%">ID</th>
                                                <th>Foto</th>
                                                <th>Participante</th>
                                                <?php if(isset($disciplinas)){ ?>
                                                <th>Disciplina</th>
                                                <?php }?>
                                                <th width="10%">Email</th>
                                                <th width="14%"></th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                        <?php foreach($registros as $registro):?>
                                            <tr class="<?=$registro->inscrito==1?'success':''?>">
                                                <td>
                                                     <?php echo  form_checkbox(array(
                                      
                                                              'name'=>'action_to[]',
                                                              'value'=>$registro->id,
                                                              'ng-checked'=>'checked_all'
                                                              
                                                        ));
                                             
                                                  ?>
                                                </td>
                                                <td><?=$registro->id?></td>
                                                <td>
                                                <?php if($registro->fotografia): ?>
                                                    <a href="<?=base_url('files/download/'.$registro->fotografia)?>" title="Descargar fotografia" target="_blank"><img src="<?=base_url('files/cloud_thumb/'.$registro->fotografia)?>" width="60" /></a>
                                                <?php else:?>
                                                    <img src="<?=base_url('files/cloud_thumb/'.$registro->fotografia)?>" width="60" />
                                                <?php endif;?>  
                                                 </td>
                                                <td>
                                                    <?=$registro->participante?><br />
                                                    <span class="text-muted"><?=$registro->module_id?></span>
                                                </td>
                                                  <?php if(isset($disciplinas)){ ?>
                                                <td>
                                                    <?php $disc = $this->db->where('id',$registro->id_disciplina)->get('disciplinas')->row(); ?>
                                                    
                                                     <?php echo $disc?$disc->nombre:'No disponible'; ?>
                                                </td>
                                                <?php }?>
                                                <td>
                                                    <a href="#" ng-click="open_modal(<?=$registro->id?>)"><?=$registro->email?></a>
                                                    <input type="hidden"  value="<?=$registro->email?>" />    
                                                </td>
                                                <td>
                                                     <?php echo anchor('admin/registros/delete/'.$registro->id.'/'.$registro->id_evento, lang('buttons:delete'), 'class="" confirm-action') ?> | 
                                                    <?php echo anchor('admin/registros/edit/'.$registro->id, lang('buttons:edit'), 'class=""') ?> 
                                                   
                                                </td>
                                                
                                            </tr>
                                        <?php endforeach;?>
                                        </tbody>
    </table>
     <?php $this->load->view('admin/partials/pagination') ?>
    <div class="action_buttons">
                <input type="hidden" name="url_return" value="<?=$this->uri->uri_string();?>" />
                <button class="btn btn-primary" confirm-action  data-msg="Desea eliminar  los siguientes registros" value="delete" name="btnAction"><i class="icon-trash"></i> Eliminar seleccionados</button>
                
    </div>
    <?php echo form_close();?>
</section>

<script type="text/ng-template" id="ModalBusqueda.html">
    <div class="modal-header">
                                <h3>Búsqueda avanzada</h3>
    </div>
    <?php echo form_open('admin/registros/'.$evento->id,'method="GET" ');?>
    <div class="modal-body">
                <div class="form-group">
    				<label>Inscritos</label>
                    <?=form_dropdown('inscrito',array(''=>'[Todos]','0'=>'No','1'=>'Si'),$this->input->get('inscrito'),'class="form-control"')?>
                </div>
                <?php if($groups){ ?>
                
                <div class="form-group">
                    <label>Centro/Plantel</label>
                    <?=form_dropdown('group',array(''=>'[ TODOS LOS GRUPOS ]')+$groups,$this->input->get('group'),'class="form-control"    ');?>
                </div>
                <?php }?>
                
                <?php if(isset($disciplinas)){ ?>
                
                <div class="form-group">
                    <label>Disciplina</label>
                    <?=form_dropdown('disciplina',array(''=>'[ TODOS ]')+$disciplinas,$this->input->get('disciplina'),'class="form-control"    ');?>
                </div>
                <?php }?>
    </div>
    <div class="modal-footer">
                                <button ui-wave class="btn btn-flat" type="button" ng-click="cancel()">Cancelar</button>
                                <button ui-wave class="btn btn-flat btn-primary" type="submit">Aceptar</button>
    </div>
     <?php echo form_close();?>
</script>

<script type="text/ng-template" id="ModalPrepend.html">
    <div class="modal-header">
                                <h3>Mensaje masivo </h3>
    </div>
    <div class="modal-body">
    <?php echo form_open();?>
       <div class="ui-tab-container ui-tab-horizontal">
      
        
    	  <uib-tabset justified="false" class="ui-tab">
              <uib-tab heading="Mensaje">
                   <div class="alert" ng-class="{'alert-danger':!status}" ng-if="message" ng-bind-html="message"></div>
                    <div class="form-group" >
                        
                            <label>Plantilla</label>
                            <?=form_dropdown('template', array(''=>'Ninguno'),null,'class="form-control" ng-options="template.name for template in templates track by template.slug" ng-model="form.template"')?>
                                
                    </div>
                    <div class="form-group" ng-if="!form.template">
                        
                            <label>Asunto</label>
                            <input type="text" class="form-control" ng-model="form.subject"/>
                                
                    </div>
                    
                    <!--div class="form-group" >
                        <div class="radio inline">
                            <label><input type="radio" ng-model="form.tipo" value="personalizado" /> Personalizado </label>  
                            <label><input type="radio" ng-model="form.tipo" value="plantilla" /> Plantilla </label>
                        </div>
                        
                    </div-->
        	        <div class="form-group" ng-if="!form.template">
                        
                            <label>Mensaje</label>
                            <textarea class="form-control" rows="4" ng-model="form.body"></textarea>
                                
                    </div>
                    <hr/>
                    
                    <md-progress-linear md-mode="indeterminate"  ng-if="show_progress"></md-progress-linear>
                    
              </uib-tab>
              <uib-tab heading="Correos electrónicos">
                    <div class="form-group">
                        <md-chips ng-model="emails" readonly="readonly"></md-chips>
                    </div>
              </uib-tab>
              <uib-tab heading="Variables">
                       <table class="table">
                        <thead>
                            <tr>
                                <th>Variable</th>
                                <th>Descripción</th>
                            </tr>
                        </thead>
                        <tbody ng-non-bindable>
                            <tr>
                                <td>
                                    {{&nbsp;evento.id&nbsp;}}    
                                </td>
                                <td>
                                    Id del evento
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    {{&nbsp;evento.titulo&nbsp;}}    
                                </td>
                                <td>
                                    Título o nombre   del evento 
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    {{&nbsp;evento.portada&nbsp;}}    
                                </td>
                                <td>
                                    Portada del evento
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    {{&nbsp;registro.id&nbsp;}}    
                                </td>
                                <td>
                                    Identificador del registro
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    {{&nbsp;registro.participante&nbsp;}}    
                                </td>
                                <td>
                                    Nombre del participante
                                </td>
                            </tr>
                        </tbody>
                    </table>
                    
                    
              </uib-tab>
          </uib-tabset>
       </div>
       
        
        
                    
                    
                    
                    
                    
                
    <?php echo form_close();?>
    </div>
    <div class="modal-footer">
                                <button ui-wave class="btn btn-flat" ng-click="cancel()">Cancelar</button>
                                <button ui-wave class="btn btn-flat btn-primary"  ng-click="send()">Aceptar</button>
    </div>
</script>