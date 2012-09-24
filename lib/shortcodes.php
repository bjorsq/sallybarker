<?php
/**
 * sallybarker.org theme shortcodes
 * @author Peter Edwards <pete@bjorsq.net>
 * @package sallybarker.org
 * @version 1.0
 */

if ( ! class_exists('sb_shortcodes') ) :

/**
 * class to manage all shortcodes
 * @version 1.0
 */

class sb_shortcodes
{
	/* prevents object instantiation */
	private final function __construct() {}
	private final function sb_shortcodes() {}

	/* registers everything with the Wordpress API */
	public static function register()
	{
		/* add images shortcode */
		add_shortcode( 'figure', array('sb_shortcodes', 'get_figure') );
	}

	/* gets images for a post and puts threm in a slideshow */
	public static function get_figure($atts)
	{
		$options = extract( shortcode_atts( array(
			"post_id" => false,
			"post_type" => false,
			"single" => false,
			"size" => "full"
			), $atts));
		$single = (bool) $single;
		$out = '';
		$images = array();
		if (false !== $post_id) {
			if (true === $single) {
				/* get featured image */
				if ( has_featured_image($post_id) ) {
					$images[] = get_post(get_featured_image_id($post_id));
				}
			} else {
				/* get attachments for post */
				$posts = get_posts(array(
					"post_type" => "attachment",
					"parent" => $post_id,
					"child_of" => $post_id,
					"numberposts" => -1,
					"status" => "publish"
				));
				if (count($posts)) {
					$images = $posts;
				}
			}
		} elseif (false !== $post_type) {
			/* get attachments for post types */
			$posts = get_posts(array(
				"post_type" => $post_type,
				"numberposts" => -1,
				"status" => "publish"
			));
			if (count($posts)) {
				foreach ($posts as $p) {
					if ( has_featured_image($p->ID) ) {
						$images[] = get_post(get_featured_image_id($p->ID));
					}
				}
			}
		}
		if ( count($images) ) {
			$fig_id = uniqid();
			$out .= sprintf('<figure id="%s">', $fig_id);
			$first = true;
			$json = array();
			foreach ($images as $image) {
				$img = wp_get_attachment_image_src($image->ID);
				$json[] = (object) array(
					"src" => $img[0],
					"width" => $img[1],
					"height" => $img[2],
					"title" => apply_filters("the_title", $image->post_title),
					"caption" => apply_filters("the_content", $image->post_excerpt),
					"desc" => apply_filters("the_content", $image->post_content)
				);
				if ( $first ) {
					$out .= sprintf('<img src="%s" title="%s">', $img[0], esc_attr($image->post_title));
					$out .= sprintf('<figcaption>%s</figcaption>', apply_filters("the_content", $image->post_content));
					$first = false;
				}
			}
			if ( count($images) > 1 && false === $single) {
				/* output json */
				$out .= '<script type="text/javascript">//<!--';
				$out .= "\nif (!slides) {\n    var slides = {};\n}\nslides['" . $fig_id . "'] = " . json_encode($json) . ";\n//--></script>\n"; 
			}
			$out .= '</figure>';
		}
		return $out;
	}

} /* end class definition */

/* register with Wordpress API */
sb_shortcodes::register();

endif;