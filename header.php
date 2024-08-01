<!doctype html>
<html lang="pt-BR">
    <head>        

        <title><?php
            if (is_single()) {
                single_post_title();
            } elseif (is_home() || is_front_page()) {
                bloginfo('name');
                print ' | ';
                bloginfo('description');
            } elseif (is_page()) {
                bloginfo('name');
                print ' | ';
                single_post_title('');
            } elseif (is_search()) {
                bloginfo('name');
                print ' | Busca por ' . wp_specialchars($s);
            } elseif (is_404()) {
                bloginfo('name');
                print ' | Não encontrado';
            } else {
                bloginfo('name');
                wp_title('|');
            }
            ?>
        </title>
        <meta http-equiv="x-ua-compatible" content="IE=9" >
        </head>

<body>
