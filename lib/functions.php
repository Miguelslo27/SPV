<?php
function getCategoryByNameslug($nameslug) {
	global $db;
	// Traer la categoría según el nombre del registro
	$db->where('nombre_sano', $nameslug);
	$db->where('estado', 1);
	$categoria = $db->getOne('categoria');
	return $categoria;
}

function getProductById($id) {
	global $db;
	// Traer la categoría según el nombre del registro
	$db->where('id', $id);
	$db->where('estado', 1);
	$seguro = $db->getOne('seguro');
	return $seguro;
}

function getProductsByCategory($catid) {
	global $db;
	// Traer los seruguros según la categoría
	$db->where('categoria', $catid);
	$db->where('estado', 1);
	$db->orderBy('orden', 'asc');

	$seguros = $db->get('seguro');
	return $seguros;
}

function getAttributesByParentID($prod) {
	global $db;
	$db->where('estado', 1);
	$db->orderBy('orden', 'asc');

	$atributos = $db->get('variable');
	$atrs_aplicable = array();

	foreach ($atributos as $atr) {
		if (in_array($prod['id'], explode(',', $atr['aplicacion'])) && !in_array($atr, $atrs_aplicable)) {
			$atrs_aplicable[] = $atr;
		}
	}
	return $atrs_aplicable;
}

function getAttributeHTML($atributo) {
	$validacion  = $atributo['validacion'];
	$tipo        = $atributo['tipo'];
	$valores     = null;
	$dependencia = $atributo['dependencia'] ? $atributo['dependencia'] : null;
	$avanzado    = $atributo['avanzado'];
	$adhiere_adv = $atributo['precios_avanzados'];
	$requerido   = $atributo['requerido'] != '' ? 'true' : 'false';
	$modelo      = $atributo['modelo'];
  $ayuda       = $atributo['ayuda'];
  $min         = $atributo['minimo'];
	$max         = $atributo['maximo'];
	$adhiere     = $avanzado ? $adhiere_adv : ($atributo['adhiere'] ? $atributo['adhiere'] : null);

	if ($tipo == 'lista') {
		$valores = json_decode($atributo['valores']);
	}

	$atributo_san = str_replace(['á', 'é', 'í', 'ó', 'ú'], ['a', 'e', 'i', 'o', 'u'], $atributo['atributo']);
	$atributo_san = str_replace(['.',' '], '_', filter_var(strtolower($atributo_san)));

	?>
	<div class="form-line border-bottom input-text input-large <?php echo ($requerido == 'true' ? 'input-required' : ''); ?>">
		<label for="<?php echo $atributo_san; ?>"><?php echo $atributo['atributo']; ?>:</label>
		<?php if ($tipo == 'lista') : ?>
		<select
		 data-realname="<?php echo $atributo['atributo']; ?>"
		 data-customtype="<?php echo $tipo; ?>"
		 data-customdependency="<?php echo $dependencia; ?>"
		 data-customadvanced="<?php echo $avanzado; ?>"
		 data-customadd='<?php echo $adhiere; ?>'
		 data-customaddin="<?php echo $atributo['porcentaje']; ?>"
		 data-customcurrency="<?php echo $atributo['moneda'];; ?>"
     data-customrequired="<?php echo $requerido; ?>"
     data-custommin="<?php echo $min; ?>"
		 data-custommax="<?php echo $max; ?>"
		 data-custommodel="<?php echo $modelo; ?>"
		 id="<?php echo $atributo_san; ?>"
		 name="<?php echo $atributo_san; ?>">
		 	<?php foreach ($valores as $val) { ?>
		 	<option value="<?php echo $val->id; ?>"><?php echo $val->valor; ?></option>
		 	<?php } ?>
		</select>
		<?php else : ?>
		<input
		 type="text"
		 data-realname="<?php echo $atributo['atributo']; ?>"
		 data-customtype="<?php echo $tipo; ?>"
		 data-customdependency="<?php echo $dependencia; ?>"
		 data-customadvanced="<?php echo $avanzado; ?>"
		 data-customadd='<?php echo $adhiere; ?>'
		 data-customaddin="<?php echo $atributo['porcentaje']; ?>"
		 data-customcurrency="<?php echo $atributo['moneda']; ?>"
     data-customrequired="<?php echo $requerido; ?>"
     data-custommin="<?php echo $min; ?>"
		 data-custommax="<?php echo $max; ?>"
		 data-customcheck="<?php echo $atributo['validacion']; ?>"
		 data-custommodel="<?php echo $modelo; ?>"
		 id="<?php echo $atributo_san; ?>"
		 name="<?php echo $atributo_san; ?>">
		<?php endif; ?>
		<?php if ($ayuda && $ayuda != '') : ?>
			<span class="field-help"><?php echo $ayuda; ?></span>
		<?php endif; ?>
	</div>
	<?php
}

function savePoliza($data, $pdfRoute) {
  global $db;

  date_default_timezone_set('America/Montevideo');

  // Create User first
  $userData = Array (
    "nombre" => $data['usuario']['nombre'],
    "apellido" => $data['usuario']['apellido'],
    "ci" => $data['usuario']['cedula_de_identidad'],
    "email" => $data['usuario']['e-mail'],
    "estado" => 1
  );

  $userId = $db->insert('usuario', $userData);

  $data['pdf'] = $pdfRoute;

  $seguroData = Array (
    "usuario" => $userId,
    "fecha" => date("Y-m-d H:m:s"),
    "seguro" => json_encode($data)
  );

  $seguroId = $db->insert('seguro_registrado', $seguroData);

  return $seguroId ? true : false;
}

