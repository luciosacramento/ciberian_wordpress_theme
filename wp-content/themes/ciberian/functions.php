<?php

function add_cors_http_header() {
    // Permitir requisições de uma origem específica
    header("Access-Control-Allow-Origin: https://ciberian-site.vercel.app"); // Substitua pelo seu domínio de desenvolvimento

    // Se você quiser permitir múltiplas origens, você pode usar um array para verificar a origem.
    /*
    $allowed_origins = ['http://localhost:3000', 'https://staging.example.com'];
    if (in_array($_SERVER['HTTP_ORIGIN'], $allowed_origins)) {
        header("Access-Control-Allow-Origin: " . $_SERVER['HTTP_ORIGIN']);
    }
    */

    // Permitir métodos HTTP específicos
    header("Access-Control-Allow-Methods: GET, POST, OPTIONS, PUT, DELETE");

    // Permitir cabeçalhos personalizados
    header("Access-Control-Allow-Headers: Content-Type, Authorization");

    // Permitir credenciais se necessário (cookies, autenticação HTTP)
    header("Access-Control-Allow-Credentials: true");

    // Retornar sucesso em respostas OPTIONS para evitar erros de CORS
    if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
        exit(0); // Finaliza o script para evitar outras saídas
    }
}

// Adicionar o CORS nas requisições REST API
add_action('rest_api_init', 'add_cors_http_header');

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

/****************Post customizado Documentos****************** */

function create_documentos_post_type() {
    $labels = array(
        'name'                  => _x('Documentos', 'Post Type General Name', 'textdomain'),
        'singular_name'         => _x('Documento', 'Post Type Singular Name', 'textdomain'),
        'menu_name'             => __('Documentos', 'textdomain'),
        'name_admin_bar'        => __('Documento', 'textdomain'),
        'archives'              => __('Arquivos de Documentos', 'textdomain'),
        'attributes'            => __('Atributos de Documentos', 'textdomain'),
        'parent_item_colon'     => __('Documento Pai:', 'textdomain'),
        'all_items'             => __('Todas Documentos', 'textdomain'),
        'add_new_item'          => __('Adicionar Nova Documento', 'textdomain'),
        'add_new'               => __('Adicionar Novo', 'textdomain'),
        'new_item'              => __('Nova Documento', 'textdomain'),
        'edit_item'             => __('Editar Documento', 'textdomain'),
        'update_item'           => __('Atualizar Documento', 'textdomain'),
        'view_item'             => __('Ver Documento', 'textdomain'),
        'view_items'            => __('Ver Documentos', 'textdomain'),
        'search_items'          => __('Buscar Documento', 'textdomain'),
        'not_found'             => __('Não encontrado', 'textdomain'),
        'not_found_in_trash'    => __('Não encontrado no Lixo', 'textdomain'),
        'featured_image'        => __('Imagem Destaque', 'textdomain'),
        'set_featured_image'    => __('Definir imagem destaque', 'textdomain'),
        'remove_featured_image' => __('Remover imagem destaque', 'textdomain'),
        'use_featured_image'    => __('Usar como imagem destaque', 'textdomain'),
        'insert_into_item'      => __('Inserir na Documento', 'textdomain'),
        'uploaded_to_this_item' => __('Enviado para esta Documento', 'textdomain'),
        'items_list'            => __('Lista de Documentos', 'textdomain'),
        'items_list_navigation' => __('Navegação da lista de Documentos', 'textdomain'),
        'filter_items_list'     => __('Filtrar lista de Documentos', 'textdomain'),
    );

    $args = array(
        'label'                 => __('Documento', 'textdomain'),
        'description'           => __('Tipo de post para Documentos', 'textdomain'),
        'labels'                => $labels,
        'supports'              => array('title', 'thumbnail'),
        'hierarchical'          => false,
        'public'                => true,
        'show_ui'               => true,
        'show_in_menu'          => true,
        'menu_position'         => 5,
        'show_in_admin_bar'     => true,
        'show_in_nav_menus'     => true,
        'can_export'            => true,
        'has_archive'           => true,
        'exclude_from_search'   => false,
        'publicly_queryable'    => true,
        'capability_type'       => 'post',
        
    );

    register_post_type('documentos', $args);
}
add_action('init', 'create_documentos_post_type', 0);


