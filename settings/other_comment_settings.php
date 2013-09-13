<?php return array
	(
		'type' => 'tabular-group',
		'label' => 'Other comment settings',

		// Custom field settings
		// ---------------------

		'options' => array
			(
				'author_email_required' => array
					(
						'label' => 'Comment author must fill out name and e-mail ',
						'default' => true,
						'type' => 'switch',
					),
				'users_must_be_registered' => array
					(
						'label' => 'Users must be registered and logged in to comment ',
						'default' => false,
						'type' => 'switch',
					),
				'users_must_be_registered' => array
					(
						'label' => 'Users must be registered and logged in to comment ',
						'default' => false,
						'type' => 'switch',
					),
				'autoclose_comments' => array
					(
						'label' => 'Automatically close comments on articles older than :autoclose_comments_days days',
						'default' => false,
						'type' => 'switch',

						// Custom field settings
						// ---------------------

						'label-fillins' => array
							(
								'autoclose_comments_days' => array
									(
										'label' => null,
										'default' => 14,
										'type' => 'counter',
										'attrs' => array
											(
												'class' => array('example-class', 'another-class')
											),
									),
							),
					),

				'threaded_comments' => array
					(
						'label' => 'Enable threaded (nested) comments :threaded_comments_levels levels deep ',
						'default' => true,
						'type' => 'switch',

						// Custom field settings
						// ---------------------

						'label-fillins' => array
							(
								'threaded_comments_levels' => array
									(
										'label' => null,
										'default' => 5,
										'type' => 'counter',
									),
							),
					),

				'show_comment_pages' => array
					(
						'label' => 'Break comments into pages with :show_comment_pages_count top level comments per page and the :show_comment_pages_target page displayed by default',
						'default' => false,
						'type' => 'switch',

						// Custom field settings
						// ---------------------

						'label-fillins' => array
							(
								'show_comment_pages_count' => array
									(
										'label' => null,
										'default' => 50,
										'type' => 'counter',
									),
								'show_comment_pages_target' => array
									(
										'name' => 'show_comment_pages_target',
										'label' => null,
										'default' => 'last',
										'type' => 'select',

										// Custom field settings
										// ---------------------

										'options' => array
											(
												'last' => 'last',
												'first' => 'first',
											),
									)
							),
					),
			)
	);