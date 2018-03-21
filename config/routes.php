<?php  if (!defined('BASEPATH')) exit('No direct script access allowed');
//$route['registros/admin/configuracion(/:any)?']			= 'admin_configuracion$1';

$route['registros/crear/(:num)']			= 'registros/create/$1';
$route['registros/editar/(:num)']			= 'registros/edit/$1';

$route['registros/(:num)']			= 'registros/load/$1';

$route['registros/detalles/(:num)/(:num)']			= 'registros/details/$1/$2';

$route['registros/admin/configuracion/(:num)']			= 'admin_configuracion/load/$1';
$route['registros/admin/(:num)/(:num)']			= 'admin/load/$1/$2';
$route['registros/admin/(:num)']			= 'admin/load/$1';
?>