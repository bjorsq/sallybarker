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
					<header class="entry-header">
						<h1 class="entry-title"><a href="<?php the_permalink(); ?>" title="<?php printf( 'Permalink to %s', the_title_attribute( 'echo=0' ) ); ?>" rel="bookmark"><?php the_title(); ?></a></h1>
						<?php if ( 'post' == get_post_type() ) : ?>
						<div class="entry-meta">
							<?php sb_posted_on(); ?>
						</div><!-- .entry-meta -->
						<?php endif; ?>

					</header><!-- .entry-header -->
						<?php
						if (has_post_thumbnail()) :
						
							$tid = get_post_thumbnail_id($post->ID);
							$att = get_post($tid);
							$caption = $att->post_excerpt;
							$print_size = wp_get_attachment_image_src($tid, 'post-thumbnail');
							$caption_width_attr = ($print_size)? ' style="width:' . $print_size[1] . 'px;"': '';
						?>
					<div class="figure featured-image">
						<?php echo wp_get_attachment_image($tid, $thumbnail_size); ?>
						<?php  if ($caption) { ?>
							<div class="figcaption"<?php echo $caption_width_attr; ?>>
								<p><?php echo $caption; ?></p>
							</div>
						<?php } ?>
					</div>
						<?php endif; ?>
					<div class="entry-summary">
						<?php the_excerpt(); ?>
					</div><!-- .entry-summary -->
				</article><!-- #post-<?php the_ID(); ?> -->
			</div>
