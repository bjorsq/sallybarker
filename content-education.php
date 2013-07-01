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
					<?php echo do_shortcode('[gallery]'); ?>
					<header class="entry-header">
						<h1 class="entry-title"><a href="<?php the_permalink(); ?>" title="<?php printf( 'Permalink to %s', the_title_attribute( 'echo=0' ) ); ?>" rel="bookmark"><?php the_title(); ?></a></h1>
						<?php if ( 'post' == get_post_type() ) : ?>
						<div class="entry-meta">
							<?php sb_posted_on(); ?>
						</div><!-- .entry-meta -->
						<?php endif; ?>
					</header><!-- .entry-header -->
					<div class="sidebar-nav">
						<?php sb_menus::category_menu(); ?>
					</div><!--/.well -->
					<div class="entry-content">
						<?php the_content( 'Continue reading <span class="meta-nav">&rarr;</span>' ); ?>
						<?php wp_link_pages( array( 'before' => '<div class="page-link"><span>Pages:</span>', 'after' => '</div>' ) ); ?>
					</div><!-- .entry-content -->

					<footer class="entry-meta">
						<?php sb_entry_meta(); ?>
					</footer><!-- #entry-meta -->
				</article><!-- #post-<?php the_ID(); ?> -->
			</div>
