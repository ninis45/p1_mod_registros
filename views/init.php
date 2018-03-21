<div class="container">
    <header><h1><?=lang('registros:title')?></h1></header>
        <article>
                          
                            <div class="row">
                                <div class="col-md-6" style="min-height: 215px;">
                                    <div class="events small">
                                       
                                        <?php $altern = false; ?>
                                        <?php foreach($list as  $evento): ?>
                                        <article class="event <?=$altern?'nearest':'nearest-second'?>">
                                            <figure class="date">
                                                <div class="month"><?=format_date($evento->fecha,'M')?></div>
                                                <div class="day"><?=format_date($evento->fecha,'d')?></div>
                                            </figure>
                                            <aside>
                                                <header>
                                                    <a href="<?=base_url('registros/'.$evento->id_evento)?>"><?=$evento->titulo?></a>
                                                </header>
                                                <div class="additional-info"><?=$evento->lugar?$evento->lugar:'No disponible'?></div>
                                            </aside>
                                        </article><!-- /article -->
                                        <?php $altern = !$altern;?>
                                        <?php endforeach;?>
                                    </div>
                                </div>
                            </div>
                        </article>
</div>