<?php return array
	(
		'type' => 'postbox',
		'label' => 'Post Types',

		// Custom field settings
		// ---------------------

		'options' => array
			(
				'enable_portfolio' => array
					(
						'label' => 'Enable Portfolio',
						'default' => true,
						'type' => 'switch',
						'show_group' => 'enable_portfolio_group',
					), /* ALL THESE PREFIXED WITH PORTFOLIO SHOULD BE KIDS!! **/

				'enable_portfolio_group' => array
					(
						'type' => 'group',
						'options' => array
							(
								'portfolio_single_item_label' => array
									(
										'label' => 'Single Item Label',
										'desc' => 'Here you can change the singular label.The default is "Project"',
										'default' => 'Project',
										'type' => 'text',
									),
								'portfolio_multiple_items_label' => array
									(
										'label' => 'Multiple Items Label (plural)',
										'desc' => 'Here you can change the plural label.The default is "Projects"',
										'default' => 'Projects',
										'type' => 'text',
									),
								'portfolio_change_single_item_slug' => array
									(
										'label' => 'Change Single Item Slug',
										'desc' => 'Do you want to rewrite the single portfolio item slug?',
										'default' => true,
										'type' => 'switch',
										'show_group' => 'portfolio_change_single_item_slug_group',
									),
								'portfolio_change_single_item_slug_group' => array
									(
										'type' => 'group',
										'options' => array
											(
												'portfolio_new_single_item_slug' => array
													(
														'label' => 'New Single Item Slug',
														'desc' => 'Change the single portfolio item slug as you need it.',
														'default' => 'project',
														'type' => 'text',

														// extra group options
														//'group-example' => 'from your.domain.com/portfolio/item1 in your.domain.com/new-slug/item1',
														//'group-note' => 'After you change this you need to go and save the permalinks to flush them.'
													),
											),
									),
								'portfolio_change_archive_slug' => array
									(
										'label' => 'Change Archive Slug',
										'desc' => 'Do you want to rewrite the portfolio archive slug? This will only be used if you don\'t have a page with the Portfolio template.',
										'default' => false,
										'type' => 'switch',
										'show_group' => 'portfolio_change_archive_slug_group',
									),
								'portfolio_change_archive_slug_group' => array
									(
										'type' => 'group',
										'options' => array
										(
											'portfolio_new_archive_slug' => array
												(
													'label' => 'New Category Slug',
													'desc' => 'Change the portfolio category slug as you need it.',
													'default' => 'portfolio',
													'type' => 'text',
												),
										),
									),
						),
				),
				'enable_gallery' => array
					(
						'label' => 'Enable Gallery',
						'default' => true,
						'type' => 'switch',
						'show_group' => 'enable_gallery_group',
					),
				'enable_gallery_group' => array(
					'type' => 'group',
					'options' => array(
						'gallery_change_single_item_slug' => array
						(
							'label' => 'Change Single Item Slug',
							'desc' => 'Do you want to rewrite the single gallery item slug?',
							'default' => true,
							'type' => 'switch',
							'show_group' => 'gallery_change_single_item_slug_group',
						),
						'gallery_change_single_item_slug_group' => array
						(
							'type' => 'group',
							'options' => array
							(
								'gallery_new_single_item_slug' => array
								(
									'label' => 'New Single Item Slug',
									'desc' => 'Change the single gallery item slug as you need it.',
									'default' => 'project',
									'type' => 'text',
								),
							),
						),
						'gallery_change_archive_slug' => array
						(
							'label' => 'Change Archive Slug',
							'desc' => 'Do you want to rewrite the gallery archive slug? This will only be used if you don\'t have a page with the gallery template.',
							'default' => false,
							'type' => 'switch',
							'show_group' => 'gallery_change_archive_slug_group',
						),
						'gallery_change_archive_slug_group' => array
						(
							'type' => 'group',
							'options' => array
							(
								'gallery_new_archive_slug' => array
								(
									'label' => 'New Category Slug',
									'desc' => 'Change the gallery category slug as you need it.',
									'default' => 'gallery',
									'type' => 'text',
								),
							),
						),
					)
				),
			)
	); # config