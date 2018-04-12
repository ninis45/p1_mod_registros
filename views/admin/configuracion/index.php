<section ng-controller="InputCtrl">
    <div class="lead text-success"><?=sprintf(lang('configuracion:title_create'),$evento->titulo)?></div>
    <?php echo form_open($this->uri->uri_string(),'method="post"');?>
       
        <div class="form-group row">
            <div class="col-lg-4">
                <label>Descripción</label>
                <br />
                <small class="text-muted">Agrega un descripción de lo que se trata el registro</small>
            </div>
            <div class="col-lg-8"><?=form_input('descripcion',$configuracion->descripcion,'class="form-control"')?></div>
        </div>
        
        <hr />
        <div class="form-group row">
            <div class="col-lg-4" ng-init="module.slug='<?=$configuracion->module?>'">
                <label>Módulo</label>
                <br />
                <small class="text-muted">Selecciona el origen de los datos a extraer</small>
            </div>
            <div class="col-lg-8"><?=form_dropdown('module',array(''=>' [ Elegir ] '),$configuracion->module,'class="form-control" ng-options="module.name for module in modules track by module.slug" ng-model="module" ')?></div>
        </div>
        <hr />
        <div class="form-group row" ng-init="module_id='<?=$configuracion->module_id?>'">
            <div class="col-lg-4">
                <label>Campo primario</label>
                <br />
                <small class="text-muted">Selecciona el campo con el cual se vinculará</small>
            </div>
            <div class="col-lg-8">
               <?=form_dropdown('module_id',array(''=>' [ Elegir ] '),$configuracion->module_id,'class="form-control" ng-options="row for row in rows_left track by row" ng-model="module_id" ')?>    
            </div>
        </div>
        <hr />
        <div class="form-group row" ng-init="group_by='<?=$configuracion->group_by?>'">
            <div class="col-lg-4">
                <label>Agrupado por</label>
                <br />
                <small class="text-muted">Agrupar registros de acuerdo a la columna</small>
            </div>
            <div class="col-lg-8">
               <?=form_dropdown('group_by',array(''=>' [ Elegir ] '),$configuracion->group_by,'class="form-control" ng-options="row for row in rows_left track by row" ng-model="group_by" ')?>    
            </div>
        </div>
        <hr />
        
        <div class="form-group row">
            <div class="col-lg-4">
                <label>Vista autocomplete</label>
                <br />
                <small class="text-muted">Diseño y forma del autocomplete</small>
            </div>
            <div class="col-lg-8">
               <?=form_input('autocomplete_display',$configuracion->autocomplete_display,'class="form-control" ng-non-bindable ');?>
            </div>
        </div>
        <hr />
        <div class="form-group row" ng-init="disciplinas='<?=$configuracion->disciplinas?>'">
            <div class="col-lg-4">
                <label>Habilitar disciplinas</label>
                <br />
                <small class="text-muted">Activa el campo disciplina para el registro del participante.</small>
            </div>
            <div class="col-lg-8">
                <label><input type="radio" name="disciplinas" ng-model="disciplinas" value="1" <?=$configuracion->disciplinas==1?'checked':''?>/> Si</label>
                <label><input type="radio" name="disciplinas" ng-model="disciplinas" value="0" <?=$configuracion->disciplinas!=1?'checked':''?>/> No</label>
            </div>
        </div>
        <hr />
        <div class="form-group row">
            <div class="col-lg-4">
                <label>Búsqueda por autocomplete</label>
                <br />
                <small class="text-muted">Activa el autocomplete para busqueda de participantes.</small>
            </div>
            <div class="col-lg-8" ng-init="autocomplete=<?=$configuracion->autocomplete?>">
               
                <label><input type="radio" name="autocomplete" ng-model="autocomplete"  value="1" <?=$configuracion->autocomplete==1?'checked':''?>/> Si</label>
                <label><input type="radio" name="autocomplete" ng-model="autocomplete"  value="0" <?=$configuracion->autocomplete!=1?'checked':''?>/> No</label>
                
            </div>
        </div>
        <hr />
        <div class="form-group row">
            <div class="col-lg-4">
                <label>Bloqueo por autocomplete</label>
                <br />
                <small class="text-muted">Permitir que solo se inscriban a través del autocomplete.</small>
            </div>
            <div class="col-lg-8">
                <label><input type="radio" name="forced" value="1" <?=$configuracion->forced==1?'checked':''?>/> Si</label>
                <label><input type="radio" name="forced" value="0" <?=$configuracion->forced!=1?'checked':''?>/> No</label> 
                  
            </div>
        </div>
        <hr />
        
        <div class="form-group row">
            <div class="col-lg-4">
                <label>Habilitar fotografia</label>
                <br />
                <small class="text-muted">Habilita la subida de fotografia del participante al registro.</small>
            </div>
            <div class="col-lg-8">
                <label><input type="radio" name="fotografia" value="1" <?=$configuracion->fotografia==1?'checked':''?>/> Si</label>
                <label><input type="radio" name="fotografia" value="0" <?=$configuracion->fotografia!=1?'checked':''?>/> No</label> 
                  
            </div>
        </div>
        <hr />
        <div class="form-group row">
            <div class="col-lg-4">
                <label>Columnas del registro</label>
                <br />
                <small class="text-muted">Presentación de la plantilla de registro.</small>
            </div>
            
            <div class="col-lg-8">
                <label><input type="radio" name="template_column" value="2-6-4" <?=$configuracion->template_column=='2-6-4'?'checked':''?>/> Imagen - Evento - Formulario</label>
                <label><input type="radio" name="template_column" value="6-6" <?=$configuracion->template_column=='6-6'?'checked':''?>/> Evento - Formulario</label> 
                  
            </div>
        </div>
        <hr />
        <div class="form-group row">
            <div class="col-lg-4">
                <label>Concatenar campo participante</label>
                <br />
                <small class="text-muted">Agrega los campos a concatenar para formar el nombre del participante</small>
            </div>
            <div class="col-lg-8"><?=form_input('participante',$configuracion->participante,'class="form-control" ng-disabled="autocomplete==0"')?></div>
        </div>
        <hr />
        <div class="form-group row" ng-if="disciplinas==1" >
            <div class="col-lg-4">
                <label>Plantilla cedula</label>
                <br />
                <small class="text-muted">Integra una plantilla para la descarga de la Cédula.</small>
            </div>
            <div class="col-lg-8"><?=form_dropdown('template_cedula',array(''=>' [ Elegir ] ')+$templates,$configuracion->template,'class="form-control"')?></div>
        </div>
        <hr />
        <div class="form-group row">
            <div class="col-lg-4">
                <label>Plantilla email</label>
                <br />
                <small class="text-muted">Integra una plantilla de email el cual será enviado una vez terminado el registro.</small>
            </div>
            <div class="col-lg-8"><?=form_dropdown('template',array(''=>' [ Elegir ] ')+$templates,$configuracion->template,'class="form-control"')?></div>
        </div>
        <hr />
        <div class="form-group row">
            <div class="col-lg-4">
                <label>Plantilla acuse</label>
                <br />
                <small class="text-muted">Integra una plantilla para el acuse de tu registro.</small>
            </div>
            <div class="col-lg-8"><?=form_dropdown('acuse',array(''=>' [ Elegir ] ')+$templates,$configuracion->acuse,'class="form-control"')?></div>
        </div>
        <hr />
        <div class="form-group row">
            <div class="col-lg-4">
                <label>Estado</label>
                <br />
                <small class="text-muted">Habilita el registro para el evento.</small>
            </div>
            <div class="col-lg-8">
            
                <label><input type="radio" name="cerrado" value="0" <?=$configuracion->cerrado?'':'checked'?>/> Abierto</label>
                <label><input type="radio" name="cerrado" value="1" <?=$configuracion->cerrado?'checked':''?>/> Cerrado</label> 
            </div>
        </div>
        <hr />
        <div class="form-group row">
            <div class="col-lg-4">
                <label>Autenticación</label>
                <br />
                <small class="text-muted">Habilita si desea autenticar la sección.</small>
            </div>
            <div class="col-lg-8"> 
                <label><input type="radio" name="auth" value="1" <?=$configuracion->auth?'checked':''?>/> Si</label> 
                <label><input type="radio" name="auth" value="0" <?=$configuracion->auth?'':'checked'?>/> No</label>
                
                
            </div>
        </div>
        <hr />
        <div class="form-group row">
            <div class="col-lg-4">
                <label>Javascript</label>
                <br />
                <small class="text-muted">Puedes agregar un codigo javascript al evento.</small>
            </div>
            <div class="col-lg-8"><?=form_textarea('javascript',$configuracion->javascript,'class="form-control"')?></div>
        </div>
        
         
        <div class="row">
            <div class="col-md-6">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Columna</th>
                            <th width="10%"></th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr ng-repeat="row in rows_left">
                            <td>{{row}}</td>
                            <td><a href="#" title="Agregar este campo al registro" class="btn btn-primary" ng-click="add_item(row)"><i class="fa fa-arrow-right"></i></a></td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <div class="col-md-6">
                <table class="table" ng-if="rows_right.length>0">
                    <thead>
                        <tr>
                            <th>Nombre</th>
                            <th>Slug</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr ng-repeat="row in rows_right">
                            <td>{{row.nombre}}
                                <input type="hidden" name="campos[{{$index}}][nombre]" value="{{row.nombre}}" />
                                <input type="hidden" name="campos[{{$index}}][slug]" value="{{row.slug}}" />
                                <input type="hidden" name="campos[{{$index}}][tipo]" value="{{row.tipo}}" />
                                <input type="hidden" name="campos[{{$index}}][opciones]" value="{{row.opciones}}" />
                                
                                <input type="hidden" name="campos[{{$index}}][grupo]" value="{{row.grupo}}" />
                                
                                <input type="hidden" name="campos[{{$index}}][obligatorio]" value="{{row.obligatorio}}" />
                            </td>
                            <td>{{row.slug}}</td>
                            <td>
                                <a href="#" class="btn btn-success" ng-click="edit_item(row)"><i class="fa fa-pencil"></i></a>
                                <a href="#" class="btn btn-danger" ng-click="remove_item($index)"><i class="fa fa-remove"></i></a>
                            </td>
                        </tr>
                    </tbody>
                </table>
                <div class="alert alert-info text-center" ng-if="rows_right.length == 0"><?=lang('global:not_found')?></div>
                <a href="#" ng-click="add_item()" class="btn btn-primary btn-block"><i class="fa fa-plus"></i> Agregar campo personalizado</a>
            </div>
        </div>
        <div class="buttons divider clearfix" >
			<?php $this->load->view('admin/partials/buttons', array('buttons' => array('save') )) ?>
            
            <a href="<?=base_url('admin/registros/'.$evento->id)?>" class="btn btn-default btn-w-md ui-wave">Cancelar</a>
         </div>
    <?php echo  form_close();?>