function add_documento_metabox() {
    add_meta_box(
        'documento_upload',
        'Upload de Documento',
        'documento_upload_callback',
        'documentos',
        'normal', 
        'high'
    );
}
add_action('add_meta_boxes', 'add_documento_metabox');

function documento_upload_callback($post) {
    wp_nonce_field(basename(__FILE__), 'documento_nonce');
    $file_id = get_post_meta($post->ID, 'documento_upload', true);
    ?>
    <input type="hidden" name="documento_upload" id="documento_upload_" value="<?php echo $file_id; ?>" />
    <input type="button" class="button button-secondary" id="upload_documento_button" value="Selecionar Documento" />
    <div id="documento_preview"><a target="_blank" href="<?php echo wp_get_attachment_url( $file_id ); ?>"><?php echo(basename( get_attached_file( $file_id ) ));?></a></div>
    <script>
        jQuery(document).ready(function($) {
            $('#upload_documento_button').click(function() {
                var file_frame = wp.media.frames.file_frame = wp.media({
                    title: '<?php _e('Selecionar Documento', 'textdomain'); ?>',
                    button: {
                        text: '<?php _e('Selecionar Documento', 'textdomain'); ?>'
                    },
                    multiple: false
                });

                file_frame.on('select', function() {
                    attachment = file_frame.state().get('selection').first().toJSON();
                    console.log(attachment);
                    document.getElementById('documento_upload_').value = attachment.id;
                    $('#documento_preview').html('<p><a target="_blank" href"'+attachment.url+'>' + attachment.title + '</a></p>');
                });

                file_frame.open();
            });
        });
    </script>
    <?php
}

function save_documento_meta($post_id) {

    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) return;
    if (!isset($_POST['documento_nonce']) || !wp_verify_nonce($_POST['documento_nonce'], basename(__FILE__))) return;
    if (isset($_POST['documento_upload'])) {
        echo($_POST['documento_upload']);
        update_post_meta($post_id, 'documento_upload', $_POST['documento_upload']);
    }
}
add_action('save_post', 'save_documento_meta');

/****Rest API***
http://localhost/cenoura/wp-json/custom/v1/documentos
*/

// Função para registrar a rota da API REST
function register_documentos_rest_route() {
    register_rest_route('custom/v1', '/documentos/', array(
        'methods'  => 'GET',
        'callback' => 'get_all_documentos_posts',
    ));
}
add_action('rest_api_init', 'register_documentos_rest_route');

// Função de callback para retornar os posts do tipo Soluções
function get_all_documentos_posts($data) {
    // Argumentos para a consulta WP_Query
    $args = array(
        'post_type'      => 'documentos',
        'post_status'    => 'publish',
        'posts_per_page' => -1,  // Retorna todos os posts
    );

    // Consulta WP_Query
    $query = new WP_Query($args);

    // Array para armazenar os resultados
    $posts_data = array();

    // Percorre os posts retornados pela consulta
    if ($query->have_posts()) {
        while ($query->have_posts()) {
            $query->the_post();

            $documento = wp_get_attachment_url( get_post_meta(get_the_ID(), 'documento_upload', true));

            // Adiciona cada post ao array
            $posts_data[] = array(
                'ID'           => get_the_ID(),
                'title'        => get_the_title(),
                'image_url'    => get_the_post_thumbnail_url(get_the_ID(), 'full'),
                'documento'    => !empty($documento) ? $documento : null,
            );
        }
        wp_reset_postdata();
    }

    // Retorna os posts como JSON
    return rest_ensure_response($posts_data);
}


/****************Post customizado Soluções****************** */