function sendEmail($dest, $data, $pdfRoute) {
  $serverhost = 'http://'.$_SERVER['HTTP_HOST'];
  $pdf        = $serverhost.$pdfRoute;
  $logo       = $serverhost.'/imagenes/logos/logo.png';
  $categoria  = $data['categoria'];
  $categoria  = getCategoryByNameslug($categoria)['nombre'];
  $seguro     = $data['seguro']['nombre'];

  if ($data['seguro']['producto'] == 'segurodenotebook') {
    $precio = $data['seguro']['moneda'].' '.$data['seguro']['precio_imp_inc'];
  } else {
    $precio = $data['seguro']['moneda'].' '.$data['seguro']['precio'];
  }

  $mail = new PHPMailer;

  $mail->setFrom('noreply@seguroparavos.com.uy', 'SeguroParaVos | Larraura Seguros');
  $mail->addReplyTo('noreply@seguroparavos.com.uy', 'SeguroParaVos | Larraura Seguros');
  $mail->addAddress($dest['email'], $dest['name']);

  $mail->addCC('miguelmail2006@gmail.com');
  $mail->addCC('dlarraura@larrauraseguros.com.uy');
  $mail->addCC('jppando101@gmail.com');

  $mail->addStringAttachment(file_get_contents($pdf), 'seguroparavos-poliza.pdf');
  $mail->CharSet = 'UTF-8';
  $mail->isHTML(true);

  $mail->Subject = 'Estado de la solicitud - SeguroParaVos.com.uy';
  $mail->Body    = '
  <table width="100%" style="border: 1px solid #888;">
    <thead>
      <tr>
        <th style="text-align: left; padding: 40px; border-bottom: 2px solid #9c3">
          <img src="'.$logo.'">
        </th>
      </tr>
    </thead>
    <tbody>
      <tr>
        <td style="padding: 40px;">
          <h2 style="color: #9c3;">Solicitud</h2>
          <div>
            <p>Producto solicitado: <span><strong>'.$categoria.'</strong></span>.</p>
            <p>Cobertura: <span><strong>'.$seguro.'</strong></span>.</p>
            <p>Precio de la cotización: <span><strong>'.$precio.'</strong></span></p>
          </div>
          <h3 class="green-style">Solicitud enviada</h3>
          <div class="push-60-left">
            <p>En 24 horas hábiles sera contactado por el equipo de <a href="'.$serverhost.'"><span style="text-transform: uppercase;"><span style="color: #666; font-weight: bolder;">Seguro</span><span style="font-weight: light; color: #666;">Para</span><span style="font-weight: bold; color: #9c3;">Vos</span></span></a> para completar los datos del producto solicitado</p>
            <p>La cobertura no entrará en vigor hasta no ser inspeccionado y aceptado el riesgo por el asegurador.</p>
          </div>
          <div class="form-line border-bottom"></div>
          <h3 class="green-style">Muchas gracias</h3>
          <div class="push-60-left">
            <p>El equipo de <a href="'.$serverhost.'"><span style="text-transform: uppercase;"><span style="color: #666; font-weight: bolder;">Seguro</span><span style="font-weight: light; color: #666;">Para</span><span style="font-weight: bold; color: #9c3;">Vos</span></span></a>.</p>
          </div>
          <div class="push-60-left">
            <p class="small">Se deja constancia que los mencionados servicios no son prestados por la aplicación Segurosparavos, la cual se limita simplemente a ofrecer los mismos siendo la única exclusivamente responsables de la totalidad de las obligaciones que emergen de dicha contratación las empresas que prestan los mencionados servicios contratados.
            “Las coberturas de seguros incluidas en este producto son proporcionadas por MAPFRE (www.mapfre.com.uy) empresa autorizada a operar en Uruguay por el Banco Central del Uruguay.”</p>
          </div>
        </td>
      </tr>
    </tbody>
  </table>
  ';

  $mail->send();
}

function sendEmailContact($dest, $message) {
  $serverhost = 'http://'.$_SERVER['HTTP_HOST'];
  $logo       = $serverhost.'/imagenes/logos/logo.png';

  $mail = new PHPMailer;

  $mail->setFrom('noreply@seguroparavos.com.uy', 'SeguroParaVos | Larraura Seguros');
  $mail->addReplyTo('noreply@seguroparavos.com.uy', 'SeguroParaVos | Larraura Seguros');
  $mail->addAddress($dest['email'], $dest['name']);

  $mail->addCC('miguelmail2006@gmail.com');
  $mail->addCC('dlarraura@larrauraseguros.com.uy');
  $mail->addCC('jppando101@gmail.com');

  $mail->CharSet = 'UTF-8';
  $mail->isHTML(true);

  $mail->Subject = 'Contacto Web - SeguroParaVos.com.uy';
  $mail->Body    = '
  <table width="100%" style="border: 1px solid #888;">
    <thead>
      <tr>
        <th style="text-align: left; padding: 40px; border-bottom: 2px solid #9c3">
          <img src="'.$logo.'">
        </th>
      </tr>
    </thead>
    <tbody>
      <tr>
        <td style="padding: 40px;">
          <h2 style="color: #9c3;">Mensaje enviado desde la web</h2>
          <div>
            <p>
              <strong>Nombre:</strong> <span>'.$dest['name'].'</span>
            </p>
            <p>
              <strong>Email:</strong> <span>'.$dest['email'].'</span>
            </p>
          </div>
          <div>
            <p>'.$message.'</p>
            <p>Dentro de las próximas 24 horas hábiles sera contactado por el equipo de <a href="'.$serverhost.'"><span style="text-transform: uppercase;"><span style="color: #666; font-weight: bolder;">Seguro</span><span style="font-weight: light; color: #666;">Para</span><span style="font-weight: bold; color: #9c3;">Vos</span></span></a>.</p>
          </div>
          <h3>Muchas gracias</h3>
          <div class="push-60-left">
            <p>El equipo de <a href="'.$serverhost.'"><span style="text-transform: uppercase;"><span style="color: #666; font-weight: bolder;">Seguro</span><span style="font-weight: light; color: #666;">Para</span><span style="font-weight: bold; color: #9c3;">Vos</span></span></a>.</p>
          </div>
        </td>
      </tr>
    </tbody>
  </table>
  ';

  return $mail->send();
}

