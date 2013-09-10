<?php
global $post;

$editor_args = array(
//    'dfw' => true,
//    'quicktags'=> false
//    'teeny'=> true,
    'media_buttons' => false,
//    'editor_class' => 'to_meta',
    'textarea_rows' => 5
);

if ( !isset($value) ) {
    $value = '';
}

$meta_rows = json_decode($meta, true);?>

<ul id="portfolio-patterns">
    <?php if ( empty( $meta_rows ) ) { ?>
        <li class="portolio-pattern" id="row-0" data-key="0">

            <?php wpgrade_display_row_controls(1); ?>

            <div class="row-content">
                <div class="row-fluid">
                    <div class="span4">
                        <div class="image-box pattern_upload_button" id="image-0-1">
                            <label class="hidden" for="image-0-1">Image 1</label>
                            <input type="hidden" name='image-0-1' class="to_meta"/>
                            <div class="image-preview"></div>
                        </div>
                    </div>
                    <div class="span4 span-border">
                        <div class="row-fluid editor-box">
                            <?php wpgrade_generate_portfolio_field_editor(0,'') ?>
                        </div>
                        <div class="row-fluid span-border-top">
                            <div class="image-box pattern_upload_button" id="image-0-2">
                                <label class="hidden" for="image-0-2">Image 2</label>
                                <input type="hidden" name='image-0-2' class="to_meta" />
                                <div class="image-preview image-long"></div>
                            </div>
                        </div>
                    </div>
                    <div class="span4">
                        <div class="image-box pattern_upload_button" id="image-0-3">
                            <label class="hidden" for="image-0-3">Image 3</label>
                            <input type="hidden" name='image-0-3' class="to_meta"/>
                            <div class="image-preview"></div>
                        </div>
                    </div>
                </div>
            </div>

            <input type="hidden" name='pattern_type' value="1" class="to_meta"/>
        </li>
        <?php
    } else {

        foreach ( $meta_rows as $key => $pattern ) {
            $pattern = (array)$pattern;
            echo wpgrade_get_portfolio_backend_type( (int)$pattern['pattern_type'], $key, $pattern );
        }
    }

    echo '<input type="hidden" name="'. $field['id'] .'" id="portfolio_gallery_val" />'; ?>

    <div id="wpgrade_portfolio_editor_modal" style="display: none">
        <div class="modal_wrapper">
            <div class="media-modal wp-core-ui">
                <a class="media-modal-close close_modal_btn" href="#" title="Close"><span class="media-modal-icon"></span></a>
<!--                <a class="close_modal_btn media-modal-close" href="#"></a>-->
                <div class="media-modal-content">
                    <div class="media-frame-title"><h1>Insert Content</h1></div>
                    <div class="media-frame-router"></div>
                    <div class="media-frame-content">
                        <?php wp_editor( '', 'the_only_editor', $editor_args ); ?>
                    </div>
                    <div class="modal_controls media-frame-toolbar">
                        <a class="close_modal_btn button button-large" href="#">Cancel</a>
                        <a class="insert_editor_content button media-button button-primary button-large" href="#">Insert Content</a>
                    </div>
                </div>
            </div>
            <div class="media-modal-backdrop close_modal_btn"></div>
        </div>
    </div>
</ul>
