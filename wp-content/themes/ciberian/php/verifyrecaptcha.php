<?php

header("Access-Control-Allow-Origin: *");
    
    $http_origin = $_SERVER['HTTP_ORIGIN'];

if ($http_origin == "https://www.ciberian.com.br" || $http_origin == "https://ciberian-site.vercel.app")
{  
    header("Access-Control-Allow-Origin: $http_origin");
}

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

        define('WP_USE_THEMES', false);
        $dir = dirname(__FILE__);
        $dir = explode("wp-content", $dir);
        $dir = $dir[0] . 'wp-blog-header.php';

        require($dir);
        global $wp_query;
        
header("Content-Type: application/json");

$recaptcha_token = isset($_POST['token']) ? sanitize_text_field($_POST['token']) : '';
    
    if (!$recaptcha_token) {
        wp_send_json_error(['message' => 'Token do reCAPTCHA não enviado','token' => $_POST['token']]);
        return;
    }

    $secret_key = '6LfIK5QqAAAAABol2vxiKYL1ShC4ytlyu-A8HtII';
    $url = 'https://www.google.com/recaptcha/api/siteverify';

    $response = wp_remote_post($url, [
        'body' => [
            'secret' => $secret_key,
            'response' => $recaptcha_token,
        ],
    ]);

    if (is_wp_error($response)) {
        wp_send_json_error(['message' => 'Erro ao validar o reCAPTCHA']);
        return;
    }

    $response_body = wp_remote_retrieve_body($response);
    $result = json_decode($response_body, true);

    if ($result['success'] === true && $result['score'] >= 0.5) {
        wp_send_json_success(['message' => 'reCAPTCHA validado com sucesso'],200);
    } else {
        wp_send_json_error(['message' => 'Falha ao validar o reCAPTCHA']);
    }
?>