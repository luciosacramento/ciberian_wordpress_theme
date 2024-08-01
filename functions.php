<?php

header("Content-Type: application/json");


/*function adicionar_tamanho_imagem_personalizado() {
    add_image_size( 'imagem-530x353', 530, 353, true ); // Largura, altura, cortar?
}
add_action( 'after_setup_theme', 'adicionar_tamanho_imagem_personalizado' );
*/

add_action( 'after_setup_theme', 'theme_setup' );

function theme_setup() {
    add_action( 'init', 'add_support_to_pages' );
}

function add_support_to_pages() {
    add_post_type_support( 'page', 'excerpt' );
    unregister_post_type('post');
}

add_theme_support( 'post-thumbnails' );

function ocultar_tipo_de_post_do_menu() {
    remove_menu_page('edit.php');
    remove_menu_page('edit-comments.php');
}

add_action('admin_menu', 'ocultar_tipo_de_post_do_menu');

/****************Post customizado LOJAS****************** */

function registrar_lojas() {
    $labels = array(
        'name'               => 'Lojas',
        'singular_name'      => 'Loja',
        'menu_name'          => 'Lojas',
        'name_admin_bar'     => 'Lojas',
        'add_new'            => 'Adicionar Novo',
        'add_new_item'       => 'Adicionar Novo loja',
        'new_item'           => 'Novo loja',
        'edit_item'          => 'Editar loja',
        'view_item'          => 'Visualizar  loja',
        'all_items'          => 'Todos os loja',
        'search_items'       => 'Pesquisar loja',
        'parent_item_colon'  => 'Meus loja Pai:',
        'not_found'          => 'Nenhum loja encontrado.',
        'not_found_in_trash' => 'Nenhum loja encontrado na lixeira.'
    );

    $args = array(
        'labels'             => $labels,
        'public'             => true,
        'publicly_queryable' => true,
        'show_ui'            => true,
        'show_in_menu'       => true,
        'query_var'          => true,
        'rewrite'            => array( 'slug' => 'lojas' ),
        'capability_type'    => 'post',
        'has_archive'        => true,
        'hierarchical'       => false,
        'menu_position'      => null,
        'supports'           => array( 'title',  'thumbnail' ),
    );

    register_post_type( 'lojas', $args );
}
add_action( 'init', 'registrar_lojas' );

function adicionar_campos_personalizados() {
    add_meta_box(
        'meus_campos',
        'Meus Campos Personalizados',
        'exibir_campos_personalizados',
        'lojas',
        'normal',
        'default'
    );
}
add_action( 'add_meta_boxes', 'adicionar_campos_personalizados' );
add_action( 'save_post', 'myplugin_save_postdata' );

function exibir_campos_personalizados( $post ) {
    // Recupere os valores salvos dos campos personalizados

    $area_atuacao = get_post_meta( $post->ID, 'area_atuacao', true );
    $rede_social = get_post_meta( $post->ID, 'rede_social', true );
    $link_rede_social = get_post_meta( $post->ID, 'link_rede_social', true );
    $tipo_rede_social = get_post_meta( $post->ID, 'tipo_rede_social', true );
    $telefone = get_post_meta( $post->ID, 'telefone', true );

    
    ?>
     
    <label for="area_atuacao">Área:</label><br>
    <input type="text" id="area_atuacao" name="area_atuacao" value="<?php echo esc_attr( $area_atuacao ); ?>" /><br><br>

    <label for="rede_social">Rede social:</label><br>
    <input type="text" id="rede_social" name="rede_social" value="<?php echo esc_attr( $rede_social ); ?>" /><br><br>

    <label for="link_rede_social">link da Rede Social:</label><br>
    <input type="text" id="link_rede_social" name="link_rede_social" value="<?php echo esc_attr( $link_rede_social ); ?>" /><br><br>

    <label for="tipo_rede_social">Tipo da Rede Social:</label><br>
    <select id="tipo_rede_social" name="tipo_rede_social">
        <option value="instagram" <?php selected( $tipo_rede_social, 'instagram' ); ?>>Instagram</option>
        <option value="facebook" <?php selected( $tipo_rede_social, 'facebook' ); ?>>Facebook</option>
        <option value="site" <?php selected( $tipo_rede_social, 'site' ); ?>>Site</option>
        <option value="twitter" <?php selected( $tipo_rede_social, 'twitter' ); ?>>Twitter</option>
    </select><br><br>

    <label for="telefone">Telefone:</label><br>
    <input type="text" id="telefone" name="telefone" value="<?php echo esc_attr( $telefone ); ?>" /><br><br>


    <?php
}

