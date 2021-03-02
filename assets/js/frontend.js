jQuery(document).ready(function() {	

	// supporting different versions of Font Awesome icons
	var icon_classes = mr_frontend_data.icon_classes;
	if (typeof icon_classes === 'string') {
		icon_classes = jQuery.parseJSON(icon_classes);
	}
	
	jQuery(".rating-form :submit").click(function(e) {

		e.preventDefault();
	
		var ratingItems = [];
		var customFields = [];
		var btnId = e.currentTarget.id; // btnType-postId-sequence
		var parts = btnId.split("-"); 
		var postId = parts[1];
		var sequence = parts[2];
		
		// rating items - hidden inputs are used to find all rating items in the rating form
		jQuery('.rating-form input[type="hidden"].rating-item-' + postId + '-' + sequence).each(function(index) {			
			
			var ratingItemId = jQuery(this).val();
			
			// get values for rating items
			var element = jQuery('[name="rating-item-' + ratingItemId + '-' + sequence + '"]');
			var value = null;
			if (jQuery(element).is(':radio')) {
				value = jQuery('input[type="radio"][name="rating-item-' + ratingItemId + '-' + sequence + '"]:checked').val(); 
			} else if (jQuery(element).is('select')) {
				value = jQuery('select[name="rating-item-' +ratingItemId + '-' + sequence + '"] :selected').val(); 
			} else {
				value = jQuery('input[type="hidden"][name="rating-item-' + ratingItemId + '-' + sequence + '"]').val();
			}
			
			var ratingItem = { 'id' : ratingItemId, 'value' : value };
			ratingItems[index] = ratingItem;
			
		});
	
		var data = {
				action : "save_rating",
				nonce : mr_frontend_data.ajax_nonce,
				ratingItems : ratingItems,
				postId : postId,
				sequence : sequence
		};
		
		var temp = postId +'-' + sequence;
		var spinnerId = 'mr-spinner-' + temp;
		
		jQuery('<i style="margin-left: 10px;" id="' + spinnerId + '" class="' + icon_classes.spinner + '"></i>').insertAfter(jQuery('input#' + btnId).parent());
	
		jQuery.post(mr_frontend_data.ajax_url, data, function(response) {
				handle_rating_form_submit_response(response);
		});
	});
	
	/**
	 * Handles rating form submit response
	 */
	function handle_rating_form_submit_response(response) {
		
		var jsonResponse = jQuery.parseJSON(response);
		var id = jsonResponse.data.post_id + "-" + jsonResponse.data.sequence;
		
		var ratingForm = jQuery("#rating-form-" + id);
		
		// update rating results if success
		if (jsonResponse.status == 'success') {
			var ratingResult = jQuery(".rating-result-" + jsonResponse.data.post_id).filter(".mr-filter");
			
			if (ratingResult) {
				ratingResult.replaceWith(jsonResponse.data.html);
			}
		}
		
		// remove existing errors for rating items, optional fields and custom fields
		jQuery("#rating-form-" + id + " .rating-item .mr-error").html("");
		
		// update messages
		if ((jsonResponse.validation_results && jsonResponse.validation_results.length > 0) || jsonResponse.message) {
			var messages = '';
			
			if (jsonResponse.validation_results) {
				var index = 0;
				for (index; index<jsonResponse.validation_results.length; index++) {
					
					if (jsonResponse.validation_results[index].field && jQuery("#" + jsonResponse.validation_results[index].field + "-" + jsonResponse.data.sequence + "-error").length) {
						jQuery("#" + jsonResponse.validation_results[index].field + "-" + jsonResponse.data.sequence + "-error")
								.html(jsonResponse.validation_results[index].message);
					} else {
						messages += '<p class="mr message mr-' + jsonResponse.validation_results[index].severity + '">' 
								+ jsonResponse.validation_results[index].message + '</p>';
					}
				}
			}
			
			if (jsonResponse.message) {
				messages += '<p class="message ' + jsonResponse.status + '">' 
						+ jsonResponse.message + '</p>';
			}
			
			if (ratingForm && ratingForm.parent().find('.message')) {
				ratingForm.parent().find('.message').remove();
			}
			
			if (ratingForm && ratingForm.parent()) {
				if (jsonResponse.status == 'success' && jsonResponse.data.hide_rating_form) {
					ratingForm.parent().after(messages);
				} else {
					ratingForm.find(".save-rating").before(messages);
				}
			}
		}
		
		// remove rating form if success
		if (jsonResponse.status == 'success' && jsonResponse.data.hide_rating_form == true && ratingForm) {
			ratingForm.remove();
		}

		var spinnerId = 'mr-spinner-' + id;
		jQuery("#" + spinnerId).remove();
	}
	
	/**
	 * Selected rating item value on hover and click
	 */
	var ratingItemStatus = {};
	
	var useCustomStarImages = mr_frontend_data.use_custom_star_images;
	if (typeof useCustomStarImages === 'string') {
		useCustomStarImages = jQuery.parseJSON(useCustomStarImages);
	}
	
	jQuery(".mr-star-rating-select .mr-star-empty, .mr-star-rating-select .mr-star-full").on("click", function(e) {
		
		updateRatingItemStatus(this.id, 'clicked');
		
		if (useCustomStarImages == true) {
			jQuery(this).not('.mr-minus').removeClass('mr-custom-empty-star mr-star-empty mr-custom-hover-star mr-star-hover').addClass('mr-custom-full-star mr-star-full');
			jQuery(this).prevAll().not('.mr-minus').removeClass('mr-custom-empty-star mr-star-empty mr-custom-hover-star mr-star-hover').addClass('mr-custom-full-star mr-star-full');
			jQuery(this).nextAll().not('.mr-minus').removeClass('mr-custom-full-star mr-star-full mr-custom-hover-star mr-star-hover').addClass('mr-custom-empty-star mr-star-empty');
		} else {
			jQuery(this).not('.mr-minus').removeClass(icon_classes.star_empty + " " + icon_classes.star_hover).addClass(icon_classes.star_full);
			jQuery(this).prevAll().not('.mr-minus').removeClass(icon_classes.star_empty + " " + icon_classes.star_hover).addClass(icon_classes.star_full);
			jQuery(this).nextAll().not('.mr-minus').removeClass(icon_classes.star_full + " " + icon_classes.star_hover).addClass(icon_classes.star_empty);
		}
		
		updateSelectedHiddenValue(this);
	});
	
	jQuery(".mr-star-rating-select .mr-minus").on("click", function(e) {
		
		updateRatingItemStatus(this.id, '');
		
		if (useCustomStarImages == true) {
			jQuery(this).not('.mr-minus').removeClass('mr-custom-empty-star mr-star-empty mr-custom-hover-star mr-star-hover').addClass('mr-custom-full-star mr-star-full');
			jQuery(this).prevAll().not('.mr-minus').removeClass('mr-custom-empty-star mr-star-empty mr-custom-hover-star mr-star-hover').addClass('mr-custom-full-star mr-star-full');
			jQuery(this).nextAll().not('.mr-minus').removeClass('mr-custom-full-star mr-star-full mr-custom-hover-star mr-star-hover').addClass('mr-custom-empty-star mr-star-empty');
		} else {
			jQuery(this).not('.mr-minus').removeClass(icon_classes.star_empty + " " + icon_classes.star_hover).addClass(icon_classes.star_full);
			jQuery(this).prevAll().not('.mr-minus').removeClass(icon_classes.star_empty + " " + icon_classes.star_hover).addClass(icon_classes.star_full);
			jQuery(this).nextAll().not('.mr-minus').removeClass(icon_classes.star_full + " " + icon_classes.star_hover).addClass(icon_classes.star_empty);
		}
		
		updateSelectedHiddenValue(this);
	});
	
	jQuery(".mr-star-rating-select .mr-minus, .mr-star-rating-select .mr-star-empty, .mr-star-rating-select .mr-star-full").on("mouseenter mouseleave", function(e) {

		var elementId = this.id;
		var ratingItemIdSequence = getRatingItemIdSequence(elementId);
		
		if (jQuery("#" + ratingItemIdSequence).val() == 0 || (ratingItemStatus[ratingItemIdSequence] != 'clicked' 
				&& ratingItemStatus[ratingItemIdSequence] != undefined)) {
			
			updateRatingItemStatus(this.id, 'hovered');
			
			if (useCustomStarImages == true) {
				jQuery(this).not('.mr-minus').removeClass('mr-custom-empty-star mr-star-empty').addClass('mr-custom-hover-star mr-star-hover');
				jQuery(this).prevAll().not('.mr-minus').removeClass('mr-custom-empty-star mr-star-empty').addClass('mr-custom-hover-star mr-star-hover');
				jQuery(this).nextAll().not('.mr-minus').removeClass('mr-custom-hover-star mr-star-hover mr-custom-full-star mr-star-full').addClass('mr-custom-empty-star mr-star-empty');	

			} else {
				jQuery(this).not('.mr-minus').removeClass(icon_classes.star_empty).addClass(icon_classes.star_hover);
				jQuery(this).prevAll().not('.mr-minus').removeClass(icon_classes.star_empty).addClass(icon_classes.star_hover);
				jQuery(this).nextAll().not('.mr-minus').removeClass(icon_classes.star_hover + " " + icon_classes.star_full).addClass(icon_classes.star_empty);	
			}
		}
	});
	
	// now cater for touch screen devices
	var touchData = {
		started : null, // detect if a touch event is sarted
		currrentX : 0,
		yCoord : 0,
		previousXCoord : 0,
		previousYCoord : 0,
		touch : null
	};
	
	jQuery(".mr-star-rating-select .mr-star-empty, .mr-star-rating-select .mr-star-full, .mr-star-rating-select .mr-minus").on("touchstart", function(e) {
		touchData.started = new Date().getTime();
		var touch = e.originalEvent.touches[0];
		touchData.previousXCoord = touch.pageX;
		touchData.previousYCoord = touch.pageY;
		touchData.touch = touch;
	});
	
	jQuery(".mr-star-rating-select .mr-star-empty, .mr-star-rating-select .mr-star-full, .mr-star-rating-select .mr-minus").on("touchend touchcancel", function(e) {
			var now = new Date().getTime();
			// Detecting if after 200ms if in the same position.
			if ((touchData.started !== null)
					&& ((now - touchData.started) < 200)
					&& (touchData.touch !== null)) {
				var touch = touchData.touch;
				var xCoord = touch.pageX;
				var yCoord = touch.pageY;
				if ((touchData.previousXCoord === xCoord)
						&& (touchData.previousYCoord === yCoord)) {
					
					if (useCustomStarImages == true) {
						jQuery(this).not('.mr-minus').removeClass('mr-custom-empty-star mr-star-empty').addClass('mr-custom-full-star mr-star-full');
						jQuery(this).prevAll().not('.mr-minus').removeClass('mr-custom-empty-star mr-star-empty').addClass('mr-custom-full-star mr-star-full');
						jQuery(this).nextAll().not('.mr-minus').removeClass('mr-custom-full-star mr-star-full').addClass('mr-custom-empty-star mr-star-empty');
					} else {
						jQuery(this).not('.mr-minus').removeClass(icon_classes.star_empty).addClass(icon_classes.star_full);
						jQuery(this).prevAll().not('.mr-minus').removeClass(icon_classes.star_empty).addClass(icon_classes.star_full);
						jQuery(this).nextAll().not('.mr-minus').removeClass(icon_classes.star_full).addClass(icon_classes.star_empty);
					}
					updateSelectedHiddenValue(this);
				}
			}
			touchData.started = null;
			touchData.touch = null;
	});
	
	/**
	 * Updates the rating item status to either hovered or clicked
	 */
	function updateRatingItemStatus(elementId, status) {
		var ratingItemIdSequence = getRatingItemIdSequence(elementId);
		if (ratingItemIdSequence != null) {
			ratingItemStatus[ratingItemIdSequence] = status;
		}
	}
	
	/**
	 * Retrieves the rating item id sequence used to store the status of a rating item option
	 */
	function getRatingItemIdSequence(elementId) {
		var parts = elementId.split("-"); 
		
		var ratingItemId = parts[4]; /// skip 2: rating-item-
		var sequence = parts[5];
		
		var ratingItemIdSequence = 'rating-item-' + ratingItemId + '-' + sequence;
		return ratingItemIdSequence;
	}
	
	/**
	 * Updates the selected hidden value for a rating item
	 */
	function updateSelectedHiddenValue(element) {
		
		// id is in format "index-3-rating-item-2-1"
		var elementId = element.id;
		
		var parts = elementId.split("-"); 
		var value = parts[1]; // this is the star index
		var ratingItemId = parts[4]; /// skipt 2: rating-item-
		var sequence = parts[5];
		    		
		// update hidden value for storing selected option
		var hiddenValue = '#rating-item-'+ ratingItemId + '-' + sequence;
		    		
		jQuery(hiddenValue).val(value);
	}

});