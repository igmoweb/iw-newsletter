<?php

/**
 * Clase del widget. 
 * Controla todas las opciones del widget así como el HTML de salida
 */
class IW_News_Widget extends WP_Widget {

	/**
	 * Constructor de la clase
	 * Especifica el nombre, clase y descripción del widget
	 */
	public function __construct() {

		parent::__construct(
			'iw-newsletter-widget', // ID
			__( 'IW Newsletter', 'iw-newsletter' ), // Nombre
			array(
				'classname'		=>	'iw-widget-newsletter', // Clase CSS
				'description'	=>	__( 'Short description of the widget goes here.', 'iw-newsletter' ) // Descripción
			)
		);

		// Añadimos el Javascript necesatio
		add_action( 'wp_enqueue_scripts', array( $this, 'add_javascript' ) );

		// Enganchamos las acciones para ejecutar AJAX cuando alguien se inscriba
		add_action( 'wp_ajax_iw_newsletter_send_form', array( $this, 'add_new_user') );
		add_action( 'wp_ajax_nopriv_iw_newsletter_send_form', array( $this, 'add_new_user') );

	} // end __construct

	public function add_javascript() {
		// Para asegurarnos, encolaremos JS sólo cuando no estemos en el panel de administración
		if ( ! is_admin() )
			wp_enqueue_script( 'iw-newsletter-js', IWNEWSLETTER_PLUGIN_URL . 'iw-newsletter.js', array( 'jquery' ), '20130314' );
	}


	/**
	 * Saca por pantall el contenido del plugin en el Front-end
	 *
	 * @param	array	args		El array de los elementos del formulario ( ver función form() )
	 * @param	array	instance	La instancia actual del widget
	 */
	public function widget( $args, $instance ) {

		// Extraemos los argumentos y las variables de instancia en sus propias variables
		extract( $args, EXTR_SKIP );
		extract( $instance, EXTR_SKIP );

		// Definido por WP. Necesario para cada widget
		echo $before_widget;

		// Si hemos especificado un título, la estructura queda así
		if ( ! empty( $title ) )
			echo $before_title . $title . $after_title;
    	
    	// HTML del Widget
		?>
			<form method="post" id="iw-newsletter-form">
				<input name="iw-newsletter-mail" type="text" value="" />
				<input name="iw-newsletter-submit" type="submit" value="<?php echo $button_text; ?>"></input>
				<!-- Esta es la URL adonde van dirigidas TODAS las peticiones de AJAX de WordPress -->
				<input name="iw-newsletter-ajax-url" type="hidden" value="<?php echo admin_url('admin-ajax.php'); ?>"/>
			</form>
		<?php

		// Definido por WP. Necesario para cada widget
		echo $after_widget;

	} // end widget


	/**
	 * Añade un nuevo usuario a la lista de newsletter
	 * 
	 * Esta función se ejecuta cada vez que alguien se inscribe a la lista.
	 * Recibe los parámetros del formulario a través de la variable $_POST
	 */
	public function add_new_user() {

		$mail = $_POST['email'];		

		// Si el usuario ha introducido un mail válido lo insertamos
		if ( is_email( $mail ) ) {
			global $wpdb;

			// Aunque insertemos el mail a la lista, no estará confirmado hasta
			// que el usuario pinche en el enlace que le vamos a mandar
			$table = $wpdb -> prefix . 'iw_newsletter';
			$wpdb -> insert(
				$table,
				array(
					'email' => $mail,
					'alta_date' => time(),
					'confirmado' => 0
				),
				array(
					'%s',
					'%d',
					'%d'
				)
			);

			// Devolvemos 1 al insertar
			echo 1;

		}
		else {
			// Devolvemos 0 si el mail no era válido
			echo 0;
		}

		// Las funciones ejecutadas por AJAX tienen siempre que morir al final
		// Porque si no, devuelve un 0 adicional
		die();

	}


	/**
	 * Procesa las opciones del formulario del widget para
	 * ser guardadas en BBDD
	 *
	 * @param array	new_instance	Los nuevos valores introducidos
	 * @param array	old_instance	Valores previos ya guardados
	 * 
	 * @return array				Valores comprobados y sanitizados
	 */
	public function update( $new_instance, $old_instance ) {

		$instance = $old_instance;

		$instance['title'] = sanitize_text_field( $new_instance['title'] );
		$instance['button_text'] = sanitize_text_field( $new_instance['button_text'] ); 

		return $instance;

	} // end update

	/**
	 * Genera el formulario del widget en la zona de Widgets de administraciónm
	 *
	 * @param array instance	Array asociativo de los valores previamente guardados
	 */
	public function form( $instance ) {

		// Definimos los valores por defecto
    	$default = array(
    		'title' => __( '', 'iw-newsletter' ),
    		'button_text' => __( 'Send', 'iw-newsletter' )
    	); 

    	// Si no existen valores guardados, cogeremos los que están guardados por defecto
		$instance = wp_parse_args(
			(array)$instance,
			$default		
		);


		// HTML del formulario
		?>
			<p>
				<label><?php _e( 'Title:'); ?></label><input type="text" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" value="<?php echo esc_attr( $instance['title'] ); ?>"/>
			</p>
			<p>
				<label><?php _e( 'Button text:'); ?></label><input type="text" id="<?php echo $this->get_field_id( 'button_text' ); ?>" name="<?php echo $this->get_field_name( 'button_text' ); ?>" value="<?php echo esc_attr( $instance['button_text'] );  ?>"/>
			</p>
		<?php

	} // end form

} // end class


/**
 * Registra el widget en el panel de administración Widgets
 * Asociado al action widgets_init
 */
function iw_register_widget() {
	register_widget( 'IW_News_Widget' );
}
add_action( 'widgets_init', 'iw_register_widget' ); 

?>