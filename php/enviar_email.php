<?php
header("Content-Type: application/json");

$destinatario = "luciosacramento@gmail.com";
$assunto = "Contato pelo Site";

  $nome = $_POST["nome"];
  $email = $_POST["email"];
  $telefone = $_POST["telefone"];
  $mensagem = $_POST["mensagem"];
  $headers = "From: $nome <$email>". "\r\n" .
             "Reply-To: $email" . "\r\n" .
             "X-Mailer: PHP/" . phpversion();

  if(mail($destinatario, $assunto, $mensagem)){
    $response = ["success" => true];
  } else {
    $response = ["success" => false];
  }

echo json_encode($response);
?>