<?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {
  // Receba os dados do formulário
  $email = $_POST["email"];
  $assunto = $_POST["assunto"];
  $mensagem = $_POST["mensagem"];

  // Configurar o destinatário do e-mail
  $destinatario = "teste@teste.com";

  // Montar o cabeçalho do e-mail
  $cabecalho = "De: $email\r\n";
  $cabecalho .= "Assunto: $assunto\r\n";

  // Enviar o e-mail
  mail($destinatario, $assunto, $mensagem, $cabecalho);

  // Redirecionar de volta para a página de suporte após o envio
  header("Location: suporte.php?enviado=1");
  exit;
}
