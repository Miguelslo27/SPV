<?php
require_once './lib/MysqliDb.php';
require_once './lib/PHPMailer-master/PHPMailerAutoload.php';
require_once './lib/helper.php';
require_once 'config.php';

$db = new MysqliDb ($dbsettings);

$db->where('estado', 1);
$categorias = $db->get('categoria');
?>
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
		<a href="#/seguro/<?php echo strtolower (sanear_string($categoria['nombre'])) ?>/cotizar" class="btn">Contratar <span class="fa fa-angle-right"></span></a>
	</div>
	<?php
}
?>
</div>
<p class="comentario_seguros">(*) Todos los seguros incluyen emergencias domiciliarias (cerrajeria, vidriería, electricidad, sanitaria)</p>