function createPDF($data) {
  $seguro_nombre_sano = strtolower(sanear_string($data['seguro']['nombre']));
  $pdfName            = time();

  switch ($seguro_nombre_sano) {
    case 'segurodenotebook':
      $pdfPaper = paperPDF_1($data);
    break;
    case 'segurodedrones':
      $pdfPaper = paperPDF_2($data);
    break;
    case 'coberturadebicibasica':
      $pdfPaper = paperPDF_3($data);
    break;
    case 'coberturadebicitotal':
      $pdfPaper = paperPDF_4($data);
    break;
    case 'asaltoenviapublica':
      $pdfPaper = paperPDF_5($data);
    break;
  }

  printPDF($pdfPaper, '../pdfs/'.@orEmpty($data['usuario']['cedula_de_identidad'], '000').'-'.$pdfName.'.pdf', DEBUG);

  return '/pdfs/'.@orEmpty($data['usuario']['cedula_de_identidad'], '000').'-'.$pdfName.'.pdf';
}

// PDF NOTEBOOK
function paperPDF_1($data) {
  $seguro_nombre_sano = strtolower(sanear_string($data['seguro']['nombre']));
  $seguro_precio      = @$data['seguro']['precio'];
  $seguro_imp_oc      = $seguro_precio * 0.12;
  $seguro_imp_iva     = ($seguro_precio + $seguro_imp_oc) * 0.22;
  $seguro_precio_ttl  = $seguro_precio + $seguro_imp_oc + $seguro_imp_iva;
  $template           = $data['categoria'].'-'.$seguro_nombre_sano.'.pdf';
  $pdfDefaults        = Array('font-style' => newPDFFontStyle('Arial', 9, 'B'));
  $pdfPages           = Array();

  $pdfPages = Array(
    newPDFPage(Array (
      // Datos del asegurado
      // Nombre
      newPDFRow(30, Array (
        newPDFCell(35, 0, 4.5, @$data['usuario']['nombre'].' '.@$data['usuario']['apellido'])
      )),
      // Direccion - CI
      newPDFRow(0, Array (
        newPDFCell(35, 110, 4.5, @$data['usuario']['direccion']),
        newPDFCell( 0, 0, 4.5, @$data['usuario']['cedula_de_identidad'])
      )),
      // Email - Cel - TEl
      newPDFRow(0, Array (
        newPDFCell(35, 45, 4.5, @$data['usuario']['e-mail']),
        newPDFCell(10, 28, 4.5, @$data['usuario']['celular']),
        newPDFCell(27, 0, 4.5, @$data['usuario']['telefono_contacto'])
      )),
      // Localidad - CP - Depto
      newPDFRow(0, Array (
        newPDFCell(35, 45, 4.5, @$data['usuario']['localidad']),
        newPDFCell(10, 28, 4.5, @$data['usuario']['cp']),
        newPDFCell(27, 0, 4.5, @$data['usuario']['departamento'])
      )),
      // Dirección Comercial - Tel. Cobro
      newPDFRow(0, Array (
        newPDFCell(35, 83, 4.5, @$data['usuario']['direccion_comercial']),
        newPDFCell(27, 0, 4.5, @$data['usuario']['telefono_de_cobro'])
      )),
      // Localidad Comercial - CP - Depto.
      newPDFRow(0, Array (
        newPDFCell(35, 45, 4.5, @$data['usuario']['localidad_comercial']),
        newPDFCell(10, 28, 4.5, @$data['usuario']['cp_comercial']),
        newPDFCell(27, 0, 4.5, @$data['usuario']['departamento_comercial'])
      )),
      // Titular responsable (empresa) - CI
      newPDFRow(0, Array (
        newPDFCell(50, 68, 4.5, ''),
        newPDFCell(27, 0, 4.5, '')
      )),
      // Vigencia
      // Desde - Hasta
      newPDFRow(16, Array (
        newPDFCell(11, 18, 4.5, date('d/m/Y')),
        newPDFCell(26, 18, 4.5, date('d/').date('m/').(date('Y') + 1))
      )),
      // Características del equipo asegurad
      // Marca y Modelo
      newPDFRow(14.2, Array (
        newPDFCell(88.5, 0, 4.5, @$data['poliza']['marca_notebook'].' '.@$data['poliza']['modelo'])
      )),
      // Número de serie
      newPDFRow(0, Array (
        newPDFCell(88.5, 0, 4.5, @$data['poliza']['numero_de_serie'])
      )),
      // Vendedor
      newPDFRow(0, Array (
        newPDFCell(88.5, 0, 4.5, @$data['poliza']['vendedor'])
      )),
      // Número de factura
      newPDFRow(0, Array (
        newPDFCell(88.5, 0, 4.5, @$data['poliza']['numero_de_factura'])
      )),
      // Fecha de emisión de la factura
      newPDFRow(0, Array (
        newPDFCell(88.5, 0, 4.5, @$data['poliza']['fecha_de_emision'])
      )),
      // Importe impuestos incluidos según la factura
      newPDFRow(0, Array (
        newPDFCell(88.5, 0, 4.5, @$data['seguro']['moneda'].' '.@$data['cotizacion']['suma_asegurada'])
      )),
      // Cobertura contratada
      // Suma asegurada
      newPDFRow(13.5, Array(
        newPDFCell(72.8, 74.5, 4.5, @$data['seguro']['moneda'].' '.number_format(@$data['cotizacion']['suma_asegurada'], 2), 'R'),
        newPDFCell(0, 42, 4.5, @$data['seguro']['moneda'].' '.number_format(@$data['seguro']['precio'], 2), 'R')
      )),
      newPDFRow(0, Array(
        newPDFCell(147.3, 42, 4.5, @$data['seguro']['moneda'].' '.number_format($seguro_precio, 2), 'R')
      )),
      newPDFRow(0, Array(
        newPDFCell(147.3, 42, 4.5, @$data['seguro']['moneda'].' '.number_format($seguro_imp_oc, 2), 'R')
      )),
      newPDFRow(0, Array(
        newPDFCell(147.3, 42, 4.5, @$data['seguro']['moneda'].' '.number_format($seguro_imp_iva, 2), 'R')
      )),
      newPDFRow(0, Array(
        newPDFCell(147.3, 42, 4.5, @$data['seguro']['moneda'].' '.number_format($seguro_precio_ttl, 2), 'R')
      )),
      // Forma de pago
      // Contado - Financiación (número de cuotas)
      newPDFRow(9, Array (
        newPDFCell(0, 4, 4.5, @$data['cotizacion']['forma_de_pago'] == '3' ? 'X' : '-'),
        newPDFCell(59, 4, 4.5, @$data['cotizacion']['forma_de_pago'] == 'b' ? 'X' : '-'),
        newPDFCell(65, 0, 4.5, @$data['cotizacion']['cuotas'])
      )),
      // Titular Tarjeta
      newPDFRow(0, Array (
        newPDFCell(77.5, 0, 4.5, @$data['cotizacion']['titular_tarjeta'])
      )),
      // Número Tarjeta
      newPDFRow(0, Array (
        newPDFCell(77.5, 0, 4.5, @$data['cotizacion']['numero_tarjeta'])
      )),
      // Vencimiento tarjeta
      newPDFRow(0, Array (
        newPDFCell(77.5, 0, 4.5, @$data['cotizacion']['vencimiento_tarjeta'])
      )),
      // Tipo tarjeta
      newPDFRow(0, Array (
        newPDFCell(77.5, 0, 4.5, @$data['cotizacion']['tipo_tarjeta'])
      )),
      // Datos del corredor
      // Nombre, código, Rut
      newPDFRow(48.3, Array (
        newPDFCell(16, 69, 4.5, 'SCUTUM SRL'),
        newPDFCell(16, 14.5, 4.5, '3947'),
        newPDFCell(20, 0, 4.5, '217708430012')
      )),
      // Dirección, Localidad
      newPDFRow(0, Array (
        newPDFCell(16, 99.5, 4.5, 'Joaquin Requena 1175'),
        newPDFCell(20, 0, 4.5, 'Montevideo')
      )),
      // Teléfono, Fax, Correo electrónico
      newPDFRow(0, Array (
        newPDFCell(16, 36.5, 4.5, '24018145'),
        newPDFCell(10, 32.5, 4.5, ''),
        newPDFCell(34, 0, 4.5, 'dlarraura@larrauraseguros.com.uy')
      )))
    ),
    // Firmas
    newPDFPage(Array (
      // Lugar y Fecha
      newPDFRow(72, Array (
        newPDFCell(36, 0, 4.5, 'Montevideo, Uruguay, '.date('d/m/Y'))
      )),
      // Firma del asegurado, Aclaración, CI
      newPDFRow(3.8, Array (
        newPDFCell(36, 35, 4.5, ''),
        newPDFCell(23, 35, 4.5, @$data['usuario']['nombre'].' '.@$data['usuario']['apellido']),
        newPDFCell(18, 0, 4.5, @$data['usuario']['cedula_de_identidad'])
      )),
      // Firma del corredor
      newPDFRow(4.4, Array (
        newPDFCell(36, 35, 4.5, 'SCUTUM SRL')
      )))
    )
  );

  $pdfPaper = newPDFPaper($pdfPages, $pdfDefaults, $template);
  return $pdfPaper;
}

