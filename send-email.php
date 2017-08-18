<?php
require_once 'lib/functions.php';
require_once 'lib/PHPMailer-master/PHPMailerAutoload.php';

$mailSent = sendEmailContact(array(
  'email' => $_POST['email'],
  'name' => $_POST['nombre'].' '.$_POST['apellido']
), $_POST['message']);

$response = ($mailSent ? 'SUCCESS' : 'ERROR');

header('Location: /?status='.$response.'#contacto');
?>