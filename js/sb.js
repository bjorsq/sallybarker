/**
 * sb.js - Javascript for sallybarker.org wordpress theme
 * @author Peter Edwards
 * @requires jQuery
 */

 /* add a js class to the html element to target CSS */
document.documentElement.className = 'js';
/* global variables for timers */
var slidetimer = null,
	gallerytimer = null;

jQuery(function($){
	/* carousels */
	//$('.carousel').carousel();
	/* make slideshows */
	if ($('.slideshow').length) {
		$('.slideshow').each(function(idx){
			/* container for slides */
			var $c = $(this), cid = $c.attr("id"), interval, img, slidesnav;
			/* show the caption? */
			if (slidesettings[cid] && (slidesettings[cid].nav || slidesettings[cid].caption)) {
				$('.slideshow-caption', this).show();
				if (!slidesettings[cid].caption) {
					$('.slideshow-captiontext').hide();
				}
			}
			/* check to see if there is more than one slide */
			if (slides[cid] && slides[cid].length > 1) {
				/* time between slides */
				interval = slidesettings[cid].interval || 5000;
				img = $('img:first', $c);
				/* set some data on the container */
				$.data($c[0], 'slideData', {'currentSlide':0});
				$c.css({'width':img.attr("width")+'px','height':img.attr("height")+'px','float':'none','clear':'left'});
				/* position image */
				img.css({'position':'absolute','left':0,'top':0});
				/* run slideshow */
				slidetimer = setTimeout(function(){go(cid);}, interval);
			}
			if (slidesettings[cid] && slidesettings[cid].nav) {
				if (slides[cid] && slides[cid].length > 1) {
					slidesnav = $('<div/>').addClass("slideshow-controls");
					for (i = 0; i < slides[cid].length; i++) {
						slidesnav.append('<a rel="'+i+'" href="#'+i+'" id="slideshow-'+cid+'slide'+i+'" class="slideshow-link'+((i == $.data($c[0], 'slideData').currentSlide)? ' currentslide': '')+'">&bull;</a>');
					}
					$('.slideshow-caption', this).append(slidesnav);
				}
			}
		});
	}
	function go(container_id) {
		var cid = container_id, 
			$c = $("#"+cid),
			s = slides[cid],
			cur = $.data($c[0], 'slideData').currentSlide,
			nxt = ((cur + 1) >= s.length)? 0: (cur + 1),
			/* time between slides */
			interval = slidesettings[cid].interval || 5000,
			/* duration of transition */
			transition = slidesettings[cid].transition || 500,
			/* create a new image object to load the next slide */
			img = new Image();
		/* load next image and add to the slideshow */
		img.onload = function() {
			var cap;
			$('<img src="'+s[nxt].src+'" title="'+s[nxt].title+'" alt="'+s[nxt].title+'" />').css({'opacity':0,'position':'absolute','left':0,'top':0}).appendTo($c);
			if (transition == 0) {
				$('img:last', $c).css({'opacity':1});
				$('img:first', $c).remove();
				$.data($c[0], 'slideData', {'currentSlide':nxt});
				slidetimer = setTimeout(function(){go(cid, interval, transition);}, interval);
			} else {
				$('img:first', $c).fadeOut(transition);
				$('img:last', $c).animate({'opacity':1}, transition, function(){
					$('img:first', $c).remove();
					if ($.browser.msie) {
						this.style.removeAttribute('filter');
					}
					$.data($c[0], 'slideData', {'currentSlide':nxt});
					slidetimer = setTimeout(function(){go(cid, interval, transition);}, interval);
				});
			}
			if (slidesettings[cid] && slidesettings[cid].caption) {
				cap = (slidesettings[cid] && slidesettings[cid].usetitle)? '<h3>'+s[nxt].title+'</h3>'+s[nxt].caption: s[nxt].caption;
				if (transition == 0) {
					$('.slideshow-captiontext', $c).html(cap);
				} else {
					$('.slideshow-captiontext', $c).fadeOut((transition / 2), function(){
						$(this).html(cap);
						if ($.trim(cap) !== "") {
							$(this).fadeIn((transition / 2));
						}
					});
				}
			}
			if (slidesettings[cid] && slidesettings[cid].nav) {
				$('.slideshow-link', $c).removeClass("currentslide");
				$('#slideshow-'+cid+'slide'+nxt).addClass("currentslide");
			}
			/*if (slidesettings[cid] && slidesettings[cid].callback && window[slidesettings[cid].callback]) {
			window[slidesettings[cid].callback](container_id, nxt);
			}*/
		}
		img.src = s[nxt].src;
	}
	$('.slideshow-link').click(function(e){
		/* pause slideshow */
		clearTimeout(slidetimer);
		/* get container */
		var $c = $(this).parents('.slideshow'),
			cid = $c.attr("id"),
			/* get target slide index */
			targetSlide = parseInt($(this).attr("rel"));
		/* set currentSlide to the previous slide */
		$.data($c[0], 'slideData', {'currentSlide':((targetSlide == 0)? (slides[cid].length - 1): (targetSlide - 1))});
		/* start the slideshow again */
		go(cid);
		e.preventDefault();
		return false;
	});

	var loadImage = function(s)
	{
		logToConsole(s);
		var cid = s.cid,
			loop = s.loop,
			img_idx = s.img_idx,
			$c = $('#'+s.cid),
			$f = $('.figure', $c),
			t = galleryimages[cid],
			/* get current image */
			cur = $.data($c[0], 'galleryData').currentImage,
			/* get next image */
			nxt = ((cur + 1) >= t.length)? 0: (cur + 1),
			/* time between slides */
			interval = gallerysettings[cid].interval || 5000,
			/* duration of transition */
			transition = gallerysettings[cid].transition || 500,
			/* create a new image object to load the next image */
			img = new Image();
		logToConsole($.data($c[0], 'galleryData').currentImage);
		logToConsole("preparing to load image");
		logToConsole("image index: "+img_idx);
		if (img_idx === false) {
			img_idx = nxt;
			logToConsole('image index set to next available: '+nxt);
		}
		if (t[img_idx]) {
			logToConsole("loading image:"+t[img_idx].full_src);
			/* load image */
			img.onload = function() {
				var cap;
				logToConsole("image loaded - adding slide and fading in");
				$('<img src="'+t[img_idx].full_src+'" title="'+t[img_idx].title+'" alt="'+t[img_idx].title+'" data-caption="'+t[img_idx].caption+'" />').css({'opacity':0,'position':'absolute','left':0,'top':0}).appendTo($f);
				$.data($c[0], 'galleryData', {'currentImage':img_idx});
				if (transition == 0) {
					$('img:last', $f).css({'opacity':1,'position':'relative'});
					$('img:first', $f).remove();
			        logToConsole("fade in complete in 0s (no transition)");
					if (loop) {
						logToConsole('looping loadImage for next image in sequence');
						gallerytimer = setTimeout(function(){loadImage({'cid':cid,'img_idx':false,'loop':true});}, interval);
					}
				} else {
					//$('img:first', $c).fadeOut(transition);
					$('img:last', $f).animate({'opacity':1}, transition, function(){
						$('img:first', $f).remove();
						$(this).css({'position':'relative'})
						if ($.browser.msie) {
							this.style.removeAttribute('filter');
						}
						if (loop) {
							gallerytimer = setTimeout(function(){loadImage({'cid':cid,'img_idx':false,'loop':true});}, interval);
						}
					});
				}
		        /* make the thumbnail active */
		        $('.thumb-link', $c).removeClass('active');
			    $('.thumb-link', $c).eq(img_idx).addClass('active');
				if (gallerysettings[cid] && gallerysettings[cid].caption) {
					cap = '<h3>'+t[img_idx].title+'</h3><p>'+t[img_idx].caption+'</p>';
					if (transition == 0) {
						$('.figcaption', $c).html(cap);
					} else {
						$('.figcaption', $c).fadeOut((transition / 2), function(){
							$(this).html(cap);
							if ($.trim(cap) !== "") {
								$(this).fadeIn((transition / 2));
							}
						});
					}
				}
				reorganiseThumbnails(cid, img_idx);
			}
			img.src = t[img_idx].full_src;
		}
	},
	reorganiseThumbnails = function(cid, img_idx)
	{
		logToConsole("reorganiseThumbnails");
		var $c = $('#'+cid),
			t = galleryimages[cid],
			$t = $('.thumbnails ul', $c),
			img_width = ($t.width() / t.length),
			cw = $c.width(),
			tw = $t.width(),
			offset = 0,
			pos_actual = ((img_idx - 0.5) * img_width),
			centre = (cw / 2);
		logToConsole("thumbnail strip width: "+tw+"\nthumbnail container width: "+cw+"\nimage width: "+img_width+"\ncurrent image index: "+img_idx+"\nactual position in strip: "+pos_actual+"\nnumber of images: "+t.length);
		if (tw > cw) {
			logToConsole("width of thumbnails exceeds that of their container");
			if (pos_actual > centre) {
				logToConsole("thumbnail position is past the centre - determining if the thumbnails need to scroll");
				offset = -((pos_actual + img_width) - centre);
				if ((tw + offset + (img_width/2)) < cw) {
					logToConsole("setting offset so thumbnails scroll to the end")
					offset = (cw - tw) + 12;
				}
			} else {
				offset = 0;
			}
			logToConsole("offset by: "+offset);
			if ($t.css("marginLeft") != offset+'px') {
				logToConsole("need to scroll thumbnails (marginLeft is "+$t.css("marginLeft")+")");
				$t.animate({'marginLeft':offset+'px'}, 500, function(){
					logToConsole("thumbnails scrolled to new position");
				});
			} else {
				logToConsole("no need to scroll thumbnails (marginLeft is same as offset)");
			}
		}
	}
	if ($('.gallery').length) {
		$('.gallery').each(function(idx){
			var $c = $(this), 
				cid = $c.attr("id"), 
				interval;
			/* show the caption on mouseover */
			$('.figure', this).on({
				'mouseenter':function(){
					$('.figcaption', this).fadeIn(200);
				},
				'mouseleave':function(){
					$('.figcaption', this).fadeOut(200);
				}
			});
			/* check to see if there is more than one image */
			if (galleryimages[cid] && galleryimages[cid].length > 1) {
				/* time between slides */
				interval = gallerysettings[cid].interval || 5000;
				/* set some data on the main image container */
				$.data($c[0], 'galleryData', {'currentImage':0});
				/* run slideshow */
				gallerytimer = setTimeout(function(){loadImage({'cid':cid,'img_idx':false,'loop':true});}, interval);
				$('.thumb-link', this).on('click',function(e){
					/* pause slideshow */
					clearTimeout(gallerytimer);
					/* get container */
					var $c = $(this).parents('.gallery'),
						cid = $c.attr("id"),
						/* get target slide index */
						targetSlide = parseInt($(this).attr("rel").substr(5));
					/* set current image */
					loadImage({'cid':cid,'img_idx':targetSlide,'loop':false});
					e.preventDefault();
					return false;
				});
			} else {
				$('.thumbnails', this).hide();
			}
		});
	}
	$('#imageCarousel').carousel({interval:9000});
	var debug = true;
	function logToConsole(msg)
	{
		if (window.console && debug) {
			console.log(msg);
		}
	}
});
