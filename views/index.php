<style type="text/css">
    .table-registros i{
        opacity:1!important;
        font-size: 16px;
    }
    .btn-small{
        padding: 5px 6px!important;
    }
</style>
<div class="container">
        <header><h1><?=$evento->titulo?></h1></header>
        
        <div class="row">
            
            <?php if($configuracion->template_columns=='2-6-4') :?>
            <!-- Course Image -->
            <div class="col-md-2">
                <figure class="course-image">
                    <?php if($evento->portada){ ?>
                    <div class="image-wrapper"><a href="<?=base_url('files/large/'.$evento->portada)?>" class="fancybox"><img src="<?=base_url('files/cloud_thumb/'.$evento->portada.'/220/200')?>"/></a></div>
                    <?php }?>
                </figure>
            </div><!-- end Course Image -->
            <?php endif; ?>
            <!--MAIN Content-->
            <div class="col-md-6">
                <div id="page-main">
                    <section id="course-detail">
                        <article class="course-detail">
                            <section id="course-header">
                                <header>
                                    <h2 class="course-date">
                                    <?=$days_week[format_date($evento->fecha,'l')]?>  
                                    <?=format_date($evento->fecha,'d')?> 
                                    <?=format_date($evento->fecha,'M, Y')?>
                                    </h2>
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
                                
                                <?=nl2br($evento->descripcion)?>
                            </section><!-- /#course-info -->
                            <?php }?>

                            

                            

                            

                            

                            

                          

                            

                        </article><!-- /.course-detail -->
                    </section><!-- /.course-detail -->
                </div><!-- /#page-main -->
            </div><!-- /.col-md-8 -->
           
            <!--SIDEBAR Content-->
           
            <div class="<?=$configuracion->template_columns=='2-6-4'?'col-md-4':'col-md-6'?>">
                <div id="page-sidebar" class="sidebar">
                    <?php if($configuracion->descripcion){ ?>
                                    <div class="alert alert-info">
                                    <?=$configuracion->descripcion?>
                                    <hr />
                                    
                                    Teléfono de oficina:  (981) 81 100 49 y (981) 81 608 11
                                    </div>
                    <?php }?>
                    {{ theme:partial name="notices" }} 
                    <ul class="nav nav-tabs" id="tabs">
                        <li class="active"><a href="#tab-registro" data-toggle="tab">Registro</a></li>
                        <?php if(isset($inscritos)){ ?>
                        <li><a href="#tab-list" data-toggle="tab">Inscritos</a></li>
                        <?php }?>
                        <?php if($configuracion->disciplinas && $configuracion->auth){ ?>
                        <li><a href="#tab-disciplinas" data-toggle="tab">Disciplinas</a></li>
                        <?php }?>
                        
                    </ul>
                    <div class="tab-content my-account-tab-content">
                        <div class="tab-pane active" id="tab-registro">
                            <?php echo form_open_multipart($this->uri->uri_string().'?'.http_build_query($_GET),!$_GET['participante'] && $configuracion->autocomplete==1?'method="GET" id="form" ':' id="form" method="GET"',$_GET); ?>
                            <section id="my-profile">
                                
                                <div class="form-group">
                                    <label>Buscar por participante</label><br />
                                    <?=form_input('participante',null,'class="form-control typeahead text-uppercase" id="text_auto"  ')?>
                                    <input type="hidden" name="module_id" value=""  />
                                 </div>
                                 <p class="text-center">
                                    
                                    <button type="submit" class="btn">Continuar</button>
                                 </p>
                            </section><!-- /#my-profile -->
                            <?php echo form_close();?>
                        </div><!-- /tab-pane -->
                        <?php if(isset($inscritos)){ ?>
                        <div class="tab-pane" id="tab-list">
                            <section id="course-list">
                                <?php if(count($inscritos)>0){ ?>
                                <!--input type="text" class="form-control" placeholder="Buscar participante" />
                                <hr /-->
                                <p class="text-right">TOTAL REGISTROS: <?=count($inscritos)?></p>
                                <table class="table table-hover table-responsive course-list-table tablesorter table-registros">
                                    <thead>
                                        <tr>
                                            <th>Participante</th>
                                            <th width="30%">Acciones</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                    <?php foreach($inscritos as $registro){ ?>
                                        <tr>
                                            <th>
                                                <a title="Ver detalles del participante" href="<?=base_url('registros/detalles/'.$registro->id_evento.'/'.$registro->id)?>"><?=$registro->participante?></a>
                                                <br />
                                                <span class="text-muted"><?=$registro->disciplina?></span>
                                                
                                                </th>
                                            <th class="text-center">
                                                <a class="btn btn-small btn-color-grey-light confirm" href="<?=base_url('registros/remove/'.$registro->id)?>"><i class="fa fa-trash"></i></a>
                                                <a class="btn btn-small" href="<?=base_url('registros/editar/'.$registro->id_evento.'?participante='.$registro->participante.'&module_id='.$registro->module_id)?>"><i class="fa fa-pencil"></i></a>
                                            </th>
                                        </tr>
                                    <?php }?>
                                    </tbody>
                                </table>
                                
                                <!--div class="center">
                                    <ul class="pagination">
                                        <li class="active"><a href="#">1</a></li>
                                        <li><a href="#">2</a></li>
                                        <li><a href="#">3</a></li>
                                    </ul>
                                </div-->
                                <?php }else{?>
                                <div class="alert alert-info text-center">
                                    <?=lang('global:not_found')?>
                                </div>
                                <?php }?>
                            </section><!-- /#course-list -->
                        </div><!-- /.tab-pane -->
                       <?php }?>
                       <?php if($configuracion->disciplinas && $configuracion->auth){ ?>
                       
                       
                       <div class="tab-pane" id="tab-disciplinas">
                            <table class="table table-hover table-responsive course-list-table tablesorter">
                                    <thead>
                                        <tr>
                                            <th>Disciplina</th>
                                            <th width="10%">Cant.</th>
                                    
                                            <th width="30%">Descargar</th>
                                        </tr>
                                    <tbody>
                                    <?php foreach($disciplinas as $id=>$disciplina):?>
                                        <tr>
                                            <th>
                                                <?=$disciplina['nombre']?><br />
                                                <em>Asesor: <a id="href-disciplina-<?=$id?>" href="<?=base_url('registros/asesores/'.$id)?>" data-target="#myModal" data-toggle="modal" class="btn-asesor"><?=$disciplina['asesor']?$disciplina['asesor']->asesor:'Sin asignar'?></a></em>
                                            </th>
                                            <th><?=$disciplina['cantidad']?></th>
                                            <th>
                                                <?php if(!$disciplina['activo']){ ?>
                                                 <a title="Descargar cédula" target="_blank" title="Descargar cédula" href="<?=base_url('registros/download/cedula/'.$id)?>">Cédula</a> |
                                                 <a title="Decarcar lista de participantes" target="_blank" title="Descargar cédula" href="<?=base_url('registros/download/lista/'.$id)?>">Lista</a>
                                                <?php }?>
                                            </th>
                                        </tr>
                                    <?php endforeach;?>
                                    </tbody>
                            </table>
                       </div>
                       <?php }?>
                    </div>
                    
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
            //disableJoin(); // Run this function after count down is over
        }
    });
}

</script>