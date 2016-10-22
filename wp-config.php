<?php

if ( file_exists( dirname( __FILE__ ) . '/local-config.php' ) ) {
    include( dirname( __FILE__ ) . '/local-config.php' );
    define( 'WP_LOCAL_DEV', true ); // We'll talk about this later
} else {

    // ** Staging Server Settings** //

    define( 'DB_NAME',     'production_db'       );
    define( 'DB_USER',     'production_user'     );
    define( 'DB_PASSWORD', 'production_password' );
    define( 'DB_HOST',     'production_db_host'  );
}



define('DB_COLLATE', '');


$table_prefix = 'wp_';





/* Inserted by Pressmatic. See: http://codex.wordpress.org/Administration_Over_SSL#Using_a_Reverse_Proxy */
if (isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] === 'https') {
	$_SERVER['HTTPS'] = 'on';
}

/* That's all, stop editing! Happy blogging. */

/** Absolute path to the WordPress directory. */
if ( !defined('ABSPATH') )
	define('ABSPATH', dirname(__FILE__) . '/');

/** Sets up WordPress vars and included files. */
require_once(ABSPATH . 'wp-settings.php');
