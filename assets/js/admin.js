jQuery(document).ready(function() {	

	jQuery("#add-new-rating-item-btn").click(function() {
		jQuery("#form-submitted").val("true");
	});

	jQuery("#clear-database-btn").live('click',function(e) {
		jQuery("#clear-database").val("true");
	});
	
	jQuery("#export-btn").click(function(event) {
		jQuery("#export-rating-results").val("true");
	});
	
	jQuery("#clear-cache-btn").click(function(event) {
		jQuery("#clear-cache").val("true");
	});
	
	jQuery(document).on('widget-updated', function(e, widget) { // save widget unbinds events...
		jQuery(".widget .mr-rating-results-widget-taxonomy, .widget .mr-user-rating-results-widget-taxonomy").change(function(e) {
			handle_taxonomy_change(this.id);
		});
	});
	jQuery(".widget .mr-rating-results-widget-taxonomy, .widget .mr-user-rating-results-widget-taxonomy").change(function(e) {
		handle_taxonomy_change(this.id);
	});
	
	/**
	 * Handles taxonomy change in the Rating Results List Widget
	 * 
	 * @returns
	 */
	function handle_taxonomy_change(elementId) {
		// retrieve widget instance
		var parts = elementId.split("-"); 
		var instance = parts[2];
		var name =  parts[1];
		
		// retrieve selected taxonomy
		var taxonomy = jQuery("#" + elementId).val();
		
		if (taxonomy == "") {
			var termSelect = jQuery("#widget-" + name + "-" + instance + "-term_id");
			termSelect.empty();
			termSelect.prepend("<option value=\"\"></option>");
			return;
		}
		
		// ajax call to retrieve new terms		
		var data = {
				action : "retrieve_terms_by_taxonomy",
				nonce : mr_admin_data.ajax_nonce, // tbc
				taxonomy : taxonomy
		};

		jQuery.post(mr_admin_data.ajax_url, data, function(response) {
				var jsonResponse = jQuery.parseJSON(response);
			
				var termSelect = jQuery("#widget-" + name + "-" + instance + "-term_id");
				termSelect.empty();
				
				var index = jsonResponse.length-1;
				for (index; index>=0; index--) {
					termSelect.prepend("<option value=\"" + jsonResponse[index]["term_id"] + "\">" + jsonResponse[index]["name"] + "</option>");
				}
		});
	}
	
	var rowActions = jQuery("#rating-item-table-form .row-actions > a");
	jQuery.each(rowActions, function(index, element) {
		jQuery(element).click(function(event) { 
			var btnId = this.id;
			var parts = btnId.split("-"); 
			var action = parts[0];
			var column = parts[1];
			var rowId = parts[2]; 
			if (action === "edit") {
				// change state
				jQuery("#view-section-" + column + "-" + rowId).css("display", "none");
				jQuery("#edit-section-" + column + "-" + rowId).css("display", "block");
			} else if (action === "save") {
			
				var field_id = "#field-" + column + "-" + rowId;
				var value = null;
				if (jQuery(field_id).is(":checkbox")) {
					value = jQuery(field_id).is(':checked');
				} else {
					value = jQuery(field_id).val();
				}
				
				var data =  { 
						
						action : "save_rating_item_table_column",
						nonce : mr_admin_data.ajax_nonce,
						column : column,
						ratingItemId : rowId,
						value : value
					};
				jQuery.post(mr_admin_data.ajax_url, data, function(response) {
					var jsonResponse = jQuery.parseJSON(response);
					if (jsonResponse.error_message && jsonResponse.error_message.length > 0) {
						alert(jsonResponse.error_message);
					} else {
						jQuery("#text-" + column + "-" + rowId).html(jsonResponse.value);
						jQuery("#view-section-" + column + "-" + rowId).css("display", "block");
						jQuery("#edit-section-" +  column + "-" + rowId).css("display", "none");
					}
				});
			}
			
			// stop event
			event.preventDefault();
		});
	});
	
	jQuery(document).ready(function() {
		
		jQuery('.color-picker').wpColorPicker({
		    defaultColor: false,
		    change: function(event, ui){},
		    clear: function() {},
		    hide: true,
		    palettes: true
		});
	    
	    jQuery('.date-picker').datepicker({
	        dateFormat : 'yy/mm/dd'
	    });
	    
	});
	
	/**
	 * Displays the media uploader for selecting an image.
	 * 
	 * @param starImage star image name for media uploader
	 */
	function renderMediaUploader(starImage) {
	 
	    var file_frame, image_data;
	 
	    /**
	     * If an instance of file_frame already exists, then we can open it
	     * rather than creating a new instance.
	     */
	    if ( undefined !== file_frame ) {
	        file_frame.open();
	        return;
	    }
	 
	    /**
	     * If we're this far, then an instance does not exist, so we need to
	     * create our own.
	     *
	     * Here, use the wp.media library to define the settings of the Media
	     * Uploader. We're opting to use the 'post' frame which is a template
	     * defined in WordPress core and are initializing the file frame
	     * with the 'insert' state.
	     *
	     * We're also not allowing the user to select more than one image.
	     */
	    file_frame = wp.media.frames.file_frame = wp.media({
	        frame:    "post",
	        state:    "insert",
	        multiple: false
	    });
	 
	    /**
	     * Setup an event handler for what to do when an image has been
	     * selected.
	     *
	     * Since we're using the 'view' state when initializing
	     * the file_frame, we need to make sure that the handler is attached
	     * to the insert event.
	     */
	    file_frame.on("insert", function() {
	 
	    	// Read the JSON data returned from the Media Uploader
	        var json = file_frame.state().get("selection").first().toJSON();
	 
	        // After that, set the properties of the image and display it
	        jQuery("#" + starImage + "-preview").attr("src", json.url ).show().parent().removeClass("hidden");
	        
	        // Store the image's information into the meta data fields
	        jQuery("#" + starImage).val( json.url );
	    });
	 
	    // Now display the actual file_frame
	    file_frame.open();
	 
	}
	
	jQuery("#custom-full-star-img-upload-btn, #custom-half-star-img-upload-btn, #custom-empty-star-img-upload-btn, #custom-hover-star-img-upload-btn").on("click", function(evt) {
        // Stop the anchor's default behavior
        evt.preventDefault();
        
        var btnId = this.id;
        var index = btnId.indexOf("-upload-btn");
		var starImage = btnId.substring(0, index);

        // Display the media uploader
        renderMediaUploader(starImage);
    });
	
	
	jQuery("#use-custom-star-images").change(function() {
		if (this.checked) {
			jQuery("#custom-star-images-details").show("slow", function() {} );
		} else {
			jQuery("#custom-star-images-details").hide("slow", function() {} );
		}
	});
	
});