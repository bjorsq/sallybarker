<?php
/**
 * sallybarker.org theme functions
 * @author Peter Edwards <pete@bjorsq.net>
 * @package sallybarker.org
 */

/**
 * include libraries
 */
require_once(dirname(__FILE__) . '/lib/post-types.php');
require_once(dirname(__FILE__) . '/lib/shortcodes.php');
require_once(dirname(__FILE__) . '/lib/widgets.php');
require_once(dirname(__FILE__) . '/lib/menus.php');
require_once(dirname(__FILE__) . '/lib/sidebars.php');

/**
 * bootstrap theme
 */
function sb_setup_theme()
{
	add_action( 'init', 'sb_add_page_excerpt' );
	/* enqueue scripts */
	add_action ('wp_enqueue_scripts', 'sb_enqueue_scripts' );
	/* This theme styles the visual editor with editor-style.css to match the theme style. */
	add_editor_style();
	/* This theme uses post thumbnails */
	add_theme_support( 'post-thumbnails' );
	// size of post thumbnails
	set_post_thumbnail_size( 500, 300, true );
	/* product image size */
	add_image_size('full-image', 1000, 600, true);
    /* sort out post classes for layout */
    add_filter('post_class', 'sb_post_classes');
    /* excerpt filters */
    add_filter( 'excerpt_length', 'sb_excerpt_length' );
    add_filter( 'excerpt_more', 'sb_continue_reading' );
    add_filter( 'get_the_excerpt', 'sb_custom_excerpt_more' );
}
add_action( 'after_setup_theme', 'sb_setup_theme' );

/**
 * adds extras for pages
 * - excerpts
 * - categories
 */
function sb_add_page_excerpt()
{
	add_post_type_support( 'page', 'excerpt' );
}

/**
 * adds a class to articles to distinguish archives and single items
 */
function sb_post_classes($classes)
{
	if ( is_search() || is_archive() ) :
		$classes[] = "summary";
	endif;
	return $classes;
}
/**
 * inserts scripts for front-end
 * including replacing default jquery script with google CDN version
 */
function sb_enqueue_scripts()
{
	wp_deregister_script( 'jquery' );
	wp_register_script( 'jquery', 'https://ajax.googleapis.com/ajax/libs/jquery/1/jquery.min.js' );
	wp_register_script( 'sb', get_template_directory_uri() . '/js/sb.all.js', array('jquery'), uniqid(), true );
	wp_enqueue_script( 'sb' );
}

function sb_posted_on()
{
	echo '<p class="muted"><time class="updated" datetime="'. get_the_time('c') .'" pubdate>'. sprintf('Posted by %s on %s at %s.', get_the_author(), get_the_time('l, F jS, Y'), get_the_time()) .'</time></p>';
}
function sb_content_nav()
{
	
}
// return entry meta information for posts, used by multiple loops.
function sb_entry_meta()
{
	return;
	$show_sep = false;
	$out = "";
	$categories_list = get_the_category_list( ', ' );
	if ( $categories_list ) :
		$out .= '<span class="tax-links">';
		$out .= sprintf( '<span class="entry-util">Posted in</span> %s', $categories_list );
		$show_sep = true;
		$out .= '</span>';
	endif;
	$tags_list = get_the_tag_list( '', ', ' );
	if ( $tags_list ) :
		if ( $show_sep ) :
			$out .= '<span class="sep"> | </span>';
		endif;
		$out .= '<span class="tax-links">';
		$out .= sprintf( '<span class="entry-util">Tagged</span> %s</span>', $tags_list );
		$show_sep = true;
	endif;
	if ( comments_open() ) :
		if ( $show_sep ) :
			$out .= '<span class="sep"> | </span>';
		endif;
		$out .= '<span class="comments-link">';
		$out .= comments_popup_link( '<span class="leave-reply">Leave a reply</span>', '<b>1</b> Reply', '<b>%</b> Replies' );
		$out .= '</span>';
	endif;
	$out .= edit_post_link( 'Edit', '<span class="edit-link">', '</span>' );
    print($out);
}
function sb_get_comments($comment, $args, $depth) 
{
	$GLOBALS['comment'] = $comment;
	?>
	<li <?php comment_class(); ?>>
		<article id="comment-<?php comment_ID(); ?>">
			<header class="comment-author vcard">
				<?php echo get_avatar($comment, $size='40'); ?>
				<?php printf('<cite class="fn">%s</cite>', get_comment_author_link()) ?>
				<time datetime="<?php echo comment_date('c') ?>"><a href="<?php echo htmlspecialchars( get_comment_link( $comment->comment_ID ) ) ?>"><?php printf('%1$s', get_comment_date(),  get_comment_time()) ?></a></time>
				<?php edit_comment_link('(Edit)', '', '') ?>
			</header>
			
			<?php if ($comment->comment_approved == '0') : ?>
       			<div class="notice">
					<p class="bottom">Your comment is awaiting moderation.</p>
          		</div>
			<?php endif; ?>
			
			<section class="comment">
				<?php comment_text() ?>
			</section>
			
			<?php comment_reply_link(array_merge( $args, array('depth' => $depth, 'max_depth' => $args['max_depth']))) ?>
			
		</article>
	</li>
	<?php
}

if ( ! function_exists( 'sb_excerpt_length' )) :
    /**
     * Sets the post excerpt length to 60 characters.
     * @return int
     */
    function sb_excerpt_length( $length )
    {
        return 60;
    }
endif;

if ( ! function_exists( 'sb_continue_reading' )) :
    /**
     * Replaces "[...]" (appended to automatically generated excerpts) with an ellipsis and Continue reading link.
     */
    function sb_continue_reading( $more = "" )
    {
        return '&hellip; <a class="read-more" href="'. get_permalink() . '"><em>Read more &raquo;</em></a>';
    }
endif;

if ( ! function_exists( 'sb_custom_excerpt_more' )) :
    /**
     * Adds a pretty "Continue Reading" link to custom post excerpts.
     */
    function sb_custom_excerpt_more( $output )
    {
        if ( has_excerpt() && ! is_attachment() )
        {
            $output .= sb_continue_reading();
        }
        return $output;
    }
endif;

