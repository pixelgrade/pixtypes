<?php
/**
 * Include and setup custom metaboxes and fields.
 *
 * @category WPGRADE_THEMENAME
 * @package  Metaboxes
 * @license  http://www.opensource.org/licenses/gpl-license.php GPL v2.0 (or later)
 * @link     https://github.com/jaredatch/Custom-Metaboxes-and-Fields-for-WordPress
 */


function load_metaboxes_fromdb( array $meta_boxes ){

	$options = get_option( 'PixTypes_settings' );
//	echo '<pre>';
//	var_dump($options);
//	echo '</pre>';



	$meta_boxes = get_option( '_wpgrade_theme_metadata'.wpgrade::prefix() );
	$meta_boxes2 = get_option( '_wpgrade_theme_metadata_citylife_' );
    if ( is_array($meta_boxes) && is_array($meta_boxes2 ) ) {
        $meta_boxes = array_merge($meta_boxes, $meta_boxes2);
    }
	return $meta_boxes;
}

add_filter( 'cmb_meta_boxes', 'load_metaboxes_fromdb',1 );
/**
 * Define the metabox and field configurations.
 *
 * @param  array $meta_boxes
 * @return array
 */
function cmb_wpgrade_metaboxes( array $meta_boxes ) {

	$meta_boxes[] = array(
		'id'         => 'homepage_slide_content',
		'title'      => 'Home Slider Content',
		'pages'      => array( 'homepage_slide' ), // Post type
		'context'    => 'normal',
		'priority'   => 'high',
		'show_names' => true, // Show field names on the left
		'fields'     => array(
            array(
                'name' => 'Image',
                'desc' => __('Upload an image or enter an URL.', wpGrade_txtd),
                'id'   => wpgrade::prefix() . 'homepage_slide_image',
                'type' => 'attachment',
            ),
            array(
                'name'    => 'Caption',
                'desc'    => __('The caption of the slider', wpGrade_txtd),
                'id'      => wpgrade::prefix() . 'homepage_slide_caption',
                'type'    => 'wysiwyg',
                'options' => array(	'textarea_rows' => 5, ),
            ),
            array(
                'name' => 'Button Label',
                'id'   => wpgrade::prefix() . 'homepage_slide_label',
                'type' => 'text_medium',
            ),
            array(
                'name' => 'Link',
                'id'   => wpgrade::prefix() . 'homepage_slide_link',
                'type' => 'text',
            ),
		),
	);

    /*
     * The Video Post Format
     */
    $meta_boxes[] = array(
        'id' => 'post_format_metabox_video',
        'title' => __('Video Settings', wpGrade_txtd),
        'pages'      => array( 'homepage_slide' ), // Post type
        'context' => 'normal',
        'priority' => 'high',
        'show_names' => true, // Show field names on the left
        'fields' => array(
            array(
                'name' => __('Youtube Link', wpGrade_txtd),
                'desc' => __('Enter here an Youtube video link. Any videos bellow will be ignored.', wpGrade_txtd),
                'id' => wpgrade::prefix() . 'youtube_id',
                'type' => 'text',
                'std' => '',
            ),
            array(
                'name' => __('Vimeo Link', wpGrade_txtd),
                'desc' => __('Enter here a Vimeo video link. Any videos bellow will be ignored.', wpGrade_txtd),
                'id' => wpgrade::prefix() . 'vimeo_link',
                'type' => 'text',
                'std' => '',
            ),
			array(
                'name' => __('Vimeo Video Width', wpGrade_txtd),
                'desc' => __('Enter here the video width (we are only interested in the aspect ratio, width/height, so you could use 16 and 9; we use this to try and get rid of the black bars)', wpGrade_txtd),
                'id' => wpgrade::prefix() . 'video_width',
                'type' => 'text_small',
                'std' => '500',
            ),
			array(
                'name' => __('Vimeo Video Height', wpGrade_txtd),
                'desc' => __('Enter here the video height', wpGrade_txtd),
                'id' => wpgrade::prefix() . 'video_height',
                'type' => 'text_small',
                'std' => '281',
            ),
            array(
                'name' => __('MP4 File URL', wpGrade_txtd),
                'desc' => __('Please enter in the URL to your .m4v video file (h.264 codec). This format is need to provide support for Safari, IE9, iPhone, iPad, Android, and Windows Phone 7', wpGrade_txtd),
                'id' => wpgrade::prefix() . 'video_m4v',
                'type' => 'file',
                'std' => ''
            ),
            array(
                'name' => __('WebM/VP8 File URL', wpGrade_txtd),
                'desc' => __('Please enter in the URL to your .webm video file. This format is need to provide support for Firefox4, Opera, and Chrome', wpGrade_txtd),
                'id' => wpgrade::prefix() . 'video_webm',
                'type' => 'file',
                'std' => ''
            ),
            array(
                'name' => __('Ogg/Vorbis File URL', wpGrade_txtd),
                'desc' => __('Please enter in the URL to your .ogv video file. This format is need to provide support for older Firefox and Opera versions', wpGrade_txtd),
                'id' => wpgrade::prefix() . 'video_ogv',
                'type' => 'file',
                'std' => ''
            ),
            array(
                'name' => __('Preview Image', wpGrade_txtd),
                'desc' => __('This will be the image displayed when the video has not been played yet. The image should be at least 640px wide. Click the "Upload" button to open the Media Manager, and click "Use as Preview Image" once you have uploaded or chosen an image from the media library.', wpGrade_txtd),
                'id' => wpgrade::prefix() . 'video_poster',
                'type' => 'file',
                'std' => ''
            ),
        )
    );

    $meta_boxes[] = array(
        'id'         => 'page_details_metabox',
        'title'      => __('Header Settings', wpGrade_txtd),
        'pages'      => array( 'page' ), // Post type
        'context'    => 'normal',
        'priority'   => 'high',
        'show_names' => true, // Show field names on the left
//        'show_on'    => array( 'key' => 'id', 'value' => array( 2, ), ), // Specific post IDs to display this metabox
        'fields' => array(
            array(
                'name' => __('Hide Title', wpGrade_txtd),
                'desc' => __('Hide the page title?', wpGrade_txtd),
                'id'   => wpgrade::prefix() . 'page_display_title',
                'type' => 'checkbox',
            ),
            array(
                'name' => __('Header HTML Content', wpGrade_txtd),
                'desc' => __('Create your own title and header content with the featured image as background.', wpGrade_txtd),
                'id'   => wpgrade::prefix() . 'page_html_title',
                'type' => 'wysiwyg',
				'options' => array (
					'textarea_rows' => 10,
					),
            ),
			array(
					'name' =>  __('Background Color', wpGrade_txtd),
					'desc' => __('If you haven\'t chosen a featured image this color will be used as background.', wpGrade_txtd),
					'id' => wpgrade::prefix() . 'header_background_color',
					'type' => 'colorpicker',
                    'std' => '#333333',
					'options' => array (),
				)
        )
    );
	
	global $wpGrade_Options;

    $meta_boxes[] = array(
        'id'         => 'wordpress_gallery',
        'title'      => __('Gallery', wpGrade_txtd),
        'pages'      => array( 'portfolio' ), // Post type
        'context'    => 'normal',
        'priority'   => 'high',
        'show_names' => true, // Show field names on the left
        'fields' => array(
            array(
                'name' => __('Images', wpGrade_txtd),
//                'desc' => __('Row type', wpGrade_txtd),
                'id'   => wpgrade::prefix() . 'portfolio_gallery',
                'type' => 'gallery',
            )
        )
    );

    $meta_boxes[] = array(
        'id'         => 'portfolio_header_features',
        'title'      => __('Header Settings', wpGrade_txtd),
        'pages'      => array( 'portfolio' ), // Post type
        'context'    => 'normal',
        'priority'   => 'high',
        'show_names' => true, // Show field names on the left
        'fields' => array(
            array(
                'name' => __('Featured Project', wpGrade_txtd),
                'desc' => __('Items checked as featured will be displayed first in the homepage portfolio section (ordered by date descending)', wpGrade_txtd),
                'id'   => wpgrade::prefix() . 'portfolio_featured',
                'type' => 'checkbox',
            ),
			array(
                'name' => __('Hide Title', wpGrade_txtd),
                'desc' => __('Hide the project title?', wpGrade_txtd),
                'id'   => wpgrade::prefix() . 'project_display_title',
                'type' => 'checkbox',
				'options' => array (
					'hidden' => ($wpGrade_Options->get('portfolio_single_show_header_image') ) ? false : true,
					),
            ),
			array(
                'name' => __('Header HTML Content', wpGrade_txtd),
                'desc' => __('Create your own title and header content with the featured image as background.', wpGrade_txtd),
                'id'   => wpgrade::prefix() . 'project_html_title',
                'type' => 'wysiwyg',
				'options' => array (
					'textarea_rows' => 10,
//					'hidden' => ($wpGrade_Options->get('portfolio_single_show_header_image') ) ? false : true,
					),
            ),
			array(
					'name' =>  __('Background Color', wpGrade_txtd),
					'desc' => __('If you haven\'t chosen a featured image this color will be used as background.', wpGrade_txtd),
					'id' => wpgrade::prefix() . 'header_background_color',
					'type' => 'colorpicker',
                    'std' => '#333333',
					'options' => array (),
				),
        )
    );

	$meta_boxes[] = array(
        'id'         => 'post_details_metabox',
        'title'      => __('Header Settings', wpGrade_txtd),
        'pages'      => array( 'post' ), // Post type
        'context'    => 'normal',
        'priority'   => 'high',
        'show_names' => true, // Show field names on the left
//        'show_on'    => array( 'key' => 'id', 'value' => array( 2, ), ), // Specific post IDs to display this metabox
        'fields' => array(
            array(
                'name' => __('Header HTML Content', wpGrade_txtd),
                'desc' => __('Create your own title and header content with the featured image as background.', wpGrade_txtd),
                'id'   => wpgrade::prefix() . 'post_html_title',
                'type' => 'wysiwyg',
				'options' => array (
					'textarea_rows' => 10,
					),
            ),
			array(
					'name' =>  __('Background Color', wpGrade_txtd),
					'desc' => __('If you haven\'t chosen a featured image this color will be used as background.', wpGrade_txtd),
					'id' => wpgrade::prefix() . 'header_background_color',
					'type' => 'colorpicker',
                    'std' => '#333333',
					'options' => array (),
				),
        )
    );
	
	/*
	 * The Quote Post Format
	 */
    $meta_boxes[] = array(
		'id' => 'post_format_metabox_quote',
		'title' =>  __('Quote Settings', wpGrade_txtd),
		'pages'      => array( 'post' ), // Post type
		'context' => 'normal',
		'priority' => 'high',
		'show_names' => true, // Show field names on the left
		'fields' => array(
			array(
					'name' =>  __('Quote Content', wpGrade_txtd),
					'desc' => __('Please type the text of your quote here.', wpGrade_txtd),
					'id' => wpgrade::prefix() . 'quote',
					'type' => 'wysiwyg',
                    'std' => '',
					'options' => array (
						'textarea_rows' => 4,
						),
				),
			array(
					'name' => __('Author Name', wpGrade_txtd),
					'desc' => '',
					'id' => wpgrade::prefix() . 'quote_author',
					'type' => 'text',
					'std' => '',
				),
			array(
					'name' => __('Author Link', wpGrade_txtd),
					'desc' => __('Insert here an url if you want the author name to be linked to that address.', wpGrade_txtd),
					'id' => wpgrade::prefix() . 'quote_author_link',
					'type' => 'text',
					'std' => '',
				),
		)
	);
	
	/*
	 * The Video Post Format
	 */
    $meta_boxes[] = array(
		'id' => 'post_format_metabox_video',
		'title' => __('Video Settings', wpGrade_txtd),
		'pages'      => array( 'post'), // Post type
		'context' => 'normal',
		'priority' => 'high',
		'show_names' => true, // Show field names on the left
		'fields' => array(
			array(
					'name' => __('Embed Code', wpGrade_txtd),
					'desc' => __('Enter here a Youtube, Vimeo (or other online video services) embed code here. The width should be a minimum of 640px. We will use this if filled, not the selfhosted options bellow!', wpGrade_txtd),
					'id' => wpgrade::prefix() . 'video_embed',
					'type' => 'textarea_small',
					'std' => '',
				),
			array( 
					'name' => __('MP4 File URL', wpGrade_txtd),
					'desc' => __('Please enter in the URL to your .m4v video file (h.264 codec). This format is need to provide support for Safari, IE9, iPhone, iPad, Android, and Windows Phone 7', wpGrade_txtd),
					'id' => wpgrade::prefix() . 'video_m4v',
					'type' => 'file',
					'std' => ''
				),
			array( 
					'name' => __('WebM/VP8 File URL', wpGrade_txtd),
					'desc' => __('Please enter in the URL to your .webm video file. This format is need to provide support for Firefox4, Opera, and Chrome', wpGrade_txtd),
					'id' => wpgrade::prefix() . 'video_webm',
					'type' => 'file',
					'std' => ''
				),
			array( 
					'name' => __('Ogg/Vorbis File URL', wpGrade_txtd),
					'desc' => __('Please enter in the URL to your .ogv video file. This format is need to provide support for older Firefox and Opera versions', wpGrade_txtd),
					'id' => wpgrade::prefix() . 'video_ogv',
					'type' => 'file',
					'std' => ''
				),
			array( 
					'name' => __('Preview Image', wpGrade_txtd),
					'desc' => __('This will be the image displayed when the video has not been played yet. The image should be at least 640px wide. Click the "Upload" button to open the Media Manager, and click "Use as Preview Image" once you have uploaded or chosen an image from the media library.', wpGrade_txtd),
					'id' => wpgrade::prefix() . 'video_poster',
					'type' => 'file',
					'std' => ''
				),
		)
	);
	
	/*
	 * The Audio Post Format
	 */
	$meta_boxes[] = array(
		'id' => 'post_format_metabox_audio',
		'title' =>  __('Audio Settings', wpGrade_txtd),
		'pages'      => array( 'post'), // Post type
		'context' => 'normal',
		'priority' => 'high',
		'show_names' => true, // Show field names on the left
		'fields' => array(
			array(
					'name' => __('Embed Code', wpGrade_txtd),
					'desc' => __('Enter here a embed code here. The width should be a minimum of 640px. We will use this if filled, not the selfhosted options bellow!', wpGrade_txtd),
					'id' => wpgrade::prefix() . 'audio_embed',
					'type' => 'textarea_small',
					'std' => '',
				),
			array( 
					'name' => __('MP3 File URL', wpGrade_txtd),
					'desc' => __('Please enter in the URL to the .mp3 file', wpGrade_txtd),
					'id' => wpgrade::prefix() . 'audio_mp3',
					'type' => 'file',
					'std' => ''
				),
			array( 
					'name' => __('M4A File URL', wpGrade_txtd),
					'desc' => __('Please enter in the URL to the .m4a file', wpGrade_txtd),
					'id' => wpgrade::prefix() . 'audio_m4a',
					'type' => 'file',
					'std' => ''
				),
			array( 
					'name' => __('OGA File URL', wpGrade_txtd),
					'desc' => __('Please enter in the URL to the .ogg or .oga file', wpGrade_txtd),
					'id' => wpgrade::prefix() . 'audio_ogg',
					'type' => 'file',
					'std' => ''
				),
			array( 
					'name' => __('Poster Image', wpGrade_txtd),
					'desc' => __('This will be the image displayed above the audio controls. The image should be at least 640px wide. Click the "Upload" button to open the Media Manager, and click "Use as Poster Image" once you have uploaded or chosen an image from the media library.', wpGrade_txtd),
					'id' => wpgrade::prefix() . 'audio_poster',
					'type' => 'file',
					'std' => ''
				),
		)
	);
	
	/*
	 * The Link Post Format
	 */ 
//	$meta_boxes[] = array(
//		'id' => 'post_format_metabox_link',
//		'title' =>  __('Link Settings', wpGrade_txtd),
//		'pages'      => array( 'post', ), // Post type
//		'context' => 'normal',
//		'priority' => 'high',
//		'show_names' => true, // Show field names on the left
//		'fields' => array(
//			array(
//					'name' =>  __('Link URL', wpGrade_txtd),
//					'desc' => __('Please input the URL of your link(i.e. http://www.pixelgrade.com)', wpGrade_txtd),
//					'id' => wpgrade::prefix() .'link',
//					'type' => 'text',
//					'std' => ''
//				)
//		)
//	);
	
	/*
     * Testimonials meta
     */

    $meta_boxes[] = array(
        'id'         => 'testimonial_metabox',
        'title'      => __( 'Testimonial Metabox', wpGrade_txtd ),
        'pages'      => array( 'testimonial' ), // Post type
        'context'    => 'normal',
        'priority'   => 'high',
        'show_names' => true, // Show field names on the left
        'fields'     => array(
            array(
                'name' => __( 'Author Name', wpGrade_txtd ),
                'desc' => __( 'The author of this confession', wpGrade_txtd ),
                'id'   => wpgrade::prefix() . 'author_name',
                'type' => 'text_medium',
            ),
            array(
                'name' => __( 'Author Function', wpGrade_txtd ),
                'desc' => __( 'The title of the author (eg. Client)', wpGrade_txtd ),
                'id'   => wpgrade::prefix() . 'author_function',
                'type' => 'text_medium',
            ),
            array(
                'name' => __( 'Author Link', wpGrade_txtd ),
                'desc' => __( 'A link to the author website (optional)', wpGrade_txtd ),
                'id'   => wpgrade::prefix() . 'author_link',
                'type' => 'text_medium',
            ),
        ),
    );

	return $meta_boxes;
}

add_action( 'init', 'cmb_initialize_cmb_meta_boxes', 9999 );
/*
 * Initialize the metabox class.
 */
function cmb_initialize_cmb_meta_boxes() {

	if ( ! class_exists( 'cmb_Meta_Box' ) )
		require_once 'init.php';

}