// PDF DRONE
function paperPDF_2($data) {
  $seguro_nombre_sano = strtolower(sanear_string($data['seguro']['nombre']));
  $template           = $data['categoria'].'-'.$seguro_nombre_sano.'.pdf';
  $pdfDefaults        = Array('font-style' => newPDFFontStyle('Arial', 9, 'B'));
  $pdfPages           = Array();

  $pdfPages = Array(
    newPDFPage(Array (
      // Vigencia del Seguro
      // Anual, Vigencia desde
      newPDFRow(52.5, Array (
        newPDFCell(22, 3.5, 4, @$data['usuario']['vigencia'] == '1' ? 'X' : ' '),
        newPDFCell(107, 5, 4, date('d')),
        newPDFCell(10, 5, 4, date('m')),
        newPDFCell(15, 10, 4, date('Y')),
      )),
      // Otro, Vigencia hasta
      newPDFRow(2.5, Array (
        newPDFCell(22, 3.5, 4, @$data['usuario']['vigencia'] != '1' ? 'X' : ' '),
        newPDFCell(107, 5, 4, date('d')),
        newPDFCell(10, 5, 4, date('m')),
        newPDFCell(15, 10, 4, date('Y') + 1),
      )),
      // Datos del asegurado
      // RUT/CI, Fecha Nac.
      newPDFRow(13.25, Array (
        newPDFCell(23, 62, 4.47, @$data['usuario']['cedula_de_identidad']),
        newPDFCell(34.5, 70.5, 4.47, @$data['usuario']['fecha_de_nacimiento']),
      )),
      // Apellidos, Teléfono
      newPDFRow(0, Array (
        newPDFCell(23, 62, 4.47, @$data['usuario']['apellido']),
        newPDFCell(34.5, 70.5, 4.47, @$data['usuario']['telefono_contacto']),
      )),
      // Nombres, Código postal
      newPDFRow(0, Array (
        newPDFCell(23, 62, 4.47, @$data['usuario']['nombre']),
        newPDFCell(34.5, 70.5, 4.47, @$data['usuario']['codigo_postal']),
      )),
      // Dirección
      newPDFRow(0, Array (
        newPDFCell(23, 0, 4.47, @$data['usuario']['direccion']),
      )),
      // Localidad, Teléfono
      newPDFRow(0, Array (
        newPDFCell(23, 62, 4.47, @$data['usuario']['localidad']),
        newPDFCell(34.5, 70.5, 4.47, @$data['usuario']['telefono']),
      )),
      // Ocupación, Correo electrónico
      newPDFRow(0, Array (
        newPDFCell(23, 62, 4.47, @$data['usuario']['ocupacion']),
        newPDFCell(34.5, 70.5, 4.47, @$data['usuario']['e-mail']),
      )),
      // Dirección de envío, Teléfono de envío
      newPDFRow(0, Array (
        newPDFCell(23, 62, 8.70, ''),
        newPDFCell(34.5, 70.5, 8.70, ''),
      )),
      // Localidad de envío, CP de envío
      newPDFRow(0, Array (
        newPDFCell(23, 62, 8.70, ''),
        newPDFCell(34.5, 70.5, 8.70, ''),
      )),
      // Código del corredor, Nombre corredor
      newPDFRow(0, Array (
        newPDFCell(23, 62, 8.70, '3947'),
        newPDFCell(34.5, 70.5, 8.70, 'SCUTUM SRL'),
      )),
      // Datos del representante
      // CI, Fecha Nac.
      newPDFRow(13, Array (
        newPDFCell(23, 62, 4.47, ''),
        newPDFCell(34.5, 70.5, 4.47, ''),
      )),
      // Apellidos, Teléfono
      newPDFRow(0, Array (
        newPDFCell(23, 62, 4.47, ''),
        newPDFCell(34.5, 70.5, 4.47, ''),
      )),
      // Nombres, Nacionalidad
      newPDFRow(0, Array (
        newPDFCell(23, 62, 4.47, ''),
        newPDFCell(34.5, 70.5, 4.47, ''),
      )),
      // Dirección
      newPDFRow(0, Array (
        newPDFCell(23, 0, 4.47, ''),
      )),
      // Localidad, Teléfono
      newPDFRow(0, Array (
        newPDFCell(23, 62, 4.47, ''),
        newPDFCell(34.5, 70.5, 4.47, ''),
      )),
      // Ocupación, Correo electrónico
      newPDFRow(0, Array (
        newPDFCell(23, 62, 4.47, ''),
        newPDFCell(34.5, 70.5, 4.47, ''),
      )),

      // Forma de pago
      // Contado, Cuotas, Cantidad de cuotas
      newPDFRow(13, Array (
        newPDFCell(0, 4, 4.5, @$data['cotizacion']['forma_de_pago'] == '3' ? 'X' : ''),
        newPDFCell(20, 4, 4.5, @$data['cotizacion']['forma_de_pago'] == 'b' ? 'X' : ''),
        newPDFCell(20, 0, 4.5, @$data['cotizacion']['cuotas'])
      )),
      // Red de cobranzas
      newPDFRow(0, Array (
        newPDFCell(0, 4, 4.5, @$data['cotizacion']['forma_de_pago'] == '3' ? 'X' : ''),
      )),
      // Débito de tarjeta, Número de tarjeta, Vencimiento
      newPDFRow(2.4, Array (
        newPDFCell(0, 4, 4.5, @$data['cotizacion']['forma_de_pago'] == 'b' ? 'X' : ''),
        newPDFCell(61, 35, 4.5, ''),
        newPDFCell(42, 8, 4.5, ''),
        newPDFCell(17, 11, 4.5, ''),
      )),
      // Débito bancario, Banco, Suc., Nº de cuenta
      newPDFRow(4, Array (
        newPDFCell(0, 4, 4.5, ''),
        newPDFCell(45, 30, 4.5, ''),
        newPDFCell(14, 20, 4.5, ''),
        newPDFCell(25, 52.1, 4.5, ''),
      )),
      // Datos del Dron
      // Marca
      newPDFRow(15  , Array(
        newPDFCell(48, 0, 4.5, @$data['poliza']['marca_drone'])
      )),
      // Modelo
      newPDFRow(0  , Array(
        newPDFCell(48, 0, 4.5, @$data['poliza']['modelo'])
      )),
      // Nº Serie Identificación
      newPDFRow(0  , Array(
        newPDFCell(48, 0, 4.5, '')
      )),
      // Peso Máximo
      newPDFRow(0  , Array(
        newPDFCell(48, 0, 4.5, '')
      )),
      // Año de construcción
      newPDFRow(0  , Array(
        newPDFCell(48, 0, 4.5, '')
      )),
      // Nombre del Operador y RUT
      newPDFRow(0  , Array(
        newPDFCell(48, 0, 4.5, '')
      )),
      // Uso del Dron
      newPDFRow(0  , Array(
        newPDFCell(48, 0, 4.5, '')
      )),
      // Ámbito Geográfico de utilización
      newPDFRow(0  , Array(
        newPDFCell(48, 0, 8.8, '')
      ))
    )),
    newPDFPage(Array(
      // Límite de Indemnización
      // Suma Asegurada
      newPDFRow(37.6, Array (
        newPDFCell(58, 0, 4, @$data['cotizacion']['suma_asegurada'])
      )),
      // Datos del corredor
      // Nombre, código, Rut
      newPDFRow(69.4, Array (
        newPDFCell(44.5, 0, 4.5, 'SCUTUM SRL'),
      )),
      newPDFRow(0, Array(
        newPDFCell(44.5, 0, 4.5, '3947'),
      )),
      newPDFRow(0, Array(
        newPDFCell(44.5, 0, 4.5, '217708430012')
      ))
    ))
  );

  $pdfPaper = newPDFPaper($pdfPages, $pdfDefaults, $template);
  return $pdfPaper;
}

