<?php


function test_theme_admin_init_action() {
	// meta box for post
	add_meta_box( 'alternative_image', // meta box #id
		'Дополнительные изображения для ПРОЕКТОВ', // meta box name
		'test_theme_meta_box_callback', // callback function
		'page', // screen: post or page
		'side' // position: site, advanced
	);
}

add_action( 'add_meta_boxes', 'test_theme_admin_init_action' );

/**
 * Meta box callback.
 *
 * @param $post WP_Post
 */
function test_theme_meta_box_callback( $post ) {
	$alt_image_id = get_post_meta( $post->ID, '_alt_thumbnail_id' ,true);
	
	$im = explode(",", $alt_image_id);
	echo test_theme_get_alt_thumbnail_html( $im );
}

/**
 * Show alternative image, if false show link to add it.
 *
 * @param null|int|array $thumb_id
 * @return string html code
 */
function test_theme_get_alt_thumbnail_html( $thumb_id = null ) {
	global $content_width;

	$set_thumbnail_link = '<p class="hide-if-no-js"><a title="%s" href="#" id="set-post-alternative-thumbnail">%s</a></p>';
	$content            = sprintf( $set_thumbnail_link, 'Зажмите Ctrl для добавления группы изобр.', 'Добавить изображение(я)' );

	if ( $thumb_id ) {
		if ( !is_array( $thumb_id ) ) {
			$thumb_id = array( $thumb_id );
		}
		$old_content_width = $content_width;
		$content_width     = 250;
		$thumbnail_html    = '';
		$size              = count( $thumb_id ) == 1 ? array( $content_width, $content_width ) : array( 50, 50 );
		foreach ( $thumb_id as $id ) {
			$thumbnail_html .= wp_get_attachment_image( $id, $size );
		}
		if ( !empty( $thumbnail_html ) ) {
			$content = sprintf( $set_thumbnail_link, 'Добавить изображения', $thumbnail_html );
			$content .= '<p class="hide-if-no-js"><a href="#" id="remove-post-alternative-thumbnail">Удалить изображение</a></p>';
		}
		$content_width = $old_content_width;
	}

	return $content;
}

/**
 * Enqueue admin scripts.
 *
 * @param $hook string
 */
function test_theme_admin_enqueue_scripts_action( $hook ) {

	global $post;

	

		wp_enqueue_media();
		wp_enqueue_style( 'editor-buttons' );

		wp_enqueue_script( 'alternative-image', get_stylesheet_directory_uri() . '/js/alternative_image.js', array( 'jquery' ) );

		wp_localize_script( 'alternative-image', 'test_theme', array(
				'l10n'  => array(
					'uploaderTitle'  => __( 'Set alternative image', 'test_theme' ),
					'uploaderButton' => __( 'Select image', 'test_theme' ),
				),
				'nonce' => wp_create_nonce( 'set_post_alternative_thumbnail' ),
			) );
	
}

add_action( 'admin_enqueue_scripts', 'test_theme_admin_enqueue_scripts_action' );

/**
 * Ajax callback for attaching/detaching alternative thumbnail to post
 */
function test_theme_ajax_action() {
	$post_ID = intval( $_POST['post_id'] );
	if ( !current_user_can( 'edit_post', $post_ID ) ) {
		wp_die( -1 );
	}
	$thumbnail_id1 = $_POST['thumbnail_id'];
    


	check_ajax_referer( "set_post_alternative_thumbnail", 'nonce' );

	$success = false;
	if ( $thumbnail_id1 == '-1' ) {
		$success = delete_post_meta( $post_ID, '_alt_thumbnail_id' );
		
	/*} elseif ( is_array( $thumbnail_id ) ) {
		if ( !empty ( $thumbnail_id ) ) {
			delete_post_meta( $post_ID, '_alt_thumbnail_id' );
			foreach ( $thumbnail_id as $id ) {
				add_post_meta( $post_ID, '_alt_thumbnail_id', $id );
			}
			$success = true;
		}*/
	} else {
		$thumbnail_id = implode(",", $thumbnail_id1);
		$success = update_post_meta( $post_ID, '_alt_thumbnail_id',  $thumbnail_id  );
	}

	if ( $success ) {
		$im1 = explode(",", $thumbnail_id);
		$return = test_theme_get_alt_thumbnail_html( $im1, $post_ID );
		wp_send_json_success( $return );
	}
	wp_die( 0 );
}

add_action( 'wp_ajax_set_alternative_thumbnail', 'test_theme_ajax_action' );