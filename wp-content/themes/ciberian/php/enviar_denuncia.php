<?php
header("Content-Type: application/json");

$http_origin = $_SERVER['HTTP_ORIGIN'];

if ($http_origin == "https://www.ciberian.com.br" || $http_origin == "https://ciberian-site.vercel.app") {  
    // Define os cabeçalhos para todas as requisições
    header("Access-Control-Allow-Origin: $http_origin");
    header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
    header("Access-Control-Allow-Headers: Content-Type, Authorization");
    header("Access-Control-Allow-Credentials: true");

    // Se for uma requisição OPTIONS (preflight), finalize com um status 200
    if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
        // Certifique-se de configurar os mesmos cabeçalhos no preflight
        header("Access-Control-Allow-Origin: $http_origin");
        header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
        header("Access-Control-Allow-Headers: Content-Type, Authorization");
        header("Access-Control-Allow-Credentials: true");
        exit(0); // Finaliza a requisição com sucesso
    }
}

// Carregar o WordPress
define('WP_USE_THEMES', false);
$dir = dirname(__FILE__);
$dir = explode("wp-content", $dir);
$dir = $dir[0] . 'wp-blog-header.php';
require($dir);
$dir = dirname(__FILE__);
$dir = explode("wp-content", $dir);
$dir = $dir[0] . 'wp-load.php';
require($dir);
global $wp_query;

// Captura os dados do formulário
$nome     = sanitize_text_field($_POST["nome"]);
$sobrenome = sanitize_text_field($_POST["sobrenome"]);
$from     = "autentica.smtp@ciberian.com.br";
$assunto  = "Denúncia";
$remetente  = sanitize_email($_POST["email"]);
$message  = $_POST["comentario"] . '<br><br>De: ' . $nome . ' ' . $sobrenome;

$message = "";

$message  .= '<table style="width:100%; max-width:800px; font-family:arial; border:1px solid #eee">';
$message  .= '<tr>';
$message  .= '<th style="background:#eee; padding:10px; text-align:left">';
$message  .= '<b>Canal de Comunicação<b/>';
$message  .= '</th>';
$message  .= '</tr>';
        
$message  .= '<tr>';
$message  .= '<td style="background:#ebf2fa; padding:10px">';
$message  .= '<b>Canal<b/>';
$message  .= '</td>';
$message  .= '</tr>';
$message  .= '<tr>';
$message  .= '<td style="background:#fff; padding:10px">';
$message  .= 'Denúncia';
$message  .= '</td>';
$message  .= '</tr>';
        
$message  .= '<tr>';
$message  .= '<td style="background:#ebf2fa; padding:10px">';
$message  .= '<b>Nome<b/>';
$message  .= '</td>';
$message  .= '</tr>';
$message  .= '<tr>';
$message  .= '<td style="background:#fff; padding:10px">';
$message  .= $nome ." ".$sobrenome;
$message  .= '</td>';
$message  .= '</tr>';
        
$message  .= '<tr>';
$message  .= '<td style="background:#ebf2fa; padding:10px">';
$message  .= '<b>E-mail<b/>';
$message  .= '</td>';
$message  .= '</tr>';
$message  .= '<tr>';
$message  .= '<td style="background:#fff; padding:10px">';
$message  .= $remetente;
$message  .= '</td>';
$message  .= '</tr>';
                
$message  .= '<tr>';
$message  .= '<td style="background:#ebf2fa; padding:10px">';
$message  .= '<b>Assunto<b/>';
$message  .= '</td>';
$message  .= '</tr>';
$message  .= '<tr>';
$message  .= '<td style="background:#fff; padding:10px">';
$message  .=  $assunto;
$message  .= '</td>';
$message  .= '</tr>';
        
$message  .= '<tr>';
$message  .= '<td style="background:#ebf2fa; padding:10px">';
$message  .= '<b>Mensagem<b/>';
$message  .= '</td>';
$message  .= '</tr>';
$message  .= '<tr>';
$message  .= '<td style="background:#fff; padding:10px">';
$message  .= nl2br(sanitize_textarea_field($_POST["mensagem"]));
$message  .= '</td>';
$message  .= '</tr>';
        
$message  .= '</table>';

// Configurar cabeçalhos do e-mail
$headers = array(
    "From: $nome <$from>",
    "Reply-To: $nome $sobrenome <" . $remetente . ">",
    "Content-Type: text/html; charset=UTF-8" // Define o conteúdo como HTML
);

// E-mail de destino (configurado no WordPress)
$to = get_option('custom_email_denuncia');

// Título do e-mail
$title = $assunto ? 'Mensagem enviada do site: ' . $assunto : 'Mensagem enviada do site por ' . $nome;

// Enviar o e-mail
if (wp_mail($to, $title, $message, $headers)) {
    // Resposta de sucesso
    $response = array(
        'message' => 'Denúncia enviada com sucesso!',
        'status'  => 'ok',
    );
    http_response_code(200);
} else {
    // Resposta de erro
    $response = array(
        'message' => 'Falha ao enviar a Denúncia.',
        'status'  => '500',
        'to'      => $to,
        'title'   => $title,
        'message' => $message,
        'headers' => $headers,
    );
    http_response_code(500);
}

// Retorna a resposta como JSON
echo json_encode($response);

?>