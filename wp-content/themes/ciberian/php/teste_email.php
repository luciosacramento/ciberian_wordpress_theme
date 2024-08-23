<?php
$para = "luciosacramento@gmail.com";
$assunto = "Teste de Envio de E-mail";
$mensagem = "Isso é um teste de envio de e-mail pelo PHP com Sendmail.";

$headers = "From: $para";

if (mail($para, $assunto, $mensagem, $headers)) {
    echo "E-mail enviado com sucesso!";
} else {
    echo "Erro ao enviar o e-mail.";
}
?>