<?php return array
	(
		'type' => 'pixtype-group',
		'label' => 'Taxonomies',

		// Custom field settings
		// ---------------------

		'options' => array
			(
				'enable_portfolio_categories' => array
					(
						'label' => 'Enable Portfolio Categories',
						'default' => true,
						'type' => 'switch',
					),
//				'portfolio_single_item_label' => array
//					(
//						'label' => 'Single Item Label',
//						'desc' => 'Here you can change the singular label.The default is "Project"',
//						'default' => 'Project',
//						'type' => 'text',
//					),
//				'portfolio_multiple_items_label' => array
//					(
//						'label' => 'Multiple Items Label (plural)',
//						'desc' => 'Here you can change the plural label.The default is "Projects"',
//						'default' => 'Projects',
//						'type' => 'text',
//					),
//				'portfolio_change_single_item_slug' => array
//					(
//						'label' => 'Change Single Item Slug',
//						'desc' => 'Do you want to rewrite the single portfolio item slug?',
//						'default' => true,
//						'type' => 'switch',
//					),
//				'portfolio_new_single_item_slug' => array
//					(
//						'label' => 'New Single Item Slug',
//						'desc' => 'Change the single portfolio item slug as you need it.',
//						'default' => 'project',
//						'type' => 'text',
//
//						// extra pixtype-group options
//						'pixtype-group-example' => 'from your.domain.com/portfolio/item1 in your.domain.com/new-slug/item1',
//						'pixtype-group-note' => 'After you change this you need to go and save the permalinks to flush them.'
//					),
//				'portfolio_change_archive_slug' => array
//					(
//						'label' => 'Change Archive Slug',
//						'desc' => 'Do you want to rewrite the portfolio archive slug? This will only be used if you don\'t have a page with the Portfolio template.',
//						'default' => false,
//						'type' => 'switch',
//					),
//				'portfolio_new_archive_slug' => array
//					(
//						'label' => 'New Category Slug',
//						'desc' => 'Change the portfolio category slug as you need it.',
//						'default' => 'portfolio',
//						'type' => 'text',
//
//						// extra pixtype-group options
//						'pixtype-group-example' => 'from your.domain.com/portfolio in your.domain.com/new-slug',
//						'pixtype-group-note' => 'After you change this you need to go and save the permalinks to flush them.'
//					),
//				'portfolio_use_tags' => array
//					(
//						'label' => 'Use Tags',
//						'desc' => 'Do you want to assign tags to portfolio items?',
//						'default' => true,
//						'type' => 'switch',
//					),
				'enable_gallery_categories' => array
					(
						'label' => 'Enable Gallery Categories',
						'default' => true,
						'type' => 'switch',
					),
			)
	); # config