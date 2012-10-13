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
	$('.carousel').carousel();
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


	if ($('.gallery').length) {
		$('.gallery').each(function(idx){
			var $c = $(this), cid = $c.attr("id"), $f = $('.figure', this), $t = $('.thumbnails', this), interval, img;
			/* show the caption? */
			if (gallerysettings[cid] && !gallerysettings[cid].caption) {
				$('.figcaption', this).hide();
			}
			/* check to see if there is more than one image */
			if (galleryimages[cid] && galleryimages[cid].length > 1) {
				/* time between slides */
				interval = gallerysettings[cid].interval || 5000;
				img = $('img:first', $t);
				/* set some data on the main image container */
				$.data($c[0], 'galleryData', {'currentImage':0});
				$f.css({'width':img.attr("width")+'px','height':img.attr("height")+'px','float':'none','clear':'left'});
				/* position image */
				img.css({'position':'absolute','left':0,'top':0});
				/* run slideshow */
				gallerytimer = setTimeout(function(){loadImage({'cid':cid,'img_idx':1,'loop':true});}, interval);
				$('.thumbnails a').click(function(e){
					/* pause slideshow */
					clearTimeout(gallerytimer);
					/* get container */
					var $c = $(this).parents('.gallery'),
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
			} else {
				$('.thumbnails', this).hide();
			}
		});
	}
	function loadImage(s)
	{
		var $c = $('#'+s.cid),
			$f = $('.figure', $c),
			loop = s.loop,
			t = galleryimages[cid],
			cur = $.data($c[0], 'galleryData').currentImage,
			nxt = ((cur + 1) >= t.length)? 0: (cur + 1),
			/* time between slides */
			interval = gallerysettings[s.cid].interval || 5000,
			/* duration of transition */
			transition = gallerysettings[s.cid].transition || 500,
			/* create a new image object to load the next image */
			img = new Image();
		/* load next image */
		img.onload = function() {
			var cap;
			$('<img src="'+t[nxt].src+'" title="'+t[nxt].title+'" alt="'+t[nxt].title+'" data-caption="'+t[nxt].caption+'" />').css({'opacity':0,'position':'absolute','left':0,'top':0}).appendTo($f);
			if (transition == 0) {
				$('img:last', $f).css({'opacity':1});
				$('img:first', $f).remove();
				$.data($c[0], 'galleryData', {'currentImage':nxt});
				if (loop) {
					gallerytimer = setTimeout(function(){go(cid, interval, transition);}, interval);
				}
			} else {
				$('img:first', $c).fadeOut(transition);
				$('img:last', $c).animate({'opacity':1}, transition, function(){
					$('img:first', $c).remove();
					if ($.browser.msie) {
						this.style.removeAttribute('filter');
					}
					$.data($c[0], 'galleryData', {'currentImage':nxt});
					if (loop) {
						gallerytimer = setTimeout(function(){go(cid, interval, transition);}, interval);
					}
				});
			}
			if (gallerysettings[cid] && gallerysettings[cid].caption) {
				cap = '<h3>'+s[nxt].title+'</h3><p>'+s[nxt].caption+'</p>';
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
		}
		img.src = s[nxt].src;
	}
});
