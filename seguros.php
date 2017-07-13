<?php
require_once './lib/MysqliDb.php';
require_once './lib/PHPMailer-master/PHPMailerAutoload.php';
require_once './lib/helper.php';
require_once 'config.php';

$db = new MysqliDb ($dbsettings);

$db->where('estado', 1);
$categorias = $db->get('categoria');

if (count($categorias)) { ?>
<div class="clear">
<?php
foreach ($categorias as $categoria) {
	?>
	<div class="seguro <?php echo sanear_string(strtolower (str_replace (' ', '_', $categoria['nombre']))) ?>">
		<span class="fa fa-5x <?php echo $categoria['icono'] ?> rounded"></span>
		<h3 class="titulo-seguro">
			<span><?php echo str_replace(' ', '</span><span>', $categoria['nombre']) ?></span>
		</h3>
		<ul>
			<li>
				<i class="fa fa-check-circle"></i><?php echo str_replace (array (', ', '[', ']'), array ('</li><li><i class="fa fa-check-circle"></i>', '<br><span class="comentario">', '</span>'), $categoria['caracteristicas']); ?>
			</li>
		</ul>
		<a href="#/seguro/<?php echo strtolower (sanear_string($categoria['nombre'])) ?>/asegurar" class="btn">Contratar <span class="fa fa-angle-right"></span></a>
	</div>
	<?php
}
?>
</div>
<?php
} else { ?>
<div class="no-category-content">
	<p>Aún no hay seguros para contratar, por favor, comunícate con nosotros por cualquier consulta <a href="#contacto" class="green-style">aquí</a>.</p>
	<p>El equpipo de <span class="site-title"><span class="seguro">Seguro</span><span class="para">Para</span><span class="vos">Vos</span></span></p>
</div>
<?php } ?>