function create_solucoes_post_type() {
    $labels = array(
        'name'                  => _x('Soluções', 'Post Type General Name', 'textdomain'),
        'singular_name'         => _x('Solução', 'Post Type Singular Name', 'textdomain'),
        'menu_name'             => __('Soluções', 'textdomain'),
        'name_admin_bar'        => __('Solução', 'textdomain'),
        'archives'              => __('Arquivos de Soluções', 'textdomain'),
        'attributes'            => __('Atributos de Soluções', 'textdomain'),
        'parent_item_colon'     => __('Solução Pai:', 'textdomain'),
        'all_items'             => __('Todas Soluções', 'textdomain'),
        'add_new_item'          => __('Adicionar Nova Solução', 'textdomain'),
        'add_new'               => __('Adicionar Novo', 'textdomain'),
        'new_item'              => __('Nova Solução', 'textdomain'),
        'edit_item'             => __('Editar Solução', 'textdomain'),
        'update_item'           => __('Atualizar Solução', 'textdomain'),
        'view_item'             => __('Ver Solução', 'textdomain'),
        'view_items'            => __('Ver Soluções', 'textdomain'),
        'search_items'          => __('Buscar Solução', 'textdomain'),
        'not_found'             => __('Não encontrado', 'textdomain'),
        'not_found_in_trash'    => __('Não encontrado no Lixo', 'textdomain'),
        'featured_image'        => __('Imagem Destaque', 'textdomain'),
        'set_featured_image'    => __('Definir imagem destaque', 'textdomain'),
        'remove_featured_image' => __('Remover imagem destaque', 'textdomain'),
        'use_featured_image'    => __('Usar como imagem destaque', 'textdomain'),
        'insert_into_item'      => __('Inserir na Solução', 'textdomain'),
        'uploaded_to_this_item' => __('Enviado para esta Solução', 'textdomain'),
        'items_list'            => __('Lista de Soluções', 'textdomain'),
        'items_list_navigation' => __('Navegação da lista de Soluções', 'textdomain'),
        'filter_items_list'     => __('Filtrar lista de Soluções', 'textdomain'),
    );

    $args = array(
        'label'                 => __('Solução', 'textdomain'),
        'description'           => __('Tipo de post para soluções', 'textdomain'),
        'labels'                => $labels,
        'supports'              => array('title', 'editor', 'thumbnail'),
        'hierarchical'          => false,
        'public'                => true,
        'show_ui'               => true,
        'show_in_menu'          => true,
        'menu_position'         => 5,
        'show_in_admin_bar'     => true,
        'show_in_nav_menus'     => true,
        'can_export'            => true,
        'has_archive'           => true,
        'exclude_from_search'   => false,
        'publicly_queryable'    => true,
        'capability_type'       => 'post',
    );

    register_post_type('solucoes', $args);
}
add_action('init', 'create_solucoes_post_type', 0);


/****Rest API***
http://localhost/cenoura/wp-json/custom/v1/solucoes
*/

// Função para registrar a rota da API REST
function register_solucoes_rest_route() {
    register_rest_route('custom/v1', '/solucoes/', array(
        'methods'  => 'GET',
        'callback' => 'get_all_solucoes_posts',
    ));
}
add_action('rest_api_init', 'register_solucoes_rest_route');

// Função de callback para retornar os posts do tipo Soluções
function get_all_solucoes_posts($data) {
    // Argumentos para a consulta WP_Query
    $args = array(
        'post_type'      => 'solucoes',
        'post_status'    => 'publish',
        'posts_per_page' => -1,  // Retorna todos os posts
    );

    // Consulta WP_Query
    $query = new WP_Query($args);

    // Array para armazenar os resultados
    $posts_data = array();

    // Percorre os posts retornados pela consulta
    if ($query->have_posts()) {
        while ($query->have_posts()) {
            $query->the_post();

            // Adiciona cada post ao array
            $posts_data[] = array(
                'ID'           => get_the_ID(),
                'title'        => get_the_title(),
                'description'  => get_the_content(),
                'image_url'    => get_the_post_thumbnail_url(get_the_ID(), 'full'),
            );
        }
        wp_reset_postdata();
    }

    // Retorna os posts como JSON
    return rest_ensure_response($posts_data);
}

/****************Post customizado Colaboradores****************** */

