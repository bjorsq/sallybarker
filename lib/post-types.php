<?php
/**
 * sallybarker.org theme post types
 * @author Peter Edwards <pete@bjorsq.net>
 * @package sallybarker.org
 * @version 1.0
 */

if ( ! class_exists('sb_post_types') ) :

/**
 * class creates two custom post types for the theme
 * - Art
 * - Education
 * @version 1.0
 */

class sb_post_types 
{
	/* prevents object instantiation */
	private final function __construct() {}
	private final function sb_post_types() {}

	/* registers everything with the Wordpress API */
	public static function register()
	{
		/* register custom post types and taxonomies */
		add_action( 'init', array('sb_post_types', 'register_post_types') );
	    /* put a taxonomy filter on custom post types */
	    add_action( 'restrict_manage_posts', array('sb_post_types', 'restrict_post_types_by_taxonomy') );
	    /* add a taxonomy column for custom post types */
	    add_filter( 'manage_posts_columns', array('sb_post_types', 'add_custom_taxonomy_column') );
	    /* populate taxonomy column */
	    add_action( 'manage_pages_custom_column', array('sb_post_types', 'custom_taxonomy_column') );
		/* add counts to the Right Now widget on the dashboard for the custom post types */
		add_action( 'right_now_content_table_end', array('sb_post_types', 'add_post_type_counts') );
	 	/* add filter to update messages */ 
		add_filter( 'post_updated_messages', array('sb_post_types', 'updated_messages' ) );
	}

	/**
	 * registers two custom post types for art and education
	 */
	public static function register_post_types()
	{
		$default_labels = array(
			'add_new' => 'Add New',
			'add_new_item' => 'Add New Page',
			'edit_item' => 'Edit Page',
			'new_item' => 'New page',
			'all_items' => 'All Pages',
			'view_item' => 'View Page',
			'search_items' => 'Search Pages',
			'not_found' =>  'No pages found',
			'not_found_in_trash' => 'No pages found in Trash', 
			'parent_item_colon' => ''
		);
		/* Art post type and taxonomy */
		$art_labels = array(
			'name' => 'Art',
			'singular_name' => 'Art',
			'menu_name' => 'Art'
		);
		register_post_type('art', array(
			'labels' => array_merge($default_labels, $art_labels),
			'public' => true,
			'query_var' => true,
			'capability_type' => 'page',
			'has_archive' => true, 
			'hierarchical' => true,
			'menu_position' => 21,
			'rewrite' => array( 'slug' => 'art', 'with_front' => false ),
			'supports' => array( 'title', 'editor', 'author', 'thumbnail', 'excerpt', 'comments' )
		) );
		$cat_labels = array(
	        'name' => 'Art Categories',
	        'singular_name' => 'Art Categories',
	        'search_items' => 'Search Art Categories',
	        'all_items' => 'All Art Categories',
	        'parent_item' => 'Parent Category',
	        'parent_item_colon' => 'Parent Category:',
	        'edit_item' => 'Edit Category', 
	        'update_item' => 'Update Category',
	        'add_new_item' => 'Add New Category',
	        'new_item_name' => 'New Art Category Name',
		);
	    register_taxonomy('art_category', array('art'), array(
	        'hierarchical' => true,
	        'labels' => $cat_labels,
	        'show_ui' => true,
	        'query_var' => true,
	        'rewrite' => array( 'slug' => 'pieces', 'with_front' => false, 'hierarchical' => true ),
	    ));
	    $education_labels = array(
			'name' => 'Education',
			'singular_name' => 'Education',
			'menu_name' => 'Education'
		);
		/* Education post type and taxonomy */
		register_post_type('education', array(
			'labels' => array_merge($default_labels, $education_labels),
			'public' => true,
			'query_var' => true,
			'capability_type' => 'page',
			'has_archive' => true, 
			'hierarchical' => true,
			'menu_position' => 22,
			'rewrite' => array( 'slug' => 'education', 'with_front' => false ),
			'supports' => array( 'title', 'editor', 'author', 'thumbnail', 'excerpt', 'comments' )
		) ); 
	    $cat_labels = array(
	        'name' => 'Education Categories',
	        'singular_name' => 'Education Categories',
	        'search_items' => 'Search Education Categories',
	        'all_items' => 'All Education Categories',
	        'parent_item' => 'Parent Category',
	        'parent_item_colon' => 'Parent Category:',
	        'edit_item' => 'Edit Category', 
	        'update_item' => 'Update Category',
	        'add_new_item' => 'Add New Category',
	        'new_item_name' => 'New Education Category Name',
	    ); 	
	    register_taxonomy('education_category', array('education'), array(
	        'hierarchical' => true,
	        'labels' => $cat_labels,
	        'show_ui' => true,
	        'query_var' => true,
	        'rewrite' => array( 'slug' => 'projects', 'with_front' => false, 'hierarchical' => true ),
	    ));
	}
	    
