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
    register_post_type('colaboradores',
        array(
            'labels' => array(
                'name' => __('Colaboradores'),
                'singular_name' => __('Colaborador')
            ),
            'public' => true,
            'has_archive' => true,
            'supports' => array('title', 'editor', 'thumbnail'), // Título, descrição e imagem
            'rewrite' => array('slug' => 'colaboradores'),
        )
    );
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
