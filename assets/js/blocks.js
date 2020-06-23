( function() { // local scope

	const { registerBlockType } = wp.blocks; // Blocks API
	const el = wp.element.createElement; // React.createElement
	const { __ } = wp.i18n; // translation functions
	const { InspectorControls } = wp.editor; 
	const { PanelBody, PanelRow, Panel, TextControl, ToggleControl } = wp.components; 
	const { serverSideRender } = wp;

	/*
	 * Rating form block
	 */
	registerBlockType( 'multi-rating/rating-form', {
		
		// Built-in attributes
		title: __( 'Rating Form', 'multi-rating' ),
		description: __( 'Adds a rating form for a post.', 'multi-rating' ),
		icon: 'star-filled',
		category: 'common',

		// Custom attributes - server side

		// Built-in functions
		edit: function( props ) {

			var className = props.className;
	        var setAttributes = props.setAttributes;

			return el('div', {}, [
				
				// Preview
				el( serverSideRender, {
					block: 'multi-rating/rating-form',
					attributes: props.attributes,
					className : className
				} ),
				
				// Block inspector
				el( InspectorControls, { className : props.className }, [
					el( 
		    			PanelBody, 
		    			{}, 
		        		el(
		        			PanelRow,
		        			{},
		        			el( TextControl, {
								value: props.attributes.title,
								label: __( 'Title', 'multi-rating' ),
								onChange: ( value ) => {
			                		props.setAttributes( { title: value } );
			              		},
			              		type: 'string'
							})
						),
						el(
		        			PanelRow,
		        			{},
							el( TextControl, {
								value: props.attributes.submit_button_text,
								label: __( 'Submit Button Text', 'multi-rating' ),
								onChange: ( value ) => {
			                		props.setAttributes( { submit_button_text: value } );
			              		}
							})
						)
		        	)
				])
			]);
		},

		save: function( props ) {
			return null;
		}

	} );



	/*
	 * Rating result block
	 */
	registerBlockType( 'multi-rating/rating-result', {
		
		// Built-in attributes
		title: __( 'Rating Result', 'multi-rating' ),
		description: __( 'Displays an average rating result for a post.', 'multi-rating' ),
		icon: 'star-filled',
		category: 'common',

		// Custom attributes - set server side

		// Built-in functions
		edit: function( props) {

			var className = props.className;
	        var setAttributes = props.setAttributes;

			return el('div', {}, [
				
				// Preview
				el( serverSideRender, {
					block: 'multi-rating/rating-result',
					attributes: props.attributes,
					className : className
				} ),
				
				// Block inspector
				el( InspectorControls, {}, [
					el(
		    			PanelBody, 
		    			{}, 
						el(
		        			PanelRow,
		        			{},
							el(ToggleControl, {
								checked: props.attributes.show_title,
								label: __( 'Show Title', 'multi-rating' ),
								onChange: ( value ) => {
									props.setAttributes( { show_title: value } );
			              		},
							})
						),
						el(
		        			PanelRow,
		        			{},
							el(ToggleControl, {
								checked: props.attributes.show_count,
								label: __( 'Show Count', 'multi-rating' ),
								onChange: ( value ) => {
									props.setAttributes( { show_count: value } );
			              		},
							})
						)
					)
				])
			]);
		},

		save: function( props ) {
			return null;
		}

	} );




	/*
	 * Rating result block
	 */
	registerBlockType( 'multi-rating/rating-results-list', {
		
		// Built-in attributes
		title: __( 'Rating Results List', 'multi-rating' ),
		description: __( 'Displays a list of the highest average rating results for posts.', 'multi-rating' ),
		icon: 'star-filled',
		category: 'common',

		// Custom attributes - set server side

		// Built-in functions
		edit: function( props) {

			var className = props.className;
	        var setAttributes = props.setAttributes;

			return el('div', {}, [
				
				// Preview
				el( serverSideRender, {
					block: 'multi-rating/rating-results-list',
					attributes: props.attributes,
					className : className
				} ),
				
				// Block inspector
				el( InspectorControls, {}, [
					el(
		    			PanelBody, 
		    			{}, 
						el( 
		        			PanelRow,
		        			{},
		        			el( TextControl, {
								value: props.attributes.title,
								label: __( 'Title', 'multi-rating' ),
								onChange: ( value ) => {
			                		props.setAttributes( { title: value } );
			              		},
			              		type: 'string'
							})
						),
						el(
		        			PanelRow,
		        			{},
							el( TextControl, {
								value: props.attributes.limit,
								label: __( 'Limit', 'multi-rating' ),
								onChange: ( value ) => {
			                		props.setAttributes( { limit: parseInt(value) } );
			              		},
			              		min: 1,
			              		max: 50,
			              		type: 'number'
							})
						),
						el(
		        			PanelRow,
		        			{},
							el(ToggleControl, {
								checked: props.attributes.show_count,
								label: __( 'Show Count', 'multi-rating' ),
								onChange: ( value ) => {
									props.setAttributes( { show_count: value } );
			              		},
							})
						),
						el(
		        			PanelRow,
		        			{},
							el(ToggleControl, {
								checked: props.attributes.show_filter,
								label: __( 'Show Filter', 'multi-rating' ),
								onChange: ( value ) => {
			                		props.setAttributes( { show_filter: value } );
			              		}
							})
						),
						el(
		        			PanelRow,
		        			{},
							el(ToggleControl, {
								checked: props.attributes.show_rank,
								label: __( 'Show Rank', 'multi-rating' ),
								onChange: ( value ) => {
			                		props.setAttributes( { show_rank: value } );
			              		}
							})
						),
						el(
		        			PanelRow,
		        			{},
							el(ToggleControl, {
								checked: props.attributes.show_featured_img,
								label: __( 'Show Featured Image', 'multi-rating' ),
								onChange: ( value ) => {
			                		props.setAttributes( { show_featured_img: value } );
			              		}
							})
						)
					)
				])
			]);
		},

		save: function( props ) {
			return null;
		}

	} );

} )( )