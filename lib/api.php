<?php
require_once 'MysqliDb.php';
require_once './PHPMailer-master/PHPMailerAutoload.php';
require_once './helper.php';
require_once '../config.php';

// $modelo    = isset ($_POST['modelo']) ? $_POST['modelo'] : null;
$modelo    = isset ($_POST['data']) && isset ($_POST['data']['modelo']) ? $_POST['data']['modelo'] : null;
$categoria = isset ($_POST['data']) && isset ($_POST['data']['categoria']) ? $_POST['data']['categoria'] : null;
$seguros   = isset ($_POST['data']) && isset ($_POST['data']['seguros']) ? $_POST['data']['seguros'] : null;
$accion    = isset ($_POST['accion']) ? $_POST['accion'] : null;

$db = new MysqliDb ($dbsettings);

if (!empty ($accion)) {
	switch ($accion) {
		case 'cotizar':
			// Obtener la categoría
			$categoria = getCategoryByNameslug($categoria);
			// Obtener los seguros
			$seguros   = getProductsByCategory($categoria['id']);
			?>

<div class="center contratar">
	<div class="content-inner">
		<form id="cotizar">
			<div class="left-side-title">
				<span class="fa <?php echo $categoria['icono'] ?> left-side-icon"></span>
				<h3>
					<span><?php echo str_replace(' ', '</span><span>', $categoria['nombre']) ?></span>
				</h3>
			</div>
			<div class="form-inputs right-side-inputs">
				<h2><span class="number-globe">1</span> Elegí tu seguro y cotizalo</h2>
				<?php foreach ($seguros as $seguro) { ?>
					<?php $seguro_sano = strtolower (sanear_string(str_replace (' ', '_', $seguro['nombre']))) ?>
					<div class="form-line border-bottom input-check">
						<input type="checkbox" data-parent="<?php echo $seguro['pertenencia'] ?>" id="<?php echo $seguro_sano ?>" name="<?php echo $seguro_sano ?>" value="<?php echo $seguro['id'] ?>">
						<label for="<?php echo $seguro_sano ?>"><?php echo $seguro['nombre'] ?></label>
					</div>
				<?php } ?>
				<div class="form-line">
					<a href="#/seguro/<?php echo strtolower (sanear_string($categoria['nombre'])) ?>/cancelar" class="btn left"><span class="fa fa-angle-left"></span><span>Cancelar</span></a>
					<a href="#/seguro/<?php echo strtolower (sanear_string($categoria['nombre'])) ?>/contratar" class="btn" data-objetformid="cotizar"><span>Continuar</span><span class="fa fa-angle-right"></span></a>
				</div>
			</div>
			<div class="clear"></div>
		</form>
	</div>
</div>

			<?php
		break;

		case 'contratar':
			// Obtener la categoría
			$categoria      = getCategoryByNameslug($categoria);
			// Obtener los atributos por id del seguro
			$atributos      = getAttributesByParentID($seguros);
			$atr_usuarios   = array ();
			$atr_cotizacion = array ();
			$atr_poliza     = array ();
			foreach ($atributos as $atindx => $atr) {
				switch ($atr['modelo']) {
					case 'usuario':
						$atr_usuarios[] = $atr;
					break;
					case 'cotizacion':
						$atr_cotizacion[] = $atr;
					break;
					case 'poliza':
						$atr_poliza[] = $atr;
					break;
				}
			}
			?>

<div class="center asegurar" id="_contratar">
	<div class="content-inner">
		<form id="contratar">
			<div class="left-side-title">
				<span class="fa <?php echo $categoria['icono'] ?> left-side-icon"></span>
				<h3>
					<span><?php echo str_replace(' ', '</span><span>', $categoria['nombre']) ?></span>
				</h3>
			</div>
			<div class="form-inputs right-side-inputs">
				<h2><span class="number-globe">2</span> Ingresá tus datos y contratalo</h2>

				<!-- Atributos modelo usuario -->
				<?php if (count($atr_usuarios)) : ?>
				<h3>Ingresa tus datos personales</h3>
				<div class="form-line border-bottom"></div>
				<?php
				foreach ($atr_usuarios as $atindx => $atributo) {
					getAttributeHTML($atributo);
				}
				?>
				<?php endif; ?>
				<!-- Atributos modelo cotizacion -->
				<?php if (count($atr_cotizacion)) : ?>
				<h3>Ingresa datos de cotización</h3>
				<div class="form-line border-bottom"></div>
				<?php
				foreach ($atr_cotizacion as $atindx => $atributo) {
					getAttributeHTML($atributo);
				}
				?>
				<?php endif; ?>
				<!-- Atributos modelo poliza -->
				<h3>Ingresa datos de la póliza</h3>
				<div class="form-line border-bottom"></div>
				<?php
				foreach ($atr_poliza as $atindx => $atributo) {
					getAttributeHTML($atributo);
				}
				?>
				<div class="form-line border-bottom input-text input-medium">
					<label for="adjuntar">Adjuntar Comprobantes:</label>
					<input type="text" id="adjuntar" name="adjuntar">
				</div>
				<div class="form-line border-bottom input-check right-message">
					<input type="checkbox" id="todo-riesgo" name="todo-riesgo">
					<label for="todo-riesgo">Acepto los <a href="javasceript:void();">Términos y condiciones</a></label>
				</div>

				<div class="form-line border-bottom input-text input-medium">
					<label for="pago">Forma de Pago:</label>
					<select name="pago" id="pago">
						<option value="">Antel</option>
						<option value="">CobrosYa</option>
					</select>
				</div>
				<div class="form-line">
					<a href="#/seguro/<?php echo strtolower (sanear_string($categoria['nombre'])) ?>/cotizar" class="btn left"><span class="fa fa-angle-left"></span><span>Atrás</span></a>
					<a href="#/seguro/<?php echo strtolower (sanear_string($categoria['nombre'])) ?>/finalizar" class="btn" data-objetformid="cotizar"><span>Finalizar</span><span class="fa fa-angle-right"></span></a>
				</div>
			</div>
			<div class="clear"></div>
		</form>
	</div>
</div>
		<?php
		break;
		case 'finalizar':
			// Obtener la categoría
			$categoria = getCategoryByNameslug($categoria);
			// Obtener los seguros
			// $seguros   = getProductsByCategory($categoria['id']);
?>
<div class="center contratar">
	<div class="content-inner">
		<form id="contratar">
			<div class="left-side-title">
				<span class="fa <?php echo $categoria['icono'] ?> left-side-icon"></span>
				<h3>
					<span><?php echo str_replace(' ', '</span><span>', $categoria['nombre']) ?></span>
				</h3>
			</div>
			<div class="form-inputs right-side-inputs">
				<h2><span class="number-globe">3</span> Confirá tu pago y ¡ya estás asegurado!</h2>
			</div>
		</form>
	</div>
</div>
<?php
		break;
	}
}

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
	$cubre       = $atributo['cubre'] ? $atributo['cubre'] : null;

	if ($tipo == 'lista') {
		$valores = json_decode($atributo['valores']);
	}
	?>
	<div class="form-line border-bottom input-text input-large">
		<label for="<?php echo $atributo['atributo']; ?>"><?php echo $atributo['atributo']; ?>:</label>
		<?php if ($tipo != 'lista') : ?>
		<input
		 type="text"
		 data-customtype="<?php echo $tipo; ?>"
		 data-customdependency="<?php echo $dependencia; ?>"
		 data-customadd="<?php echo $adhiere; ?>"
		 data-customcurrency="<?php echo $moneda; ?>"
		 data-customsave="<?php echo $cubre;5 ?>"
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