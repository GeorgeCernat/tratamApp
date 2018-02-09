if (navigator.userAgent.toLowerCase().indexOf('firefox') > -1) {
	document._oldGetElementById = document.getElementById;
	document.getElementById = function(id) {
		if(id === undefined || id === null || id === '') {
			return undefined;
		}
		return document._oldGetElementById(id);
	};
}

/**
 * Function to generate a Random Password
 **/
function generatePassword(limit) {
	limit = limit || 6;
	var password = '';
	// You can add or remove any characters you wish between the two single quote marks (')
	// Do NOT use singe quote marks in your characters list (')
	var chars = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789!"£$&=^*#_-@+,.';
	var list = chars.split('');
	var len = list.length,
		i = 0;
	do {
		i++;
		var index = Math.floor(Math.random() * len);
		password += list[index];
	}
	while (i < limit);
	// Return the newly generated password
	return password;
}

$(document).ready(function() {

	/** ******************************
	 * Log In/Sign Up Overlay
	 ****************************** **/
	 
	var full = $('#fullscreen');
	$(full).data('state','open');

	$('.signinup').click(function(e) {
		e.preventDefault();
		if ($(full).data('state') == 'open') {
			$(full).fadeIn(300);
			$(full).data('state','close');
		}
	});
	$('.signup-btn').click(function(e) {
		e.preventDefault();
		$('.signin-form, .whyDiv').fadeOut(300, function() {
			$(this).hide;
		});
		$('.signup-form').delay(300).fadeIn("slow", function() {
			$(this).show;
		});
	});
	$('.why-btn').click(function(e) {
		e.preventDefault();
		$('.signup-form').fadeOut(300, function() {
			$(this).hide;
		});
		$('.whyDiv').delay(300).fadeIn("slow", function() {
			$(this).show;
		});
	});
	$('.signin-btn').click(function(e) {
		e.preventDefault();
		$('.signup-form, .password-form, .whyDiv').fadeOut(300, function() {
			$(this).hide;
		});
		$('.signin-form').delay(300).fadeIn("slow", function() {
			$(this).show;
		});
	});
	$('.password-btn').click(function(e) {
		e.preventDefault();
		$('.signin-form').fadeOut(300, function() {
			$(this).hide;
		});
		$('.password-form').delay(300).fadeIn("slow", function() {
			$(this).show;
		});
	});
	
	$('.close-overlay').click(function(e) {
		e.preventDefault();
		if ($(full).data('state') == 'close') {
			$(full).fadeOut();
			$(full).data('state','open');
		}
	});
	

	(function($, window, document){
		var panelSelector = '[data-perform="panel-collapse"]';

		$(panelSelector).each(function() {
			var $this = $(this),
			parent = $this.closest('.togglePanel'),
			wrapper = parent.find('.panel-wrapper'),
			collapseOpts = {toggle: false};

			if (!wrapper.length) {
				wrapper =
				parent.children('.panel-toggle').nextAll()
				.wrapAll('<div/>')
				.parent()
				.addClass('panel-wrapper');
				collapseOpts = {};
			}
			wrapper
			.collapse(collapseOpts)
			.on('hide.bs.collapse', function() {
				$('#confessToggle').html('<i class="fa fa-comment-o"></i> Incarca un medicament');
			})
			.on('show.bs.collapse', function() {
				$('#confessToggle').html('<i class="fa fa-times"></i> Anuleaza');
			});
		});
		$(document).on('click', panelSelector, function (e) {
			e.preventDefault();
			var parent = $(this).closest('.togglePanel');
			var wrapper = parent.find('.panel-wrapper');
			wrapper.collapse('toggle');
		});
	}(jQuery, window, document));
	
	/** ******************************
	 * Like/Dislike Votes
	 ****************************** **/
	 
	$(".likes a").click(function(e) {
		e.preventDefault();
		var isLiked = '1';
		var confId = $(this).parent().parent().parent().parent().find('input').val();
		var updateLikes = $(this).find('span');
		var hasVoted = $(this).parent().parent().parent().find('.hasVoted');
		post_data = {'confId':confId, 'isLiked':isLiked};
		
		$.post('includes/process.php', post_data, function(likesTotal) {
			if (likesTotal > 0) {
				updateLikes.text(likesTotal);
			} else {
				hasVoted.show();
				hasVoted.delay(5000).fadeOut();
			}
		});
	});
	
	$(".dislikes a").click(function(e) {
		e.preventDefault();
		var isDisliked = '2';
		var confId = $(this).parent().parent().parent().parent().find('input').val();
		var updateDislikes = $(this).find('span');
		var hasVoted = $(this).parent().parent().parent().find('.hasVoted');
		post_data = {'confId':confId, 'isDisliked':isDisliked};
		
		$.post('includes/process.php', post_data, function(dislikesTotal) {
			if (dislikesTotal > 0) {
				updateDislikes.text(dislikesTotal);
			} else {
				hasVoted.show();
				hasVoted.delay(5000).fadeOut();
			}
		});
	});

	/** ******************************
	 * Custom Upload Input
	 ****************************** **/
	 
	$('#file-upload').change(function() {
		var filepath = this.value;
		var m = filepath.match(/([^\/\\]+)$/);
		var filename = m[1];
		$('#filename').html(filename);
	});

	/** ******************************
	 * Alert Message Boxes
	 ****************************** **/
	 
    $('.alertMsg .alert-close').each(function() {
        $(this).click(function(e) {
            e.preventDefault();
            $(this).parent().fadeOut("slow", function() {
                $(this).addClass('hidden');
            });
        });
    });

	/** ******************************
	* Activate Tool-tips
	****************************** **/
    $("[data-toggle='tooltip']").tooltip();

	/** ******************************
	* Activate Popovers
	****************************** **/
	$("[data-toggle='popover']").popover();

});