// Registrar o tipo de conteúdo "Colaboradores"
function create_colaboradores_post_type() {

    $labels = array(
        'name'                  => _x('Colaboradores', 'Post Type General Name', 'textdomain'),
        'singular_name'         => _x('Colaborador', 'Post Type Singular Name', 'textdomain'),
        'menu_name'             => __('Colaboradores', 'textdomain'),
        'name_admin_bar'        => __('Colaborador', 'textdomain'),
        'archives'              => __('Arquivos de Colaboradores', 'textdomain'),
        'attributes'            => __('Atributos de Colaboradores', 'textdomain'),
        'parent_item_colon'     => __('Colaborador Pai:', 'textdomain'),
        'all_items'             => __('Todas Colaboradores', 'textdomain'),
        'add_new_item'          => __('Adicionar Novo Colaborador', 'textdomain'),
        'add_new'               => __('Adicionar Novo', 'textdomain'),
        'new_item'              => __('Novo Colaborador', 'textdomain'),
        'edit_item'             => __('Editar Colaborador', 'textdomain'),
        'update_item'           => __('Atualizar Colaborador', 'textdomain'),
        'view_item'             => __('Ver Colaborador', 'textdomain'),
        'view_items'            => __('Ver Colaboradores', 'textdomain'),
        'search_items'          => __('Buscar Colaborador', 'textdomain'),
        'not_found'             => __('Não encontrado', 'textdomain'),
        'not_found_in_trash'    => __('Não encontrado no Lixo', 'textdomain'),
        'featured_image'        => __('Imagem Destaque', 'textdomain'),
        'set_featured_image'    => __('Definir imagem destaque', 'textdomain'),
        'remove_featured_image' => __('Remover imagem destaque', 'textdomain'),
        'use_featured_image'    => __('Usar como imagem destaque', 'textdomain'),
        'insert_into_item'      => __('Inserir na Colaborador', 'textdomain'),
        'uploaded_to_this_item' => __('Enviado para esta Colaborador', 'textdomain'),
        'items_list'            => __('Lista de Colaboradores', 'textdomain'),
        'items_list_navigation' => __('Navegação da lista de Colaboradores', 'textdomain'),
        'filter_items_list'     => __('Filtrar lista de Colaboradores', 'textdomain'),
    );

    $args = array(
        'label'                 => __('Colaborador', 'textdomain'),
        'description'           => __('Tipo de post para Colaboradores', 'textdomain'),
        'labels'                => $labels,
        'supports'              => array('title', 'editor', 'thumbnail'),
        'hierarchical'          => false,
        'public'                => true,
        'show_ui'               => true,
        'show_in_menu'          => true,
        'menu_position'         => 5,
        'show_in_admin_bar'     => true,
        'show_in_nav_menus'     => true,
        'can_export'            => true,
        'has_archive'           => true,
        'exclude_from_search'   => false,
        'publicly_queryable'    => true,
        'capability_type'       => 'post',
    );
    
    register_post_type('colaboradores',$args);
}
add_action('init', 'create_colaboradores_post_type');

// Adicionar metabox para as redes sociais
function colaboradores_socials_metabox() {
    add_meta_box(
        'colaboradores_redes_sociais', 
        'Redes Sociais', 
        'colaboradores_redes_sociais_callback', 
        'colaboradores',
        'normal', 
        'high'
    );
}
add_action('add_meta_boxes', 'colaboradores_socials_metabox');

// Callback da metabox das redes sociais
function colaboradores_redes_sociais_callback($post) {
    wp_nonce_field('save_colaboradores_redes_sociais', 'colaboradores_redes_sociais_nonce');

    $redes_sociais = get_post_meta($post->ID, 'colaboradores_redes_sociais', true);

    echo '<div id="redes-sociais-wrapper">';
    if (!empty($redes_sociais) && is_array($redes_sociais)) {
        foreach ($redes_sociais as $rede) {
            echo '<div class="rede-social">
                    <label>Nome:</label>
                    <input type="text" name="colaboradores_redes_sociais_nome[]" value="' . esc_attr($rede['nome']) . '" />
                    <label>URL:</label>
                    <input type="url" name="colaboradores_redes_sociais_url[]" value="' . esc_attr($rede['url']) . '" />
                    <button type="button" class="remove-social">Remover</button>
                  </div>';
        }
    }
    echo '</div>';
    echo '<button type="button" id="add-rede-social">Adicionar Rede Social</button>';
}

// Salvar as redes sociais ao salvar o post
function save_colaboradores_redes_sociais($post_id) {
    if (!isset($_POST['colaboradores_redes_sociais_nonce']) ||
        !wp_verify_nonce($_POST['colaboradores_redes_sociais_nonce'], 'save_colaboradores_redes_sociais')) {
        return;
    }

    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
        return;
    }

    if (!current_user_can('edit_post', $post_id)) {
        return;
    }

    if (isset($_POST['colaboradores_redes_sociais_nome']) && isset($_POST['colaboradores_redes_sociais_url'])) {
        $redes_sociais = array();
        $nomes = $_POST['colaboradores_redes_sociais_nome'];
        $urls = $_POST['colaboradores_redes_sociais_url'];

        foreach ($nomes as $index => $nome) {
            if (!empty($nome) && !empty($urls[$index])) {
                $redes_sociais[] = array(
                    'nome' => sanitize_text_field($nome),
                    'url' => esc_url_raw($urls[$index])
                );
            }
        }

        update_post_meta($post_id, 'colaboradores_redes_sociais', $redes_sociais);
    } else {
        delete_post_meta($post_id, 'colaboradores_redes_sociais');
    }
}
add_action('save_post', 'save_colaboradores_redes_sociais');