// PDF BICI BASICO
function paperPDF_3($data) {
  $seguro_nombre_sano = strtolower(sanear_string($data['seguro']['nombre']));
  $template           = $data['categoria'].'-'.$seguro_nombre_sano.'.pdf';
  $pdfDefaults        = Array('font-style' => newPDFFontStyle('Arial', 9, 'B'));
  $pdfPages           = Array();

  $pdfPages = Array(
    newPDFPage(Array(
      // Vigencia del seguro
      // Vigencia desde
      newPDFRow(27, Array(
        newPDFCell(93, 34.4, 4, date('d'), 'C'),
        newPDFCell(0, 28.8, 4, date('m'), 'C'),
        newPDFCell(0, 34, 4, date('Y'), 'C')
      )),
      // Vigencia hasta
      newPDFRow(0, Array(
        newPDFCell(93, 34.4, 4, date('d'), 'C'),
        newPDFCell(0, 28.8, 4, date('m'), 'C'),
        newPDFCell(0, 34, 4, date('Y') + 1, 'C')
      )),
      // Datos del asegurado
      // RUT/CI, Fecha Nac.
      newPDFRow(11, Array(
        newPDFCell(35.5, 77, 4, @$data['usuario']['cedula_de_identidad']),
        newPDFCell(35, 42.6, 4, @$data['usuario']['nombre']),
      )),
      // Apellidos, Celular
      newPDFRow(0, Array(
        newPDFCell(35.5, 77, 4, @$data['usuario']['apellido']),
        newPDFCell(35, 42.6, 4, @$data['usuario']['telefono_contacto']),
      )),
      // Nombres, Teléfono
      newPDFRow(0, Array(
        newPDFCell(35.5, 77, 4, @$data['usuario']['nombre']),
        newPDFCell(35, 42.6, 4, ''),
      )),
      // Dirección
      newPDFRow(0, Array(
        newPDFCell(35.5, 0, 4, @$data['usuario']['direccion']),
      )),
      // Localidad, CP
      newPDFRow(0, Array(
        newPDFCell(35.5, 77, 4, ''),
        newPDFCell(35, 42.6, 4, ''),
      )),
      // Dirección de envío, Teléfono de envío
      newPDFRow(0, Array(
        newPDFCell(35.5, 77, 4, ''),
        newPDFCell(35, 42.6, 4, ''),
      )),
      // Ocupación, Correo electrónico
      newPDFRow(0, Array(
        newPDFCell(35.5, 77, 4, ''),
        newPDFCell(35, 42.6, 4, ''),
      )),
      // localidad de envío, CP de envío
      newPDFRow(0, Array(
        newPDFCell(35.5, 77, 4, ''),
        newPDFCell(35, 42.6, 4, ''),
      )),
      // Código de corredor, Nombre corredor
      newPDFRow(0, Array(
        newPDFCell(35.5, 77, 4, '3947'),
        newPDFCell(35, 42.6, 4, 'SCUTUM SRL'),
      )),
      // Forma de pago
      // Contado, Cuotas, Cantidad de cuotas, Moneda Pesos, Moneda Dólares
      newPDFRow(11.5, Array(
        newPDFCell(0, 4, 3.95, @$data['cotizacion']['forma_de_pago'] == '3' ? 'X' : '-', 'C'),
        newPDFCell(21, 4, 3.95, @$data['cotizacion']['forma_de_pago'] == 'b' ? 'X' : '-', 'C'),
        newPDFCell(20, 32.2, 3.95, @$data['cotizacion']['cuotas']),
        newPDFCell(22, 6, 3.95, @$data['seguro']['moneda'] == '$' ? 'X' : '', 'C'),
        newPDFCell(40, 6, 3.95, @$data['seguro']['moneda'] == 'USD' ? 'X' : '', 'C'),
      )),
      // Red de cobranzas
      newPDFRow(0, Array(
        newPDFCell(0, 4, 3.95, @$data['cotizacion']['forma_de_pago'] == '3' ? 'X' : '', 'C'),
      )),
      // Débito de tarjeta, Nº de tarjeta, Vencimiento
      newPDFRow(0, Array(
        newPDFCell(0, 4, 3.95, @$data['cotizacion']['forma_de_pago'] == 'b' ? 'X' : '', 'C'),
        newPDFCell(64, 42, 3.95, ''),
        newPDFCell(38.5, 23, 3.95, '', 'C'),
        newPDFCell(0, 19, 3.95, '', 'C'),
      )),
      // Débito bancario, Banco, Suc., Nº de Cta.
      newPDFRow(0, Array(
        newPDFCell(0, 4, 3.95, '', 'C'),
        newPDFCell(46, 36, 3.95, ''),
        newPDFCell(13, 25, 3.95, ''),
        newPDFCell(24.5, 42, 3.95, ''),
      )),
      // Detalles del bien asegurado
      // Marca, Observaciones
      newPDFRow(11.3, Array(
        newPDFCell(42.5, 49.5, 3.95, @$data['poliza']['marca_bicicleta']),
        newPDFCell(49, 49.5, 3.95, ''),
      )),
      // Modelo
      newPDFRow(0, Array(
        newPDFCell(42.5, 49.5, 3.95, @$data['poliza']['modelo']),
      )),
      // Valor Factura
      newPDFRow(0, Array(
        newPDFCell(42.5, 49.5, 3.95, ''),
      )),
      // Datos factura (Vendedor, Nº)
      newPDFRow(-7.8, Array(
        newPDFCell(141, 49.5, 7.5, ''),
      )),
      // Cobertura Básica
      newPDFRow(72, Array(
        newPDFCell(101.5, 38.5, 5, 'X', 'C'),
      )),
      // Lugar y fecha
      newPDFRow(33.3, Array(
        newPDFCell(25, 0, 4.5, 'Montevideo, '.date('d').'/'.date('m').'/'.date('Y')),
      )),
      // Aclaracion del asegurado, CI
      newPDFRow(5, Array(
        newPDFCell(86, 38.5, 5, @$data['usuario']['nombre'].' '.@$data['usuario']['apellido'], 'C'),
        newPDFCell(20, 38.5, 5, @$data['usuario']['cedula_de_identidad'], 'C'),
      )),
      // Firma del corredor
      newPDFRow(8, Array(
        newPDFCell(16, 38.5, 5, 'SCUTUM SRL', 'C'),
        newPDFCell(31.5, 38.5, 5, 'SCUTUM SRL', 'C'),
        newPDFCell(20, 38.5, 5, '3947', 'C'),
      )),
    )),
  );

  $pdfPaper = newPDFPaper($pdfPages, $pdfDefaults, $template);
  return $pdfPaper;
}

