
<div class="container">
        <header><h1><?=$evento->titulo?></h1></header>
        
        <div class="row">
            
            <?php if(!$this->input->get('facebook')) :?>
            <!-- Course Image -->
            <div class="col-md-2">
                <figure class="course-image">
                    <?php if($evento->portada){ ?>
                    <div class="image-wrapper"><a href="<?=base_url('files/large/'.$evento->portada)?>" class="fancybox"><img src="<?=base_url('files/cloud_thumb/'.$evento->portada.'/220/200')?>"/></a></div>
                    <?php }?>
                </figure>
            </div><!-- end Course Image -->
            <!--MAIN Content-->
            <div class="col-md-6">
                <div id="page-main">
                    <section id="course-detail">
                        <article class="course-detail">
                            <section id="course-header">
                                <header>
                                    <h2 class="course-date"><?=$days_week[format_date($evento->fecha,'l')]?>  <?=format_date($evento->fecha,'d')?> <?=format_date($evento->fecha,'M, Y')?></h2>
                                    <!--div class="course-category">Lugar:<a href="#">Marketing</a></div-->
                                </header>
                                <hr/>
                                 <div class="course-count-down" id="course-count-down">
                                    <figure class="course-start">El evento inicia en:</figure>
                                    <!-- /.course-start -->

                                    <div class="count-down-wrapper">
                                        <script type="text/javascript">var _date = '<?=$evento->date_countdown?>';</script>
                                    </div><!-- /.count-down-wrapper -->

                                </div><!-- /.course-count-down -->
                                <hr>
                                <figure>
                                    <span class="course-summary" id="course-length"><i class="fa fa-calendar-o"></i><?=format_date($evento->fecha,'d M y')?></span>
                                    <span class="course-summary" id="course-time-amount"><i class="fa fa-map-marker"></i><?=$evento->lugar?></span>
                                    <span class="course-summary" id="course-course-time"><i class="fa fa-clock-o"></i><?=$evento->horario?></span>
                                </figure>
                            </section><!-- /#course-header -->
                            <?php if($evento->descripcion){ ?>
                            <section id="course-info">
                                <!---header><h2>Descripción</h2></header-->
                                <?=nl2br($evento->descripcion)?>
                            </section><!-- /#course-info -->
                            <?php }?>

                            

                            

                            

                            

                            

                          

                            

                        </article><!-- /.course-detail -->
                    </section><!-- /.course-detail -->
                </div><!-- /#page-main -->
            </div><!-- /.col-md-8 -->
           
            <!--SIDEBAR Content-->
            <?php else:?>
            <div class="col-md-6 col-md-offset-3">
                <figure class="course-image">
                    <?php if($evento->portada){ ?>
                   <img src="<?=base_url('files/large/'.$evento->portada)?>"/>
                    <?php }?>
                </figure>
            </div><!-- end Course Image -->
            <?php endif;?>
            <div class="<?=!$this->input->get('facebook')?'col-md-4':'col-md-6 col-md-offset-3'?>">
                <div id="page-sidebar" class="sidebar">
                <?php if($configuracion->cerrado != 1){ ?>
                    <aside>
                    <?php echo form_open_multipart($this->uri->uri_string().'?'.http_build_query($_GET),!$_GET['participante'] && $configuracion->autocomplete==1?'method="GET" id="form" ':' id="form"',$_GET); ?>
                        <header><h2>Registro al evento</h2></header>
                        <?php if($this->method == 'edit'){ ?>
                            <input type="hidden" name="id" value="<?=$registro->id?$registro->id:0?>"/>
                        <?php }?>
                        {{ theme:partial name="notices" }} 
                        
                        <?php if($configuracion->autocomplete==1 && !$this->input->get('participante')){ ?>
                        <div class="form-group">
                            <label>Buscar por participante</label>
                            <?=form_input('participante',null,'class="form-control typeahead text-uppercase" id="text_auto"  ')?>
                            <input type="hidden" name="module_id" value=""  />
                         </div>
                         <p class="text-center">
                            
                            <button type="submit" class="btn">Continuar</button>
                         </p>
                        <?php }else{?>
                         <div class="form-group">
                            <label>Participante</label>
                            <?=form_input('participante',$registro->participante,'class="form-control text-uppercase" placeholder="Nombre del participante" '.($this->method == 'details'?'disabled':''))?>
                            <?=form_error('participante','<span class="text-danger">','</span>')?>
                            
                            <input type="hidden" name="evento" value="<?=$evento->id?>" />
                         </div>
                        <?php foreach($configuracion->campos as  $campo): ?>
                            <div class="form-group">
                            <?php if($campo->tipo!='hidden'){ ?>
                                <label><?=$campo->nombre?></label>
                            <?php }?>
                            <?php switch($campo->tipo){
                                    case 'text': ?>
                                    <?php echo  form_input($campo->slug,$registro->{$campo->slug},'placeholder="'.$campo->nombre.'" '.($this->method == 'details'?'disabled':''));?>
                                <?php break;
                                      case 'upload':
                                 ?>
                                   
                                    <div class="row">
                                        <div class="col-md-3">
                                         <?php if($registro->{$campo->slug}){ ?>
                                        
                                            <img  style="width: 100%;"  src="<?=base_url('files/cloud_thumb/'.$registro->{$campo->slug})?>" />
                                            <input type="hidden" name="<?=$campo->slug?>" value="<?=$registro->{$campo->slug}?>" />
                                        
                                         <?php }else{?>
                                            <?=Asset::img('logo_mini.jpg','No imagen',array('style'=>'width:100%;'));?>
                                         <?php }?> 
                                         </div>
                                         <?php if($this->method != 'details'){ ?>
                                        <div class="col-md-9">
                                            <?php echo form_upload($campo->slug); ?>
                                        </div>
                                        <?php }?>
                                    </div>
                                        
                                   
                                    
                                 
                                 <?php break;
                                      case 'select':
                                 ?>
                                    <?php echo  form_dropdown($campo->slug,option_select($campo->opciones,$campo->nombre),$registro->{$campo->slug},'class="selectize" placeholder="'.$campo->nombre.'" '.($this->method == 'details'?'disabled':''));?>
                                 <?php break;
                                    case 'hidden':
                                 ?>
                                    <input type="hidden" name="<?=$campo->slug?>" value="<?=$registro->{$campo->slug}?>"/>
                                 <?php break;?>
                            
                            <?php }?>
                            <?=form_error($campo->slug,'<span class="text-danger">','</span>')?>
                            </div>
                        <?php endforeach;?>
                        <?php if($configuracion->disciplinas && is_array($disciplinas)){ ?>
                         <div class="form-group">
                            <label>Disciplina</label>
                            <?=form_dropdown('id_disciplina',array(''=>'[ Elegir ]')+$disciplinas,$registro->id_disciplina,'class="form-control" '.($this->method == 'details'?'disabled':''))?>
                            <?=form_error('id_disciplina','<span class="text-danger">','</span>')?>
                         </div>
                         <?php }?>
                        <p class="text-center" id="form-actions">
                            
                            <?php if($this->method == 'details'){ ?>
                            
                            <a href="<?=base_url('registros/'.$evento->id)?>" class="btn btn-color-grey-light">Reiniciar</a>
                                <?php if($configuracion->acuse){ ?>
                                <a target="_blank" class="btn btn-color-grey-light" href="<?=base_url('registros/acuse/'.$configuracion->acuse.'/'.$registro->id.'?file_name=acuse')?>"><i class="fa fa-print"></i> Imprimir acuse</a>
                                <?php }?>
                            <?php }else{?>
                            <a href="<?=base_url('registros/'.$evento->id)?>" class="btn btn-color-grey-light">Reiniciar</a>
                            <button type="submit" class="btn confirm" title="¿Los datos introducidos son correctos?"><?=$this->method == 'create'?'Registrarme':'Guardar información'?></button>
                            <?php }?>
                        </p>
                        <?php }?>
                    <?php echo form_close();?>
                    </aside>
                <?php }?>
                
                </div><!-- /#sidebar -->
            </div><!-- /.col-md-4 -->
            <!-- end SIDEBAR Content-->
        </div><!-- /.row -->
    </div>
<script type="text/javascript">
var _messageAfterCount = 'El evento ha comenzado!';
if (typeof _date != 'undefined') { // run function only if _date is defined
    var Countdown = new Countdown({
        dateEnd: new Date(_date),
        msgAfter: _messageAfterCount,
        onEnd: function() {
            disableJoin(); // Run this function after count down is over
        }
    });
}

</script>