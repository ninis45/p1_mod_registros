<div class="modal-header">
      <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
      <h4 class="modal-title" id="myModalLabel">Asignar asesor</h4>
</div>
<?php echo form_open('','id="form-asesor"')?>
<div class="modal-body">
    <div id="inner-message"></div>
    
    <div class="form-group">
        <label>Empleado</label>
        <?=form_dropdown('asesor',array(''=>'[ Elegir] ')+$empleados, $asesor->asesor,'class="form-control" '.($disciplina->activo?'':'disabled'));?>
    </div>
    <div class="form-group">
        <label>Observaciones</label>
        <textarea name="observaciones" rows="2" class="form-control" <?=($disciplina->activo?'':'disabled')?>><?=$asesor->observaciones?></textarea>
    </div>
</div>
<div class="modal-footer">
      <button type="button" class="btn btn-color-grey-light" data-dismiss="modal">Cerrar</button>
      <?php if($disciplina->activo){ ?>
      <button type="submit" class="btn">Aceptar</button>
      <?php }?>
</div>
<?php echo form_close();?>
<script type="text/javascript">
    $(document).ready(function(){
         $('#form-asesor').on('submit',function(){
            
            var form = $(this),
                 url = form.attr('action');
                 
            $.post(url,$(form).serialize(),function(response){
                 var data = response.data;
                 if(!response.status)
                 {
                    $('#inner-message').html(response.message);
                 }else
                 {
                    $('#myModal').modal('hide');
                    
                    $('#href-disciplina-'+data.id_disciplina).html(data.asesor);
                 }
                
            });
            
            
            return false;
           
        });
    });

</script>