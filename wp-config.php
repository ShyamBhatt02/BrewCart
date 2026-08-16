<?php
/**
 * The base configuration for WordPress
 *
 * The wp-config.php creation script uses this file during the installation.
 * You don't have to use the web site, you can copy this file to "wp-config.php"
 * and fill in the values.
 *
 * This file contains the following configurations:
 *
 * * Database settings
 * * Secret keys
 * * Database table prefix
 * * Localized language
 * * ABSPATH
 *
 * @link https://wordpress.org/support/article/editing-wp-config-php/
 *
 * @package WordPress
 */

// ** Database settings - You can get this info from your web host ** //
/** The name of the database for WordPress */
define( 'DB_NAME', 'brewcart' );

/** Database username */
define( 'DB_USER', 'root' );

/** Database password */
define( 'DB_PASSWORD', '' );

/** Database hostname */
define( 'DB_HOST', '127.0.0.1' );

/** Database charset to use in creating database tables. */
define( 'DB_CHARSET', 'utf8' );

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
define( 'AUTH_KEY',          'IMsp?oIcoopj`auU N=P%TW)2NJZfL@;}KcU?eKC<EJP82<Cq3Q4duCu-cQmhTh<' );
define( 'SECURE_AUTH_KEY',   'NV/VZH<j,X}LE^53D+yN<K1 ;dTor3RYQB>$6Gy(LeNT%Z7a$!Y%}^dCHMk(5$*]' );
define( 'LOGGED_IN_KEY',     'NH132nT2*|!-N&G|`SH3ZN=n=uimXm{aNy^BH8_XKV`lcz]2ul.sTthcu5YgF_.3' );
define( 'NONCE_KEY',         'EjUl,k9u0FX.G1,Ow,3h!)8[+()GyfF,dxcAAL_I9~;AMokhySYIy8|NJ1DNiW3M' );
define( 'AUTH_SALT',         '3tNrvksU#@YmtVUm8)E6=[b(NQugd2!r^98Y;J~Rk,GVS5HtmZ896`<P5`3#(}6k' );
define( 'SECURE_AUTH_SALT',  ',y{augmAjZ4w.W>mpVx`Y#qjT(u$>c{4-RKAOssuYUD4 eYlz#/:;)/g*z%gqQRo' );
define( 'LOGGED_IN_SALT',    '^[C{unCE$|j(_xwh0BAGWtGD5K>5G2hn<{0k>b69Rw@!T9>M43I2+Vrn1w22lHw+' );
define( 'NONCE_SALT',        'xG2i7ZP9G2cG41Q]MkQW#hGPjh8[,p+Z6acjv+Y*3JK]5?gzsM-rORT.|5OcsC}&' );
define( 'WP_CACHE_KEY_SALT', '.-b3/nt%_I SuMQ$d 3?{2+i80T=eq>NVI7_4y$AHvq5gEcmGzBc/Xd,R%i8xRPK' );


/**#@-*/

/**
 * WordPress database table prefix.
 *
 * You can have multiple installations in one database if you give each
 * a unique prefix. Only numbers, letters, and underscores please!
 */
$table_prefix = 'wp_';


/* Add any custom values between this line and the "stop editing" line. */

define( 'WP_DEBUG', true );
define( 'WP_DEBUG_LOG', true );
define( 'WP_DEBUG_DISPLAY', false );
define( 'DISALLOW_FILE_EDIT', true );
define( 'AUTOMATIC_UPDATER_DISABLED', true );
define( 'WP_AUTO_UPDATE_CORE', false );


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
if ( ! defined( 'WP_DEBUG' ) ) {
	define( 'WP_DEBUG', false );
}

/* That's all, stop editing! Happy publishing. */

/** Absolute path to the WordPress directory. */
if ( ! defined( 'ABSPATH' ) ) {
	define( 'ABSPATH', __DIR__ . '/' );
}

/** Sets up WordPress vars and included files. */
require_once ABSPATH . 'wp-settings.php';
