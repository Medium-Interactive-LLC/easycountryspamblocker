/* global jQuery, global ECSBAdmin */
jQuery(document).ready(function()
{
	// Create empty array of countries.
	let countries = [];

	// Try to find the settings form.
	let form = document.getElementById('ecsb-settings-form');
	if(form === null || form === undefined)
	{
		return;
	}

	// Find all supported countries.
	jQuery('.country-toggle').each((index, obj) => {
		// If the current toggle is enabled, add it to the countries list.
		if(obj.checked) {
			countries.push(obj.value);
		}
	});

	// Add click event to country toggles.
	jQuery('.country-toggle').on('click', (e) => {
		// Grab the target element.
		let target = e.target;
		if(target == null || target == undefined) {
			return;
		}

		if((target.checked == null || target.checked == undefined) || (target.value == null || target.value == undefined)) {
			return;
		}

		if(target.id === 'usa-toggle') {
			e.preventDefault();
			target.checked = true;
		}

		// If the toggle is checked, add to the countries list.
		if(target.checked) {
			// Only add if this country isn't already on the list.
			if(!countries.includes(target.value)) {
				countries.push(target.value);
			}
		} else { // Otherwise, remove from countries list.
			// Only remove if this country is on the list.
			if(countries.includes(target.value)) {
				countries = countries.filter((value) => {
					return value != target.value;
				});
			}
		}
	});
	
	// Add an event to the save button.
	jQuery('.save-button').each((index, obj) => {
		obj.addEventListener('click', (e) => {
			// Override the default form behavior.
			e.preventDefault();
			
			// Set the request data.
			let formData =
			{
				action: form.action.value,
				isEnabled: jQuery(form.isEnabled).prop('checked'),
				countries: countries,
				redirectUrl: form.redirectUrl.value,
				urlProtocol: form.url_protocol.value + '://'
			};
			
			// Sanitize the URL.
			formData.redirectUrl = formData.redirectUrl.replace(/[^-A-Za-z0-9+&@#/%?=~_|!:,.;\(\)]/, '');
			
			// Strip away any URL protocols from the redirect URL.
			formData.redirectUrl = formData.redirectUrl.replace(/(^\w+:|^)\/\//, '');
			
			// Add the user selected URL protocol to the URL.
			formData.redirectUrl = formData.urlProtocol + formData.redirectUrl;
			
			// Remove any whitespace from the URL.
			formData.redirectUrl = formData.redirectUrl.replace(/\s+/, '');
			
			// If the input does end with a domain extension, do not continue.
			if(formData.redirectUrl.indexOf('.') === -1)
			{
				displayErrorNotice(ECSBAdmin.InvalidURLMsg);
				return;
			}
			
			// Update redirect URL.
			jQuery.ajax(
			{
				type: 'POST',
				url: ECSBAdmin.AjaxURL,
				data: formData,
				success: function(urlSaveResponse)
				{
					console.log(urlSaveResponse);

					if(urlSaveResponse.data === null || urlSaveResponse.data === undefined)
					{
						displayErrorNotice(ECSBAdmin.UnknownErrorMsg);
						return;
					}
					
					if(urlSaveResponse.data.exit_code === null || urlSaveResponse.data.exit_code === undefined)
					{
						displayErrorNotice(ECSBAdmin.UnknownErrorMsg);
						return;
					}
					
					// There was an error.
					if(urlSaveResponse.data.exit_code !== 0)
					{
						displayErrorNotice(
							urlSaveResponse.data.msg === null || urlSaveResponse.data.msg === undefined ?
								ECSBAdmin.ErrorCodeMsg + ' ' + urlSaveResponse.data.exit_code :
								ECSBAdmin.ErrorCodeMsg + ' ' + urlSaveResponse.data.exit_code + ' - ' + urlSaveResponse.data.msg
						);
						
						return;
					}
					
					// Display success!
					if(urlSaveResponse.data.msg !== null && urlSaveResponse.data.msg !== undefined)
					{
						displaySuccessNotice(urlSaveResponse.data.msg);
					}
					else
					{
						displayErrorNotice(ECSBAdmin.UnkownSuccessMsg);
					}
				},
				error: function(urlSaveResponse)
				{
					displayErrorNotice(ECSBAdmin.UnknownErrorMsg);
				}
			});
		});
	});
	
	// Setup functionality for URL protocol selection.
	jQuery('.url-select-item').each(function(itemIndex, itemObject)
	{
		// Do not proceed if the current item is null.
		if(itemObject === null || itemObject === undefined)
		{
			console.error('Item is null!');
			return;
		}
		
		// Add click events to the protocol buttons.
		jQuery(itemObject).find('.url-protocol-button').each(function(buttonIndex, buttonObject)
		{
			// Do not proceed if the current button is null.
			if(buttonObject === null || buttonObject === undefined)
			{
				console.error('Button is null!');
				return;
			}
			
			// Add required attributes to button.
			if(buttonObject.getAttribute('data-url-protocol') === null || buttonObject.getAttribute('data-url-protocol') === undefined)
			{
				buttonObject.setAttribute('data-url-protocol', buttonObject.innerHTML.toLowerCase());
			}
			
			// Add click event to the button!
			buttonObject.addEventListener('click', function(e)
			{
				e.preventDefault();
				
				// Try to find the correct options element for this button.
				let options = jQuery(itemObject).find('.url-protocol-options')[buttonIndex];
				
				// If the options were not found, do not continue.
				if(options === null || options === undefined)
				{
					console.error('No options were found for this button!');
					return;
				}
				
				// Show the options menu!
				jQuery(options).removeClass('initial');
				jQuery(options).toggleClass('show');
			});
		});
		
		// Add click events to protocol options.
		jQuery(itemObject).find('.url-protocol-options').each(function(optionsIndex, optionsObject)
		{
			// Do not proceed if the current option holder is null.
			if(optionsObject === null || optionsObject === undefined)
			{
				console.error('Options is null!');
				return;
			}
			
			jQuery(optionsObject).find('.url-protocol-option').each(function(optionIndex, optionObject)
			{
				// Do not proceed if the current option is null.
				if(optionObject === null || optionObject === undefined)
				{
					console.error('Option is null!');
					return;
				}
				
				// Add required attributes to option.
				if(optionObject.getAttribute('data-url-protocol') === null || optionObject.getAttribute('data-url-protocol') === undefined)
				{
					optionObject.setAttribute('data-url-protocol', optionObject.innerHTML.toLowerCase());
				}
				
				// Set the form's URL protocol field!
				form.url_protocol.value = optionObject.getAttribute('data-url-protocol');
				
				// Add click event to option!
				optionObject.addEventListener('click', function(e)
				{
					e.preventDefault();
					
					// Hide the options.
					jQuery(optionsObject).removeClass('initial');
					jQuery(optionsObject).removeClass('show');
					
					// Try to find the correct button.
					let button = jQuery(itemObject).find('.url-protocol-button')[optionsIndex];
					
					// If the button was not fonud, do not continue.
					if(button === null || button === undefined)
					{
						console.error('No button was found for this options!');
						return;
					}
					
					// Get the option's atrribute.
					let urlProtocol = optionObject.getAttribute('data-url-protocol');
					
					// Set the button's text!
					button.innerHTML = optionObject.innerHTML.toUpperCase();
					
					// Set the button's attribute!
					button.setAttribute('data-url-protocol', urlProtocol);
					
					// If the form doesn't have a URL protocol field, do not proceed.
					if(form.url_protocol === null || form.url_protocol === undefined)
					{
						console.error('No URL protocol input found!');
						return;
					}
					
					// Set the form's URL protocol field!
					form.url_protocol.value = urlProtocol;
				});
			});
		});
		
		// Add event listeners to URL text field.
		jQuery(itemObject).find('.url-text').each(function(urlIndex, urlObject)
		{
			// Do not proceed if the current URL field is null.
			if(urlObject === null || urlObject === undefined)
			{
				console.error('URL field is null!');
				return;
			}
			
			// Make sure the field never has any URL protocols in it.
			urlObject.value = urlObject.value.replace(/(^\w+:|^)\/\//, '')
			
			// Add event listener to URL field for when the text is changed.
			urlObject.addEventListener('input', function(e)
			{
				// Make sure the field never has any URL protocols in it.
				urlObject.value = urlObject.value.replace(/(^\w+:|^)\/\//, '')
				
				// Remove any whitespace from the URL.
				urlObject.value = urlObject.value.replace(/\s+/, '');
				
				// Sanitize the URL as it is typed.
				urlObject.value = urlObject.value.replace(/[^-A-Za-z0-9+&@#/%?=~_|!:,.;\(\)]/, '');
			});
			
			// Add event listener to URL field for when the text is changed and then the user clicks elsewhere.
			urlObject.addEventListener('change', function(e)
			{
				// If the input does end with a domain extension, let the user know.
				if(urlObject.value.indexOf('.') === -1)
				{
					displayErrorNotice(ECSBAdmin.InvalidURLMsg);
				}
			});
		});
	});
	
	// Add event listeners for entire document.
	jQuery(document).on('click', function(e)
	{
		// If the target is null, do nothing.
		if(e.target === null || e.target === undefined)
		{
			return;
		}
		
		// If the target is a URL protocol related element, also do nothing.
		if(e.target.classList.contains('url-protocol-button') || e.target.classList.contains('url-protocol-option'))
		{
			return;
		}
		
		// Hide all of the URL protocol options menus.
		jQuery('.url-protocol-options').each(function(index, object)
		{
			jQuery(object).removeClass('initial');
			jQuery(object).removeClass('show');
		});
	});
	
	/**
	 * Displays a notice.
	 */
	function displayNotice(msg, time, noticeClass)
	{
		// Try to find the notices container.
		let noticeContainer = document.getElementById('ecsb-notices');
		if(noticeContainer === null || noticeContainer === undefined)
		{
			console.log('Notices container not found!');
			return;
		}
		
		// Create a new div container for the notice.
		let noticeDiv = jQuery('<div>', { class: 'ecsb-notice notice ' + noticeClass });
		let noticeContent = jQuery('<p>');
		
		// Set the content.
		noticeContent.html(msg);
		
		// Append the content to the div.
		noticeDiv.append(noticeContent);
		
		// Append the notice to the notices container.
		jQuery(noticeContainer).append(noticeDiv);
		
		// Hide notice after X amount of time.
		setTimeout(function()
		{
			noticeDiv.addClass('hide');
			
			// Delete notice after one second.
			setTimeout(function()
			{
				noticeDiv.remove();
			}, 1000);
		}, time);
	}
	
	/**
	 * Displays a success notice.
	 */
	function displaySuccessNotice(msg, time)
	{
		// Set the default amount of time equal to three and a half seconds.
		time = time === null || time === undefined ? 3500 : time;
		
		// Display the notice
		displayNotice(msg, time, 'notice-success');
	}
	
	/**
	 * Display an error notice.
	 */
	function displayErrorNotice(msg, time)
	{
		// Set the default amount of time equal to ten seconds.
		time = time === null || time === undefined ? 10000 : time;
		
		// Display the notice!
		displayNotice(msg, time, 'notice-error');
	}
});
