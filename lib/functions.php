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
	// TODO
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
	$adhiere     = $atributo['adhiere'] ? $atributo['adhiere'] : null;
	$requerido   = $atributo['requerido'] != '' ? 'true' : 'false';
	$modelo      = $atributo['modelo'];
	$ayuda       = $atributo['ayuda'];

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
		 data-customadd="<?php echo $adhiere; ?>"
		 data-customaddin="<?php echo $atributo['porcentaje']; ?>"
		 data-customcurrency="<?php echo $atributo['moneda'];; ?>"
		 data-customrequired="<?php echo $requerido; ?>"
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
		 data-customadd="<?php echo $adhiere; ?>"
		 data-customaddin="<?php echo $atributo['porcentaje']; ?>"
		 data-customcurrency="<?php echo $atributo['moneda']; ?>"
		 data-customrequired="<?php echo $requerido; ?>"
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
?>