<!DOCTYPE HTML>
<html>
<head>
	<title>SeguroParaVos</title>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">

	<link rel="icon" type="image/png" href="/imagenes/logos/favicon.png"/>
	<link rel="stylesheet" type="text/css" href="/estilos/font-awesome.min.css">
	<link rel="stylesheet" type="text/css" href="/estilos/estilo.css">
	<link rel="stylesheet" type="text/css" href="/estilos/estilo.res.css">
</head>

<body id="_home">
	<header>
		<div class="center">
			<div class="logo_contenedor">
				<a href="#home">
					<img class="logo desktop" src="/imagenes/logos/logo.png">
					<img class="logo_solo mobile tablet" src="/imagenes/logos/logo_solo.png">
				</a>
			</div>
			<div class="navegacion_contenedor">
				<nav class="navegacion desktop">
					<a class="hover_verde" href="#seguro">Seguros</a>
					<a class="hover_verde" href="#nosotros">Quiénes somos</a>
					<a class="hover_verde" href="#aseguradoras">Aseguradoras</a>
					<a class="hover_verde" href="#pagos">Como Pagar</a>
					<a class="fa fa-envelope hover_verde" href="#contacto"></a>
				</nav>
				<nav class="navegacion tablet">
					<a class="hover_verde" href="#seguro">Seguros</a>
					<a class="fa fa-envelope hover_verde" href="#contacto"></a>
					<a class="hover_verde fa fa-bars" href="#" data-target="menu-tablet"></a>
					<div id="menu-tablet" class="nav-menu hidden">
						<a class="hover_verde" href="#nosotros">Quiénes somos</a>
						<a class="hover_verde" href="#aseguradoras">Aseguradoras</a>
						<a class="hover_verde" href="#pagos">Como Pagar</a>
					</div>
				</nav>
				<nav class="navegacion mobile">
					<a class="hover_verde fa fa-bars" href="#" data-target="menu-mobile"></a>
				</nav>
			</div>
		</div>	
	</header>

	<div id="menu-mobile" class="nav-menu mobile hidden">
		<a class="hover_verde" href="#seguro">Seguros</a>
		<a class="hover_verde" href="#nosotros">Quiénes somos</a>
		<a class="hover_verde" href="#aseguradoras">Aseguradoras</a>
		<a class="hover_verde" href="#pagos">Como Pagar</a>
		<a class="hover_verde" href="#contacto"><span class="fa fa-envelope"></span>Contacto</a>
		<a class="fa fa-times close-menu" href="#" data-target="menu-mobile"></a>
	</div>

	<section class="pagina">
		<div id="_quienes_somos" class="center quienes_somos_contenedor">
			<div class="slider-banner" id="slider-banner">
				<ul>
					<li>
						<img src="/imagenes/banners/01.png" usemap="#servicios-express">
					</li>
					<li>
						<img src="/imagenes/banners/02.png" usemap="#servicios-express">
					</li>
					<li>
						<img src="/imagenes/banners/03.png" usemap="#servicios-express">
					</li>
				</ul>
			</div>
			<p><span class="site-title"><span class="seguro">Seguro</span><span class="para">Para</span><span class="vos">Vos</span></span> es una plataforma de Larraura Seguros, quien con más de 15 años de experiencia en el mercado asegurador crea <span class="site-title"><span class="seguro">Seguro</span><span class="para">Para</span><span class="vos">Vos</span></span> para brindar soluciones innovadoras en la contratación de seguros.</p>
		</div>

		<div class="seguros_contenedor sombreado">
			<div class="center seguros" id="_seguro">
				<div class="seguros_inner">
					<div class="categorias">
						<?php
						// Cargar categorías de seguros desde la base de datos en el siguiente template
						include 'seguros.php';
						?>
					</div>
					<div class="formularios"></div>
				</div>
			</div>
		</div>

		<div class="pasos_seguro_contenedor desktop">
			<div class="center">
				<h2 class="rotulo_pasos">Pasos para contratar tu seguro</h2>
				<div class="pasos_seguro">
					<div class="paso primer_paso">
						<span class="fa rounded">1 <span class="fa fa-check-circle"></span></span>
						<h3 class="titulo-seguro">
							<span>Elegí tu seguro</span>
						</h3>
					</div>
					<span class="connect"><img src="/imagenes/recursos/flecha-design.png"></span>
					<div class="paso segundo_paso">
						<span class="fa rounded">2 <span class="fa fa-check-circle"></span></span>
						<h3 class="titulo-seguro">
							<span>Ingresá tus datos</span>
						</h3>
					</div>
					<span class="connect mirror"><img src="/imagenes/recursos/flecha-design.png"></span>
					<div class="paso tercer_paso">
						<span class="fa rounded">3 <span class="fa fa-check-circle"></span></span>
						<h3 class="titulo-seguro">
							<span>¡Contratalo!</span>
						</h3>
					</div>
					<div class="clear"></div>
				</div>
			</div>
		</div>

		<div class="nosotros sombreado" id="_nosotros">
			<div class="center">
				<div class="content-inner">
					<!-- NOSOTROS -->
					<h2>Qué hacemos</h2>	
					<p>Apuntamos a ofrecer una nueva experiencia en la contratación de seguros, sin trámites, en 3 rápidos  pasos y desde la comodidad de tu dispositivo electrónico.</p>
					<p><span class="site-title"><span class="seguro">Seguro</span><span class="para">Para</span><span class="vos">Vos</span></span> brinda una solución integral, para contratar tu seguro de una forma sencilla y ágil, respaldando a sus clientes desde la asesoría para la contratación hasta la gestión de un siniestro. Para esto, contamos con un equipo, listo para ayudarte.</p>
					<!-- MISION -->
					<h2>Misión</h2>	
					<p>Somos líderes en innovación en la industria de seguros, nuestra tecnología nos permite procesos más veloces y eficaces mejorando el servicio brindado al cliente.</p>
					<p>Todos nuestros esfuerzos se centran en seguir mejorando la interacción con el cliente y aumentar la gama de servicios disponibles bajo este nuevo formato de comercialización de seguros.</p>
					<!-- VISION -->
					<h2>Visión</h2>	
					<p>Queremos ser reconocidos como un equipo de trabajo profesional, enfocados en la mejora continua, con tecnología de punta y satisfaciendo las expectativas de nuestros clientes, por medio de una atención personalizada.</p>
					<p>Reflejando así las bases de una empresa en continuo desarrollo, para alcanzar los más altos estándares de sinergia con las compañías aseguradoras y  satisfacción con el cliente.</p>
					<!-- COMPAÑÍAS QUE CONFÍAN EN NOSOTROS -->
					<h2 id="_aseguradoras">Compañías que confían en nosotros</h2>
					<p>En <span class="site-title"><span class="seguro">Seguro</span><span class="para">Para</span><span class="vos">Vos</span></span> contamos con el mayor respaldo financiero de todas las compañias aseguradoras que solo te puede brindar el broker online lider del país.</p>
					<p>
						<img src="/imagenes/logos/brands/mapfre.png" class="aseguradora" alt="Mapfre">
						<img src="/imagenes/logos/brands/bse.png" class="aseguradora" alt="Banco de Seguros del Estado">
					</p>
					<!-- FORMAS DE PAGO -->
					<h2 id="_pagos">Formas de pago de acuerdo al producto</h2>
					<p>
						<img src="/imagenes/logos/brands/redpagos.png" class="pagos" alt="RedPagos">
						<img src="/imagenes/logos/brands/abitab.png" class="pagos" alt="Abitab">
						<img src="/imagenes/logos/brands/visa.png" class="pagos" alt="Visa">
						<img src="/imagenes/logos/brands/master.png" class="pagos" alt="MasterCard">
				</div>
			</div>
		</div>

		<div class="contacto" id="_contacto">
			<div class="center">
				<div class="content-inner">
					<form action="/send-email.php">
						<div class="left-side-title">
							<span class="fa fa-envelope left-side-icon"></span>
							<h3>Contacto</h3>
						</div>
						<div class="form-inputs right-side-inputs">
							<h2 class="tablet desktop">Envianos tu consulta o comentario</h2>
							<h2 class="mobile">Envianos tu consulta</h2>
							<div class="form-line input-text input-large">
								<label for="nombre">Nombre:</label>
								<input type="text" id="nombre">
							</div>
							<div class="form-line input-text input-large">
								<label for="apellido">Apellido:</label>
								<input type="text" id="apellido">
							</div>
							<div class="form-line input-text input-large">
								<label for="email">e-Mail:</label>
								<input type="text" id="email">
							</div>
							<div class="form-line input-textarea">
								<label for="message">Consulta o Comentario:</label>
								<textarea id="message"></textarea>
							</div>
							<a href="/" class="btn">Enviar <span class="fa fa-angle-right"></span></a>
						</div>
						<div class="clear"></div>
					</form>
				</div>
			</div>
		</div>
	</section>

	<footer>
		<div class="footer_navegacion center">
			<div class="footer_nav">
				<a class="hover_verde" href="#nosotros">Quiénes somos</a>
				<span class="separador">|</span>
				<a class="hover_verde" href="#contacto">Contacto</a>
			</div>
			<div class="logo_footer">
				<a href="#home"><img src="/imagenes/logos/logo-footer.png"></a>
			</div>
			<div class="clear"></div>
		</div>
		<div class="center">
			<div class="derechos_reservados">
				<div>© 2017 / SeguroParaVos/Larraura Seguros. Todos los derechos reservados.</div>
				<div>Términos y condiciones / Declaración de privacidad</div>
			</div>
			<div class="redes_sociales">
				<a class="fa fa-instagram rounded hover_blanco" href="http://www.instagram.com" target="blank"></a>
				<a class="fa fa-facebook rounded hover_blanco" href="http://www.facebook.com" target="blank"></a>
				<a class="fa fa-twitter rounded hover_blanco" href="http://www.twitter.com" target="blank"></a>
				<a class="fa fa-youtube-play rounded hover_blanco" href="http://www.youtube.com" target="blank"></a>
			</div>
		</div>
		<div class="derechos_apssxxi tablet desktop">
			<a class="hover_blanco" href="http://www.appsxxi.com">AppsXXI</a><span> - www.seguroparavos.com - Todos los derechos reservados 2017</span>
		</div>
		<div class="derechos_apssxxi mobile">
			<a class="hover_blanco" href="http://www.appsxxi.com">AppsXXI</a><span> - Todos los derechos reservados 2017</span>
		</div>
	</footer>

	<a class="scroll-up fa fa-angle-up" href="#home" style="display: inline;"></a>

	<script src="/script/jquery.js"></script>
	<script src="/script/jquery.jcarousel.min.js"></script>
	<script src="/script/general.js"></script>
</body>
</html>