// Adicionando o JavaScript para manipular o campo de redes sociais
function colaboradores_admin_scripts($hook) {
    if ('post.php' != $hook && 'post-new.php' != $hook) {
        return;
    }

    wp_enqueue_script('colaboradores-admin-js', get_template_directory_uri() . '/colaboradores-admin.js', array('jquery'), '1.0', true);
}
add_action('admin_enqueue_scripts', 'colaboradores_admin_scripts');


/****Rest API***
http://localhost/cenoura/wp-json/custom/v1/colaboradores
*/
// Registrar o tipo de conteúdo "Colaboradores"
// Função para registrar a rota da API REST
function register_colaboradores_rest_route() {
    register_rest_route('custom/v1', '/colaboradores/', array(
        'methods'  => 'GET',
        'callback' => 'get_all_colaboradores_posts',
    ));
}

add_action('rest_api_init', 'register_colaboradores_rest_route');

// Função de callback para retornar os posts do tipo Colaboradores
function get_all_colaboradores_posts($data) {
    // Argumentos para a consulta WP_Query
    $args = array(
        'post_type'      => 'colaboradores',
        'post_status'    => 'publish',
        'posts_per_page' => -1,  // Retorna todos os posts
    );

    // Consulta WP_Query
    $query = new WP_Query($args);

    // Array para armazenar os resultados
    $posts_data = array();

    // Percorre os posts retornados pela consulta
    if ($query->have_posts()) {
        while ($query->have_posts()) {
            $query->the_post();

            // Captura as redes sociais, se houver
            $social_media = get_post_meta(get_the_ID(), 'colaboradores_redes_sociais', true);

            // Adiciona cada post ao array
            $posts_data[] = array(
                'ID'           => get_the_ID(),
                'title'        => get_the_title(),
                'description'  => get_the_content(),
                'image_url'    => get_the_post_thumbnail_url(get_the_ID(), 'full'),
                'social_media' => !empty($social_media) ? $social_media : array(),  // Inclui as redes sociais, se houver
            );
        }
        wp_reset_postdata();
    }

    // Retorna os posts como JSON
    return rest_ensure_response($posts_data);
}



// Renderiza a página de configurações personalizada
function custom_settings_page_html() {
    if (!current_user_can('manage_options')) {
        return;
    }

    // Exibe a notificação de sucesso de alteração nas configurações
    if (isset($_GET['settings-updated'])) {
        add_settings_error('custom_messages', 'custom_message', 'Configurações salvas', 'updated');
    }
    settings_errors('custom_messages');
    
    // Formulário HTML
    echo '<div class="wrap">';
    echo '<h1>Localização e Contato</h1>';
    echo '<form action="options.php" method="post" enctype="multipart/form-data">';
    
    // Configurações gerais
    settings_fields('custom_settings');
    do_settings_sections('custom-settings');
    submit_button();
    
    echo '</form>';
    echo '</div>';
}

// 
/****************Registra as configurações e os campos****************** */
/// Registra as configurações e adiciona os campos na página de Configurações Gerais

function registrar_configuracoes_personalizadas(){
    register_rest_route('custom/v1', '/configuracoes-personalizadas/', array(
        'methods' => 'GET',
        'callback' => 'obter_configuracoes_personalizadas',
    ));
}

add_action('rest_api_init', 'registrar_configuracoes_personalizadas');

function obter_configuracoes_personalizadas() {
    $configuracoes = array(
        'marca' => wp_get_attachment_url(get_option('custom_brand')),
        'endereco' => get_option('custom_address'),
        'telefone' => get_option('custom_phone'),
        'email' => get_option('custom_email'),
        'email_denuncia' => get_option('custom_email_denuncia'),
    );

    return rest_ensure_response($configuracoes);
}


