<?php
/**
 * sallybarker.org theme header
 * @author Peter Edwards <pete@bjorsq.net>
 * @package WordPress
 * @subpackage sallybarker.org
 */
?><!DOCTYPE html>
<html lang="en">
	<head>
		<meta charset="<?php bloginfo( 'charset' ); ?>">
		<meta name="viewport" content="width=device-width, initial-scale=1.0">
		<title><?php
			/*
			 * Print the <title> tag based on what is being viewed.
			 */
			wp_title( '|', true, 'right' );

			// Add the blog name.
			bloginfo( 'name' );

			// Add a page number if necessary:
			global $page, $paged;
			if ( $paged >= 2 || $page >= 2 )
				echo ' | ' . sprintf( 'Page %s', max( $paged, $page ) );

			?></title>
		<link rel="profile" href="http://gmpg.org/xfn/11" />
		<link rel="stylesheet" type="text/css" media="all" href="<?php echo get_stylesheet_directory_uri(); ?>/css/sb.css" />
		<?php wp_head(); ?>

  		<script>window.jQuery || document.write('<script src="<?php echo get_stylesheet_directory_uri(); ?>/js/libs/jquery-1.7.2.min.js"><\/script>')</script>
		<!--[if lt IE 9]>
		  <script src="http://html5shim.googlecode.com/svn/trunk/html5.js"></script>
		<![endif]-->
		<!--[if !IE 7]>
			<style type="text/css">
				#wrap {display:table;height:100%}
			</style>
		<![endif]-->
		<!-- Le fav and touch icons -->
		<link rel="shortcut icon" href="<?php echo get_stylesheet_directory_uri(); ?>/images/icons/favicon.ico">
		<link rel="apple-touch-icon-precomposed" sizes="144x144" href="<?php echo get_stylesheet_directory_uri(); ?>/images/icons/apple-touch-icon-144-precomposed.png">
		<link rel="apple-touch-icon-precomposed" sizes="114x114" href="<?php echo get_stylesheet_directory_uri(); ?>/images/icons/apple-touch-icon-114-precomposed.png">
		<link rel="apple-touch-icon-precomposed" sizes="72x72" href="<?php echo get_stylesheet_directory_uri(); ?>/images/icons/apple-touch-icon-72-precomposed.png">
		<link rel="apple-touch-icon-precomposed" href="<?php echo get_stylesheet_directory_uri(); ?>/images/icons/apple-touch-icon-57-precomposed.png">
	</head>

	<body <?php body_class(); ?>>

		<div id="wrap">

			<!-- top navbar -->
			<div class="navbar top-navbar">
				<div class="navbar-inner">
					<div class="container-fluid">

						<!-- menu button -->
						<a class="btn btn-navbar" data-toggle="collapse" data-target=".nav-collapse">
							<span class="icon-bar"></span>
							<span class="icon-bar"></span>
							<span class="icon-bar"></span>
						</a>

						<!-- logo -->
						<a class="brand" href="<?php echo get_bloginfo('url'); ?>">Sally Barker</a>


						<!- top navbar -->
						<div class="nav-collapse collapse">
							<?php sb_menus::top(); ?>
						</div><!--/.nav-collapse -->
					</div>
				</div>
			</div>

			<!-- main fluid container -->
			<div class="container">

