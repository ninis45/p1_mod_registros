<h4>Eventos</h4>
                    <ul class="modal_select">
                        <?php foreach($eventos as $evento):?>
                        <li>
                            <a href="<?=base_url('admin/registros/import/'.$id_evento.'/'.$evento->id)?>">
                                <?=$evento->titulo?>
                                <?php if($evento->id==$id_evento){ ?>
                                (Carga directa desde base de datos)
                                <?php }?>
                            </a>
                        </li>
                        <?php endforeach;?>
                    </ul>
 <h4>Base de datos</h4>               