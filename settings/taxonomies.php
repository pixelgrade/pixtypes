<?php return array
	(
		'type' => 'postbox',
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
						'show_group' => 'enable_portfolio_categories_group'
					),
				'enable_portfolio_categories_group' => array
					(
						'type' => 'group',
						'options' => array
							(
								'portfolio_categories_change_archive_slug' => array
									(
										'label' => 'Change Category Slug',
										'desc' => 'Do you want to rewrite the portfolio category slug?',
										'default' => false,
										'type' => 'switch',
										'show_group' => 'portfolio_categories_change_archive_slug_group'
									),
								'portfolio_categories_change_archive_slug_group' => array
									(
										'type' => 'group',
										'options' => array
										(
											'portfolio_categories_new_archive_slug' => array
											(
												'label' => 'New Category Slug',
												'desc' => 'Change the portfolio category slug as you need it.',
												'default' => 'portfolio_categories',
												'type' => 'text',
											),
										),
									),
							),
					),
				'enable_gallery_categories' => array
					(
						'label' => 'Enable Gallery Categories',
						'default' => true,
						'type' => 'switch',
						'show_group' => 'enable_gallery_categories_group'
					),
				'enable_gallery_categories_group' => array
					(
						'type' => 'group',
						'options' => array
						(
							'gallery_categories_change_archive_slug' => array
							(
								'label' => 'Change Category Slug',
								'desc' => 'Do you want to rewrite the gallery category slug?',
								'default' => false,
								'type' => 'switch',
								'show_group' => 'gallery_categories_change_archive_slug_group'
							),
							'gallery_categories_change_archive_slug_group' => array
							(
								'type' => 'group',
								'options' => array
								(
									'gallery_categories_new_archive_slug' => array
									(
										'label' => 'New Category Slug',
										'desc' => 'Change the gallery category slug as you need it.',
										'default' => 'gallery_categories',
										'type' => 'text',
									),
								),
							),
						),
					),
			)
	); # config