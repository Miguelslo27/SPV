<?php
require_once 'MysqliDb.php';
require_once './PHPMailer-master/PHPMailerAutoload.php';
require_once './helper.php';
require_once './functions.php';
require_once '../config.php';

// $modelo    = isset ($_POST['modelo']) ? $_POST['modelo'] : null;
$modelo    = isset ($_POST['data']) && isset ($_POST['data']['modelo']) ? $_POST['data']['modelo'] : null;
$categoria = isset ($_POST['data']) && isset ($_POST['data']['categoria']) ? $_POST['data']['categoria'] : null;
$seguros   = isset ($_POST['data']) && isset ($_POST['data']['seguros']) ? $_POST['data']['seguros'] : null;
$accion    = isset ($_POST['accion']) ? $_POST['accion'] : null;

$db = new MysqliDb ($dbsettings);

if (!empty ($accion)) {
	switch ($accion) {
		case 'asegurar':
			// Obtener la categoría
			$categoria = getCategoryByNameslug($categoria);
			// Obtener los seguros
			$seguros   = getProductsByCategory($categoria['id']);
			?>

<div class="center contratar">
	<div class="content-inner">
		<form id="asegurar">
			<div class="left-side-title">
				<span class="fa <?php echo $categoria['icono'] ?> left-side-icon"></span>
				<h3>
					<span><?php echo str_replace(' ', '</span><span>', $categoria['nombre']) ?></span>
				</h3>
			</div>
			<?php if (count($seguros)) : ?>
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
					<a href="#/seguro/<?php echo strtolower (sanear_string($categoria['nombre'])) ?>/cotizar" class="btn" data-objetformid="asegurar"><span>Continuar</span><span class="fa fa-angle-right"></span></a>
				</div>
			</div>
			<?php else : ?>
			<div class="no-category-content">
				<p>Aún no hay seguros para contratar, por favor, comunícate con nosotros por cualquier consulta <a href="#contacto" class="green-style">aquí</a>.</p>
				<p>El equpipo de <span class="site-title"><span class="seguro">Seguro</span><span class="para">Para</span><span class="vos">Vos</span></span>.</p>
				<p><a href="#/seguro/<?php echo strtolower (sanear_string($categoria['nombre'])) ?>/cancelar" class="green-style">Click aquí para cerrar</a>.</p>
			</div>
			<?php endif; ?>
			<div class="clear"></div>
		</form>
		<?php foreach ($seguros as $seguro) { ?>
			<?php
				$coberturas = json_decode($seguro['coberturas'], true);
				$premios    = json_decode($seguro['premio_anual'], true);
			?>
			<?php if (count($coberturas) || count($premios)) : ?>
			<div id="tablas-seg<?php echo $seguro['id']; ?>" class="tablas">
				<?php if (count($coberturas)) : ?>
				<div id="cobertura-seg<?php echo $seguro['id']; ?>" class="tabla">
					<table>
						<thead>
							<tr>
								<th colspan="2">Coberturas</th>
							</tr>
						</thead>
						<tbody>
							<?php foreach ($coberturas as $key => $value) { ?>
							<tr>
								<td class="tabla-label"><?php echo $key; ?></td>
								<td class="tabla-valor"><?php echo $value; ?></td>
							</tr>
							<?php } ?>
						</tbody>
					</table>
				</div>
				<?php endif; ?>
				<?php if (count($premios)) : ?>
				<div id="premios-seg<?php echo $seguro['id']; ?>" class="tabla">
					<table>
						<thead>
							<tr>
								<th colspan="2">Premio anual a pagar</th>
							</tr>
						</thead>
						<tbody>
							<?php foreach ($premios as $key => $value) { ?>
							<tr>
								<td class="tabla-label"><?php echo $key; ?></td>
								<td class="tabla-valor"><?php echo $value; ?></td>
							</tr>
							<?php } ?>
						</tbody>
					</table>
				</div>
				<?php endif; ?>
			</div>
			<?php endif; ?>
		<?php } ?>
	</div>
</div>

			<?php
		break;

		case 'cotizar':
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
		<form id="cotizar">
			<div class="left-side-title">
				<span class="fa <?php echo $categoria['icono'] ?> left-side-icon"></span>
				<h3>
					<span><?php echo str_replace(' ', '</span><span>', $categoria['nombre']) ?></span>
				</h3>
			</div>
			<?php if (count($atributos)) : ?> 
			<div class="form-inputs right-side-inputs">
				<h2><span class="number-globe">2</span> Ingresá tus datos y contratalo</h2>
				<div class="form-line required-message">
					<p>* Los campos marcados como requeridos (*) son obligatorios</p>
				</div>
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
					<a href="#/seguro/<?php echo strtolower (sanear_string($categoria['nombre'])) ?>/asegurar" class="btn left"><span class="fa fa-angle-left"></span><span>Atrás</span></a>
					<a href="#/seguro/<?php echo strtolower (sanear_string($categoria['nombre'])) ?>/contratar" class="btn" data-objetformid="cotizar"><span>Finalizar</span><span class="fa fa-angle-right"></span></a>
				</div>
			</div>
			<?php else : ?>
			<div class="no-category-content">
				<p>Aún no un formulario para cotizar, por favor, comunícate con nosotros por cualquier consulta <a href="#contacto" class="green-style">aquí</a>.</p>
				<p>El equpipo de <span class="site-title"><span class="seguro">Seguro</span><span class="para">Para</span><span class="vos">Vos</span></span>.</p>
				<p><a href="#/seguro/<?php echo strtolower (sanear_string($categoria['nombre'])) ?>/cancelar" class="green-style">Click aquí para cerrar</a>.</p>
			</div>
			<?php endif; ?>
			<div class="clear"></div>
		</form>
	</div>
</div>
		<?php
		break;
		case 'contratar':
			// Obtener la categoría
			$categoria = getCategoryByNameslug($categoria);
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
				<h2><span class="number-globe">3</span> Confirá tu pago y <span class="green-style">¡ya estás asegurado!</span></h2>
				<h3>Resumen de tu seguro</h3>
				<div class="push-60-left">
					<p>Seguro contratado: <strong>Seguro <span class="green-style">Móvil</span></strong>.</p>
					<p>Cobertura: <strong>Protección de Pantalla</strong>.</p>
				</div>
				<h3 class="green-style">Solicitud enviada</h3>
				<div class="form-line border-bottom"></div>
				<h3 class="green-style">Muchas gracias</h3>
				<div class="push-60-left">
					<p>El equipo de <span class="site-title"><span class="t-seguro">Seguro</span><span class="t-para">Para</span><span class="t-vos">Vos</span></span>.</p>
				</div>
				<div class="form-line">
					<a href="#/seguro/<?php echo strtolower (sanear_string($categoria['nombre'])) ?>/terminar" class="btn" data-objetformid="contratar"><span>Terminar</span><span class="fa fa-angle-right"></span></a>
				</div>
			</div>
		</form>
	</div>
</div>
<?php
		break;
	}
}

?>