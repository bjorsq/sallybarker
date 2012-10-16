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
	    /* remove wordpress slideshow shortcode */
	    remove_shortcode( 'slideshow' );
	    /* add uol slideshow shortcode */
		add_shortcode( 'slideshow', array( 'sb_shortcodes', 'slideshow_shortcode' ) );
	    /* remove wordpress gallery shortcode */
	    remove_shortcode( 'gallery' );
	    /* remove inline styles from wordpress gallery shortcode */
	    add_filter( 'gallery_style', array( 'sb_shortcodes', 'remove_gallery_css' ) );
	    /* add uol gallery shortcode */
		add_shortcode( 'gallery', array( 'sb_shortcodes', 'gallery_shortcode' ) );
	    /* add comic shortcode */
		add_shortcode( 'comic', array( 'sb_shortcodes', 'comic_shortcode' ) );
	    /* add uol slideshow shortcode */
		add_shortcode( 'carousel', array( 'sb_shortcodes', 'carousel_shortcode' ) );
	    /* add uol slideshow shortcode */
		add_shortcode( 'homepage_carousel', array( 'sb_shortcodes', 'homepage_carousel_shortcode' ) );
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

    /**
     * Remove inline styles printed when the gallery shortcode is used.
     */
    public static function remove_gallery_css( $css )
    {
        return preg_replace( "#<style type='text/css'>(.*?)</style>#s", '', $css );
    }

	/**
	 * gallery shortcode
	 */
	public static function gallery_shortcode($attr)
	{
		global $post;

		static $instance = 0;
		$instance++;

		/* make sure we have a valid orderby statement */
		if ( isset( $attr['orderby'] ) ) {
			$attr['orderby'] = sanitize_sql_orderby( $attr['orderby'] );
			if ( !$attr['orderby'] ) {
				unset( $attr['orderby'] );
			}
		}

		$options = shortcode_atts(array(
			'order'      => 'ASC',
			'orderby'    => 'menu_order ID',
			'id'         => $post->ID,
			'full_size'  => 'full-image',
			'thumb_size' => 'thumbnail',
		    'class'      => '',
		    'interval'   => 5000,
		    'transition' => 500,
		    'loop'       => true,
		    'caption'    => true,
			'include'    => '',
			'exclude'    => ''
		), $attr);

		$id = intval($id);

		$attachments = self::get_image_attachments($options);

		if ( empty($attachments) ) {
			return '';
		}

		if ( is_feed() ) {
			$output = "\n";
			foreach ( $attachments as $att_id => $attachment ) {
				$output .= wp_get_attachment_link($att_id, $size, true) . "\n";
			}
			return $output;
		}

		if (trim($class) !== '') {
			$class = ' ' . $class;
		}

		$selector = "gallery-{$instance}";
		$output = '<div class="gallery' . $class . '" id="' . $selector . '">';
		$json = array();
		$options["max_w"] = 0;
		$options["max_h"] = 0;
		$thumbs_w = 3;
		$feature = false;
		$thumbs = '';
		$idx = 0;
		foreach ( $attachments as $id => $attachment ) {
			/* get full size image details */
	    	$src = wp_get_attachment_image_src($id, $options["full_size"]);
	    	/* collect html of first image for output */
	    	if ($feature === false) {
	    		$feature = sprintf('<div class="figure"><img src="%s" alt="%s" title="%s" data-caption="%s" /><div class="figcaption"><h3>%s</h3>%s</div></div>', $src[0], esc_attr($attachment->post_title), esc_attr($attachment->post_title), esc_attr($attachment->post_excerpt), apply_filters('the_title', $attachment->post_title), apply_filters('the_content', $attachment->post_excerpt));
	    	}
	    	/* set max width and height */
	    	$options["max_w"] = max($options["max_w"], $src[1]);
	    	$options["max_h"] = max($options["max_h"], $src[2]);
	    	/* get thumbnail image details */
	    	$thumb = wp_get_attachment_image_src($id, $options["thumb_size"]);
	    	$thumbs_w += ($thumb[1] + 15);
	    	$class = ($idx == 0)? ' active': '';
		    $thumbs .= sprintf('<li><a href="%s" class="thumb-link%s" rel="slide%d"><img src="%s" width="%s" height="%s" alt="%s" title="%s" data-caption="%s" /></a></li>', $src[0], $class, $idx, $thumb[0], $thumb[1], $thumb[2], esc_attr($attachment->post_title), esc_attr($attachment->post_title), esc_attr($attachment->post_excerpt));
		    $idx++;
	    	$json[] = (object) array(
			    "full_src" => $src[0],
			    "full_w" => $src[1],
			    "full_h" => $src[2],
			    "thumb_src" => $thumb[0],
			    "thumb_w" => $thumb[1],
			    "thumb_h" => $thumb[2],
			    "title" => $attachment->post_title,
			    "caption" => $attachment->post_excerpt,
			    "description" => $attachment->post_content,
			    "id" => $id
			);
		}
	    $settings = (object) $options;
	    $output .= $feature;
	    if (count($attachments) > 1) {
		    $output .= sprintf('<div class="thumbnails"><ul style="width:%dpx">%s</ul></div>', $thumbs_w, $thumbs);
	    }
		$output .= "<script type=\"text/javascript\">\n  if (typeof gallerysettings === 'undefined') { var gallerysettings = {}; };\n  gallerysettings['$selector'] = " . json_encode($settings) . ";\n if (typeof galleryimages === 'undefined') { var galleryimages = {}; };\ngalleryimages['$selector'] = " . json_encode($json) . ";</script>\n";
		$output .= '</div>';
		return $output;
	}

	/**
	 * Slideshow shortcode.
	 * @param array $attr Attributes attributed to the shortcode.
	 * @return string HTML content to display gallery.
	 */
	public static function slideshow_shortcode($attr)
	{
		global $post;

		static $instance = 0;
		$instance++;

		/* make sure we have a valid orderby statement */
		if ( isset( $attr['orderby'] ) ) {
			$attr['orderby'] = sanitize_sql_orderby( $attr['orderby'] );
			if ( !$attr['orderby'] ) {
				unset( $attr['orderby'] );
			}
		}

		$options = shortcode_atts(array(
			'order'      => 'ASC',
			'orderby'    => 'menu_order ID',
			'id'         => $post->ID,
			'size'       => 'medium',
		    'class'      => '',
		    'caption'    => true,
		    'usetitle'   => true,
		    'navigation' => true,
		    'interval'   => 5000,
		    'transition' => 500,
			'include'    => '',
			'exclude'    => '',
			'callback'   => ''
		), $attr);

		$id = intval($id);

		$attachments = self::get_image_attachments($options);

		if ( empty($attachments) )
			return '';

		$caption = (bool) $options["caption"];
		$navigation = (trim(strtolower($options["navigation"])) == "thumbnails")? "thumbnails": (bool) $options["navigation"];
		$usetitle = (bool) $options["usetitle"];

		if ( is_feed() ) {
			$output = "\n";
			foreach ( $attachments as $att_id => $attachment ) {
				$output .= wp_get_attachment_link($att_id, $size, true) . "\n";
			}
			return $output;
		}

		$selector = "slideshow-{$instance}";
		if (trim($options["class"]) != "") {
			$class = " " . trim($options["class"]);
		}
		$i = 0;
		$output = "<div class=\"slideshow$class\" id=\"$selector\">\n";
	    $json = array();
	    $first = true;
	    foreach ( $attachments as $att_id => $attachment ) {
	    	$src = wp_get_attachment_image_src($att_id, $options["size"]);
	    	if ($first) {
	    		$src = wp_get_attachment_image_src($att_id, $options["size"]);
		    	$output .= sprintf('<img src="%s" width="%s" height="%s" alt="%s" title="%s" />', $src[0], $src[1], $src[2], $attachment->post_title, $attachment->post_title);
			    if ($caption || $navigation) {
				    /* make sure caption spans width of image */
				    $cap_style = 'style="width:' . $src[1] . 'px;';
				    /* add display:none for javascript-shy browsers when caption is omitted */
				    $cap_style .= ($caption)? '"': 'display:none;"';
				    $output .= "      <div class=\"slideshow-caption\"" . $cap_style . "><div class=\"slideshow-captiontext\">" . wptexturize($attachment->post_excerpt) . "</div></div>\n";
			    }
			    $first = false;
	    	}
	    	$json[] = (object) array(
			    "src" => $src[0],
			    "w" => $src[1],
			    "h" => $src[2],
			    "title" => $attachment->post_title,
			    "caption" => $attachment->post_excerpt,
			    "description" => $attachment->post_content,
			    "id" => $att_id
			);
	    }
	    $settings = (object) array(
	        "nav" => $navigation,
	        "caption" => $caption,
	        "interval" => $options["interval"],
	        "transition" => $options["transition"],
	        "callback" => $options["callback"],
	        "usetitle" => $usetitle
	    );
		$output .= "  <script type=\"text/javascript\">\n  if (typeof slidesettings === 'undefined') { var slidesettings = {}; };\n  slidesettings['$selector'] = " . json_encode($settings) . ";\n if (typeof slides === 'undefined') { var slides = {}; };\nslides['$selector'] = " . json_encode($json) . ";</script>\n";
		$output .= "  </div>\n";
		return $output;
	}

	/**
	 * get_image_attachments
	 * gets the image attachments for a post
	 */
	public static function get_image_attachments($options)
	{
		$attachments = array();
		if ( !empty($options["include"]) ) {
			$include = preg_replace( '/[^0-9,]+/', '', $options["include"] );
			$_attachments = get_posts( array(
				'include' => $include,
				'post_status' => 'inherit',
				'post_type' => 'attachment',
				'post_mime_type' => 'image',
				'order' => $options["order"],
				'orderby' => $options["orderby"]
			) );
			foreach ( $_attachments as $key => $val ) {
				$attachments[$val->ID] = $_attachments[$key];
			}
		} elseif ( !empty($options["exclude"]) ) {
			$exclude = preg_replace( '/[^0-9,]+/', '', $options["exclude"] );
			$attachments = get_children( array(
				'post_parent' => $options["id"],
				'exclude' => $exclude,
				'post_status' => 'inherit',
				'post_type' => 'attachment',
				'post_mime_type' => 'image',
				'numberposts' => -1,
				'order' => $options["order"],
				'orderby' => $options["orderby"]
			) );
		} else {
			$attachments = get_children( array(
				'post_parent' => $options["id"],
				'post_status' => 'inherit',
				'post_type' => 'attachment',
				'post_mime_type' => 'image',
				'numberposts' => -1,
				'order' => $options["order"],
				'orderby' => $options["orderby"]
			) );
		}
		return $attachments;
	}
 
	/**
	 * shortcode to generate comics pages
	 */
	public static function comic_shortcode($attr)
	{
		extract(shortcode_atts(array(
			'class' => 'linear'
		), $attr));

		$out = '';
		global $post;
		if ($post) {
			$args = array(
				"post_type" => "attachment",
				"orderby" => "menu_order",
				"order" => "ASC",
				"post_parent" => $post->ID,
				"numberposts" => -1
			);
			if ( has_post_thumbnail($post->ID) ) {
				$args["exclude"] = get_post_thumbnail_id($post->ID);
			}
			$attachments = get_posts($args);
			$images = array();
			if (count($attachments)) {
				$total_width = 0;
				$max_height = 0;
				foreach ($attachments as $attachment) {
	    			$img = wp_get_attachment_image_src($attachment->ID, 'full');
	    			$total_width += $img[1];
	    			$max_height = max($max_height, $img[2]);
	    			$images[] = $img;
				}
				if ($class == "linear") {
					$out .= sprintf('<div class="comic-frame-%s"><div class="frames" style="width:%spx;height:%spx">', $class, ($total_width + 2), $max_height);
				} else {
					$out .= sprintf('<div class="comic-frame-%s"><div class="frames">', $class);
				}
				foreach ($images as $img) {
					$out .= sprintf('<img src="%s" />', $img[0]);
				}
				$out .= '</div></div>';
			}
		}
		return $out;
	}

	/**
	 * carousel shortode
	 */
	public static function carousel_shortcode($attr)
	{
		global $post;

		static $instance = 0;
		$instance++;

		/* make sure we have a valid orderby statement */
		if ( isset( $attr['orderby'] ) ) {
			$attr['orderby'] = sanitize_sql_orderby( $attr['orderby'] );
			if ( !$attr['orderby'] ) {
				unset( $attr['orderby'] );
			}
		}

		$options = shortcode_atts(array(
			'order'      => 'ASC',
			'orderby'    => 'menu_order ID',
			'id'         => $post->ID,
			'size'  	 => 'large',
			'interval'   => 5000,
			'pause'      => 'hover',
		    'class'      => '',
			'include'    => '',
			'exclude'    => ''
		), $attr);

		$options['id'] = intval($options['id']);

		$attachments = self::get_image_attachments($options);

		if ( empty($attachments) ) {
			return '';
		}

		if ( is_feed() ) {
			$output = "\n";
			foreach ( $attachments as $att_id => $attachment ) {
				$output .= wp_get_attachment_link($att_id, $options['size'], true) . "\n";
			}
			return $output;
		}
		return self::get_carousel_html($instance, $options, $attachments);
	}

	/**
	 * Homepage carousel
	 */
	public static function homepage_carousel_shortcode($attr)
	{
		static $instance = 0;
		$instance++;

		/* make sure we have a valid orderby statement */
		if ( isset( $attr['orderby'] ) ) {
			$attr['orderby'] = sanitize_sql_orderby( $attr['orderby'] );
			if ( !$attr['orderby'] ) {
				unset( $attr['orderby'] );
			}
		}

		$options = shortcode_atts(array(
			'order'      => 'ASC',
			'orderby'    => 'menu_order ID',
			'size'  	 => 'large',
			'interval'   => 5000,
			'pause'      => 'hover',
		    'class'      => '',
		    'post_type'  => 'art',
			'exclude'    => '',
			'limit'      => -1
		), $attr);

		/* get all pages of given post type */
		$all_posts = get_posts( array(
			'post_type' => $options['post_type'],
			'numberposts' => $options['limit'],
			'post_status' => 'publish',
			'order'      => $options['order'],
			'orderby'    => $options['orderby'],
			'exclude'    => $options['exclude']
		) );

		$attachments = array();
		if (count($all_posts)) {
			foreach ($all_posts as $post) {
				if (has_post_thumbnail($post->ID)) {
					$tid = get_post_thumbnail_id($post->ID);
					$attachments[$tid] = get_post($tid);
					$attachments[$tid]->post_title = $post->post_title;
					$attachments[$tid]->post_excerpt = $post->post_excerpt;
				}
			}
			return self::get_carousel_html($instance, $options, $attachments);
		}
	}

	private static function get_carousel_html($instance, $options, $attachments)
	{
		$selector = "carousel-{$instance}";
		$class = ' class="carousel slide' . ($options['class'] != ''? ' ' . trim($options['class']): '') . '"';
		$data_attr = sprintf(' data-interval="%s" data-pause="%s"', $options["interval"], $options["pause"]);
		$first = true;
		$output = sprintf('<div id="%s"%s><div class="carousel-inner">', $selector, $class);
		foreach ( $attachments as $id => $attachment ) {
	    	$src = wp_get_attachment_image_src($id, $options["size"]);
	    	$class = $first? ' active': '';
		    $output .= sprintf('<div class="item%s"><img src="%s" width="%s" height="%s" alt="%s" title="%s" /><div class="carousel-caption"><h4>%s</h4>%s</div></div>', $class, $src[0], $src[1], $src[2], esc_attr($attachment->post_title), esc_attr($attachment->post_title), $attachment->post_title, $attachment->post_excerpt);
		    $first = false;
		}
		$output .= '<a class="carousel-control left" href="#myCarousel" data-slide="prev">&lsaquo;</a>';
		$output .= '<a class="carousel-control right" href="#myCarousel" data-slide="next">&rsaquo;</a>';
		$output .= '</div></div>';
		return $output;
	}

} /* end class definition */

/* register with Wordpress API */
sb_shortcodes::register();

endif;