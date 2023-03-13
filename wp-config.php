<?php
define( 'WP_CACHE', true );
/**
 * The base configuration for WordPress
 *
 * The wp-config.php creation script uses this file during the installation.
 * You don't have to use the web site, you can copy this file to "wp-config.php"
 * and fill in the values.
 *
 * This file contains the following configurations:
 *
 * * MySQL settings
 * * Secret keys
 * * Database table prefix
 * * ABSPATH
 *
 * @link https://wordpress.org/support/article/editing-wp-config-php/
 *
 * @package WordPress
 */

// ** MySQL settings - You can get this info from your web host ** //
/** The name of the database for WordPress */
define( 'DB_NAME', 'foodblog' );

/** MySQL database username */
define( 'DB_USER', 'root' );

/** MySQL database password */
define( 'DB_PASSWORD', '' );

/** MySQL hostname */
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
define( 'AUTH_KEY',         '3gXHXmBsyp7JzX%D(gy.U[;<9Po%vW=vPWpZ_1 L>dRc}eVbCvE9lSkY}S<)E4d|' );
define( 'SECURE_AUTH_KEY',  'N58l}o`oSK<P`)|_KmCv+(Wfpq<=|_+q9*(%;~y/It]wSP{6E 70?_-b9nO+5kkq' );
define( 'LOGGED_IN_KEY',    ':u%{D?mu3F/e0n2p_41yVGNEk ]oPZQzv76%Tg%y+x`uE/X6$Qk`sKT<z%hGB,Yr' );
define( 'NONCE_KEY',        '7?*tmUZf#[?g ~Fc,iJ[ljor#iej{zs2=/1GYUb,VlltiC.eVMvg4a}d_=yz!A03' );
define( 'AUTH_SALT',        '6>6n948ejKa9s$IG@nSl,h$O]~U5l6GOa6Vet>?Q$Zpl>*Ol@_57fG<A&6sxuo}g' );
define( 'SECURE_AUTH_SALT', '&M!Zx<!;A5b~(`s)*VtzIIW}!mu^Dv)k??AArDajQMQ(&q>=GER|U-_|8i{YMD[m' );
define( 'LOGGED_IN_SALT',   'uUg9~r]-JUumb;jZ2gync^-C-rB:V5Y-.Tel}pre4{!Rwu{5Qv|.u1Au?^#w3t.q' );
define( 'NONCE_SALT',       'BT/.eBnrz5V#Vq(-8G^rk|vsW>/[n5rt)(2m<qK. %X=W+OQ$#B%Z+~gy=nsYPOa' );

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
 * @link https://wordpress.org/support/article/debugging-in-wordpress/
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
