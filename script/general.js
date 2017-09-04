String.prototype.replaceAll = function(search, replace) {
	return this.toString().split(search).join(replace);
}

$(document).on('ready', function() {
	showScrollUpCommand();
	updateSliderResolution();

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
			$('header .navegacion, .nav-menu').find('a[href=' + document.location.hash + ']');
			$('header .navegacion, .nav-menu').find('a.activo').removeClass('activo');
			$('header .navegacion, .nav-menu').find('a[href=' + document.location.hash + ']').addClass('activo');

			setTimeout(function() {
				// Reset cache	
				localStorage.clear();
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
	$('body').on('click', 'a[href], area[href]', function(e) {
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

		$('header .navegacion, .nav-menu').find('a.activo').removeClass('activo');
		$('header .navegacion, .nav-menu').find('a[href=' + $this.attr('href') + ']').addClass('activo');

		$('#_seguro').find('.categorias').slideDown();
		$('#_seguro').find('.formularios').slideUp();

		$('html, body').animate({
			scrollTop: $('#_' + $this.attr('href').replace('#', '')).offset().top - ($this.attr('href') != '#home' ? 50 : 80)
		}, 500);

		document.location.hash = $this.attr('href');
		return false;
	});

	$('body').on('click', 'a[data-target]', function (e) {
		e.preventDefault();
		var $target = $('#' + $(this).data('target'));

		if ($target.is(':visible')) {
			$target.addClass('hidden');
		} else {
			$target.removeClass('hidden');
		}
	});

	$('body').on('click', '.nav-menu a', function () {
		$(this)
		 .parents('.nav-menu')
		 .addClass('hidden');
	});

	var selected = null;
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
		}

		// resest selected
		selected = null;
		$thisform.find('input[type=checkbox][value!=' + thisvalue + ']:checked').prop({
			'checked': false,
			'disabled': false
		});

		// Update selecteds
		selected = isthischecked ? thisvalueint : null;

		// Check and disabled items with parents selected
		$thisinputs.each(function() {
			if($(this).data('parent') == thisvalueint) {
				$(this).prop({
					'checked': isthischecked,
					'disabled': isthischecked
				});
			}
		});

		if(!isthischecked) {
			selected = null;
		}
	});

	$('body').on('blur', '#cotizar input, #cotizar select', function() {
		var $form          = $('#cotizar');
		var $form_product  = $form.data('product');
		var precio_seguro  = parseFloat($('#precio_seguro_original').text());
		var precio_seguro_ = 0;

		$form
		 .find('input, select')
		 .each(function () {
		 	if ($(this).data('customadd') && $(this).data('customadd') != '') {
		 		var current_val  = parseFloat($(this).val());
		 		var advanced     = $(this).data('customadvanced');
		 		var precio_add   = $(this).data('customadd');
		 		var add_in_per   = $(this).data('customaddin');
		 		var add_to_price = 0;
		 		var advanced_pce;

		 		if (advanced && precio_add.length) {
		 			advanced_pce = JSON.stringify(precio_add).split('[').join('').split(']').join('');
					advanced_pce = advanced_pce.split('},');
					advanced_pce = advanced_pce.map(function (curr) {
					  var result = [];
					      result = curr.split('{').join('').split('}').join('');
					      result = result.split('"valor_a_comparar":').join('');
					      result = result.split('"variable.value",').join('current_val');
					      result = result.split('"seguro.valor",').join('precio_seguro');
					      result = result.split('"operador":"').join(' ');
					      result = result.split('","referencia":"').join(' ');
					      result = result.split('","resultado":"').join(' ? ');
					      result = result.split('"').join('');
					      result = '(' + result + ' : ';

					  return result;
					});

					for (var exp in advanced_pce) {
					  if (exp == 0) advanced_pce[advanced_pce.length -1] += 0;
					  advanced_pce[advanced_pce.length -1] += ')';
					}

					advanced_pce = advanced_pce.join('');
					advanced_pce = advanced_pce.split('##seguro.valor##').join(precio_seguro);
					advanced_pce = advanced_pce.split('##atributo.valor##').join(current_val || 0);

					eval('precio_add = ' + advanced_pce);
		 		}

		 		add_to_price = advanced ? precio_add : (add_in_per ? (!isNaN(current_val) ? current_val : precio_seguro) * (precio_add/100) : precio_add);
		 		precio_seguro_ += add_to_price;
		 	}

		 	if ($(this).data('customcheck') == 'telefono') {
		 		$(this).val(function () {
		 			return $(this).val().split(' ').join('');
		 		});
		 	}
		 });

		var precio_final = precio_seguro + precio_seguro_;

		if ($form_product == 'segurodenotebook') {
		 var imp_op = precio_final * 0.12;
		 var imp_iva = (precio_final + imp_op) * 0.22;
		 
		 precio_final += imp_op + imp_iva;
		}

		$('#precio_seguro').data('preciooriginal', precio_seguro + precio_seguro_);
		$('#precio_seguro').text(precio_final.toFixed(2));
	});
});

