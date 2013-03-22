
$('a.show-room-info').click(function() {
	return false;
});
$('a.show-room-info').mouseover(function() {
	$(this).next().show();
});
$('a.show-room-info').mouseleave(function() {
	$(this).next().hide();
});


$(document).ready(function(){
	$('#showmore').toggle(  
        function(){  
		 	 $("#bottomfeature1").fadeIn("slow");
		},
        function(){
		    $("#bottomfeature1").fadeOut("slow");
		}
	);
	$('#showmorereviews').toggle(  
        function(){  
		 	 $("#bottomfeature2").fadeIn("slow");
		},
        function(){
		    $("#bottomfeature2").fadeOut("slow");
		}
	);
});


function showimage(imageurl)
{
	document.getElementById("largeimage").src=imageurl;
}

  $(function() { 
    var galleries = $('.ad-gallery').adGallery();
    $('#switch-effect').change(
      function() {
        galleries[0].settings.effect = $(this).val();
        return false;
      }
    );
    $('#toggle-slideshow').click(
      function() {
        galleries[0].slideshow.toggle();
        return false;
      }
    );
    $('#toggle-description').click(
      function() {
        if(!galleries[0].settings.description_wrapper) {
          galleries[0].settings.description_wrapper = $('#descriptions');
        } else {
          galleries[0].settings.description_wrapper = false;
        }
        return false;
      }
    );
  });