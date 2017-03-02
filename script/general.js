String.prototype.replaceAll = function(search, replace) {
	return this.toString().split(search).join(replace);
}

$(document).on('ready', function() {
	showScrollUpCommand();
	// Slideshow de banners
	$('#slider-banner').jcarousel({
		transitions: {
	        transforms3d: true,
	        easing:       'ease'
	    }
	});

	// Auto empezar slider
	jcarouselAutostart();
	function jcarouselAutostart() {
		// lo hago recursivo
		window.carouselTimer = setTimeout(function() {
			var $carouselVisible = $('#slider-banner').jcarousel('visible');
			var carouselIndex    = $carouselVisible.index();

			if(carouselIndex < $carouselVisible.parent().find('> li').length -1) {
				$('#slider-banner').jcarousel('scroll', carouselIndex +1, true, function() {
					jcarouselAutostart();
				});
			} else {
				$('#slider-banner').jcarousel('scroll', 0, true, function() {
					jcarouselAutostart();
				});
			}
		}, 3000);
	}

	// Check hash on load
	if(document.location.hash != '') {
		try {
			$('header .navegacion').find('a[href=' + document.location.hash + ']')

			$('header .navegacion').find('a.activo').removeClass('activo');
			$('header .navegacion').find('a[href=' + document.location.hash + ']').addClass('activo');

			setTimeout(function() {
				// reseteo el scroll top
				$('html, body').scrollTop(0);
				// Lo envio al hash que viene por url
				$('html, body').animate({
					scrollTop: $('#_' + document.location.hash.replace('#', '')).offset().top - (document.location.hash != '#home' ? 50 : 80)
				}, 500);
			}, 50);
		} catch(e) {
			if(document.location.hash.indexOf('#/') != -1) {
				processActionHash(document.location.hash);
			}
		}

	}

	// Check hash on link click
	$('body').on('click', 'a', function(e) {
		e.preventDefault();
		var $this = $(this);

		if($this.attr('href') == '#') return false;

		try {
			$('#_' + $this.attr('href').replace('#', ''));
		} catch(e) {
			if($this.attr('target')) {
				window.open($this.attr('href'), $this.attr('target'));
			} else {
				// Check for ajax type hash
				if($this.attr('href').indexOf('#/') != -1) {
					processActionHash($this.attr('href'), $('#' + $this.data('objetformid')));
				} else {
					document.location.href = $this.attr('href');
				}
			}
			return false;
		}

		$('header .navegacion').find('a.activo').removeClass('activo');
		$('header .navegacion').find('a[href=' + $this.attr('href') + ']').addClass('activo');

		$('html, body').animate({
			scrollTop: $('#_' + $this.attr('href').replace('#', '')).offset().top - ($this.attr('href') != '#home' ? 50 : 80)
		}, 500);

		document.location.hash = $this.attr('href');
		return false;
	});

	var selecteds = [];
	$('body').on('click', 'input[type=checkbox]', function() {
		// get all variables needed
		var thisvalue     = $(this).val(),
			thisvalueint  = parseInt(thisvalue),
			isthischecked = $(this).prop('checked'),
			$thisform     = $(this).parents('form'),
			$thisinputs   = $thisform.find('input[type=checkbox][data-parent][value!=' + thisvalueint + ']'),
			$formtables   = $thisform.parent().find('.tablas:visible'),
			$thistable    = $thisform.parent().find('#tablas-seg' + thisvalueint);

		$formtables.slideUp();

		if (isthischecked) {
			$thistable.slideDown();
			$formtables = $thisform.parent().find('.tablas:visible');
		}

		// resest selecteds
		$thisform.find('input[type=checkbox][value!=' + thisvalue + ']:checked').prop('checked', false);
			
		if($.inArray(thisvalueint, selecteds) == -1 && isthischecked) {
			selecteds.push(thisvalueint);
		}

		// Update selecteds
		$thisinputs.each(function() {
			var updateSelecteds = false;

			for(var i = 0; i < selecteds.length; i++) {
				if(isthischecked && parseInt($(this).data('parent')) == selecteds[i] && $.inArray($(this).val(), selecteds) == -1) {
					updateSelecteds = true;
				}
			}

			if(updateSelecteds) {
				selecteds.push(parseInt($(this).val()));
			}
		});

		// Check and disabled items with parents selected
		$thisinputs.each(function() {
			for(var i = 0; i < selecteds.length; i++) {
				if($(this).data('parent') == selecteds[i]) {
					$(this).prop({
						'checked': isthischecked,
						'disabled': isthischecked
					});
				}
			}
		});

		if(!isthischecked) {
			selecteds = $.grep(selecteds, function(selected, index) {
				return selected != thisvalueint;
			});
		}
	});
});

$(window).on('scroll', function(e) {
	showScrollUpCommand();
});

function showScrollUpCommand() {
	if($(window).scrollTop() > 50) {
		$('header').addClass('compressed');
		$('a.scroll-up').fadeIn();
	} else {
		$('header').removeClass('compressed');
		$('a.scroll-up').fadeOut();
	}
}