/* When the post is saved, saves our custom data */
function myplugin_save_postdata( $post_id ) {



    // verify if this is an auto save routine. 
    // If it is our form has not been submitted, so we dont want to do anything
    if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) 
        return;
  
    // verify this came from the our screen and with proper authorization,
    // because save_post can be triggered at other times
    if ( ( isset ( $_POST['myplugin_noncename'] ) ) && ( ! wp_verify_nonce( $_POST['myplugin_noncename'], plugin_basename( __FILE__ ) ) ) )
        return;
  
    // Check permissions
    if ( ( isset ( $_POST['post_type'] ) ) && ( 'lojas' == $_POST['post_type'] )  ) {
      if ( ! current_user_can( 'edit_page', $post_id ) ) {
        return;
      }    
    }
    else {
      if ( ! current_user_can( 'edit_post', $post_id ) ) {
        return;
      }
    }

    $fields = array( 'area_atuacao','rede_social', 'link_rede_social', 'tipo_rede_social','telefone');
    foreach ($fields as $key => $value) {
        if ($value === 'tipo_rede_social') {
            // Lidar com o campo tipo_rede_social como um campo de seleção (select)
            if ( isset( $_POST['tipo_rede_social'] ) ) {
                update_post_meta( $post_id, 'tipo_rede_social', sanitize_text_field( $_POST['tipo_rede_social'] ) );
            }
        } else {
            // Lidar com os outros campos como antes
            updateCustomField($value,$post_id);
        }
    }   
  
  }

  function updateCustomField($slug,$post_id){
    if ( isset ( $_POST[$slug] ) ) {
        update_post_meta( $post_id, $slug, $_POST[$slug] );
    }
  }

  /****************FIM - Post customizado LOJAS****************** */
 
  /****************Adicionando campo personalizados em Configurações****************** */

    // Função para exibir os campos personalizados no formulário de configurações
function exibir_campos_personalizados_configuracao() {
    ?>
    <h2>Configurações Personalizadas</h2>
    <form action="options.php" method="post">
        <?php settings_fields( 'configuracoes-personalizadas' ); ?>
        <?php do_settings_sections( 'configuracoes-personalizadas' ); ?>
        <table class="form-table">
            <tr valign="top">
                <th scope="row">Email:</th>
                <td><input type="text" name="email_field" value="<?php echo esc_attr( get_option( 'email_field' ) ); ?>" /></td>
            </tr>
            <tr valign="top">
                <th scope="row">Telefone:</th>
                <td><input type="text" name="telefone_field" value="<?php echo esc_attr( get_option( 'telefone_field' ) ); ?>" /></td>
            </tr>
            <tr valign="top">
                <th scope="row">instagram:</th>
                <td><textarea name="instagram_field"><?php echo esc_textarea( get_option( 'instagram_field' ) ); ?></textarea></td>
            </tr>
            <tr valign="top">
                <th scope="row">Endereço:</th>
                <td><textarea name="endereco_field"><?php echo esc_textarea( get_option( 'endereco_field' ) ); ?></textarea></td>
            </tr>
            <tr valign="top">
                <th scope="row">Mapa:</th>
                <td><textarea name="mapa_field" ><?php echo esc_attr( get_option( 'mapa_field' ) ); ?></textarea></td>
            </tr>
            <tr valign="top">
                <th scope="row">Horário de funcionamento:</th>
                <td><textarea name="horario_funcionamento_field"><?php echo esc_attr( get_option( 'horario_funcionamento_field' ) ); ?></textarea></td>
            </tr>
           
        </table>
        <?php submit_button(); ?>
    </form>
    <?php
}