$(window).on('scroll', function(e) {
	showScrollUpCommand();
});

$(window).on('resize', function(e) {
	updateSliderResolution();
});

function showScrollUpCommand() {
	if($(window).scrollTop() > 50) {
		$('header').addClass('compressed');
		$('#menu-mobile').attr('style', 'top: 60px !important');
		$('a.scroll-up').fadeIn();
	} else {
		$('#menu-mobile').attr('style', 'top: 85px !important');
		$('header').removeClass('compressed');
		$('a.scroll-up').fadeOut();
	}
}

function updateSliderResolution() {
	if ($(window).innerWidth() < 1100) {
		$('.slider-banner ul > li, .slider-banner ul > li > img').css('width', $(window).innerWidth() + 'px');
	}
}

function processActionHash(hash, $form) {
	document.location.hash = hash;
	var requestData = hash.split('/');
	var $form_product = $form ? $form.data('product') : null;

	$('header .navegacion, .nav-menu').find('a.activo').removeClass('activo');
	$('header .navegacion, .nav-menu').find('a[href=#seguros]').addClass('activo');

	var seguro     = null;
	var usuario    = null;
	var cotizacion = null;
	var poliza     = null;

	if ($form && $form.length) {
		if ($form.attr('id') == 'asegurar') {
			seguro = {};

			if ($form.find('input:checked').length) {
				$form.find('.required-message .required-fields-error').remove();
				$form.find('input:checked:not(:disabled)').each(function() {
					seguro = {
						id: $(this).val(),
						nombre: $(this).next().text(),
						precio: $(this).data('price'),
						moneda: $(this).data('currency')
					};
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
			usuario    = {};
			cotizacion = {};
			poliza     = {};
			var status = checkFieldsPass($form);

			if (!status.error) {
				$form.find('.required-message .required-fields-error').remove();

				var precio_seguro = $form_product == 'segurodenotebook' ? parseFloat($('#precio_seguro').data('preciooriginal')) : parseFloat($('#precio_seguro').text());
				var precio_seguro_ = 0;

				// usuario
				$form
				 .find('[data-custommodel=usuario]')
				 .each(function () {
				 	usuario[$(this).attr('id')] = $(this).val();
				 });

				$form
				 .find('[data-custommodel=cotizacion]')
				 .each(function () {
				 	cotizacion[$(this).attr('id')] = $(this).val();
				 });

				$form
				 .find('[data-custommodel=poliza]')
				 .each(function () {
				 	poliza[$(this).attr('id')] = $(this).val();
				 });
			} else {
				// Show error
				$form.find('.required-message .required-fields-error').remove();
				$form.find('.required-message').append('<div class="required-fields-error">' + status.errors.join('') + '</div>');

				// Animate up to show the message
				$('html, body').animate({
					scrollTop: $('#_seguro').offset().top - 50
				}, 500);

				// Change hash to stay in the current location
				document.location.hash = '/' + requestData[1] + '/' + requestData[2] + '/cotizar';
				return;
			}
		}
	}

	var modelo    = (requestData[1] ? requestData[1] : null),
		categoria = (requestData[2] ? requestData[2] : null),
		accion    = (requestData[3] ? requestData[3] : null),
		data      = (localStorage.getItem(modelo + '::' + categoria) ? JSON.parse(localStorage.getItem(modelo + '::' + categoria)) : {
			'modelo': modelo,
			'categoria': categoria,
			'seguro': {},
			'usuario': {},
			'cotizacion': {},
			'poliza': {}
		});

	data.seguro                = seguro ? seguro : data.seguro;
	data.usuario               = usuario ? usuario : data.usuario;
	data.cotizacion            = cotizacion ? cotizacion : data.cotizacion;
	data.poliza                = poliza ? poliza : data.poliza;

	data.seguro.producto       = $form_product;
	data.seguro.precio         = !isNaN(parseFloat($('#precio_seguro').data('preciooriginal'))) ? parseFloat($('#precio_seguro').data('preciooriginal')) : data.seguro.precio;
	data.seguro.precio_imp_inc = !isNaN(parseFloat($('#precio_seguro').text())) ? parseFloat($('#precio_seguro').text()) : data.seguro.precio_imp_inc;

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
		default:
			// Reset cache	
			localStorage.clear();
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

function checkFieldsPass($form) {
	var $form_inputs = $form.find('input, select, textarea');
	var status = {
		ok: true,
		error: false,
		errors: []
	}

	$form_inputs.each(function() {
		var $this = $(this);
		
		// Check if required first
		if (($this.data('customrequired') && $.trim($this.val()) == '')
		    || $this.data('customrequired') && $this.attr('type') == 'checkbox' && !$this.prop('checked')) {
			status.ok = false;
			status.error = true;
			if (!status.errors.length) {
				status.errors.push('Hay campos con errores:');
				status.errors.push('<ul>');
			}
			status.errors.push('<li>El campo <strong>"' + $this.data('realname') + '"</strong> es requerido</li>');
		}

		// Check the type here
		if ($this.data('customtype')) {
			status = checkType($this.data('realname'), $this.data('customtype'), $this.data('custommin'), $this.data('custommax'), $this.val(), status);
		}

		// Check the customcheck here
		if ($this.data('customcheck') || $this.data('customcheck') != 'none') {
			status = checkValidation($this.data('realname'), $this.data('customcheck'), $this.val(), status);
		}

	});

	if (status.errors.length) {
		status.errors.push('</ul>');
	}

	return status;
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

function checkType(fieldname, type, min, max, value, status) {
	switch (type) {
		case 'text':
			var text_re = /^[a-zA-Z\.]*$/;
			if (!text_re.test(value)) {
				status.ok = false;
				status.error = true;
				if (!status.errors.length) {
					status.errors.push('Hay campos con errores:');
					status.errors.push('<ul>');
				}
				status.errors.push('<li>El campo <strong>"' + fieldname + '"</strong> debe ser sólo alfabético</li>');
			}
		break;
		case 'numero':
		case 'moneda-peso':
		case 'moneda-dolar':
			var number_re = /^[0-9]*\,?[0,9]{0,2}$/;
			if (!number_re.test(value)) {
				status.ok = false;
				status.error = true;
				if (!status.errors.length) {
					status.errors.push('Hay campos con errores:');
					status.errors.push('<ul>');
				}
				status.errors.push('<li>El campo <strong>"' + fieldname + '"</strong> debe ser sólo numérico y como máximo tener 2 decimales (ejemplo: 1000,00)</li>');
			}

			min = parseInt(min);
			max = parseInt(max);

			if (value < min) {
				status.ok = false;
				status.error = true;
				if (!status.errors.length) {
					status.errors.push('Hay campos con errores:');
					status.errors.push('<ul>');
				}
				status.errors.push('<li>El campo <strong>"' + fieldname + '"</strong> debe ser mayor a ' + min + '</li>');
			}

			if (max > 0 && value > max) {
				status.ok = false;
				status.error = true;
				if (!status.errors.length) {
					status.errors.push('Hay campos con errores:');
					status.errors.push('<ul>');
				}
				status.errors.push('<li>El campo <strong>"' + fieldname + '"</strong> debe ser menor a ' + max + '</li>');
			} 
		default:
		break;
	}

	return status;
}

function checkValidation(fieldname, validation, value, status) {
	switch (validation) {
		case 'ci':
			if (!verifyDNI(value)) {
				status.ok = false;
				status.error = true;
				if (!status.errors.length) {
					status.errors.push('Hay campos con errores:');
					status.errors.push('<ul>');
				}
				status.errors.push('<li>El campo <strong>"' + fieldname + '"</strong> no es una Cédula de Identidad válida, por favor verifica la información</li>');
			}
		break;
		case 'numero':
			var number_re = /^[0-9]*\,?[0,9]{0,2}$/;
			if (!number_re.test(value)) {
				status.ok = false;
				status.error = true;
				if (!status.errors.length) {
					status.errors.push('Hay campos con errores:');
					status.errors.push('<ul>');
				}
				status.errors.push('<li>El campo <strong>"' + fieldname + '"</strong> debe ser sólo numérico y como máximo tener 2 decimales (ejemplo: 1000,00)</li>');
			}
		break;
		case 'telefono':
			var phone_re = /^([\d]{8,9})$/;
			if (!phone_re.test(value)) {
				status.ok = false;
				status.error = true;
				if (!status.errors.length) {
					status.errors.push('Hay campos con errores:');
					status.errors.push('<ul>');
				}
				status.errors.push('<li>El campo <strong>"' + fieldname + '"</strong> debe ser un número de teléfono válido de 8 o 9 dígitos.</li>');
			}
		break;
		case 'email':
			var email_re = /^.+@.+$/;
			if (!email_re.test(value)) {
				status.ok = false;
				status.error = true;
				if (!status.errors.length) {
					status.errors.push('Hay campos con errores:');
					status.errors.push('<ul>');
				}
				status.errors.push('<li>El campo <strong>"' + fieldname + '"</strong> debe ser un email válido</li>');
			}
		break;
	}

	return status;
}

function verifyDNI(ci) {
	var cedula,
		str_ci,
		verificador,
		coeficientes,
		sumatoria,
		redondeoMult10;

	cedula       = ci;
	str_ci       = cedula.toString();
	verificador  = str_ci[str_ci.length -1];
	coeficientes = [2,9,8,7,6,3,4];
	sumatoria    = 0;

	for (var c = 0; c < str_ci.length -1; c++) {
		sumatoria += str_ci[c] * coeficientes[c];
	}

	redondeoMult10 = Math.ceil(sumatoria/10) * 10;
	return redondeoMult10 - sumatoria == verificador;
}