</section>
<script type="text/ng-template" id="ModalPrepend.html">
    <div class="modal-header">
                                <h3>Asignar campo</h3>
    </div>
    <div class="modal-body">
    <?php echo form_open();?>
       
        
                    <div class="form-group">
                        
                            <label>Nombre</label>
                            <input type="text" class="form-control" ng-model="form.nombre"/>
                                
                    </div>
        	        <div class="form-group">
                        
                            <label>Slug</label>
                            <input type="text" class="form-control" ng-model="form.slug"/>
                                
                    </div>
                    
                    <div class="form-group">
                        
                            <label>Tipo</label>
                            <select class="form-control" ng-model="form.tipo">
                                <option value="text">Texto</option>
                                <option value="select">Seleccion</option>
                                <option value="upload">Archivo</option>
                                <option value="hidden">Oculto</option>
                                <option value="textarea">Area de texto(Chips)</option>
                            </select>
                                
                    </div>
                    
                    <div class="form-group" ng-if="form.tipo=='select'">
                        
                            <label>Opciones</label>
                            <textarea class="form-control" ng-model="form.opciones" placeholder="Ejemplo de sintaxis: 0=Inactivo|1=Activo"></textarea>
                                
                    </div>
                    
                    <div class="form-group">
                        
                            <label>Obligatorio</label>
                            <select class="form-control" ng-model="form.obligatorio">
                                <option value="0">No</option>
                                <option value="1">Si</option>
                                
                            </select>
                                
                    </div>
                    
                
    <?php echo form_close();?>
    </div>
    <div class="modal-footer">
                                <button ui-wave class="btn btn-flat" ng-click="cancel()">Cancelar</button>
                                <button ui-wave class="btn btn-flat btn-primary"  ng-click="save_item()">Aceptar</button>
    </div>
</script>