<div class="row">
    <?php foreach($eventos as $evento){?>
        <div class="col-lg-4">
            <div class="card bg-primary">
                <div class="card-content">
                                <span class="card-title"><?=$evento->titulo?></span>
                                <p></p>
                                </div>
                                <div class="card-action">
                                    <a href="<?=base_url('admin/registros/'.$evento->id)?>" class="btn btn-default color-primary"><span>Administrar</span></a>
                                    
                                </div>
            </div> 
        </div>


    <?php }?>
</div>