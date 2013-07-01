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
						<h2 class="entry-title"><a href="<?php the_permalink(); ?>" title="<?php the_title_attribute(); ?>" rel="bookmark"><?php the_title(); ?></a></h2>
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
						<a href="<?php the_permalink(); ?>" title="<?php the_title_attribute(); ?>" rel="bookmark"><img src="<?php echo $print_size[0]; ?>" /></a>
						<?php  if ($caption) { ?>
							<div class="figcaption"<?php echo $caption_width_attr; ?>>
								<p><?php echo $caption; ?></p>
							</div>
						<?php } ?>
					</div>
						<?php endif; ?>
					<div class="entry">
						<?php the_content(); ?>
					</div><!-- .entry -->
				</article><!-- #post-<?php the_ID(); ?> -->
			</div>
