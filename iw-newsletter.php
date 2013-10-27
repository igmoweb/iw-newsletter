<?php
/*
Plugin Name: WP Madrid Newsletter
Plugin URI: http://wordpress.org/extend/plugins/hello-dolly/
Description: Plugin desarrollado para Meetup WP Madrid.
Author: Wordpress Madrid
Version: 1.0
Author URI: http://www.meetup.com/WordPress-Madrid/
*/

// CARACTERÍSTICAS:
// ----------------

// Widget:
// 		- input tipo text para el mail (HECHO)
//		- botón enviar (HECHO)
//		- Checkbox para aceptar condiciones
// Página de opciones para customizar:
//    	- Tiempo de envío
//		- Contenido
//		- Elegir etiqueta
//		- Número máximo de posts
//		- Texto del botón
//		- Asunto
//		- Remitente
//		- Exportar la tabla
// 		- Seleccionar una página para recibir el link de confirmación
//		- Otra para darse de baja
// Cuando se registre enviar al mail un link de confirmación
// Posibilidad de darse de baja del mail

// Enviar newsletter a usuarios registrados
// Ofrecer posibilidad de darse de baja desde el Admin Panel

// Borrar cada día mails no confirmados

// Guardar los mails TABLA
//		- email
//		- Fecha alta
//		- Confirmado

// Pantalla de desinstalación

define( 'IWNEWSLETTER_PLUGIN_URL', plugin_dir_url( __FILE__ ) );

// Incluimos el fichero que contiene la clase del widget
include_once( 'widget.php' );


/**
 * Crea la tabla necesaria para guardar los emails
 * de los usuarios subscritos
 * 
 * Se hace uso de la función dbDelta para ello
 * Más iformación en http://codex.wordpress.org/Creating_Tables_with_Plugins
 * Esta función se ejecuta sólo al activar el plugin
 */
function iw_create_table() {

	// Necesitamos esta vable global de WP para ejecutar sentencias en la BBDD
	global $wpdb;

	$sql = "CREATE TABLE IF NOT EXISTS `" . $wpdb -> prefix . "iw_newsletter` (
	  `email` varchar(255) NOT NULL,
	  `alta_date` datetime NOT NULL,
	  `confirmado` int(1) NOT NULL DEFAULT '0',
	  `id` bigint(20) NOT NULL AUTO_INCREMENT,
	  PRIMARY KEY (`id`)
	);";
	
	// Necesitamos este fichero para ejecutar dbDelta
	require_once(ABSPATH . 'wp-admin/includes/upgrade.php');

	dbDelta( $sql );

}
register_activation_hook( __FILE__, 'iw_create_table' );
// NOTA: Necesitamos una función que revise si el plugin se ha actualizado por si hay que actualizar la tabla


/**
 * Borra la tabla cuando se desactiva el plugin
 */
function iw_delete_table() {
	
	global $wpdb;

	$sql = "DROP TABLE `wp_iw_newsletter`";
	$wpdb -> query($sql);

}
register_deactivation_hook( __FILE__, 'iw_delete_table' );
// NOTA: No es muy buena práctica ya que el usuario podría querer desactivarlo sin perder todos los datos.
// Para ello hay que crear una pantalla de desinstalación o preguntar antes si se desea borrar los datos


?>
