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
define( 'DB_NAME', 'mywordpress' );

/** Database username */
define( 'DB_USER', 'root' );

/** Database password */
define( 'DB_PASSWORD', '' );

/** Database hostname */
define( 'DB_HOST', 'localhost' );

/** Database charset to use in creating database tables. */
define( 'DB_CHARSET', 'utf8mb4' );

/** The database collate type. Don't change this if in doubt. */
define( 'DB_COLLATE', '' );

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
define( 'AUTH_KEY',         'X</1Yrb>QHypg1?ip_%+NE4a]oeXs Sz=?#r>|?2Rtf#u?z2U[JH~^;b8(yn7xjJ' );
define( 'SECURE_AUTH_KEY',  'cYI8M4Nr#(^l(izS@q=4|>--tSX!;fx?xoux6m`Vf<`g$->px0(kYn>8aBkyn. @' );
define( 'LOGGED_IN_KEY',    '=3f,/;wd-K jSotW$MHD?tj:1TvPA@_1 BbC{,FY,)5,z ]qLn{cs7uTh&PmH}<O' );
define( 'NONCE_KEY',        '1R0<e|3]U{6iYe d{Uyi7iDi}Qi&LBTKMf|0w[U)cb+Hg<R1`Vn=gW7<q0 TIgKx' );
define( 'AUTH_SALT',        '6PUX;@VqC%y?q&FB%=E{5d.KT~&:?LJG`JEeykeXw^pYUp7D<<^BEf&xh^&Io,db' );
define( 'SECURE_AUTH_SALT', '1Wq?.+g{t}cRr5HvRI(p1wq!})W1R-+=</c3D #tQhC?V^N!eQgEY^jI7Vbpq`XQ' );
define( 'LOGGED_IN_SALT',   '2HH7Yxa4(U:nkkMn608A Op<z_K)P+G{?t+HloAY+q&`yY77~Q1gShxNJmc9)bB*' );
define( 'NONCE_SALT',       'u:-)}:LMB&<P`tJ9qKTv].$cu;uNVjBv$2qCylDAFv|Z+xAJOOf%`pOGLqhC;N}<' );

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
