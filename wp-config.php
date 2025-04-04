<?php
/**
 * The base configuration for WordPress
 *
 * The wp-config.php creation script uses this file during the installation.
 * You don't have to use the website, you can copy this file to "wp-config.php"
 * and fill in the values.
 *
 * This file contains the following configurations:
 *
 * * Database settings
 * * Secret keys
 * * Database table prefix
 * * ABSPATH
 *
 * @link https://wordpress.org/documentation/article/editing-wp-config-php/
 *
 * @package WordPress
 */

// ** Database settings - You can get this info from your web host ** //
/** The name of the database for WordPress */
define( 'DB_NAME', 'wp_cenoura' );

/** Database username */
define( 'DB_USER', 'root' );

/** Database password */
define( 'DB_PASSWORD', '' );

/** nome do host do MySQL */
//define('DB_HOST', '10.15.246.42');
define( 'DB_HOST', 'localhost' );

/** Conjunto de caracteres do banco de dados a ser usado na criação das tabelas. */
define('DB_CHARSET', 'utf8');

/** O tipo de collate do banco de dados. Não altere isso se tiver dúvidas. */
define('DB_COLLATE', 'utf8_general_ci');

/**#@+
 * Authentication unique keys and salts.
 *
 * Change these to different unique phrases! You can generate these using
 * the {@link https://api.wordpress.org/secret-key/1.1/salt/ WordPress.org secret-key service}.
 *
 * You can change these at any point in time to invalidate all existing cookies.
 * This will force all users to have to log in again.
 *
 * @since 2.6.0
 */
define('AUTH_KEY',         'uF]Y]fpp=Gb -) .#XX+OOdpvMN 7H-GrL.]YoU&x006*<LjAd[xs~[+,3Pj<Mo+');
define('SECURE_AUTH_KEY',  '#g{f|naA3--(PsaL KY:<+OC)MTJj-s::[e:7[Zh!-`|V:(i&P=E$`l<5Xq4:25E');
define('LOGGED_IN_KEY',    'hsA-wcW|#tCXA()l1SKi#!;ht-*Yctj+VU3Dl|6+l^Bc{l C&3||towPCR_MN/>_');
define('NONCE_KEY',        'ErB8NL)T=(+~kQ[UbSx@!R,!yqxB#@(AP3Vz:U+R1_.K=c?%`]K+#VVD/-},c$@y');
define('AUTH_SALT',        'cvL|j#RnQ/x~6OkX&`@-%anjSE}o`S2<n_RVqy3A;y;|J#A&Ed/+D,iydftSFI$~');
define('SECURE_AUTH_SALT', 'ui{vQv-jE_4wWFi|JtgW(6FR[{kuhts67]Fuu;)MM2D|6C$/CDZrO+M{Zz 9V[Cd');
define('LOGGED_IN_SALT',   '|4dGjy[Pyhqv> ]O_E+rEWogq?dG|-#&DF4H%p%+?R4nTa|VRn6VeX@NMh-d)|^a');
define('NONCE_SALT',       'zA}N`LeB EOix|?Qf7#U9+OTL+ Se*2!JYXq4JlwKGX55A J8M7TioGnK.*p{eT?');

/**#@-*/

/**
 * WordPress database table prefix.
 *
 * You can have multiple installations in one database if you give each
 * a unique prefix. Only numbers, letters, and underscores please!
 */
$table_prefix = 'wp_';

/**
 * For developers: WordPress debugging mode.
 *
 * Change this to true to enable the display of notices during development.
 * It is strongly recommended that plugin and theme developers use WP_DEBUG
 * in their development environments.
 *
 * For information on other constants that can be used for debugging,
 * visit the documentation.
 *
 * @link https://wordpress.org/documentation/article/debugging-in-wordpress/
 */
define( 'WP_DEBUG', false );

/* Add any custom values between this line and the "stop editing" line. */



/* That's all, stop editing! Happy publishing. */

/** Absolute path to the WordPress directory. */
if ( ! defined( 'ABSPATH' ) ) {
	define( 'ABSPATH', __DIR__ . '/' );
}

/** Sets up WordPress vars and included files. */
require_once ABSPATH . 'wp-settings.php';