// Adicionar as configurações personalizadas ao menu
function adicionar_pagina_configuracoes_personalizadas() {
    add_options_page( 'Configurações Personalizadas', 'Configurações Personalizadas', 'manage_options', 'configuracoes-personalizadas', 'exibir_campos_personalizados_configuracao' );
}
add_action( 'admin_menu', 'adicionar_pagina_configuracoes_personalizadas' );

// Registrar os campos personalizados e seus valores
function registrar_campos_personalizados() {
    // Registrar campos para salvar
    register_setting( 'configuracoes-personalizadas', 'email_field' );
    register_setting( 'configuracoes-personalizadas', 'telefone_field' );
    register_setting( 'configuracoes-personalizadas', 'instagram_field' );
    register_setting( 'configuracoes-personalizadas', 'endereco_field' );
    register_setting( 'configuracoes-personalizadas', 'horario_funcionamento_field' );
    register_setting( 'configuracoes-personalizadas', 'mapa_field' );
}
add_action( 'admin_init', 'registrar_campos_personalizados' );

// Funções para exibir os campos nos formulários
function exibir_email_field() {
    echo '<input type="text" name="email_field" value="' . esc_attr( get_option( 'email_field' ) ) . '" />';
}
function exibir_telefone_field() {
    echo '<input type="text" name="telefone_field" value="' . esc_attr( get_option( 'telefone_field' ) ) . '" />';
}
function exibir_instagram_field() {
    echo '<textarea name="instagram_field">' . esc_textarea( get_option( 'instagram_field' ) ) . '</textarea>';
}
function exibir_endereco_field() {
    echo '<textarea name="endereco_field">'.esc_textarea( get_option( 'endereco_field' ) ).'</textarea>';
}
function exibir_horario_funcionamento_field() {
    echo '<textarea name="horario_funcionamento_field" >' . esc_attr( get_option( 'horario_funcionamento_field' ) ) . '</textarea>';
}
function exibir_mapa_field() {
    echo '<textarea name="mapa_field">' . esc_attr( get_option( 'mapa_field' ) ) . '</textarea>';
}


  /****************FIM - Adicionando campo personalizados em Configurações****************** */

  /**************** Rest API LOJAS****************** */

  function adicionar_cors_headers() {
    header( 'Access-Control-Allow-Origin: *' );
    header( 'Access-Control-Allow-Methods: GET,POST' );
    header( 'Access-Control-Allow-Credentials: true' );
    header( 'Access-Control-Expose-Headers: Link' );
}

add_action( 'rest_api_init', function() {
    add_filter( 'rest_pre_serve_request', 'adicionar_cors_headers' );
}, 15 );

 function registrar_endpoint_lojas() {
    register_rest_route('lojas/v1', '/lista/', array(
        'methods' => 'GET',
        'callback' => 'obter_lojas',
        'args' => array(
            'busca' => array(
                'validate_callback' => function($param, $request, $key) {
                    return is_string($param);
                }
            ),
        ),
    ));
    register_rest_route('lojas/v1', '/pagina/', array(
        'methods' => 'GET',
        'callback' => 'obter_pagina_por_id',
        'args' => array(
            'id' => array(
                'validate_callback' => function($param, $request, $key) {
                    return is_string($param);
                }
            ),
        ),
    ));

    register_rest_route('lojas/v1', '/configuracoes-personalizadas/', array(
        'methods' => 'GET',
        'callback' => 'obter_configuracoes_personalizadas',
    ));

    register_rest_route( 'lojas/v1', '/enviar-email/', array(
        'methods' => 'POST',
        'callback' => 'enviar_email',
        'args' => array(
            'remetente' => array(
                'required' => true,
                'validate_callback' => function($param, $request, $key) {
                    return is_email($param);
                }
            ),
            'telefone' => array(
                'required' => false,
            ),
            'nome' => array(
                'required' => false,
            ),
            'mensagem' => array(
                'required' => true,
            ),
        ),
    ) );

}

