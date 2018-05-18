<section> 
    <div class="lead text-success">Reporte de registros</div>
    
    <!--pre><?php print_r($data);?></pre-->
    <?php if(!$tipo){?>
    <?php echo form_open(null,'method="get"');?>
        <div class="row">
            <div class="col-md-5">
                <div class="form-group">
                     <?=form_dropdown('tipo',array(''=>'[ Selecccionar disciplina ]','cultural'=>'Cultural','deportivo'=>'Deportivo','civico'=>'Cívico','academico'=>'Academico'),null,'class="form-control"')?>
                    <p class="help-block">Puedes buscar por tipo de disciplina y a continuacion pulsa el boton  <em>Buscar</em> </p>
                </div>
            </div>
            <div class="col-md-5">
                <div class="form-group">
                     <?=form_dropdown('centro',array(''=>' [ Todos los centros ] ')+$centros,false,'class="form-control" ')?>
                </div>
            </div>
            <div class="col-md-2">
                <button type="submit" class="btn btn-success">Buscar</button>
            </div>
        </div>
        
        
    <?php echo form_close()?>
    <?php }else{?>
    <div style="overflow-x: scroll;">
        <table class="table table-bordered" style="font-size: 10px;" >
            <thead>
                <tr>
                    <th rowspan="2">Plantel</th>

                    <?php if($tipo == 'deportivo'):?>
                 <?php foreach($head as $disciplina):?>
                        <th colspan="<?=count($disciplina['rama'])?>" width="10%" class="text-center"><?=strtoupper($disciplina['nombre'])?></th>
                    <?php endforeach;?> 
                <?php else: ?>  
                 <?php foreach($head as $disciplina):?>
                        <th colspan="<?=count($disciplina['params'])?>" width="10%" class="text-center"><?=strtoupper($disciplina['nombre'])?></th>
                    <?php endforeach;?> 
                  <?php endif; ?>
                   
                    <th rowspan="2" width="10%" class="text-center">Participantes</th>
                    <th rowspan="2" width="10%">Personas</th>
                    <th rowspan="2" width="10%">Asesores</th>
                </tr>
                
                <tr>
                <?php if($tipo == 'deportivo'):?>
                 <?php foreach($head as $disciplina):?>                      
                       <?php foreach($disciplina['rama'] as $rama):?>
                        <?php if($rama!=0):?>

                          <th class="text-center <?=$rama==1?'info':($rama==2?'danger':'')?>"><?=$rama==1?'VARONIL':($rama==2?'FEMENIL':' ')?></th>
                        <?php else: ?>
                         <?php foreach($disciplina['params'] as $param):?>
                        <th class="text-center <?=$param==1?'info':($param==2?'danger':'')?>"><?=$param==1?'HOMBRE':($param==2?'MUJER':'ND')?></th>
                      <?php endforeach;?>
                        <?php endif ?>
                     <?php endforeach;?>
                  <?php endforeach;?>  
                <?php else: ?>  
                 <?php foreach($head as $disciplina):?> 
                      <?php foreach($disciplina['params'] as $param):?>
                        <th class="text-center <?=$param==1?'info':($param==2?'danger':'')?>"><?=$param==1?'HOMBRE':($param==2?'MUJER':'ND')?></th>
                      <?php endforeach;?>
                  <?php endforeach;?>  
                  <?php endif; ?>                  
                </tr>               
            </thead>
            <tbody>
            <?php 
            
            $totales = array(
               'total'=>0
                
            );
            ?>
            <?php  foreach($data as $group=>$group_data): //$id_centro=>$centro?>
                <tr>
                    <?php $totales['total'] = 0;?>
                    <td><?=$group?></td> 
                    <?php foreach($head as $id_disciplina=>$disciplina):?>
                      
                        <?php if($tipo == 'deportivo'){;?>

                        <?php foreach($disciplina['rama'] as $param): ?>
                                <?php 
                                $totales['total'] +=  $data[$group]['disciplinas'][$id_disciplina][$param];?>
                                <?php $totales[$id_disciplina][$param] +=  $data[$group]['disciplinas'][$id_disciplina][$param];?>
                                 <?php if($param==0): ?>.
                                   <td class="text-center">                                    
                                    <?=$data[$group]['disciplinas'][$id_disciplina][$param]?'<a target="_blank" title="Descargar listado" href="'.base_url('admin/registros/download/'.$id_evento.'?group='.$group.'&disciplina='.$id_disciplina).'&rama='.$param.'">'.$data[$group]['disciplinas'][$id_disciplina][$param].'</a>':0?>
                                </td>  
                                <?php else: ?>
                                <td class="text-center">
                                    
                                    <?=$data[$group]['disciplinas'][$id_disciplina][$param]?'<a target="_blank" title="Descargar listado" href="'.base_url('admin/registros/download/'.$id_evento.'?group='.$group.'&disciplina='.$id_disciplina).'&rama='.$param.'">'.$data[$group]['disciplinas'][$id_disciplina][$param].'</a>':0?>
                                </td>
                                <?php endif ?>
                            <?php endforeach;?>

                        <?php }else{?>
                            <?php foreach($disciplina['params'] as $param): ?>
                                <?php $totales['total'] +=  $data[$group]['disciplinas'][$id_disciplina][$param];?>
                                <?php $totales[$id_disciplina][$param] +=  $data[$group]['disciplinas'][$id_disciplina][$param];?>
                                <td class="text-center">
                                    
                                    <?=$data[$group]['disciplinas'][$id_disciplina][$param]?'<a target="_blank" title="Descargar listado" href="'.base_url('admin/registros/download/'.$id_evento.'?group='.$group.'&disciplina='.$id_disciplina).'&param='.$param.'">'.$data[$group]['disciplinas'][$id_disciplina][$param].'</a>':0?>
                                </td>
                            <?php endforeach;?>
                        <?php }?>
                    <?php endforeach;?>  
                    
                    <td class="text-center"><?=$totales['total']>0?'<a class="'.($totales['total']<count($centro['personas'])?'label label-danger':'').'" title="Descargar listado" target="_blank" href="'.base_url('admin/registros/download/'.$id_evento.'?group='.$group.'&tipo='.$tipo).'">'.$totales['total'].'</a>':'0'?></td>   
                    <td class="text-center"><?='<a href="#" class="'.($totales['total']>$group_data['personas']?'label label-warning':'').'">'.count($group_data['personas']).'</a>'?></td>
                    <td class="text-center"><?=$group_data['asesores']>0?'<a target="_blank" href="'.base_url('admin/registros/asesores/'.$id_evento.'?centro='.$id_centro.'&tipo='.$tipo).'">'.$group_data['asesores'].'</a>':'0'?></td>
                </tr>
            <?php endforeach;?>
           
            </tbody>
             <tfoot>
                <tr style="font-weight: bold;">
                    <td class="text-right">TOTAL:</td>
                    <?php $total = 0;?>
                    <?php foreach($head as $id_disciplina=> $disciplina):?>
                        
                    <?php if($tipo == 'deportivo'){?>
                        <?php $total += $totales[$disciplina]['Varonil']+$totales[$disciplina]['Femenil'];  ?>
                        <td class="text-center"><?=$totales[$disciplina]['Varonil']>0?'<a target="_blank" title="Descargar listado" href="'.base_url('admin/registros/download/'.$id_evento.'?disciplina='.$disciplina.'&rama=1').'">'.$totales[$disciplina]['Varonil'].'</a>':'0'?></td>
                        <td class="text-center"><?=$totales[$disciplina]['Femenil']>0?'<a target="_blank" title="Descargar listado" href="'.base_url('admin/registros/download/'.$id_evento.'?disciplina='.$disciplina.'&rama=2').'">'.$totales[$disciplina]['Femenil'].'</a>':'0'?></strong></a></td>
                    <?php }else{?>
                          <?php foreach($disciplina['params'] as $param): ?>
                            <?php $total += $totales[$id_disciplina][$param];  ?>
                            <td class="text-center"><?=$totales[$id_disciplina][$param]>0?'<a target="_blank" title="Descargar listado" href="'.base_url('admin/registros/download/'.$id_evento.'?disciplina='.$id_disciplina).'&param='.$param.'">'.$totales[$id_disciplina][$param].'</a>':'0'?></td>
                          <?php endforeach;?>
                    <?php }?>
                    <?php endforeach;?>  
                    
                    <td class="text-center"><?=$total>0?'<a target="_blank" href="'.base_url('admin/registros/download/'.$id_evento.'?tipo='.$tipo).'">'.$total.'</a>':'0'?></td>
                </tr>
            </tfoot>
        </table>
        
    </div>
    <hr />
    <p><span class="label label-danger"><i></i></span>  Se recomienda revisar el listado esto sucede porque algunos datos estan incompletos.</p>
    <p><span class="label label-warning"><i></i></span> Hubieron personas que fueron asignados a mas  de una disciplina.</p>
    <div class="divider clearfix text-center">
            <a class="btn btn-default" href="<?=base_url('admin/registros/report/'.$id_evento)?>"><i class="fa fa-refresh"></i> Ir a inicio de la búsqueda</a>
        </div>
    <?php }?>
</section>