// PDF BICI TOTAL
function paperPDF_4($data) {
  $seguro_nombre_sano = strtolower(sanear_string($data['seguro']['nombre']));
  $template           = $data['categoria'].'-'.$seguro_nombre_sano.'.pdf';
  $pdfDefaults        = Array('font-style' => newPDFFontStyle('Arial', 9, 'B'));
  $pdfPages           = Array();

  $pdfPages = Array(
    newPDFPage(Array(
      // Vigencia del seguro
      // Vigencia desde
      newPDFRow(27, Array(
        newPDFCell(93, 34.4, 4, date('d'), 'C'),
        newPDFCell(0, 28.8, 4, date('m'), 'C'),
        newPDFCell(0, 34, 4, date('Y'), 'C')
      )),
      // Vigencia hasta
      newPDFRow(0, Array(
        newPDFCell(93, 34.4, 4, date('d'), 'C'),
        newPDFCell(0, 28.8, 4, date('m'), 'C'),
        newPDFCell(0, 34, 4, date('Y') + 1, 'C')
      )),
      // Datos del asegurado
      // RUT/CI, Fecha Nac.
      newPDFRow(11, Array(
        newPDFCell(35.5, 77, 4, @$data['usuario']['cedula_de_identidad']),
        newPDFCell(35, 42.6, 4, @$data['usuario']['nombre']),
      )),
      // Apellidos, Celular
      newPDFRow(0, Array(
        newPDFCell(35.5, 77, 4, @$data['usuario']['apellido']),
        newPDFCell(35, 42.6, 4, @$data['usuario']['telefono_contacto']),
      )),
      // Nombres, Teléfono
      newPDFRow(0, Array(
        newPDFCell(35.5, 77, 4, @$data['usuario']['nombre']),
        newPDFCell(35, 42.6, 4, ''),
      )),
      // Dirección
      newPDFRow(0, Array(
        newPDFCell(35.5, 0, 4, @$data['usuario']['direccion']),
      )),
      // Localidad, CP
      newPDFRow(0, Array(
        newPDFCell(35.5, 77, 4, ''),
        newPDFCell(35, 42.6, 4, ''),
      )),
      // Dirección de envío, Teléfono de envío
      newPDFRow(0, Array(
        newPDFCell(35.5, 77, 4, ''),
        newPDFCell(35, 42.6, 4, ''),
      )),
      // Ocupación, Correo electrónico
      newPDFRow(0, Array(
        newPDFCell(35.5, 77, 4, ''),
        newPDFCell(35, 42.6, 4, ''),
      )),
      // localidad de envío, CP de envío
      newPDFRow(0, Array(
        newPDFCell(35.5, 77, 4, ''),
        newPDFCell(35, 42.6, 4, ''),
      )),
      // Código de corredor, Nombre corredor
      newPDFRow(0, Array(
        newPDFCell(35.5, 77, 4, '3947'),
        newPDFCell(35, 42.6, 4, 'SCUTUM SRL'),
      )),
      // Forma de pago
      // Contado, Cuotas, Cantidad de cuotas, Moneda Pesos, Moneda Dólares
      newPDFRow(11.5, Array(
        newPDFCell(0, 4, 3.95, @$data['cotizacion']['forma_de_pago'] == '3' ? 'X' : '-', 'C'),
        newPDFCell(21, 4, 3.95, @$data['cotizacion']['forma_de_pago'] == 'b' ? 'X' : '-', 'C'),
        newPDFCell(20, 32.2, 3.95, @$data['cotizacion']['cuotas']),
        newPDFCell(22, 6, 3.95, @$data['seguro']['moneda'] == '$' ? 'X' : '', 'C'),
        newPDFCell(40, 6, 3.95, @$data['seguro']['moneda'] == 'USD' ? 'X' : '', 'C'),
      )),
      // Red de cobranzas
      newPDFRow(0, Array(
        newPDFCell(0, 4, 3.95, @$data['cotizacion']['forma_de_pago'] == '3' ? 'X' : '', 'C'),
      )),
      // Débito de tarjeta, Nº de tarjeta, Vencimiento
      newPDFRow(0, Array(
        newPDFCell(0, 4, 3.95, @$data['cotizacion']['forma_de_pago'] == 'b' ? 'X' : '', 'C'),
        newPDFCell(64, 42, 3.95, ''),
        newPDFCell(38.5, 23, 3.95, '', 'C'),
        newPDFCell(0, 19, 3.95, '', 'C'),
      )),
      // Débito bancario, Banco, Suc., Nº de Cta.
      newPDFRow(0, Array(
        newPDFCell(0, 4, 3.95, '', 'C'),
        newPDFCell(46, 36, 3.95, ''),
        newPDFCell(13, 25, 3.95, ''),
        newPDFCell(24.5, 42, 3.95, ''),
      )),
      // Detalles del bien asegurado
      // Marca, Observaciones
      newPDFRow(11.3, Array(
        newPDFCell(42.5, 49.5, 3.95, @$data['poliza']['marca_bicicleta']),
        newPDFCell(49, 49.5, 3.95, ''),
      )),
      // Modelo
      newPDFRow(0, Array(
        newPDFCell(42.5, 49.5, 3.95, @$data['poliza']['modelo']),
      )),
      // Valor Factura
      newPDFRow(0, Array(
        newPDFCell(42.5, 49.5, 3.95, ''),
      )),
      // Datos factura (Vendedor, Nº)
      newPDFRow(-7.8, Array(
        newPDFCell(141, 49.5, 7.5, ''),
      )),

      // USD 130
      newPDFRow(57.3, Array(
        newPDFCell(141, 49.5, 5, (@$data['seguro']['precio'] <= '130' ? 'X' : ''), 'C'),
      )),
      // USD 275
      newPDFRow(0, Array(
        newPDFCell(141, 49.5, 5, (@$data['seguro']['precio'] > '130' && @$data['seguro']['precio'] <= '275' ? 'X' : ''), 'C'),
      )),
      // USD 362
      newPDFRow(0, Array(
        newPDFCell(141, 49.5, 5, (@$data['seguro']['precio'] >= '362' ? 'X' : ''), 'C'),
      )),

      // Cobertura Total
      newPDFRow(0, Array(
        newPDFCell(141, 49.5, 5, 'X', 'C'),
      )),
      // Lugar y fecha
      newPDFRow(33.3, Array(
        newPDFCell(25, 0, 4.5, 'Montevideo, '.date('d').'/'.date('m').'/'.date('Y')),
      )),
      // Aclaracion del asegurado, CI
      newPDFRow(5, Array(
        newPDFCell(86, 38.5, 5, @$data['usuario']['nombre'].' '.@$data['usuario']['apellido'], 'C'),
        newPDFCell(20, 38.5, 5, @$data['usuario']['cedula_de_identidad'], 'C'),
      )),
      // Firma del corredor
      newPDFRow(8, Array(
        newPDFCell(16, 38.5, 5, 'SCUTUM SRL', 'C'),
        newPDFCell(31.5, 38.5, 5, 'SCUTUM SRL', 'C'),
        newPDFCell(20, 38.5, 5, '3947', 'C'),
      )),
    )),
  );

  $pdfPaper = newPDFPaper($pdfPages, $pdfDefaults, $template);
  return $pdfPaper;
}

