<?php
if( isset($_POST['nome']) )
{
	$to = $empresa['mailContato']; // Replace with your email	
	$subject = 'Contato do Site: '.$empresa['dominio']; // Replace with your $subject
	$headers = 'From: ' . $_POST['email'] . "\r\n" . 'Reply-To: ' . $_POST['email'];	
	
	$message = 'Nome: ' . $_POST['nome'] . "\n" .
	           'E-mail: ' . $_POST['email'] . "\n" .
	           'Assunto: ' . $_POST['assunto'] . "\n" .
	           'Mensagem: ' . $_POST['mensagem'];
	
	   var_dump($empresa);
	mail($to, $subject, $message, $headers);	
	if( $_POST['copy'] == 'on' )
	{
		mail($_POST['email'], $subject, $message, $headers);
	}
}
?>