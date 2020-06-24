( function() { // local scope

	const el = wp.element.createElement; // React.createElement
	const { __ } = wp.i18n; // translation functions
	const { SelectControl, PanelBody, PanelRow, Panel } = wp.components; //Block inspector wrapper
	const { Fragment } = wp.element;
	const { registerPlugin } = wp.plugins;
	const { PluginSidebar, PluginSidebarMoreMenuItem } = wp.editPost;
	const { withSelect, withDispatch, dispatch, select } = wp.data;
	const { compose } = wp.compose;


	/**
	 * Rating form position select control
	 */
	var ratingFormPositionSelect = compose(
		withDispatch( function( dispatch, props ) {
			return {
				setRatingFormPosition: function( metaValue ) {
					dispatch( 'core/editor' ).editPost(
						{ meta: { [ 'rating_form_position' ]: metaValue } }
					);
				}
			}
		} ),
		withSelect( function( select, props ) {
			return {
				ratingFormPosition: select( 'core/editor' ).getEditedPostAttribute( 'meta' )[ 'rating_form_position' ],
			}
		} ) )( function( props ) {
			return el( SelectControl, { 
		      	label: __( 'Rating Form Position', 'multi-rating' ),
		       	description: __( 'Add the rating form to the post.', 'multi-rating' ),
		       	options: [
		       		{ value: 'do_not_show', label: __( 'Do not show', 'multi-rating' ) },
		       		{ value: '', label: __( 'Use default settings', 'multi-rating' ) },
		       		{ value: 'before_content', label: __( 'Before content', 'multi-rating' ) },
		       		{ value: 'after_content', label: __( 'After content', 'multi-rating' ) }
		       	],
		       	help : __( 'Auto placement position for the rating form on the post.', 'multi-rating' ),
		       	value: props.ratingFormPosition,
		       	onChange: function( value ) {
	               	props.setRatingFormPosition( value );
	            },
		    });
		}
	);

	/**
	 * Rating result position select control
	 */
	var ratingResultPositionSelect = compose(
		withDispatch( function( dispatch, props ) {
			return {
				setRatingResultPosition: function( metaValue ) {
					dispatch( 'core/editor' ).editPost(
						{ meta: { [ 'rating_results_position' ]: metaValue } }
					);
				}
			}
		} ),
		withSelect( function( select, props ) {
			return {
				ratingResultPosition: select( 'core/editor' ).getEditedPostAttribute( 'meta' )[ 'rating_results_position' ],
			}
		} ) )( function( props ) {
			return el( SelectControl, { 
		      	label: __( 'Rating Result Position', 'multi-rating' ),
		       	description: __( 'Add the rating result to the post.', 'multi-rating' ),
		       	options: [
		       		{ value: 'do_not_show', label: __( 'Do not show', 'multi-rating' ) },
		       		{ value: '', label: __( 'Use default settings', 'multi-rating' ) },
		       		{ value: 'before_title', label: __( 'Before title', 'multi-rating' ) },
		      		{ value: 'after_title', label: __( 'After title', 'multi-rating' ) },
		       		{ value: 'before_content', label: __( 'Before content', 'multi-rating' ) },
		      		{ value: 'after_content', label: __( 'After content', 'multi-rating' ) }
		       	],
		       	help : __( 'Auto placement position for the rating result on the post.', 'multi-rating' ),
		       	value: props.ratingResultPosition,
		       	onChange: function( value ) {
	               	props.setRatingResultPosition( value );
	            },
		    });
		}
	);


	/**
	 * Structured data type select
	 */
	var structuredDataTypeSelect = compose(
		withDispatch( function( dispatch, props ) {
			return {
				setStruturedDataType: function( metaValue ) {
					dispatch( 'core/editor' ).editPost(
						{ meta: { [ 'mr_structured_data_type' ]: metaValue } }
					);
				}
			}
		} ),
		withSelect( function( select, props ) {
			return {
				structuredDataType: select( 'core/editor' ).getEditedPostAttribute( 'meta' )[ 'mr_structured_data_type' ],
			}
		} ) )( function( props ) {

			return el( SelectControl, { 
		      	label: 'Create New Type',
		       	description: __( 'Schema.org item type for post.', 'multi-rating' ),
		       	options: [
		       		{ value: '', label: '' },
		       		{ value: 'Book', label: __( 'Book', 'multi-rating' ) },
		       		{ value: 'Course', label: __( 'Course', 'multi-rating' ) },
		       		{ value: 'CreativeWorkSeason', label: __( 'CreativeWorkSeason', 'multi-rating' ) },
		       		{ value: 'CreativeWorkSeries', label: __( 'CreativeWorkSeries', 'multi-rating' ) },
					{ value: 'Episode', label: __( 'Episode', 'multi-rating' ) },
					{ value: 'Event', label: __( 'Event', 'multi-rating' ) },
					{ value: 'Game', label: __( 'Game', 'multi-rating' ) },
					{ value: 'HowTo', label: __( 'HowTo', 'multi-rating' ) },
					{ value: 'LocalBusiness', label: __( 'LocalBusiness', 'multi-rating' ) },
					{ value: 'MediaObject', label: __( 'MediaObject', 'multi-rating' ) },
					{ value: 'Movie', label: __( 'Movie', 'multi-rating' ) },
					{ value: 'MusicPlaylist', label: __( 'MusicPlaylist', 'multi-rating' ) },
					{ value: 'MusicRecording', label: __( 'MusicRecording', 'multi-rating' ) },
		       		{ value: 'Organization', label: __( 'Organization', 'multi-rating' ) },
		       		{ value: 'Product', label: __( 'Product', 'multi-rating' ) },
		       		{ value: 'Recipe', label: __( 'Recipe', 'multi-rating' ) },
		       		{ value: 'SoftwareApplication', label: __( 'SoftwareApplication', 'multi-rating' ) }
		       	],
		       	help :	__( 'Schema.org item type for post. If you have the WordPress SEO or WooCommerce plugins adding structured data for the type already, do not set. Note some types may require additional structured data.', 'multi-rating' ),
		       	value: props.structuredDataType,
		       	onChange: function( value ) {
	               	props.setStruturedDataType( value );
	            },
		    });

		}
	);


	/**
	 * Adds to the plugin post settings to the Gutenberg plugin and sidebar menus
	 */
	registerPlugin( 'multi-rating', {

		icon: 'star-filled',
	    
	    render: function () {
	    	return el( 
	    		Fragment, 
	    		{},
		        el( 
		        	PluginSidebarMoreMenuItem, 
		        	{
		            	target: 'multi-rating',
		            	icon: 'star-filled'
		        	},
		        	__( 'Multi Rating', 'multi-rating' )
		    	),
		    	el( 
		    		PluginSidebar, 
		    		{
		    			name: 'multi-rating',
		    			icon: 'star-filled',
		    			title: __( 'Multi Rating', 'multi-rating' ),
		    			className: 'mr-plugin-sidebar'
		    		},
		    		el( 
		    			Panel,
		    			{},
		    			el(
			    			PanelBody, 
			    			{ 
			    				title: __( 'Auto Placement', 'multi-rating' ),
			    				initialOpen: false
			    			}, 
			        		el(
			        			PanelRow,
			        			{},
			        			el( ratingFormPositionSelect )
			        		),
			        		el(
			        			PanelRow,
			        			{},
			        			el( ratingResultPositionSelect )
			        		)
			        	),
		        		el(
			    			PanelBody, 
			    			{ 
			    				title: __( 'Structured Data', 'multi-rating' ),
			    				initialOpen: false
			    			}, 
			        		el(
				        		PanelRow,
				        		{},
				        		el( 
				        			'div', 
				        			{}, 
				        			__( 'Supports rich snippets with aggregate ratings for the post in search engine results pages (SERP).', 'multi-rating' )
				        		)
				        	),
				        	el(
				        		PanelRow,
				        		{},
				        		el( structuredDataTypeSelect )
				        	)
			        	)
		        	)
		    	)
		    )
	    }
	} );

} )( )