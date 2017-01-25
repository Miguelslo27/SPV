<!DOCTYPE HTML>
<html>
<head>
	<title>SeguroParaVos</title>
	<meta charset="UTF-8">
	<link rel="icon" type="image/png" href="/imagenes/logos/favicon.png"/>
	<link rel="stylesheet" type="text/css" href="/estilos/estilo.css">
	<link rel="stylesheet" type="text/css" href="/estilos/font-awesome.min.css">
</head>

<body id="_home">
	<header>
		<div class="center">
			<div class="logo_contenedor">
				<a href="#home"><img class="logo" src="/imagenes/logos/logo.png"></a>
				<a href="#home"><img class="logo_solo" src="/imagenes/logos/logo_solo.png"></a>
			</div>
			<div class="navegacion_contenedor">
				<nav class="navegacion">
					<a class="hover_verde" href="#seguro">Seguros</a>
					<a class="hover_verde" href="#nosotros">Quiénes somos</a>
					<a class="hover_verde" href="#aseguradoras">Aseguradoras</a>
					<a class="hover_verde" href="#comopagar">Como Pagar</a>
					<a class="fa fa-envelope hover_verde" href="#contacto"></a>
				</nav>
			</div>
		</div>	
	</header>

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

		<div class="seguros_contenedor">
			<div class="center seguros" id="_seguro">
				<div class="sombra"></div>
				<div class="seguros_inner">
					<div class="categorias">
						<?php
						// Cargar categorías de seguros desde la base de datos en el siguiente template
						include 'seguros.php';
						?>
					</div>
					<div class="formularios">Algo por aquí?</div>
				</div>
			</div>
		</div>

		<div class="center pasos_seguro_contenedor">
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
						<span>Ingresá tus datos y contratalo</span>
					</h3>
				</div>
				<span class="connect mirror"><img src="/imagenes/recursos/flecha-design.png"></span>
				<div class="paso tercer_paso">
					<span class="fa rounded">3 <span class="fa fa-check-circle"></span></span>
					<h3 class="titulo-seguro">
						<span>Confirmá tu pago y<br><strong>¡ya estás asegurado!</strong></span>
					</h3>
				</div>
				<div class="clear"></div>
			</div>
		</div>

		<div class="center nosotros" id="_nosotros">
			<div class="sombra"></div>
			<div class="content-inner">
				<h2>Qué hacemos</h2>	
				<p>Apuntamos a ofrecer una nueva experiencia en la contratación de seguros, sin trámites, en 3 rápidos  pasos y desde la comodidad de tu dispositivo electrónico.</p>
				<p><span class="site-title"><span class="seguro">Seguro</span><span class="para">Para</span><span class="vos">Vos</span></span> brinda una solución integral, para contratar tu seguro de una forma sencilla y ágil, respaldando a sus clientes desde la asesoría para la contratación hasta la gestión de un siniestro. Para esto, contamos con un equipo, listo para ayudarte.</p>
				<h2>Misión</h2>	
				<p>Somos líderes en innovación en la industria de seguros, nuestra tecnología nos permite procesos más veloces y eficaces mejorando el servicio brindado al cliente.</p>
				<p>Todos nuestros esfuerzos se centran en seguir mejorando la interacción con el cliente y aumentar la gama de servicios disponibles bajo este nuevo formato de comercialización de seguros.</p>
				<h2>Visión</h2>	
				<p>Queremos ser reconocidos como un equipo de trabajo profesional, enfocados en la mejora continua, con tecnología de punta y satisfaciendo las expectativas de nuestros clientes, por medio de una atención personalizada.</p>
				<p>Reflejando así las bases de una empresa en continuo desarrollo, para alcanzar los más altos estándares de sinergia con las compañías aseguradoras y  satisfacción con el cliente.</p>
			</div>
		</div>

		<div class="center aseguradoras" id="_aseguradoras">
			<div class="content-inner">
				<h2>Compañías que confían en nosotros</h2>
				<p>En <span class="site-title"><span class="seguro">Seguro</span><span class="para">Para</span><span class="vos">Vos</span></span> te ofrecemos el abanico más amplio de compañías aseguradoras para que puedas elegir y comparar con nuestro multicotizador online la que más se adapte a tus necesidades.</p>
				<p>Contamos con el mayor respaldo financiero de todas las compañias aseguradoras que solo te puede brindar el broker online lider del país.</p>
			</div>
		</div>

		<div class="center comopagar" id="comopagar">
			<div class="sombra"></div>
			<div class="content-inner">
				<h2>Formas de pago de acuerdo al producto</h2>
				<p></p>
			</div>
		</div>

		<!-- <div class="center razones" id="_razones">
			<div class="sombra"></div>
			<div class="content-inner">
				<h2>10 Razones para contratar <span class="site-title"><span class="seguro">Seguro</span><span class="para">Para</span><span class="vos">Vos</span></span></h2>	
				<h4>#1 / CONTRATACIÓN ONLINE</h4>
				<p>SegurosOnline te ofrece la posibilidad de contratar desde el primer momento. ¡Sin necesidad de registrarte! Introduces los datos, obtienes un presupuesto y contratas online o telefónicamente.</p>
				<h4>#2 / FÁCIL</h4>
				<p>Simple, sencilla, directa. Sin necesidad de largos cuestionarios, basta rellenar unos pocos datos, sin identificación personal alguna, pulsar aceptar y el resultado de la cotización salta a tu pantalla en unos pocos segundos. Las características funcionales y de ahorro de tiempo de SegurosOnline, le permitirán una introducción de datos mínima para la presentación de una oferta clara y comparable, facilitándote una toma de decisión. Ante cualquier duda ya sea por chat on-line o por línea telefónica lo guiarán gustosamente.</p>
				<h4>#3 / ESTÁNDAR</h4>
				<p>Te encuentras en un sitio web, dinámico, para que fácilmente puedas optar por la cobertura que más se adapta a su necesidad y posibilidad. Enlazamos online a las compañías de seguros seleccionadas, agilizamos los procesos y minimizamos su trabajo de búsqueda y comparación. El uso de un estándar del sistema te evita equívocos a la hora de comparar las diferentes coberturas. También te evita interpretaciones erróneas de coberturas, exclusiones, y las condiciones de diferentes contratos.</p>
				<h4>#4 / ATENCIÓN PERSONALIZADA POST-CONTRATACIÓN</h4>
				<p>Nuestros profesionales atienden y resuelven tus dudas de forma personalizada dedicándote todo el tiempo que precises. Además, SegurosOnline te ayudará a ser mucho más ágil en el tiempo de respuesta y acción ante emergencias o situaciones críticas. Nosotros siempre estamos y estaremos a tu lado, desde el momento que ingresas a nuestro sitio y fundamentalmente cuando te surja cualquier percance.</p>
				<h4>#5 / GARANTÍA DE COMPROMISO</h4>
				<p>El tener contratado un buen seguro es clave en tu tranquilidad. Trabajamos con las mejores aseguradoras para ofrecerte el seguro que mejor se adapte a tus necesidades. Tenemos en cuenta las opiniones de nuestros clientes y tanto para ellos como para ti hemos desarrollado segurosonline.com.uy, la web más eficaz e innovadora del mercado. Cuando es necesario solventar un problema, lo último que deseas es que te pongan obstáculos inoportunos, por ello, nuestros profesionales realizan todas las acciones necesarias para resolver con la mayor eficiencia tu problema.</p>
				<h4>#6 / SOLVENCIA, PROFESIONALIDAD, FIABILIDAD Y CONFIANZA</h4>
				<p>Uno de nuestros principales principios es la seriedad en todos nuestros actos, perseguimos con ello tu credibilidad. Porque la relación lleva a la confianza y el creciente conocimiento mutuo, nos permitirá ofrecerte los mejores productos. Nuestra experiencia y profesionalidad sumada a la solvencia de las compañías seleccionadas, primeras marcas nacionales y pertenecientes a los primeros grupos internacionales en seguros, es la garantía que tú necesitas.</p>
				<h4>#7 / EXCELENTES COBERTURAS Y SERVICIOS</h4>
				<p>La selección de la compañía, nuestras normas de calidad propias, la dedicación de nuestros profesionales, el conocimiento y estudio permanente del mercado nos permiten disponer de unas ofertas en coberturas adaptadas a sus necesidades. Todos nuestros procesos se revisan continua y exhaustivamente, incluyendo toda la documentación que le entregamos al contratar la póliza. Disponemos de diferentes tecnologías integradas entre sí para darte siempre el mejor servicio.</p>
				<h4>#8 / SEGURIDAD</h4>
				<p>El acceso a los servicios transaccionales y aquellos que incluyen la captura de datos personales cuando contratas con nosotros se realiza en un entorno seguro utilizando protocolos por demás confiables. El servidor seguro establece una conexión de modo que la información se transmite cifrada. Esto asegura que el contenido transmitido es sólo inteligible entre tu computadora y nuestro servidor.</p>
				<h4>#9 / PRIVACIDAD</h4>
				<p>Manteniendo siempre nuestra independencia, estamos integrados con las aseguradoras para permitir trabajar sin esfuerzos adicionales a nuestros profesionales con información online sobre las situaciones a analizar. Tenemos establecidos los mejores flujos de trabajo mediante la integración con los centros operativos de siniestros, respetando en todo momento tu privacidad.</p>
				<h4>#10 / LA ÚLTIMA RAZÓN LA TIENES TÚ</h4>
				<p>Esperamos que estas reflexiones te animen a dar el click y contratar a través de SegurosOnline el mejor seguro que se adapte a tus necesidades. Y si aún no te animas a contratar con nosotros te agradecemos el tiempo que le has dedicado a conocer nuestra Web.</p>
			</div>
		</div> -->

		<div class="center contacto" id="_contacto">
			<!-- <div class="sombra"></div> -->
			<div class="content-inner">
				<form action="/send-email.php">
					<div class="left-side-title">
						<span class="fa fa-envelope left-side-icon"></span>
						<h3>Contacto</h3>
					</div>
					<div class="form-inputs right-side-inputs">
						<h2>Envianos tu consulta o comentario</h2>
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
	</section>

	<footer>
		<div class="footer_navegacion center">
			<div class="footer_nav">
				<a class="hover_verde" href="#nosotros">Quiénes somos</a>
				<span class="separador">|</span>
				<a class="hover_verde" href="#razones">Por qué elegirnos</a>
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
				<div>© 2016 / SeguroParaVos/Larraura Seguros. Todos los derechos reservados.</div>
				<div>Términos y condiciones / Declaración de privacidad</div>
			</div>
			<div class="redes_sociales">
				<a class="fa fa-instagram rounded hover_blanco" href="http://www.instagram.com" target="blank"></a>
				<a class="fa fa-facebook rounded hover_blanco" href="http://www.facebook.com" target="blank"></a>
				<a class="fa fa-twitter rounded hover_blanco" href="http://www.twitter.com" target="blank"></a>
				<a class="fa fa-youtube-play hover_blanco" href="http://www.youtube.com" target="blank"></a>
			</div>
		</div>
		<div class="derechos_apssxxi">
			<a class="hover_blanco" href="htttp://www.appsxxi.com">AppsXXI</a><span> - www.seguroparavos.com - Todos los derechos reservados 2016</span>
		</div>
	</footer>

	<a class="scroll-up fa fa-angle-up" href="#home" style="display: inline;"></a>

	<script src="/script/jquery.js"></script>
	<script src="/script/jquery.jcarousel.min.js"></script>
	<script src="/script/general.js"></script>
</body>
</html>