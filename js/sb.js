/**
 * sb.js - Javascript for sallybarker.org wordpress theme
 * @author Peter Edwards
 * @requires jQuery
 */
 jQuery(function($){
  if ($('#slideheader').length) {
    var w = $('#slideheader').width();
    var h = $('#slideheader').height();
    var $slides = $('#slideheader div');
    var ns = $slides.length;
    var offset = Math.round(((w/3)/ns));
    $slides.each(function(idx){
      $(this).css({left:(offset*idx),width:w,height:h,zIndex:idx,cursor:'pointer'});
      $(this).data({'idx':idx});
      $(this).bind('click',function(e){
        var cur_idx = $(this).data('idx');
        var cover = ($(this).css("left") == ((cur_idx*offset)+'px'))? false: true;
        $slides.each(function(){
          this_idx = $(this).data('idx');
          if (!cover) {
            if (this_idx > cur_idx){
              $(this).animate({'left':w-(offset*(ns-this_idx))},1000);
            } else {
            }
          } else {
            if (this_idx <= cur_idx) {
              $(this).animate({'left':(offset*this_idx)},1000);
            } else {
              
            }
          }
        });
      });
    });
  }
});