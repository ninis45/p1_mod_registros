<?php defined('BASEPATH') OR exit('No direct script access allowed');

// labels

$lang['registros:create'] 					= 'Nuevo registro';
$lang['registros:title'] 					= 'Registro para eventos';
$lang['registros:help'] 					= 'Bienvenido al panel para registro a eventos.';
$lang['registros:export'] 					= 'Exportar';
$lang['registros:import'] 					= 'Importar registros';
$lang['registros:report'] 					= 'Resumen';
$lang['registros:download'] 				= 'Descargar XLSX';
$lang['registros:error_duplicate'] 					= 'El/La %s  ya se encuentra registrado en este evento';
$lang['registros:config_error'] 		    = 'Error al exportar los datos, primero configure los datos.';

$lang['registros:delete_success'] 		    = 'Los participantes "%s" han sido eliminados satisfactoriamente';

$lang['registros:import_help'] 					= 'Selecciona los registros del lado izquierdo de este panel';

$lang['registros:import_success'] 			    = 'La importación se llevo a cabo satisfactoriamente con %s registros nuevos.';

$lang['registros:duplicate'] 					= 'El participante con el/la %s %s ya se encuentra registrado en este evento';
$lang['registros:add_success'] 					= 'El participante ha sido agregado satisfactoriamente';
$lang['registros:edit_success'] 					= 'El participante ha sido modificado satisfactoriamente';

$lang['registros:add_thanks'] 					= 'Gracias por registrarte en este evento.';
$lang['registros:add_thanks_email'] 					= 'Gracias por registrarte en este evento, tambien te hemos enviado un mensaje al correo %s con el acuse del registro.<script>window.open("'.base_url().'registros/acuse/%s/%s?file_name=acuse");</script>';
$lang['registros:error_input'] 					= 'Error al cargar el campo, se recomienda verificar la configuración';
$lang['registros:table_not_found']    = 'Al parecer no tienes asignado una tabla para extraer los datos.';

$lang['registros:edit_warning'] 					= '<div class="alert alert-warning">La disciplina ya no se encuentra disponible, solamente la podras cambiar por una que este activa.</div>';

$lang['registros:banned_disciplina'] 	= 'La disciplina "%s" en el que se desea registrar no es posible porque ya esta inscrito en uno del ramo deportivo.';
$lang['registros:duplicate_disciplina'] = 'El participante ya se encuentra registrado en esta disciplina';

$lang['registros:edit'] 					= 'Modificar registro ';

$lang['registros:create'] 					= 'Nuevo  registro ';
$lang['registros:details'] 					= 'Detalles del  registro ';


$lang['registros:save_error'] 					= 'Error al guardar el registro';
$lang['registros:limit_max'] 					= 'Se ha rebasado el limite permitido de inscritos para esta disciplina.';

$lang['registros:send_email_success'] 					= 'Se han enviado mensaje a %s correos electrónicos de  %s';
$lang['registros:inactive'] 					= 'El formulario para el registro ha sido cerrado por el adminisrador.';
$lang['registros:empty_free'] = 'Si no aparece tu nombre, regístralo al  pulsando la tecla <em>Enter</em> o haciendo clic en el botón <em>Continuar</em>.';
$lang['registros:empty_forced'] = 'No existe registro con este nombre.';
$lang['registros:not_found'] 				= 'No existe el registro del participante en este evento.';

$lang['registros:no_group'] 				= 'Para llevar a cabo esta operación es necesario asignar y especificar el grupo.';
//$lang['registros:inactive'] 				= 'El participante no es posible modificar sus datos por encontrarse inactivo.';
$lang['configuracion:title'] 					= 'Configuracion';
$lang['configuracion:title_create'] 					= 'Configuracion para el evento "%s"';
$lang['configuracion:save_success'] 			= 'Configuracion guardada correctamente';
$lang['configuracion:save_error'] 			    = 'Error al tratar de guardar la configuración';
?>