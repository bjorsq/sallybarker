<?php
/**
 * sallybarker.org theme content
 * @author Peter Edwards <pete@bjorsq.net>
 * @package WordPress
 * @subpackage sallybarker.org
 */
?>

	<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
		<header class="entry-header">
			<h1 class="entry-title"><a href="<?php the_permalink(); ?>" title="<?php printf( 'Permalink to %s', the_title_attribute( 'echo=0' ) ); ?>" rel="bookmark"><?php the_title(); ?></a></h1>
			<?php if ( 'post' == get_post_type() ) : ?>
			<div class="entry-meta">
				<?php sb_posted_on(); ?>
			</div><!-- .entry-meta -->
			<?php endif; ?>

		</header><!-- .entry-header -->

		<?php if ( is_search() || is_archive() ) :
		/* display excerpts/thumbnails for Search/Archives */ ?>
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
		<?php else : ?>
		<div class="entry-content">
			<?php the_content( 'Continue reading <span class="meta-nav">&rarr;</span>' ); ?>
			<?php wp_link_pages( array( 'before' => '<div class="page-link"><span>Pages:</span>', 'after' => '</div>' ) ); ?>
		</div><!-- .entry-content -->

		<footer class="entry-meta">
			<?php $show_sep = false; ?>
			<?php if ( 'post' == get_post_type() ) : // Hide category and tag text for pages on Search ?>
			<?php
				$categories_list = get_the_category_list( ', ' );
				if ( $categories_list ):
			?>
			<span class="cat-links">
				<?php printf( '<span class="entry-util entry-util-cat-links">Posted in</span> %s', $categories_list );
				$show_sep = true; ?>
			</span>
			<?php endif; // End if categories ?>
			<?php
				/* translators: used between list items, there is a space after the comma */
				$tags_list = get_the_tag_list( '', ', ' );
				if ( $tags_list ):
				if ( $show_sep ) : ?>
			<span class="sep"> | </span>
				<?php endif; // End if $show_sep ?>
			<span class="tag-links">
				<?php printf( '<span class="entry-util entry-util-tag-links">Tagged</span> %s', $tags_list );
				$show_sep = true; ?>
			</span>
			<?php endif; // End if $tags_list ?>
			<?php endif; // End if 'post' == get_post_type() ?>

			<?php if ( comments_open() ) : ?>
			<?php if ( $show_sep ) : ?>
			<span class="sep"> | </span>
			<?php endif; // End if $show_sep ?>
			<span class="comments-link"><?php comments_popup_link( '<span class="leave-reply">Leave a reply</span>', '<b>1</b> Reply', '<b>%</b> Replies' ); ?></span>
			<?php endif; // End if comments_open() ?>

			<?php edit_post_link( 'Edit', '<span class="edit-link">', '</span>' ); ?>
		</footer><!-- #entry-meta -->
		<?php endif; ?>
	</article><!-- #post-<?php the_ID(); ?> -->
