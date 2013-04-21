function QuickView() {
	this.galleries = null;
}

QuickView.prototype.init = function() {
	this.galleries = $('.ad-gallery').adGallery();

	this.bind();
}

QuickView.prototype.bind = function() {
	$('a.show-room-info').click(function() {
		return false;
	});

	$('a.show-room-info').mouseover(function() {
		$(this).next().show();
	});

	$('a.show-room-info').mouseleave(function() {
		$(this).next().hide();
	});

	$('#showmore').toggle(  
	        function() {  
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

	var self = this;

	$('#switch-effect').change(
		function() {
			self.galleries[0].settings.effect = $(this).val();

			return false;
		}
	);

	$('#toggle-slideshow').click(
		function() {
			self.galleries[0].slideshow.toggle();

			return false;
		}
	);

	$('#toggle-description').click(
		function() {
			if (!self.galleries[0].settings.description_wrapper) {
				self.galleries[0].settings.description_wrapper = $('#descriptions');
			}
			else {
				self.galleries[0].settings.description_wrapper = false;
			}

			return false;
		}
	);
}

QuickView.prototype.showImage = function(imageurl) {
	document.getElementById("largeimage").src = imageurl;
}

$(document).ready(function(){
	var quickView = new QuickView();

	quickView.init();
});


function showimage(imageurl) {
	document.getElementById("largeimage").src=imageurl;
}
