

<section ng-controller="InputImport">
    <div class="lead text-success">
        <?=lang('registros:import')?>
    </div>
    <?php if($id_evento && $id_resource){ ?>
    <div class="row">
        <div class="col-md-6">
            <h4>Evento recurso</h4>
            <input type="hidden" ng-model="id_resourse" value="<?=$id_resource?>" />
            <?=form_input('txtsearch_left',null,'ng-model="txtsearch_left" placeholder="Buscar disciplina" class="form-control"')?>
            <hr />
            <uib-accordion close-others="true" class="ui-accordion">
                <uib-accordion-group ng-repeat="(index,disciplina) in disciplinas|filter:txtsearch_left" heading="{{disciplina.nombre}}({{disciplina.rama}})" >
                    
                    <table class="table">
                        <thead>
                            <tr>
                                
                                <th>Centro/Plantel</th>
                                <th>Cantidad</th>
                                <th width="10%"></th>
                            </tr>
                        </thead>
                        <tbody >
                            <tr ng-repeat="centro in disciplina.participantes" >
                               
                                <td>{{centro.nombre}}</td>
                                <td>{{centro.total}}</td>
                                <td><a href="#" ng-click="add_items(centro,disciplina,<?=$id_evento?>)" confirm-action  class="btn btn-mini btn-primary"><i class="fa fa-arrow-right"></i></a></td>
                            </tr>
                        </tbody>
                    </table>
                </uib-accordion-group>
            </uib-accordion>
        </div>
        <div class="col-md-6">
            <h4>Evento actual</h4>
            <div class="alert alert-info" ng-if="!disciplinas_right"><?=lang('registros:import_help')?></div>
            <uib-accordion close-others="true"  class="ui-accordion">
                <uib-accordion-group ng-repeat="disciplina in disciplinas_right" heading="{{disciplina.nombre}}({{disciplina.rama}})" >
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Centro/Plantel</th>
                                <th>Cantidad</th>
                                <th width="10%"></th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr ng-repeat="centro in disciplina.participantes">
                                <td>{{centro.nombre}}</td>
                                <td>{{centro.total}}</td>
                                <td><a href="#" ng-click="remove_item(centro)" confirm-action  class="btn btn-mini btn-danger"><i class="fa fa-remove"></i></a></td>
                            </tr>
                        </tbody>
                    </table>
                </uib-accordion-group>
            </uib-accordion>
        </div>
    </div>
    <?php }else{?>
         <?php if(!$configuracion->module){ ?>
            <div class="alert alert-warning text-center"><i class="fa fa-warning"></i> <?=lang('registros:table_not_found')?></div>
         <?php }?>
         <?php echo form_open();?>    
              <?php if($configuracion->group_by){?>
             <div class="form-group row">
                <label class="col-md-3">
                Grupos
                <br />
                <small class="text-muted">Selecciona los grupos para cargar los registros.</small>
                </label>
                 <div class="col-md-9">
                         <label ><input type="checkbox" name="groups[]" value="all" ng-model="select_all" />TODOS LOS REGISTROS</label>
                         <hr />
                   
                    
                        <?php foreach($groups as $group){?>
                   
                        <label ><input ng-checked="select_all" type="checkbox" name="groups[]" value="<?=$group?>" /><?=$group?></label>
                        <?php }?>
                   
                  </div>
            </div>
            <hr />
             <?php }?>
                    
            <?php if($configuracion->auth){?>
             <div class="form-group row">
                <label class="col-md-3">
                Usuario
                <br />
                <small class="text-muted">Elige un usuario al cual va ser asignado los registros.</small>
                </label>
                 <div class="col-md-9">
                    <?=form_dropdown('user_id',$users,'','class="form-control"')?>
                 </div>
             </div>
             <?php }?>
             <?php if($configuracion->group_by){ ?>
             <div class="form-actions">
                <?php $this->load->view('admin/partials/buttons', array('buttons' => array('save', 'cancel'))) ?>
             </div>
             <?php }else{?>
                    <div class="alert alert-warning text-center"><?=lang('registros:no_group')?><br />
                    <a  href="<?=base_url('admin/registros/'.$id_evento)?>" class="btn btn-default">Salir</a>
                </div>
             <?php }?>
       <?php echo form_close(); ?>
    <?php }?>
    
</section>