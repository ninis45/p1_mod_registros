<section ng-controller="InputRegistro">
     <div class="lead text-success"><?=lang('registros:'.$this->method)?></div>
     <?php echo form_open_multipart(uri_string(),'',array('id'=>$registro->id?$registro->id:0,'evento'=>$id_evento,'table_config'=>$configuracion->module_id)); ?>
    
    
  
    <div class="ui-tab-container ui-tab-horizontal">
        
        
    	<uib-tabset justified="false" class="ui-tab">
    	        <uib-tab heading="Información General">
                    
                     <div class="form-group" ng-init="form.module_id='<?=$registro->module_id?>'" >   
                        <label><?=ucfirst($configuracion->module_id)?></label>
                        
                        <div class="input-group" >
                        
                            <?=form_input('module_id',$registro->module_id,'class="form-control" ng-model="form.module_id"')?>
                            <span class="input-group-addon"><a href="#" ng-click="add_part()"><i class="fa fa-plus"></i> </a></span>
                            
                            
                        </div>
                    </div>
                    <div class="form-group" ng-init="<?=$configuracion->module_id?>='<?=$registro->module_id?>'" >   
                        <label>Participante</label>
                        
                        
                        
                            <?=form_input('participante',$registro->participante,'class="form-control" ')?>
                            
                            
                           
                        
                    </div>
                    
                     
                      <div class="form-group">
                                    <label>Estatus</label>
                                    <?=form_dropdown('activo',array(''=>' [ Elegir ]','0'=>'Inactivo','1'=>'Activo'),$registro->activo,'class="form-control"');?>
                     </div>
                </uib-tab>
                <uib-tab heading="Datos participante">
                    
                    <div class="row">
                    
                       
                        <div class="col-md-6">
                            <?php foreach(json_decode($configuracion->campos) as $conf):?>
                            <?php if($conf->tipo =='upload')continue?>
                            <div class="form-group" <?=$conf->grupo == 'table'?'ng-init="form.'.$conf->slug.'=\''.$registro->{$conf->slug}.'\'"':''?>>
                                <label><?=$conf->nombre?></label>
                                <?php switch($conf->tipo){
                                      case 'text':
                                       case 'hidden':
                                      ?>
                                        <?=form_input($conf->slug,$registro->{$conf->slug},'class="form-control"  '.($conf->grupo == 'table'?'ng-model="form.'.$conf->slug.'"':''))?>
                                    <?php break;
                                       case 'select':
                                           
                                    ?>
                                    <?=form_dropdown($conf->slug,option_select($conf->opciones,'Seleccionar'),$registro->{$conf->slug},'class="form-control"')?>
                                    <?php break;
                                       default:
                                    ?>
                                    <div class="alert alert-warning"><i class="fa fa-warning"></i> <?=lang('registros:error_input')?></div>
                                    <?php break;?>
                                    
                                <?php }?>
                            </div>
                            <?php endforeach;?>
                        </div>
                        <div class="col-md-6">
                            <div class="row">
                            
                                <div class="col-lg-4">
                                    <?php foreach(json_decode($configuracion->campos) as $conf):?>
                                    <?php if($conf->tipo =='upload'){?>
                                    <div class="block-file">
                                        <?php if($values[$conf->slug]):?>
                                        <input type="hidden" value="<?=$values[$conf->slug]?>" name="extra[<?=$conf->slug?>]" />
                                        <img src="<?=base_url('files/cloud_thumb/'.$values[$conf->slug].'/100/100')?>"/>
                                        <button title="Eliminar elemento" data-dismiss="alert" class="close" close-block data-parent=".block-file"  >×</button>
                                         <?php else:?>
                                         <?php echo Asset::img('no-image.jpg',true);?>
                                         <?php endif;?>
                                         
                                    </div>
                                    <?php }?>
                                    <?php endforeach;?>
                                </div>
                                
                                <div class="col-lg-6">
                                            <?=form_upload('fotografia',null,'accept="image/*"')?>
                                            <p class="help-block">Puedes subir/cambiar  la fotografía en JPG|PNG|GIF.</p>
                                </div>
                               
                            </div>
                            
                        </div>
                    </div>
                    
                </uib-tab>
        </uib-tabset>
        <div class="clearfix"></div>
     </div>
     <div class="buttons form-action">
        <br />
    	<?php $this->load->view('admin/partials/buttons', array('buttons' => array('save'))) ?>
        <a href="<?=base_url('admin/registros/'.$id_evento)?>" class="btn btn-w-md ui-wave btn-default">Cancelar</a>
        <input type="hidden" value="<?=base_url('admin/registros/'.$id_evento)?>" name="uri_redirect"/>
     </div>
      
    <?php echo form_close();?>
</section>
<script type="text/ng-template" id="ModalPrepend.html">
    <div class="modal-header">
                                <h3>Buscar participante</h3>
    </div>
    <div class="modal-body">
        <input type="text" class="form-control" ng-model="txtSearch"/>
        <table class="table">
            <thead>
                <tr>
                    <th><?=ucfirst($configuracion->module_id)?></th>
                    <th width="10%"></th>
                </tr>
            </thead>
            <tbody>
                <tr ng-repeat="resource in resources | filter:txtSearch | limitTo:8">
                    <td>{{resource.module_id}}</td>
                    <td><a href="#" class="btn btn-primary" ng-click="save_item(resource)"><i class="fa fa-arrow-right"></i></a></td>
                </tr>
            </tbody>
        </table>
    </div>
    <div class="modal-footer">
                                <button ui-wave class="btn btn-flat" ng-click="cancel()">Cancelar</button>
                                
    </div>
</script>