// PDF ASALTO EN LA VIA PUBLICA
function paperPDF_5($data) {
  $seguro_nombre_sano = strtolower(sanear_string($data['seguro']['nombre']));
  $template           = $data['categoria'].'-'.$seguro_nombre_sano.'.pdf';
  $pdfDefaults        = Array('font-style' => newPDFFontStyle('Arial', 9, 'B'));
  $pdfPages           = Array();

  $pdfPages = Array(
    newPDFPage(Array(
      // Datos del asegurado
      // Nombre, CI
      newPDFRow(45.5, Array(
        newPDFCell(38, 87, 5.5, @$data['usuario']['nombre']),
        newPDFCell(28, 36, 5.5, @$data['usuario']['cedula_de_identidad'])
      )),
      // Direción, Teléfono
      newPDFRow(0, Array(
        newPDFCell(38, 87, 5.5, ''),
        newPDFCell(28, 36, 5.5, @$data['usuario']['telefono_contacto'])
      )),
      // Localidad, CP, Departamento
      newPDFRow(0, Array(
        newPDFCell(38, 45, 5.5, ''),
        newPDFCell(10, 32, 5.5, ''),
        newPDFCell(28, 36, 5.5, '')
      )),
      // Fecha de nacimiento
      newPDFRow(0, Array(
        newPDFCell(38, 151, 5.5, ''),
      )),
      // Correo electrónico
      newPDFRow(0, Array(
        newPDFCell(38, 151, 5.5, @$data['usuario']['e-mail']),
      )),
      // Código Corredor, Nombre Corredor
      newPDFRow(0, Array(
        newPDFCell(38, 23, 5.5, '3947'),
        newPDFCell(34, 94, 5.5, 'SCUTUM SRL')
      )),
      // Nuevo Cliente
      newPDFRow(7.5, Array(
        newPDFCell(82.5, 43, 5.5, 'X', 'C'),
      )),
    ))
  );

  $pdfPaper = newPDFPaper($pdfPages, $pdfDefaults, $template);
  return $pdfPaper;
}