// Função de callback para o campo de upload de imagem de Marca
// Registra as configurações e adiciona os campos na página de Configurações Gerais
function custom_general_settings_register_fields() {
    // Adiciona o campo de upload de imagem para Marca
    add_settings_field(
        'custom_brand',                           // ID do campo
        'Marca (Imagem)',                         // Título do campo
        'custom_brand_field_cb',                  // Função de callback para renderizar o campo
        'general'                                 // Página do menu ("general" é para Configurações Gerais)
    );
    register_setting('general', 'custom_brand'); // Registra a configuração para o campo

    // Adiciona o campo de Endereço com editor de texto
    add_settings_field(
        'custom_address',                         // ID do campo
        'Endereço',                               // Título do campo
        'custom_address_field_cb',                // Função de callback para renderizar o campo
        'general'                                 // Página do menu
    );
    register_setting('general', 'custom_address'); // Registra a configuração para o campo

    // Adiciona o campo de Telefone
    add_settings_field(
        'custom_phone',                           // ID do campo
        'Telefone',                               // Título do campo
        'custom_phone_field_cb',                  // Função de callback para renderizar o campo
        'general'                                 // Página do menu
    );
    register_setting('general', 'custom_phone');  // Registra a configuração para o campo

    // Adiciona o campo de E-mail
    add_settings_field(
        'custom_email',                           // ID do campo
        'E-mail',                                 // Título do campo
        'custom_email_field_cb',                  // Função de callback para renderizar o campo
        'general'                                 // Página do menu
    );
    register_setting('general', 'custom_email');  // Registra a configuração para o campo

    // Adiciona o campo de E-mail da Denúncia
    add_settings_field(
        'custom_email_denuncia',                           // ID do campo
        'E-mail da Denúncia',                                 // Título do campo
        'custom_email_field_cb_denuncia',                  // Função de callback para renderizar o campo
        'general'                                 // Página do menu
    );
    register_setting('general', 'custom_email_denuncia');  // Registra a configuração para o campo

}
add_action('admin_init', 'custom_general_settings_register_fields');

// Função de callback para o campo de upload de imagem de Marca
function custom_brand_field_cb() {
    $brand = get_option('custom_brand');
    $image_url = $brand ? wp_get_attachment_url($brand) : '';
    ?>
    <div>
        <img id="brand-image" src="<?php echo esc_url($image_url); ?>" style="max-width: 150px; display: <?php echo $image_url ? 'block' : 'none'; ?>;" />
        <input type="hidden" id="custom_brand" name="custom_brand" value="<?php echo esc_attr($brand); ?>" />
        <button type="button" class="button" id="upload-brand-button">Upload Imagem</button>
        <button type="button" class="button" id="remove-brand-button" style="display: <?php echo $image_url ? 'inline-block' : 'none'; ?>;">Remover Imagem</button>
    </div>
    <script>
        jQuery(document).ready(function ($) {
            var mediaUploader;

            $('#upload-brand-button').click(function (e) {
                e.preventDefault();
                if (mediaUploader) {
                    mediaUploader.open();
                    return;
                }
                mediaUploader = wp.media({
                    title: 'Escolher Imagem',
                    button: {
                        text: 'Escolher Imagem'
                    },
                    multiple: false
                });
                mediaUploader.on('select', function () {
                    var attachment = mediaUploader.state().get('selection').first().toJSON();
                    $('#custom_brand').val(attachment.id);
                    $('#brand-image').attr('src', attachment.url).show();
                    $('#remove-brand-button').show();
                });
                mediaUploader.open();
            });

            $('#remove-brand-button').click(function (e) {
                e.preventDefault();
                $('#custom_brand').val('');
                $('#brand-image').hide();
                $(this).hide();
            });
        });
    </script>
    <?php
}

// Função de callback para o campo de Endereço com editor de texto
function custom_address_field_cb() {
    $address = get_option('custom_address');
    wp_editor($address, 'custom_address', array('textarea_name' => 'custom_address'));
}

// Função de callback para o campo de Telefone
function custom_phone_field_cb() {
    $phone = get_option('custom_phone');
    echo '<input type="text" name="custom_phone" value="' . esc_attr($phone) . '" />';
}

// Função de callback para o campo de E-mail
function custom_email_field_cb() {
    $email = get_option('custom_email');
    echo '<input type="email" name="custom_email" value="' . esc_attr($email) . '" />';
}


