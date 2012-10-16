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
		console.log(s);
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
		console.log($.data($c[0], 'galleryData').currentImage);
		console.log("preparing to load image");
		console.log("image index: "+img_idx);
		if (img_idx === false) {
			img_idx = nxt;
			console.log('image index set to next available: '+nxt);
		}
		if (t[img_idx]) {
			console.log("loading image:"+t[img_idx].full_src);
			/* load image */
			img.onload = function() {
				var cap;
				console.log("image loaded - adding slide and fading in");
				$('<img src="'+t[img_idx].full_src+'" title="'+t[img_idx].title+'" alt="'+t[img_idx].title+'" data-caption="'+t[img_idx].caption+'" />').css({'opacity':0,'position':'absolute','left':0,'top':0}).appendTo($f);
				$.data($c[0], 'galleryData', {'currentImage':img_idx});
				if (transition == 0) {
					$('img:last', $f).css({'opacity':1,'position':'relative'});
					$('img:first', $f).remove();
			        console.log("fade in complete in 0s (no transition)");
					if (loop) {
						console.log('looping loadImage for next image in sequence');
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
		var $c = $('#'+cid),
			t = galleryimages[cid],
			$t = $('.thumbnails ul', $c);
		console.log($t.width());
		console.log($c.width());
		if ($t.width() > $c.width()) {


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

});
/* ============================================================
 * bootstrap-dropdown.js v2.1.1
 * http://twitter.github.com/bootstrap/javascript.html#dropdowns
 * ============================================================
 * Copyright 2012 Twitter, Inc.
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 * http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 * ============================================================ */


!function ($) {



 /* DROPDOWN CLASS DEFINITION
  * ========================= */

  var toggle = '[data-toggle=dropdown]'
    , Dropdown = function (element) {
        var $el = $(element).on('click.dropdown.data-api', this.toggle)
        $('html').on('click.dropdown.data-api', function () {
          $el.parent().removeClass('open')
        })
      }

  Dropdown.prototype = {

    constructor: Dropdown

  , toggle: function (e) {
      var $this = $(this)
        , $parent
        , isActive

      if ($this.is('.disabled, :disabled')) return

      $parent = getParent($this)

      isActive = $parent.hasClass('open')

      clearMenus()

      if (!isActive) {
        $parent.toggleClass('open')
        $this.focus()
      }

      return false
    }

  , keydown: function (e) {
      var $this
        , $items
        , $parent
        , isActive
        , index

      if (!/(38|40|27)/.test(e.keyCode)) return

      $this = $(this)

      e.preventDefault()
      e.stopPropagation()

      if ($this.is('.disabled, :disabled')) return

      $parent = getParent($this)

      isActive = $parent.hasClass('open')

      if (!isActive || (isActive && e.keyCode == 27)) return $this.click()

      $items = $('[role=menu] li:not(.divider) a', $parent)

      if (!$items.length) return

      index = $items.index($items.filter(':focus'))

      if (e.keyCode == 38 && index > 0) index--                                        // up
      if (e.keyCode == 40 && index < $items.length - 1) index++                        // down
      if (!~index) index = 0

      $items
        .eq(index)
        .focus()
    }

  }

  function clearMenus() {
    getParent($(toggle))
      .removeClass('open')
  }

  function getParent($this) {
    var selector = $this.attr('data-target')
      , $parent

    if (!selector) {
      selector = $this.attr('href')
      selector = selector && /#/.test(selector) && selector.replace(/.*(?=#[^\s]*$)/, '') //strip for ie7
    }

    $parent = $(selector)
    $parent.length || ($parent = $this.parent())

    return $parent
  }


  /* DROPDOWN PLUGIN DEFINITION
   * ========================== */

  $.fn.dropdown = function (option) {
    return this.each(function () {
      var $this = $(this)
        , data = $this.data('dropdown')
      if (!data) $this.data('dropdown', (data = new Dropdown(this)))
      if (typeof option == 'string') data[option].call($this)
    })
  }

  $.fn.dropdown.Constructor = Dropdown


  /* APPLY TO STANDARD DROPDOWN ELEMENTS
   * =================================== */

  $(function () {
    $('html')
      .on('click.dropdown.data-api touchstart.dropdown.data-api', clearMenus)
    $('body')
      .on('click.dropdown touchstart.dropdown.data-api', '.dropdown form', function (e) { e.stopPropagation() })
      .on('click.dropdown.data-api touchstart.dropdown.data-api'  , toggle, Dropdown.prototype.toggle)
      .on('keydown.dropdown.data-api touchstart.dropdown.data-api', toggle + ', [role=menu]' , Dropdown.prototype.keydown)
  })

}(window.jQuery);/* =============================================================
 * bootstrap-collapse.js v2.1.1
 * http://twitter.github.com/bootstrap/javascript.html#collapse
 * =============================================================
 * Copyright 2012 Twitter, Inc.
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 * http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 * ============================================================ */


!function ($) {



 /* COLLAPSE PUBLIC CLASS DEFINITION
  * ================================ */

  var Collapse = function (element, options) {
    this.$element = $(element)
    this.options = $.extend({}, $.fn.collapse.defaults, options)

    if (this.options.parent) {
      this.$parent = $(this.options.parent)
    }

    this.options.toggle && this.toggle()
  }

  Collapse.prototype = {

    constructor: Collapse

  , dimension: function () {
      var hasWidth = this.$element.hasClass('width')
      return hasWidth ? 'width' : 'height'
    }

  , show: function () {
      var dimension
        , scroll
        , actives
        , hasData

      if (this.transitioning) return

      dimension = this.dimension()
      scroll = $.camelCase(['scroll', dimension].join('-'))
      actives = this.$parent && this.$parent.find('> .accordion-group > .in')

      if (actives && actives.length) {
        hasData = actives.data('collapse')
        if (hasData && hasData.transitioning) return
        actives.collapse('hide')
        hasData || actives.data('collapse', null)
      }

      this.$element[dimension](0)
      this.transition('addClass', $.Event('show'), 'shown')
      $.support.transition && this.$element[dimension](this.$element[0][scroll])
    }

  , hide: function () {
      var dimension
      if (this.transitioning) return
      dimension = this.dimension()
      this.reset(this.$element[dimension]())
      this.transition('removeClass', $.Event('hide'), 'hidden')
      this.$element[dimension](0)
    }

  , reset: function (size) {
      var dimension = this.dimension()

      this.$element
        .removeClass('collapse')
        [dimension](size || 'auto')
        [0].offsetWidth

      this.$element[size !== null ? 'addClass' : 'removeClass']('collapse')

      return this
    }

  , transition: function (method, startEvent, completeEvent) {
      var that = this
        , complete = function () {
            if (startEvent.type == 'show') that.reset()
            that.transitioning = 0
            that.$element.trigger(completeEvent)
          }

      this.$element.trigger(startEvent)

      if (startEvent.isDefaultPrevented()) return

      this.transitioning = 1

      this.$element[method]('in')

      $.support.transition && this.$element.hasClass('collapse') ?
        this.$element.one($.support.transition.end, complete) :
        complete()
    }

  , toggle: function () {
      this[this.$element.hasClass('in') ? 'hide' : 'show']()
    }

  }


 /* COLLAPSIBLE PLUGIN DEFINITION
  * ============================== */

  $.fn.collapse = function (option) {
    return this.each(function () {
      var $this = $(this)
        , data = $this.data('collapse')
        , options = typeof option == 'object' && option
      if (!data) $this.data('collapse', (data = new Collapse(this, options)))
      if (typeof option == 'string') data[option]()
    })
  }

  $.fn.collapse.defaults = {
    toggle: true
  }

  $.fn.collapse.Constructor = Collapse


 /* COLLAPSIBLE DATA-API
  * ==================== */

  $(function () {
    $('body').on('click.collapse.data-api', '[data-toggle=collapse]', function (e) {
      var $this = $(this), href
        , target = $this.attr('data-target')
          || e.preventDefault()
          || (href = $this.attr('href')) && href.replace(/.*(?=#[^\s]+$)/, '') //strip for ie7
        , option = $(target).data('collapse') ? 'toggle' : $this.data()
      $this[$(target).hasClass('in') ? 'addClass' : 'removeClass']('collapsed')
      $(target).collapse(option)
    })
  })

}(window.jQuery);/* ===================================================
 * bootstrap-transition.js v2.1.1
 * http://twitter.github.com/bootstrap/javascript.html#transitions
 * ===================================================
 * Copyright 2012 Twitter, Inc.
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 * http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 * ========================================================== */


!function ($) {

  $(function () {



    /* CSS TRANSITION SUPPORT (http://www.modernizr.com/)
     * ======================================================= */

    $.support.transition = (function () {

      var transitionEnd = (function () {

        var el = document.createElement('bootstrap')
          , transEndEventNames = {
               'WebkitTransition' : 'webkitTransitionEnd'
            ,  'MozTransition'    : 'transitionend'
            ,  'OTransition'      : 'oTransitionEnd otransitionend'
            ,  'transition'       : 'transitionend'
            }
          , name

        for (name in transEndEventNames){
          if (el.style[name] !== undefined) {
            return transEndEventNames[name]
          }
        }

      }())

      return transitionEnd && {
        end: transitionEnd
      }

    })()

  })

}(window.jQuery);