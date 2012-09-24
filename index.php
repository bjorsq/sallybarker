<?php
/**
 * sallybarker.org theme index
 * @author Peter Edwards <pete@bjorsq.net>
 * @package WordPress
 * @subpackage sallybarker.org
 */

get_header();
global $wp_query;
?>
<!--<pre><?php print_r($wp_query); ?></pre>-->
		<div id="content" role="main">

		<?php if ( have_posts() ) : ?>

			<?php /* Start the Loop */ ?>
			<?php while ( have_posts() ) : the_post(); ?>

				<?php get_template_part( 'content' ); ?>

			<?php endwhile; ?>


		<?php else : ?>

			<article id="post-0" class="post no-results not-found">
				<header class="entry-header">
					<h1 class="entry-title">Nothing Found</h1>
				</header><!-- .entry-header -->

				<div class="entry-content">
					<p>Sorry, your request didn't match anything on the website.</p>
				</div><!-- .entry-content -->
			</article><!-- #post-0 -->

		<?php endif; ?>

		</div><!-- #content -->

<?php get_footer(); ?>