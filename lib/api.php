<?php
require_once 'MysqliDb.php';
require_once './PHPMailer-master/PHPMailerAutoload.php';
require_once './helper.php';

$modelo    = isset ($_POST['modelo']) ? $_POST['modelo'] : null;
$categoria = isset ($_POST['categoria']) ? $_POST['categoria'] : null;
$accion    = isset ($_POST['accion']) ? $_POST['accion'] : null;

$db = new MysqliDb (Array (
	'host'     => 'localhost',
	'username' => 'root', 
	'password' => '',
	'db'       => 'seguropa_db',
	'charset'  => 'utf8'
));

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
				<span class="fa fa-mobile left-side-icon"></span>
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
			$categoria = getCategoryByNameslug($categoria);
			// Obtener los seguros
			$seguros   = getProductsByCategory($categoria['id']);
			?>

<div class="center asegurar" id="_contratar">
	<div class="content-inner">
		<form action="/send-email.php">
			<div class="left-side-title">
				<span class="fa fa-mobile left-side-icon"></span>
				<h3>
					<span>Seguro</span><strong>Móvil</strong>
				</h3>
			</div>
			<div class="form-inputs right-side-inputs">
				<h2><span class="number-globe">2</span> Ingresá tus datos y contratalo</h2>
				<h3>Ingresa tus datos personales</h3>
				<div class="form-line border-bottom input-text input-large">
					<label for="nombre">Nombre:</label>
					<input type="text" id="nombre" name="nombre">
				</div>
				<div class="form-line border-bottom input-text input-large">
					<label for="apellido">Apellido:</label>
					<input type="text" id="apellido" name="apellido">
				</div>
				<div class="form-line border-bottom input-text input-large">
					<label for="documento">Cédula:</label>
					<input type="text" id="documento" name="documento">
				</div>
				<div class="form-line border-bottom input-text input-large">
					<label for="numero-celular">Nº Celular:</label>
					<input type="text" id="numero-celular" name="numero-celular">
				</div>
				<div class="form-line border-bottom input-text input-large">
					<label for="marca-celular">Marca:</label>
					<!-- <input type="text" id="marca-celular" name="marca-celular"> -->
					<select name="marca-celular" id="marca-celular">
						<option value="">Samsung</option>
						<option value="">Apple</option>
						<option value="">Haweii</option>
						<option value="">HTC</option>
					</select>
				</div>
				<div class="form-line border-bottom input-text input-large">
					<label for="modelo-celular">Modelo:</label>
					<select name="modelo-celular" id="modelo-celular">
						<option value="">Samsung</option>
						<option value="">Apple</option>
						<option value="">Haweii</option>
						<option value="">HTC</option>
					</select>
				</div>
				<div class="form-line border-bottom input-text input-large">
					<label for="imei-celular">IMEI:</label>
					<input type="text" id="imei-celular" name="imei-celular">
				</div>
				<div class="form-line border-bottom input-text input-medium">
					<label for="monto-asegurado">Monto Asegurado:</label>
					<input type="text" id="monto-asegurado" name="monto-asegurado">
				</div>
				<div class="form-line border-bottom input-text input-medium">
					<label for="precio-seguro">Precio del Seguro:</label>
					<input type="text" id="precio-seguro" name="precio-seguro">
				</div>
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

function getProductsByCategory($catid) {
	global $db;
	// Traer los seruguros según la categoría
	$db->where('categoria', $catid);
	$db->where('estado', 1);
	$seguros = $db->get('seguro');
	return $seguros;
}
?>