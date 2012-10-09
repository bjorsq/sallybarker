<?php
/**
 * Navigation menus and sidebars
 * @author Peter Edwards <pete@bjorsq.net>
 * @package sallybarker.org
 * @version 1.0
 */

if ( ! class_exists('sb_sidebars') ) :

/**
 * class to manage all menus
 * extends Walker_Nav_Menu so it can act as a custom walker
 * @version 1.0
 */

class sb_menus
{
	/* registers everything with the Wordpress API */
	public static function register()
	{
		/* wait until theme is set up before adding any of these */
		add_action( 'after_setup_theme', array('sb_menus', 'register_menus') );
		/* add filter for menu arguments */
		add_filter( 'wp_nav_menu_args', array('sb_menus', 'nav_menu_args') );
	}

	/* registers menus */
	public static function register_menus()
	{
		register_nav_menus( array(
			'top-bar' => 'Top bar menu',
			'side-bar' => 'Sidebar menu',
			'footer' => 'Footer Menu'
		) );
	}

	/* modifies default menu arguments for all menus */
	public static function nav_menu_args( $args = '' )
	{
		$args['container'] = 'nav';
		$args['fallback_cb'] = '';
		return $args;
	}

	/* output top menu */
	public static function top()
	{
		wp_nav_menu( array(
			'menu' => 'top-bar',
			'items_wrap' => '<ul id="%1$s" class="%2$s nav" role="navigation">%3$s</ul>',
			'walker' => new sb_walker_nav_menu
		) );
	}
	/* output menu for single pages */
	public static function single()
	{
		wp_nav_menu( array(
			'menu' => 'side-bar',
			'items_wrap' => '<ul id="%1$s" class="%2$s nav" role="navigation">%3$s</ul>'
		) );
	}


} /* end class definition */

/* register with Wordpress API */
sb_menus::register();

endif;

/**
 * Custom walker for top menu bar
 */
class sb_walker_nav_menu extends Walker_Nav_Menu 
{
	/* add classes to ul sub-menus */
	function start_lvl( &$output, $depth ) {
		// depth dependent classes
		$indent = ( $depth > 0  ? str_repeat( "\t", $depth ) : '' ); // code indent
		$display_depth = ( $depth + 1); // because it counts the first submenu as 0
		$classes = array(
			'dropdown-menu',
			( $display_depth >=2 ? 'sub-dropdown-menu' : '' ),
			'menu-depth-' . $display_depth
		);
		$class_names = implode( ' ', $classes );
		/* build html */
		$output .= "\n" . $indent . '<ul class="' . $class_names . '" role="menu">' . "\n";
	}
  
	// add main/sub classes to li's and links
	function start_el( &$output, $item, $depth, $args ) {
		global $wp_query;
		$indent = ( $depth > 0 ? str_repeat( "\t", $depth ) : '' ); // code indent

		// depth dependent classes
		$depth_classes = array(
			( $depth == 0 && $item->title != 'C.V.' ? 'dropdown' : 'sub-menu-item' ),
			( $depth >=2 ? 'sub-sub-menu-item' : '' ),
			'menu-item-depth-' . $depth
		);
		$depth_class_names = esc_attr( implode( ' ', $depth_classes ) );

		// passed classes
		$classes = empty( $item->classes ) ? array() : (array) $item->classes;
		$class_names = esc_attr( implode( ' ', apply_filters( 'nav_menu_css_class', array_filter( $classes ), $item ) ) );

		// build html
		$output .= $indent . '<li id="nav-menu-item-'. $item->ID . '" class="' . $depth_class_names . ' ' . $class_names . '">';

		/* link attributes */
		$attributes  = ! empty( $item->attr_title ) ? ' title="'  . esc_attr( $item->attr_title ) .'"' : '';
		$attributes .= (! empty( $item->url ) && ! ( $depth == 0 && $item->title != 'C.V.' ) ) ? ' href="'   . esc_attr( $item->url        ) .'" data-target="#"' : ' href="#"';
		$attributes .= ' class="menu-link ' . ( $depth > 0 ? 'sub-menu-link' : 'dropdown-toggle' ) . '"';
		$attributes .= ( $depth == 0 && $item->title != 'C.V.' )? ' data-toggle="dropdown"': '';

		$item_output = sprintf( '%1$s<a%2$s>%3$s%4$s%5$s</a>%6$s',
			$args->before,
			$attributes,
			$args->link_before,
			apply_filters( 'the_title', $item->title, $item->ID ),
			( ( $depth == 0 && $item->title != 'C.V.' )? '<b class="caret"></b>': $args->link_after ),
			$args->after
		);

		// build html
		$output .= apply_filters( 'walker_nav_menu_start_el', $item_output, $item, $depth, $args );
	}
}
