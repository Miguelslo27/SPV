<?php
require_once 'MysqliDb.php';
require_once './PHPMailer-master/PHPMailerAutoload.php';
require_once './helper.php';
require_once './functions.php';
require_once '../config.php';

// $modelo    = isset ($_POST['modelo']) ? $_POST['modelo'] : null;
$modelo     = isset ($_POST['data']) && isset ($_POST['data']['modelo']) ? $_POST['data']['modelo'] : null;
$categoria  = isset ($_POST['data']) && isset ($_POST['data']['categoria']) ? $_POST['data']['categoria'] : null;
$seguro     = isset ($_POST['data']) && isset ($_POST['data']['seguro']) ? $_POST['data']['seguro'] : null;
$poliza     = isset ($_POST['data']) && isset ($_POST['data']['poliza']) ? $_POST['data']['poliza'] : null;
$cotizacion = isset ($_POST['data']) && isset ($_POST['data']['cotizacion']) ? $_POST['data']['cotizacion'] : null;
$usuario    = isset ($_POST['data']) && isset ($_POST['data']['usuario']) ? $_POST['data']['usuario'] : null;

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
				<h2 class="tablet desktop"><span class="number-globe">1</span> <span class="number-text">Elegí tu seguro y cotizalo</span></h2>
				<h2 class="mobile"><span class="number-globe">1</span> <span class="number-text">Elegí tu seguro</span></h2>
				<div class="clear"></div>
				<div class="form-line required-message"></div>
				<?php foreach ($seguros as $seg) { ?>
					<?php $seg_sano = strtolower (sanear_string(str_replace (' ', '_', $seg['nombre']))) ?>
					<div class="form-line border-bottom input-check">
						<input
						 type="checkbox"
						 data-parent="<?php echo $seg['pertenencia'] ?>"
						 data-price="<?php echo $seg['valor'] ?>"
						 data-currency="<?php echo $seg['moneda']; ?>"
						 id="<?php echo $seg_sano ?>"
						 name="<?php echo $seg_sano ?>"
						 value="<?php echo $seg['id'] ?>">
						<label for="<?php echo $seg_sano ?>"><?php echo $seg['nombre'] ?></label>
					</div>
				<?php } ?>
				<div class="form-line form-buttons tablet desktop">
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
		<?php foreach ($seguros as $seg) { ?>
			<?php
				$coberturas = json_decode($seg['coberturas'], true);
				$premios    = json_decode($seg['premio_anual'], true);
			?>
			<?php if (count($coberturas) || count($premios)) : ?>
			<div id="tablas-seg<?php echo $seg['id']; ?>" class="tablas">
				<?php if (count($coberturas)) : ?>
				<div id="cobertura-seg<?php echo $seg['id']; ?>" class="tabla">
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
				<div id="premios-seg<?php echo $seg['id']; ?>" class="tabla">
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
				<div class="form-line form-buttons mobile">
					<a href="#/seguro/<?php echo strtolower (sanear_string($categoria['nombre'])) ?>/cancelar" class="btn left"><span class="fa fa-angle-left"></span><span>Cancelar</span></a>
					<a href="#/seguro/<?php echo strtolower (sanear_string($categoria['nombre'])) ?>/cotizar" class="btn" data-objetformid="asegurar"><span>Continuar</span><span class="fa fa-angle-right"></span></a>
				</div>
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
			$atributos      = getAttributesByParentID($seguro);
			// Obtener el objeto seguro
			$segruro_ob     = getProductById($seguro['id']);

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
				<h2 class="tablet desktop"><span class="number-globe">2</span> <span class="number-text">Ingresá tus datos y contratalo</span></h2>
				<h2 class="mobile"><span class="number-globe">2</span> <span class="number-text">Datos del seguro</span></h2>
				<div class="clear"></div>
				<div class="form-line required-message">
					<p>* Los campos marcados como requeridos (*) son obligatorios</p>
				</div>

				<?php if (count($atr_usuarios)) : ?>
				<!-- Atributos modelo usuario -->
				<h3>Ingresa tus datos personales</h3>
				<div class="form-line border-bottom"></div>
				<?php
				foreach ($atr_usuarios as $atindx => $atributo) {
					getAttributeHTML($atributo);
				}
				?>
				<?php endif; ?>
				
				<?php if (count($atr_poliza)) : ?>
				<!-- Atributos modelo poliza -->
				<h3>Ingresa datos de la póliza</h3>
				<div class="form-line border-bottom"></div>
				<?php
				foreach ($atr_poliza as $atindx => $atributo) {
					getAttributeHTML($atributo);
				}
				?>
				<?php endif; ?>

				<?php if (count($atr_cotizacion)) : ?>
				<!-- Atributos modelo cotizacion -->
				<h3>Ingresa datos de cotización</h3>
				<div class="form-line border-bottom"></div>
				<?php
				foreach ($atr_cotizacion as $atindx => $atributo) {
					getAttributeHTML($atributo);
				}
				?>
				<?php endif; ?>

				<div class="form-line border-bottom input-text input-medium input-required">
					<label for="adjuntar">Adjuntar Comprobantes:</label>
					<input
					 type="text"
					 id="adjuntar"
					 name="adjuntar"
					 data-realname="Adjuntar Comprobantes"
					 data-customrequired="true">
				</div>
				<?php if (isset($segruro_ob['condiciones']) && $segruro_ob['condiciones'] != '') : ?>
				<div class="form-line border-bottom input-check right-message input-required">
					<input
					 type="checkbox"
					 id="terminos"
					 name="terminos"
					 value="true"
					 data-realname="Términos y condiciones"
					 data-customrequired="true">
					<label for="terminos">Acepto los <a href="http://dev.backoffice.seguroparavos.com.uy/<?php echo $segruro_ob['condiciones']; ?>">Términos y condiciones</a></label>
				</div>
				<?php endif; ?>

				<div class="form-line border-bottom input-text input-medium">
					<label><strong>Precio del seguro:</strong> </label>
					<label class="upper_text"><strong><span><?php echo $seguro['moneda']; ?></span> <span id="precio_seguro_original" class="hidden"><?php echo $seguro['precio']; ?></span><span id="precio_seguro"><?php echo $seguro['precio']; ?></span></strong></label>
				</div>
				<div class="form-line form-buttons">
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
				<h2><span class="number-globe">3</span> <span class="number-text">Solicitud procesada</span></h2>
				<div class="clear"></div>
				<h3>Resumen de tu seguro</h3>
				<div class="push-60-left">
					<p>Seguro solicitado: <span><strong><?php echo str_replace(' ', '</strong> <strong>', $categoria['nombre']); ?></strong></span>.</p>
					<p>Cobertura: <span><strong><?php echo str_replace(' ', '</strong> <strong>', $seguro['nombre']); ?></strong></span>.</p>
					<p>Precio de la cotización: <span><strong><?php echo $seguro['moneda'].' '.$seguro['precio']; ?></strong></span></p>
				</div>
				<h3 class="green-style">Solicitud enviada</h3>
				<div class="push-60-left">
					<p>En 24 horas hábiles sera contactado por el equipo de <strong>Larraura Seguros</strong>.</p>
				</div>
				<div class="form-line border-bottom"></div>
				<h3 class="green-style">Muchas gracias</h3>
				<div class="push-60-left">
					<p>El equipo de <span class="site-title"><span class="t-seguro">Seguro</span><span class="t-para">Para</span><span class="t-vos">Vos</span></span>.</p>
				</div>
				<div class="form-line form-buttons">
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