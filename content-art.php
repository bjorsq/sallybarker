<?php
/**
 * sallybarker.org theme content
 * @author Peter Edwards <pete@bjorsq.net>
 * @package WordPress
 * @subpackage sallybarker.org
 */
?>
			<div class="row-fluid">
				<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
					<?php
					if (!has_term('comics', 'art_category')) {
						echo do_shortcode('[gallery]'); 
					}?>
					<header class="entry-header">
						<h1 class="entry-title"><a href="<?php the_permalink(); ?>" title="<?php printf( 'Permalink to %s', the_title_attribute( 'echo=0' ) ); ?>" rel="bookmark"><?php the_title(); ?></a></h1>
					</header><!-- .entry-header -->
					<div class="sidebar-nav">
						<?php sb_menus::category_menu($post); ?>
					</div><!--/.well -->
					<div class="entry-content">
						<?php the_content( 'Continue reading <span class="meta-nav">&rarr;</span>' ); ?>
						<?php wp_link_pages( array( 'before' => '<div class="page-link"><span>Pages:</span>', 'after' => '</div>' ) ); ?>
					</div><!-- .entry-content -->

				</article><!-- #post-<?php the_ID(); ?> -->
			</div>