	/*
	 * adds a bit at the end of the content part of the Right Now widget
	 * on the dashboard to show counts for custom post types
	 */
	public static function add_post_type_counts()
	{
	    $status = array("art" => array("count" => 0), "education" => array("count" => 0));
	    $allPosts = get_posts(array(
	    	"numberposts" => -1,
	    	"post_status" => "any",
	    	"post_type" => array("art", "education")
		    ));
	    foreach ($allPosts as $p) {
	        if (!isset($status[$p->post_type][$p->post_status])) {
	        	$status[$p->post_type][$p->post_status] = 0;
	        }
	        $status[$p->post_type][$p->post_status]++;
	        $status[$p->post_type]["count"]++;
	    }
	    foreach ($status as $type => $s) {
	    	$total = $s["count"];
		    $published = isset($s["publish"])? $s["publish"]: 0;
		    $pending = isset($s["pending"])? $s["pending"]: 0;
		    $draft = isset($s["draft"])? $s["draft"]: 0;
		    $text = ($total != 0 && $total > 1)? ucfirst($type) . ' Posts': ucfirst($type) . ' Post';
		    if ( current_user_can( 'edit_posts' ) ) {
		        $total = "<a href='edit.php?post_type=$type'>$total</a>";
		        $text = sprintf('<a href="edit.php?post_type=%s">%s</a> (<a href="edit.php?post_status=publish&post_type=%s">%d published</a>', $type, $text, $type, $published);
		        if ($pending) {
		        	$text .= sprintf(', <a href="edit.php?post_status=pending&post_type=">%d pending review', $type, $pending);
		        }
		        if ($draft) {
		        	$text .= sprintf(', <a href="edit.php?post_status=draft&post_type=%s">%d draft', $type, $draft);
		        }
		        $text .= ")";
		    }
		    echo '<tr>';
		    echo '<td class="first b b-cats">' . $total . '</td>';
		    echo '<td class="t cats">' . $text . '</td>';
		    echo '</tr>';
		}
	}
	    
	/**
	 * hooks into restrict_manage_posts to filter post types by taxonomies
	 */
	public static function restrict_post_types_by_taxonomy()
	{
	    global $typenow;
	    switch ($typenow) {
	    	case "art":
	    	case "education":
	    		$terms = get_terms($typenow . '_category');
		        if ($terms && count($terms)) {
		            printf('<select name="%s_category" id="%s_category" class="postform"><option value="">Show All %s Categories</option>', $typenow, $typenow, ucfirst($typenow));
		            foreach ($terms as $term) {
		                $sel = (isset($_GET[$typenow . "_category"]) && ($_GET[$typenow . "_category"] == $term->slug))? ' selected="selected"' : '';
		                printf('<option value="%s"%s>%s (%s)</option>', $term->slug, $sel, $term->name, $term->count);
		            }
		            print('</select>');
		        }
	    }
	}

	/**
	 * hooks into manage_posts_columns to add a column for the custom taxonomy
	 */
	public static function add_custom_taxonomy_column($defaults)
	{
	    global $typenow;
	    $defaults[$typenow . "_category"] = "Category";
	    return $defaults;
	}
	 
	/**
	 * hooks into manage_pages_custom_column to populate the taxonomy column with terms
	 */
	public static function custom_taxonomy_column($column_name)
	{
	    global $post;
	    switch ( $column_name ) 
	    {
	    	case "art_category":
			case "education_category":
	    		$terms = get_the_term_list( $post->ID , $column_name , '' , ',' , '' );
				if ( is_string( $terms ) ) {
					echo $terms;
				}  
				else 
				{
					echo '<em>Uncategorised</em>';
				}
				break;
	    }
	}

	/**
	 * updates all update messages for custom post types
	 */
	public static function updated_messages( $messages ) {
	    global $post, $post_ID;
	    $messages["art"] = $messages["education"] = array(
			0 => '', // Unused. Messages start at index 1.
			1 => sprintf( 'Page updated. <a href="%s">View page</a>', esc_url( get_permalink($post_ID) ) ),
	    	2 => 'Custom field updated.',
	    	3 => 'Custom field deleted.',
	    	4 => 'Page updated.',
			5 => isset($_GET['revision']) ? sprintf( 'Page restored to revision from %s', wp_post_revision_title( (int) $_GET['revision'], false ) ) : false,
			6 => sprintf( 'Page published. <a href="%s">View page</a>', esc_url( get_permalink($post_ID) ) ),
			7 => 'Page saved.',
			8 => sprintf( 'Page submitted. <a target="_blank" href="%s">Preview page</a>', esc_url( add_query_arg( 'preview', 'true', get_permalink($post_ID) ) ) ),
			9 => sprintf( 'Page scheduled for: <strong>%1$s</strong>. <a target="_blank" href="%2$s">Preview page</a>', date_i18n( 'M j, Y @ G:i', strtotime( $post->post_date ) ), esc_url( get_permalink($post_ID) ) ),
			10 => sprintf( 'Draft updated. <a target="_blank" href="%s">Preview page</a>', esc_url( add_query_arg( 'preview', 'true', get_permalink($post_ID) ) ) ),
		);
		return $messages;
	}

} /* end class definition */

/* register with Wordpress API */
sb_post_types::register();

endif;