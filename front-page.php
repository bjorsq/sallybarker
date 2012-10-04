<?php
/**
 * sallybarker.org theme home page
 * @author Peter Edwards <pete@bjorsq.net>
 * @package WordPress
 * @subpackage sallybarker.org
 */

get_header();

if ( have_posts() ) : 
	while ( have_posts() ) : the_post(); 
		get_template_part( 'content', 'home' );
	endwhile; 
else :
	get_template_part( 'content', '404');
endif;

get_footer(); 
?>