function processActionHash(hash, $form) {
	document.location.hash = hash;
	var requestData = hash.split('/');

	$('header .navegacion').find('a.activo').removeClass('activo');
	$('header .navegacion').find('a[href=#seguros]').addClass('activo');

	var seguros    = null;
	var cotizacion = null;
	var usuario    = null;

	if ($form && $form.length) {
		seguros = [];
		cotizacion = {};
		usuario = {};

		if ($form.attr('id') == 'asegurar') {
			if ($form.find('input:checked').length) {
				$form.find('input:checked').each(function() {
					seguros.push({
						id: $(this).val(),
						name: $(this).next().text()
					});
				});
			} else {
				// Show error
				$form.find('.required-message .required-fields-error').remove();
				$form.find('.required-message').append('<div class="required-fields-error">Debes seleccionar un tipo de cobertura para continuar.</div>');

				// Animate up to show the message
				$('html, body').animate({
					scrollTop: $('#_seguro').offset().top - 50
				}, 500);

				// Change hash to stay in the current location
				document.location.hash = '/' + requestData[1] + '/' + requestData[2] + '/asegurar';

				return;
			}
		}

		if ($form.attr('id') == 'cotizar') {
			var requiredfields = false;
			
			$form
			 .find('input, select, textarea')
			 .each(function(index, input) {
			 	if(($(input).data('customrequired') && $.trim($(input).val()) == '') || requiredfields) {
			 		requiredfields = true;
			 		return false;
			 	}
			 });

			// NEEDED, UNCOMMENT AFTER COMPLETE TODO-1
			// if (requiredfields) {
			// 	// Show error
			// 	$form.find('.required-message .required-fields-error').remove();
			// 	$form.find('.required-message').append('<div class="required-fields-error">Hay campos requeridos sin completar</div>');

			// 	// Animate up to show the message
			// 	$('html, body').animate({
			// 		scrollTop: $('#_seguro').offset().top - 50
			// 	}, 500);

			// 	// Change hash to stay in the current location
			// 	document.location.hash = '/' + requestData[1] + '/' + requestData[2] + '/cotizar';

			// 	return;
			// }

			// TODO-1
			if (checkFieldsPass($form)) {
				// TODO-1
				console.log('Do something here');

				// Change hash to `erve the current location
				document.location.hash = '/' + requestData[1] + '/' + requestData[2] + '/cotizar';
				return;
			}

			// TODO - Agarrar los atributos de cotización y crear el objeto
			// TODO - Agarrar los atributos de usuario y crear el objeto
			
			// $form.find('input:checked').each(function() {
			// 	seguros.push({
			// 		id: $(this).val(),
			// 		name: $(this).next().text()
			// 	});
			// });
		}

		if ($form.attr('id') == 'contratar') {
			// TODO - Agarrar los atributos de cotización y crear el objeto
			// TODO - Agarrar los atributos de usuario y crear el objeto
			
			// $form.find('input:checked').each(function() {
			// 	seguros.push({
			// 		id: $(this).val(),
			// 		name: $(this).next().text()
			// 	});
			// });
		}
	}

	var modelo    = (requestData[1] ? requestData[1] : null),
		categoria = (requestData[2] ? requestData[2] : null),
		accion    = (requestData[3] ? requestData[3] : null),
		data      = (localStorage.getItem(modelo + '::' + categoria) ? JSON.parse(localStorage.getItem(modelo + '::' + categoria)) : {
			'modelo': modelo,
			'categoria': categoria,
			'seguros': [],
			'cotizacion': {},
			'usuario': {}
		});

	data.seguros    = seguros ? seguros : data.seguros;
	data.cotizacion = cotizacion ? cotizacion : data.cotizacion;
	data.usuario    = usuario ? usuario : data.usuario;

	// Set storage for this model
	localStorage.setItem(modelo + '::' + categoria, JSON.stringify(data));

	switch(accion) {
		case 'asegurar':
		case 'cotizar':
		case 'contratar':
			// Set loading
			$('#_' + modelo).append('<div class="loading"><i class="fa fa-circle-o-notch fa-spin fa-2x"></i><br><span>Cargando <h3 class="titulo-seguro"><span>' + modelo + '</span><strong>' + categoria.replace('seguro', '') + '</strong></h3></span><br><span><a href="#/' + modelo + '/' + categoria + '/cancelar">Cancelar</a></span></div>');
			$('#_' + modelo).find('.loading').fadeIn();

			// Scroll to model box
			$('html, body').animate({
				scrollTop: $('#_' + modelo).offset().top - 50
			}, 500);

			// Save form request promise
			var form_html_req = getFormAction(accion, data);
				// On form request done ...
				form_html_req.done(function(response, status, request) {
					// On form request success
					if(status == 'success') {
						// Remove loading
						$('#_' + modelo).find('.loading').fadeOut(function() {
							$(this).remove();
						});
						// Hide categories
						$('#_' + modelo).find('.categorias').slideUp();
						// Show response form
						$('#_' + modelo).find('.formularios').html(response);
						$('#_' + modelo).find('.formularios').slideDown();
					}
				});
		break;
		case 'cancelar':
		case 'terminar':
			// Reset hash
			document.location.hash = '#seguro';

			// Remove loading
			$('#_' + modelo).find('.loading').remove();
			if(form_html_req) {
				form_html_req.abort();
			}
			// Show categories
			$('#_' + modelo).find('.categorias').slideDown();
			// Show response form
			$('#_' + modelo).find('.formularios').slideUp();
		break;
	}
}

// TODO-1
function checkFieldsPass($form) {
	var $form_inputs = $form.find('input, select, textarea');

	$form_inputs.each(function() {
		// TODO-1
	});

	return true;
}

function getFormAction(accion, data) {
	// Get this ajax event
	return $.ajax({
		url: '/lib/api.php',
		method: 'post',
		data: {
			accion:    accion,
			data:      data
		}
	});
}