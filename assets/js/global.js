$(document).ready(function(){

	// Fadein
	$('body').fadeIn(1000);

	// Notices
	$(function () {
		var alert = $('.success-box, .info-box, .error-box, .warning-box, .no-box');
		if (alert.length > 0)
		{
			alert.show()

			window.setTimeout(function() {
			  alert.toggle(750);
			}, 3000);
		}
	});

	// menu tree functions
	$(document).ready(function() {

		var $cookie = '';
		var $item_list = $("#menu_list>ul");

		var update_cookie = function() {
			var items = [];

			// get all of the open parents
			$item_list.find('li.minus:visible').each(function(){ items.push('#' + this.id) });

			// save open parents in the cookie
			$.cookie($cookie, items.join(','), { expires: 1, path: '/' });
		}

		// this gets ran again after drop
		var refresh_tree = function() {

			// add the minus icon to all parent items that now have visible children
			$item_list.parent().find('ul li:has(li:visible)').removeClass('plus').addClass('minus');

			// add the plus icon to all parent items with all hidden children
			$item_list.parent().find('ul li:not(:has(li:visible))').removeClass('minus').addClass('plus');

			// remove the class if the child was removed
			$item_list.parent().find('ul li:not(:has(li))').removeClass('plus minus');
		}

		// tree toggle functions
		setTimeout(function() {
			$('#docs .three .expand_all').click(function() {
				$item_list.find('ul').children().show();
				refresh_tree();
				update_cookie();
			});
			$('#docs .three .collapse_all').click(function() {
				$item_list.find('ul').children().not('.no-collapse').hide();
				refresh_tree();
				update_cookie();
			});
		}, 0);

		if ($item_list.length > 0)
		{
			// make the divs with embedded a's clickable
			$item_list.find('li').clickable(true,  true);

			// collapse all ordered lists but the top level
//			$item_list.find('ul').children().hide();

			refresh_tree();

			// determine the cookie name based on the id prefix
			$cookie = $item_list.find('li').last().attr('id');
			$cookie = $cookie.substr(0, $cookie.indexOf("_")) + '_menustate';

			// set the icons properly on parents restored from cookie
			$($.cookie($cookie)).has('ul').removeClass('plus').addClass('minus');

			// show the parents that were open on last visit
			$($.cookie($cookie)).children('ul').children().show();

			// show/hide the children when clicking on an <li>
			$item_list.find('li').on('click', function(event)
			{
				if ($(this).children('ul').length > 0)
				{
					$(this).children('ul').children().slideToggle('fast');

					$(this).has('ul').toggleClass('minus plus');

					update_cookie();
				}

				event.stopImmediatePropagation();
			});
		}

	});

});
