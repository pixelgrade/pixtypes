<?php return array
	(
		'type' => 'pixtype-group',
//		'label' => 'Taxonomies',

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
				'enable_portfolio_categories_group' => array
					(
						'type' => 'pixtype-group',
						'options' => array
							(
								'portfolio_categories_change_archive_slug' => array
									(
										'label' => 'Change Category Slug',
										'desc' => 'Do you want to rewrite the portfolio category slug?',
										'default' => false,
										'type' => 'switch',
									),
								'portfolio_categories_change_archive_slug_group' => array
									(
										'type' => 'pixtype-group',
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
					),
			)
	); # config