jQuery(document).ready(function($) {

	//Fade portfolio
	$(".fade").fadeTo(1, 1);
	$(".fade").hover(
	function () {$(this).fadeTo("fast", 0.45);},
	function () { $(this).fadeTo("slow", 1);}
	);

	// Toggler

	// choose text for the show/hide link - can contain HTML (e.g. an image)
	var showText='More...';
	var hideText='Less...';

	// initialise the visibility check
	var is_visible = false;

	// append show/hide links to the element directly preceding the element with a class of "toggle"
	$('.toggle').prev().append('<div class="clear"></div>(<a href="#" class="toggleLink">'+showText+'</a>)');

	// hide all of the elements with a class of 'toggle'
	$('.toggle').hide();

	// capture clicks on the toggle links
	$('a.toggleLink').click(function() {
		// switch visibility
		is_visible = !is_visible;

		// change the link depending on whether the element is shown or hidden
		$(this).html( (!is_visible) ? showText : hideText);

		// toggle the display - uncomment the next line for a basic "accordion" style
//		$('.toggle').hide();$('a.toggleLink').html(showText);
		$(this).parent().next('.toggle').toggle(250);

		// return false so any link destination is not followed
		return false;
	});

	//Portfolio Filter Jquery
	$(window).load(function(){
	var $container = $('.pf-box-2col, .pf-box-3col, .pf-box-4col');
	if ($container.isotope)
	{
		$container.isotope({
			filter: '*',
			animationOptions: {
				duration: 750,
				easing: 'linear',
				queue: false,
			}
		});
		$('#pf-filter a').click(function(){
			var selector = $(this).attr('data-filter');
			$container.isotope({
				filter: selector,
				animationOptions: {
					duration: 750,
					easing: 'linear',
					queue: false,
				}
			});
		  return false;
		});
	}

	var $optionSets = $('#pf-filter'),
	       $optionLinks = $optionSets.find('a');

	       $optionLinks.click(function(){
	          var $this = $(this);
		  // don't proceed if already selected
		  if ( $this.hasClass('selected') ) {
		      return false;
		  }
	   var $optionSet = $this.parents('#pf-filter');
	   $optionSet.find('.selected').removeClass('selected');
	   $this.addClass('selected');
		});

	});

	//Tab Jquery
	$(".tab_content").hide();
	$("ul.tabs li:first").addClass("active").show();
	$(".tab_content:first").show();
	$("ul.tabs li").click(function() {
		$("ul.tabs li").removeClass("active");
		$(this).addClass("active");
		$(".tab_content").hide();
		var activeTab = $(this).find("a").attr("href");
		$(activeTab).fadeIn();
		return false;
	});

	//Twitter Jquery
	$("#twitter").getTwitter({
		userName: "fuelPHP",
		numTweets: 3,
		loaderText: "Loading tweets...",
		headingText: "",
		slideIn: true,
		slideDuration: 750
	});

	//Fancybox Jquery
	$(".fancybox").fancybox({
		padding: 0,
		openEffect : 'elastic',
		openSpeed  : 250,
		closeEffect : 'elastic',
		closeSpeed  : 250,
		closeClick : true,
		helpers : {
			overlay : {
				opacity : 0.65
			}
		}
	});

	//TinyNav Jquery
	$('#menu').tinyNav({
	  active: 'selected'
	});

	//To top Jquery
	$().UItoTop({ easingType: 'easeOutQuart' });

	// Notices
	var alert = $('div.info, div.success, div.error');

	if (alert.length > 0)
	{
		alert.show()

		window.setTimeout(function() {
		  alert.toggle(750);
		}, 3000);
	}

});