function newPDFPaper($pages, $defaults = null, $template = null) {
  $paper = Array (
    'pages' => $pages
  );

  if ($template)
    $paper['template'] = $template;

  if ($defaults)
    $paper['defaults'] = $defaults;

  return $paper;
}

function newPDFPage($rows) {
  return Array (
    'rows' => $rows
  );
}

function newPDFRow($marginTop = 0, $cells) {
  return Array (
    'margin-top' => $marginTop,
    'cells' => $cells
  );
}

function newPDFCell($marginLeft = 0, $width = 0, $height = 0, $content = null, $align = null, $font = null) {
  $cell = Array ();
  $cell['width'] = $width;

  if ($marginLeft)
    $cell['margin-left'] = $marginLeft;

  if ($height)
    $cell['height'] = $height;

  if ($content)
    $cell['content'] = $content;

  if ($align)
    $cell['align'] = $align;

  if ($font)
    $cell['font-style'] = $font;
  
  return $cell;
}

function newPDFFontStyle($family = 'Arial', $size = 12, $style = '') {
  return Array (
    'family' => $family,
    'style' => $style,
    'size' => $size
  );
}

function printPDF($pdfObj, $dest, $test = false) {
  // Start a new PDF
  $pdf       = new FPDI();
  // Import template
  $pageCount = $pdf->setSourceFile('../pdfs/templates/'.$pdfObj['template']);
  $tmplPages = Array ();

  // Get template pages
  for ($i = 1; $i <= $pageCount; $i++) {
    $tmplPages[] = $pdf->importPage($i, '/MediaBox');
  }

  // Set font styles
  $pdf->SetFont($pdfObj['defaults']['font-style']['family'], $pdfObj['defaults']['font-style']['style'], $pdfObj['defaults']['font-style']['size']);
  $pdf->SetFillColor('230');
  $pdf->SetAutoPageBreak(false, 0);

  // For each page in pdfObj
  foreach ($pdfObj['pages'] as $index => $page) {
    if (!isset ($tmplPages[$index]))
      break;

    // Add new page to final PDF file
    $pdf->AddPage();

    // Use corresponding template
    $pdf->useTemplate($tmplPages[$index]);

    // For each row in current page
    foreach ($page['rows'] as $row) {
      // Set the margin top
      $pdf->Ln($row['margin-top']);

      // For each cell in current row
      foreach ($row['cells'] as $cell) {
        // Override font if required
        if (isset ($cell['font-style'])) {
          $pdf->SetFont($cell['font-style']['family'], $cell['font-style']['style'], $cell['font-style']['size']);
        }
  
        // Print margin-left if required
        if (isset ($cell['margin-left']) && $cell['margin-left'] > 0) {
          $pdf->Cell($cell['margin-left']);
        }

        if ($test)
          $pdf->Cell($cell['width'], $cell['height'], @orEmpty($cell['content'], '---'), 1, 0, @$cell['align'], true);
        else
          $pdf->Cell($cell['width'], $cell['height'], @orEmpty($cell['content'], ''), 0, 0, @$cell['align']);

        // Get back to default font
        if (isset ($cell['font-style'])) {
          $pdf->SetFont($pdfObj['defaults']['font-style']['family'], $pdfObj['defaults']['font-style']['style'], $pdfObj['defaults']['font-style']['size']);
        }
      }

      $pdf->Ln();
    }
  }

  if ($test)
    $pdf->Output('F', '../pdfs/tests/'.$pdfObj['template']);
  else
    $pdf->Output('F', $dest);
}

function callAPI($method, $url, $data = false) {
  $curl = curl_init();

  switch ($method) {
    case "POST":
      curl_setopt($curl, CURLOPT_POST, 1);

      if ($data) {
        curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
      }
    break;
    case "PUT":
      curl_setopt($curl, CURLOPT_PUT, 1);
    break;
    default:
      if ($data) {
        $url = sprintf("%s?%s", $url, http_build_query($data));
      }
    break;
  }

  // Optional Authentication:
  curl_setopt($curl, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
  curl_setopt($curl, CURLOPT_USERPWD, "username:password");

  curl_setopt($curl, CURLOPT_URL, $url);
  curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);

  $result = curl_exec($curl);
  $error = curl_exec($curl);

  curl_close($curl);

  return array ("result" => $result, "error" => $error);
}

?>