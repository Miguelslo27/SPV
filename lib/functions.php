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
}

function getProductsByCategory($catid) {
	global $db;
	// Traer los seruguros según la categoría
	$db->where('categoria', $catid);
	$db->where('estado', 1);

	$seguros = $db->get('seguro');
	return $seguros;
}

function getAttributesByParentID($prods) {
	global $db;
	$db->where('estado', 1);
	$db->orderBy('orden', 'asc');

	$atributos = $db->get('variable');
	$atrs_aplicable = array();

	// Traer los atributos según los seguros
	foreach ($prods as $prod) {
		foreach ($atributos as $atr) {
			if (in_array($prod['id'], explode(',', $atr['aplicacion'])) && !in_array($atr, $atrs_aplicable)) {
				$atrs_aplicable[] = $atr;
			}
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
	$moneda      = $atributo['moneda'] != '' ? $atributo['moneda'] : null;
	$requerido   = $atributo['requerido'] != '' ? 'true' : 'false';

	if ($tipo == 'lista') {
		$valores = json_decode($atributo['valores']);
	}
	?>
	<div class="form-line border-bottom input-text input-large <?php echo ($requerido == 'true' ? 'input-required' : ''); ?>">
		<label for="<?php echo $atributo['atributo']; ?>"><?php echo $atributo['atributo']; ?>: <?php echo ($requerido == 'true' ? '<span class="required">*</span>' : ''); ?></label>
		<?php if ($tipo != 'lista') : ?>
		<input
		 type="text"
		 data-customtype="<?php echo $tipo; ?>"
		 data-customdependency="<?php echo $dependencia; ?>"
		 data-customadd="<?php echo $adhiere; ?>"
		 data-customcurrency="<?php echo $moneda; ?>"
		 data-customrequired="<?php echo $requerido ?>"
		 id="<?php echo $atributo['atributo']; ?>"
		 name="<?php echo $atributo['atributo']; ?>">
		<?php else : ?>
		<select
		 data-customtype="<?php echo $tipo; ?>"
		 data-customdependency="<?php echo $dependencia; ?>"
		 data-customadd="<?php echo $adhiere; ?>"
		 data-customcurrency="<?php echo $moneda; ?>"
		 data-customsave="<?php echo $cubre;5 ?>"
		 id="<?php echo $atributo['atributo']; ?>"
		 name="<?php echo $atributo['atributo']; ?>">
		 	<?php foreach ($valores as $key => $val) { ?>
		 	<option value="<?php echo $key; ?>"><?php echo $val; ?></option>
		 	<?php } ?>
		</select>
		<?php endif; ?>
	</div>
	<?php
}
?>