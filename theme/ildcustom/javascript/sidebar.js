var $jq = jQuery.noConflict();
$jq(document).ready(function() {
  
  // block-region-side-post
// data = toggleflag
initOcAside(); 


function initOcAside() {
   var b = $jq(window).innerWidth();
   var t = "u";
   if (b <= 991) {
     t = "s";
   } else {
     t = "w";
   }
   $jq('#post-block-toggle').data("vp",t);
   
  var abH = $jq('#post-block-toggle').position();
  var abH_y = abH.top;
  $jq('#post-block-toggle').data("originaltop",abH_y);
  // console.log("original "+abH_y);
  
}
if ($jq(window).innerWidth() <=991) {
  
  // console.log("eingeklappt");
}

 
 $jq(window).resize(function() {
 var b = $jq(window).innerWidth();
 
 var tf = $jq('#post-block-toggle').data("toggleflag");
 //console.log("tf = "+tf);
 // 767 -> 991px
 // console.log("innerwidth = "+b);
 
   b = 999999;
 
 
 if (b <=974) {
    $jq('#block-region-side-post').addClass("magicEffect");
     if ($jq('#block-region-side-post').data("toggleflag") == 2) {
        var abH = $jq('#post-block-toggle').position();
          var abH_y = abH.top;
           abH_y -= 15;
            $jq('#block-region-side-post').css({
            "top" : abH_y + "px" 
        });
         $jq('#block-region-side-post').data("store",abH_y);
         
        $jq('#block-region-side-post').show();
     } else {
       $jq('#block-region-side-post').hide();
     }
 } else {
    $jq('#block-region-side-post').removeClass("magicEffect");
    $jq('#block-region-side-post').show();
     $jq('#block-region-side-post').css({
        "top" : "0px"
     });
     
     if (tf == 2) {
        $jq('#block-region-side-post').addClass("magicEffect");
          $jq('#block-region-side-post').show();
          tmp =  $jq('#post-block-toggle').data("originaltop");
          console.log("restoring original "+tmp);
          
          var abH = $jq('#post-block-toggle').position();
          var abH_y = abH.top;
          abH_y -= 15;
           
          $jq('#block-region-side-post').css({ "top" : abH_y+"px"});
     }
 }
 });
 
    
  // post-block-toggle' data-toggleflag='1' class='block-toggle pull-right
   $jq('#post-block-toggle').click(function() {
    var _cond = $jq(this).data("toggleflag");
    if (_cond == 1) {
      $jq(this).data("toggleflag",2);
    } else {
       $jq(this).data("toggleflag",1);
    }
     var _cond = $jq(this).data("toggleflag");
    if (_cond == 2) {
      $jq('#block-region-side-post').addClass("magicEffect");
	  $jq('#post-block-toggle').addClass("button-hide-region");
      $jq('#block-region-side-post').hide();
      
      // page-content
      var abH = $jq('#post-block-toggle').position();
      var abH_y = abH.top;
      var abH_x = abH.left;
      
      
      abH_y -= 15;
      // console.log("ABH = " + abH_y);
        $jq('#block-region-side-post').css({
          "top" : abH_y + "px", 
            "right" : "0px"
        }).fadeIn("slow");
        //  $jq('#block-region-side-post').fadeIn("slow"); 
          
         //$jq('#block-region-side-post').animate({
         //   right : "+=220" 
             
         //});
    } else {
      
       $jq('#block-region-side-post').fadeOut().ready(function() {
          $jq('#block-region-side-post').removeClass("magicEffect").hide();
          $jq('#post-block-toggle').removeClass("button-hide-region");
       });
    }
    // console.log("cond = " +_cond);
     
      
   });
});