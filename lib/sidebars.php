<?php
/**
 * sallybarker.org theme sidebars
 * @author Peter Edwards <pete@bjorsq.net>
 * @package sallybarker.org
 * @version 1.0
 */

if ( ! class_exists('sb_sidebars') ) :

/**
 * class to manage all sidebars
 * @version 1.0
 */

class sb_sidebars
{
	/* prevents object instantiation */
	private final function __construct() {}
	private final function sb_sidebars() {}

	/* registers everything with the Wordpress API */
	public static function register()
	{
		/* wait until theme is set up before adding any of these */
		add_action( 'after_setup_theme', array('sb_sidebars', 'register_sidebars') );
	}

	/* registers sidebars */
	public static function register_sidebars()
	{
		register_sidebar( array(
			'name'			=> 'Blog sidebar',
			'id'            => 'blog-sidebar',
			'description'   => 'Sidebar on posts (blog)',
			'class'         => 'blog-sidebar',
		) );
		register_sidebar( array(
			'name'			=> 'Page sidebar',
			'id'            => 'page-sidebar',
			'description'   => 'Sidebar on pages',
			'class'         => 'page-sidebar',
		) );
		register_sidebar( array(
			'name'			=> 'Art sidebar',
			'id'            => 'art-sidebar',
			'description'   => 'Sidebar on Art pages',
			'class'         => 'art-sidebar',
		) );
		register_sidebar( array(
			'name'			=> 'Education sidebar',
			'id'            => 'education-sidebar',
			'description'   => 'Sidebar on Education pages',
			'class'         => 'education-sidebar',
		) );
		register_sidebar( array(
			'name'			=> 'Footer',
			'id'            => 'footer-sidebar',
			'description'   => 'Sidebar in Footer',
			'class'         => 'footer-sidebar',
		) );
	}

} /* end class definition */

/* register with Wordpress API */
sb_sidebars::register();

endif;