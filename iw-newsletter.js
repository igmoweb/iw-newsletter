jQuery(document).ready(function($) {
	// Usuario plsa botón enviar interceptamos el envío
	// Es decir, el formulario que envuelve ese botón se envía y
	// lo interceptamos
	$( '#iw-newsletter-form' ).submit(function(e) {

		// Con esto no dejamos que el formulario se envíe
		// (No se ejecuta la acción por defecto del formulario)
		e.preventDefault();
		
		// Estos son los datos que vamos a enviar con el formulario
		var mi_data = {
			action: 'iw_newsletter_send_form', 					// Acción necesaria para ejecutar AJAX en WordPress
			email: $('input[name="iw-newsletter-mail"]').val() 	// Email del usuario
		}

		// Dentro del formulario hemos guardado la URL
		// adonde van dirigidas TODAS las peticiones AJAX de WordPress
		var mi_url = $('input[name="iw-newsletter-ajax-url"]').val();

		// Iniciamos la petición mediante Ajax
		$.ajax({
			url: mi_url,
			data: mi_data,
			type: 'POST',
			success: function(content) { // Función ejecutada cuando la función AJAX devuelve algo
				if ( content == '1')
					alert('GRACIAS');
			}
		})
	});

});