// Função de callback para o campo de E-mail Denúncia
function custom_email_field_cb_denuncia() {
    $email = get_option('custom_email_denuncia');
    echo '<input type="email" name="custom_email_denuncia" value="' . esc_attr($email) . '" />';
}



/****************Envio de e-mail****************** */

function register_envio_email() {
   
    register_rest_route( 'custom/v1', '/enviar-email/', array(
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
            'assunto' => array(
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

add_action('rest_api_init', 'register_envio_email');

function enviar_email( $data ) {
    $from = $data['remetente'];
    $telefone = $data['telefone'];
    $nome = $data['nome'];
    $assunto = $data['assunto'];
    $to = get_option('custom_email');
    $message = $data['mensagem'].'<br><br>De: '.$nome.'<br><br>Telefone: '.$telefone;

    $headers = "From: $nome <$from>". "\r\n" .
               "Reply-To: $from" . "\r\n" .
               "X-Mailer: PHP/" . phpversion();

    $title = $assunto ? 'Mensagem enviada do site: '.$assunto : 'Mensagem enviada do site por '.$nome;           
    $result = wp_mail( $to, $title, $message, $headers );

    if ( $result ) {
        $response =  rest_ensure_response( array( 'message' => 'E-mail enviado com sucesso!','status' => 'ok' ) );
        $response->set_status( 200 );
    } else {
        $response =  rest_ensure_response( array( 'message' => 'Falha ao enviar o e-mail.','status' => '500' ) );
        $response->set_status( 500 );
    }

    return $response;
}


/****************Envio de Denúncia****************** */

function register_envio_denuncia() {
   
    register_rest_route( 'custom/v1', '/enviar-denuncia/', array(
        'methods' => 'POST',
        'callback' => 'enviar_denuncia',
        'args' => array(
            'email' => array(
                'required' => true,
                'validate_callback' => function($param, $request, $key) {
                    return is_email($param);
                }
            ),
            'sobrenome' => array(
                'required' => false,
            ),
            'nome' => array(
                'required' => false,
            ),
            'comentario' => array(
                'required' => true,
            ),
        ),
    ) );

}

add_action('rest_api_init', 'register_envio_denuncia');

function enviar_denuncia( $data ) {
    $from = $data['email'];

    $to = get_option('custom_email_denuncia');

    if(!$from || $from == '') {
        $from = $to;
    }

    $nome = $data['nome'];

    if(!$nome || $nome == '') {
        $nome = 'Anônimo';
    }

    $sobrenome = $data['sobrenome'];
    $to = get_option('email_field_denuncia');
    $message = $data['comentario'].'<br><br>De: '.$nome.' '.$sobrenome;

    $headers = "From: $nome <$from>". "\r\n" .
               "Reply-To: $from" . "\r\n" .
               "X-Mailer: PHP/" . phpversion();

    $title = $assunto ? 'Denúncia enviada do site: '.$assunto : 'Denúncia enviada do site por '.$nome.' '.$sobrenome;          
    $result = wp_mail( $to, $title, $message, $headers );

    if ( $result ) {
        $response =  rest_ensure_response( array( 'message' => 'Denúncia enviada com sucesso!','status' => 'ok' ) );
        $response->set_status( 200 );
    } else {
        $response =  rest_ensure_response( array( 'message' => 'Falha ao enviar a Denúncia.','status' => '500' ) );
        $response->set_status( 500 );
    }

    return $response;
}


/****************Pegar pagina****************** */

function add_pagina_interna_meta_box() {
    add_meta_box(
        'pagina_interna_meta_box',       // ID do Meta Box
        'Página Interna',                // Título do Meta Box
        'render_pagina_interna_meta_box', // Função de callback para renderizar o conteúdo
        'page',                          // Tipo de conteúdo (post type)
        'side',                          // Localização do Meta Box (side, normal, advanced)
        'default'                        // Prioridade de carregamento
    );
}
add_action('add_meta_boxes', 'add_pagina_interna_meta_box');

function render_pagina_interna_meta_box($post) {
    // Recuperar valor salvo (se houver)
    $pagina_interna_value = get_post_meta($post->ID, '_pagina_interna', true);

    // Criar o campo checkbox
    echo '<input type="checkbox" id="pagina_interna" name="pagina_interna" value="1" '. checked(1, $pagina_interna_value, false) .'/>';
    echo '<label for="pagina_interna"> Página Interna</label>';
}

function save_pagina_interna_meta_box($post_id) {
    // Verificar se o campo foi definido
    if (isset($_POST['pagina_interna'])) {
        update_post_meta($post_id, '_pagina_interna', 1); // Salvar valor 1 (marcado)
    } else {
        update_post_meta($post_id, '_pagina_interna', 0); // Salvar valor 0 (não marcado)
    }
}
add_action('save_post', 'save_pagina_interna_meta_box');


function register_pagina() {
    register_rest_route('custom/v1', '/pagina/(?P<id>\d+)', array(
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
}

add_action('rest_api_init', 'register_pagina');

function obter_pagina_por_id($data) {
    $pagina_id = $data['id'];
    $pagina = get_post($pagina_id);

    if ($pagina) {
        $resposta = array(
            'id' => $pagina->ID,
            'slug' => $pagina->post_name,
            'titulo' => $pagina->post_title,
            'resumo' => $pagina->post_excerpt,
            'conteudo' => apply_filters('the_content', $pagina->post_content),
            'pagina_interna' => (bool) get_post_meta(get_the_ID(), '_pagina_interna', true)
            // Adicione outros campos personalizados conforme necessário
        );
        return rest_ensure_response($resposta);
    } else {
        return new WP_Error('nao_encontrado', 'Página não encontrada ou não publicada', array('status' => 404));
    }
}

// Função para registrar a rota da API REST para pegar uma página pelo slug
function register_custom_page_by_slug_rest_route() {
    register_rest_route('custom/v1', '/pagina/slug/(?P<slug>[a-zA-Z0-9-]+)', array(
        'methods'  => 'GET',
        'callback' => 'get_custom_page_by_slug',
        'args' => array(
            'slug' => array(
                'validate_callback' => function($param, $request, $key) {
                    return is_string($param);
                }
            ),
        ),
    ));
}
add_action('rest_api_init', 'register_custom_page_by_slug_rest_route');

// Função de callback para retornar a página com base no slug
function get_custom_page_by_slug($data) {
    $slug = $data['slug'];
    $pagina = get_page_by_path($slug, OBJECT, 'page');

    if ($pagina) {

        $resposta = array(
            'id' => $pagina->ID,
            'slug' => $pagina->post_name,
            'titulo' => $pagina->post_title,
            'resumo' => $pagina->post_excerpt,
            'conteudo' => apply_filters('the_content', $pagina->post_content),
            'pagina_interna' => (bool) get_post_meta(get_the_ID(), '_pagina_interna', true)
            // Adicione outros campos personalizados conforme necessário
        );
        return rest_ensure_response($resposta);

    } else {
        // Retorna um erro caso a página não seja encontrada ou não esteja publicada
        return new WP_Error('no_page', 'Página não encontrada ou não publicada', array('status' => 404));
    }
}

/****************Pegar paginas****************** */

function register_paginas() {
    register_rest_route('custom/v1', '/paginas/', array(
        'methods' => 'GET',
        'callback' => 'obter_paginas',
        'args' => array(
            'id' => array(
                'validate_callback' => function($param, $request, $key) {
                    return is_string($param);
                }
            ),
        ),
    ));
}

add_action('rest_api_init', 'register_paginas');

function obter_paginas() {

    // Argumentos para a consulta WP_Query
    $args = array(
        'post_type'      => 'page',
        'post_status'    => 'publish',
        'posts_per_page' => -1,  // Retorna todos os posts
    );

    // Consulta WP_Query
    $query = new WP_Query($args);

    $resposta = array();

    if ($query->have_posts()) {
        while ($query->have_posts()) {
            $query->the_post();
            $resposta[] = array(
                'id' => get_the_ID(),
                'slug' => get_post_field('post_name', get_the_ID()),  
                'titulo' => get_the_title(),
                'resumo' => get_the_excerpt(),
                'conteudo' => apply_filters('the_content', get_the_content()),
                'pagina_interna' => (bool) get_post_meta(get_the_ID(), '_pagina_interna', true), 
            );
        }
        wp_reset_postdata();

        return rest_ensure_response($resposta);
    } else {
        return new WP_Error('nao_encontrado', 'Não exitem páginas.', array('status' => 404));
    }
}