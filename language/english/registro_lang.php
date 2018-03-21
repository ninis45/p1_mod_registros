<?php defined('BASEPATH') OR exit('No direct script access allowed');

// labels

$lang['registros:create'] 					= 'Nuevo registro';
$lang['registros:title'] 					= 'Registros';
$lang['registros:export'] 					= 'Exportar';
$lang['registros:import'] 					= 'Importar';
$lang['registros:report'] 					= 'Resumen';

$lang['registros:error_duplicate'] 					= 'El participante ya se encuentra registrado en este evento.';
$lang['registros:config_error'] 		    = 'Error al exportar los datos, primero configure los datos.';

$lang['registros:delete_success'] 		    = 'Los participantes "%s" han sido eliminados satisfactoriamente';

$lang['registros:import_help'] 					= 'Selecciona los registros del lado izquierdo de este panel';
$lang['registros:duplicate'] 					= 'El participante ya se encuentra registrado en este evento';
$lang['registros:add_success'] 					= 'El participante ha sido agregado satisfactoriamente';
$lang['registros:edit_success'] 					= 'El participante ha sido modificado satisfactoriamente';

$lang['registros:add_thanks'] 					= 'Gracias por registrarte en este evento.';
$lang['registros:add_thanks_email'] 					= 'Gracias por registrarte en este evento, tambien te hemos enviado un mensaje al correo %s con el acuse del registro.<script>window.open("'.base_url().'registros/acuse/boleto-egresado/%s?file_name=acuse");</script>';
$lang['registros:error_input'] 					= 'Error al cargar el campo, se recomienda verificar la configuración';

$lang['registros:edit_warning'] 					= '<div class="alert alert-warning">La disciplina ya no se encuentra disponible, solamente la podras cambiar por una que este activa.</div>';

$lang['registros:banned_disciplina'] 	= 'La disciplina "%s" en el que se desea registrar no es posible porque ya esta inscrito en uno del ramo deportivo.';
$lang['registros:duplicate_disciplina'] = 'El participante ya se encuentra registrado en esta disciplina';

$lang['registros:edit'] 					= 'Modificar registro ';
//$lang['marcas:not_found'] 					= 'Aún no hay categorías en este apartado';
$lang['modelo:save_success'] 					= 'El modelo %s ha sido guardada satisfactoriamente';


$lang['configuracion:title'] 					= 'Configuracion';
$lang['configuracion:save_success'] 			= 'Configuracion guardada correctamente';
$lang['configuracion:save_error'] 			    = 'Error al tratar de guardar la configuración';
?>