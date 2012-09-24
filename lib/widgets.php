<?php
/**
 * sallybarker.org theme widgets
 * @author Peter Edwards <pete@bjorsq.net>
 * @package sallybarker.org
 * @version 1.0
 */

if ( ! class_exists('sb_widgets') ) :

/**
 * class to manage all widgets
 * @version 1.0
 */

class sb_widgets
{
	/* prevents object instantiation */
	private final function __construct() {}
	private final function sb_widgets() {}

	/* registers everything with the Wordpress API */
	public static function register()
	{

	}


} /* end class definition */

/* register with Wordpress API */
sb_widgets::register();

endif;