<?php
    require_once('../../services/common.php');

/**
 * The base configurations of the WordPress.
 *
 * This file has the following configurations: MySQL settings, Table Prefix,
 * Secret Keys, WordPress Language, and ABSPATH. You can find more information
 * by visiting {@link http://codex.wordpress.org/Editing_wp-config.php Editing
 * wp-config.php} Codex page. You can get the MySQL settings from your web host.
 *
 * This file is used by the wp-config.php creation script during the
 * installation. You don't have to use the web site, you can just copy this file
 * to "wp-config.php" and fill in the values.
 *
 * @package WordPress
 */

// ** MySQL settings - You can get this info from your web host ** //
/** The name of the database for WordPress */
define('DB_NAME', $sqlDatabaseConnectionInfo['db']);

/** MySQL database username */
define('DB_USER', $sqlDatabaseConnectionInfo['user']);

/** MySQL database password */
define('DB_PASSWORD', $sqlDatabaseConnectionInfo['password']);

/** MySQL hostname */
define('DB_HOST', $sqlDatabaseConnectionInfo['host']);

/** Database Charset to use in creating database tables. */
define('DB_CHARSET', 'utf8');

/** The Database Collate type. Don't change this if in doubt. */
define('DB_COLLATE', '');

/**#@+
 * Authentication Unique Keys and Salts.
 *
 * Change these to different unique phrases!
 * You can generate these using the {@link https://api.wordpress.org/secret-key/1.1/salt/ WordPress.org secret-key service}
 * You can change these at any point in time to invalidate all existing cookies. This will force all users to have to log in again.
 *
 * @since 2.6.0
 */
define('AUTH_KEY',         '@%pa=IEX@CG9!=sOEhy9-A-M3uK%_>3}sRO3y$-o$|pJ|<8U47jK+1>F-v)b$!YE');
define('SECURE_AUTH_KEY',  '+m+r!X.:E=.VC@[;VMR9@yT(jT^G|WMOf|8o>L`>+eKQ!{sL;^>x+,4R7rLe_E:&');
define('LOGGED_IN_KEY',    'Abvj1mRqJ3QjzCIT<n-2V,^BLAce-TsCe&~IU;BXIC|f_xPI?pdCQDxAdrX+qdB0');
define('NONCE_KEY',        'n%SyUg@+P-L`P Dd`J9>,TE|mt~O+eDWFP1o r9b~rRZGo3XjC.v;Ra|s=f<veIf');
define('AUTH_SALT',        'rqsUMBSidJ*jT{YDsK.Obm}@R,+R+.]qCk<Mm2/B0m&r?h+,{KPFde+kpBxhMn?P');
define('SECURE_AUTH_SALT', '=8vVn;[[+2J%LF+k|]>{;hs#RRfka);O6(xD.j)9vD0}-E,M!HJl~!p]FNmi(|Za');
define('LOGGED_IN_SALT',   'Z_|%}-1Y_v(M=o$G++9+t}7G|^{f_||Nn,Aml=$=$(&MjO`5~k@;uCs20Y+w;+/R');
define('NONCE_SALT',       'wTz_~dio-dh #d@|8U8B~(N{qV39Mcl@8h]!-BF<cp#3!?fh%I$<+8-}e+6-[bj$');

/**#@-*/

/**
 * WordPress Database Table prefix.
 *
 * You can have multiple installations in one database if you give each a unique
 * prefix. Only numbers, letters, and underscores please!
 */
$table_prefix  = 'wp_';

/**
 * WordPress Localized Language, defaults to English.
 *
 * Change this to localize WordPress. A corresponding MO file for the chosen
 * language must be installed to wp-content/languages. For example, install
 * de_DE.mo to wp-content/languages and set WPLANG to 'de_DE' to enable German
 * language support.
 */
define('WPLANG', '');

/**
 * For developers: WordPress debugging mode.
 *
 * Change this to true to enable the display of notices during development.
 * It is strongly recommended that plugin and theme developers use WP_DEBUG
 * in their development environments.
 */
define('WP_DEBUG', false);

/* That's all, stop editing! Happy blogging. */

/** Absolute path to the WordPress directory. */
if ( !defined('ABSPATH') )
	define('ABSPATH', dirname(__FILE__) . '/');

/** Sets up WordPress vars and included files. */
require_once(ABSPATH . 'wp-settings.php');
