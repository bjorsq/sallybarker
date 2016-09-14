<?php
/**
 * sallybarker.org theme footer
 * @author Peter Edwards <pete@bjorsq.net>
 * @package WordPress
 * @subpackage sallybarker.org
 */
?>
				<div class="page-footer row-fluid">
					<div class="col">
						<h3><a href="<?php bloginfo('url'); ?>/art/">Art</a></h3>
						<?php sb_menus::menu("art"); ?>
					</div>
					<div class="col">
						<h3><a href="<?php bloginfo('url'); ?>/education/">Employment</a></h3>
						<?php sb_menus::menu("education"); ?>
					</div>
					<div class="col last">
						<?php 
						$latest = get_posts(array(
							'numberposts' => 3,
							'orderby'     => 'date',
							'order'       => 'DESC'
						));
						if (count($latest)) {
							printf('<h3><a href="%s/blog/">Latest Posts</a></h3><ul>', get_bloginfo('url'));
							foreach ($latest as $lp) {
								printf('<li><a href="%s">%s</a></li>', get_permalink($lp->ID), $lp->post_title);
							}
							print('</ul>');
						}
						?>
					</div>
					<div class="clear"></div>
				</div>
			</div><!-- #main fluid container -->
			<div class="push"></div>
		</div><!-- #wrap -->

		<div class="footer">

				
		</div>

	<?php wp_footer(); ?>

	</body>
</html>