add_action('rest_api_init', 'registrar_endpoint_lojas');

function obter_lojas($data) {

    if($data['busca'] && $data['busca'] != ''){
        $busca = $data['busca'];
        $args = array(
            'post_type' => 'lojas',
            'posts_per_page' => -1,
            'post_status' => 'publish', 
            's' => $busca // Realiza a pesquisa no título e conteúdo do post
        );
    
        $lojas = get_posts($args);
    
        $args = array(
            'post_type' => 'lojas',
            'posts_per_page' => -1,
            'meta_query' => array(
                array(
                    'key' => 'area_atuacao',
                    'value' => $busca,
                    'compare' => 'LIKE',
                ),
            ),
            'post_status' => 'publish', 
        );
    
        $lojas2 = get_posts($args);
    
        $lojas = array_merge( $lojas2, $lojas );
    
        $lojas = array_unique($lojas);
    }else{
        $args = array(
            'post_type' => 'lojas',
            'posts_per_page' => -1,
            'post_status' => 'publish', 
        );
        $lojas = get_posts($args);

    }

    $resposta = array();

    foreach ($lojas as $loja) {
        $imagem_url = get_the_post_thumbnail_url($loja->ID);
        $resposta[] = array(
            'id' => $loja->ID,
            'titulo' => $loja->post_title,
            'area_atuacao' => get_post_meta($loja->ID, 'area_atuacao', true),
            'imagem_url' => $imagem_url,
            'rede_social' => get_post_meta($loja->ID, 'rede_social', true),
            'link_rede_social' => get_post_meta($loja->ID, 'link_rede_social', true),
            'tipo_rede_social' => get_post_meta($loja->ID, 'tipo_rede_social', true),
            'telefone' => get_post_meta($loja->ID, 'telefone', true),
        );
    }

    return rest_ensure_response($resposta);
}

function obter_pagina_por_id($data) {
    $pagina_id = $data['id'];
    $pagina = get_post($pagina_id);

    if ($pagina) {
        $resposta = array(
            'id' => $pagina->ID,
            'titulo' => $pagina->post_title,
            'conteudo' => apply_filters('the_content', $pagina->post_content),
            // Adicione outros campos personalizados conforme necessário
        );
        return rest_ensure_response($resposta);
    } else {
        return new WP_Error('nao_encontrado', 'Página não encontrada', array('status' => 404));
    }
}

function obter_configuracoes_personalizadas() {
    $configuracoes = array(
        'email' => get_option('email_field'),
        'telefone' => get_option('telefone_field'),
        'instagram' => get_option('instagram_field'),
        'endereco' => get_option('endereco_field'),
        'horario_funcionamento' => get_option('horario_funcionamento_field'),
        'mapa' => get_option('mapa_field'),
    );

    return rest_ensure_response($configuracoes);
}

add_filter('wp_mail_content_type', function( $content_type ) {
    return 'text/html';
});

function enviar_email( $data ) {
    $from = $data['remetente'];
    $telefone = $data['telefone'];
    $nome = $data['nome'];
    $to = get_option('email_field');
    $message = $data['mensagem'].'<br><br>De: '.$nome.'<br><br>Telefone: '.$telefone;

    $headers = "From: $nome <$from>". "\r\n" .
               "Reply-To: $from" . "\r\n" .
               "X-Mailer: PHP/" . phpversion();

    $result = wp_mail( $to, "Mensagem enviada do site por ".$nome, $message, $headers );

    if ( $result ) {
        return rest_ensure_response( array( 'message' => 'E-mail enviado com sucesso!','status' => 'ok' ) );
    } else {
        return rest_ensure_response( array( 'message' => 'Falha ao enviar o e-mail.','status' => 'error' ) );
    }
}

 /**************** FIM - Rest API LOJAS****************** */