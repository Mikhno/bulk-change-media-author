<?php
/*
Plugin Name: Bulk Change Media Author
Description: Allows you to bulk change the author of media items
Plugin Author: Ruslan Mikhno
Version: 0.1.0
Author URI: http://www.mikhno.org
*/

defined('ABSPATH') or die('Direct access is not allowed.');

/* Register the new bulk actions. */
function bulk_change_media_author_register_actions($bulk_actions) {
	$bulk_actions['bulk_change_media_author_action'] = __( 'Change author', 'bulk-change-media-author');
	return $bulk_actions;
}
add_filter('bulk_actions-upload', 'bulk_change_media_author_register_actions');


/* Handle the actions. */
function bulk_change_media_author_action_handler($redirect_to, $action_name, $media_ids) {
	if ('bulk_change_media_author_action' === $action_name) {
		$redirect_to = add_query_arg('media', urlencode(json_encode($media_ids)), 'options.php?page=bulk-change-media-author-edit-page' );
	}

	return $redirect_to;
}
add_filter('handle_bulk_actions-upload', 'bulk_change_media_author_action_handler', 10, 3);


/* Register the edit page. */
function bulk_change_media_author_register_edit_page() {
	add_submenu_page(
		'',
		__( 'Bulk Change Media Author', 'bulk-change-media-author' ),
		__( '', 'bulk-change-media-author' ),
		'manage_options',
		'bulk-change-media-author-edit-page',
		'bulk_change_media_author_edit_page_callback'
	);
}
add_action('admin_menu', 'bulk_change_media_author_register_edit_page');

/* Change author for the media. */
function bulk_change_media_author_update_author($author_id, $media_ids) {
	foreach ($media_ids as $media_id) {
		wp_update_post(array(
			'ID' => $media_id,
			'post_author' => $author_id,
		));
	}
}

/* Display author change page. */
function bulk_change_media_author_edit_page_callback() {
	$author = (isset($_REQUEST['author'])) ? $_REQUEST['author'] : false;
	$media = urldecode(stripslashes($_REQUEST['media']));
	$media_ids = json_decode($media);

	?>
	<div class="wrap">
		<h1><?php _e('Bulk change author for media', 'bulk-change-media-author'); ?></h1>
		<?php
		if ($author) {
			bulk_change_media_author_update_author($author, $media_ids);
			echo '<hr />';
			echo '<div class="result success">Updated! New author: ' . get_the_author_meta('display_name', $author) . '.</div>';
		}
		?>
		<hr />
		<form action="<?php echo $_SERVER['REQUEST_URI']; ?>">
			<p><?php _e('Select a new author for the media:', 'bulk-change-media-author'); ?></p>
			<input type="hidden" name="page" value="bulk-change-media-author-edit-page" />
			<input type="hidden" name="media" value="<?php echo urlencode($media); ?>" />
			<div>
				<select name="author"><?php
				$users = get_users();
				foreach ($users as $user):
					echo '<option value="' . esc_html($user->ID) . '">' . esc_html($user->user_login) . '</option>';
				endforeach;
				?></select>
				<input type="submit" class="button" value="Change">
			</div>
		</form>
		<hr />
		<p>Selected media items (the author will be changed for the items below):</p>
		<div><?php
		foreach ($media_ids as $media_id):
			echo '<div class="media">';
			echo '<a href="'. get_edit_post_link($media_id) . '" target="_blank">';
			echo '<div>' . get_the_title($media_id) . ' (' . basename(get_attached_file($media_id)) . ')' . '</div>';
			echo '<div class="media-thumb">' . wp_get_attachment_image($media_id) . '</div>';
			echo '</a>';
			echo '</div>';
		endforeach;
		?></div>
		<hr />
		<style>
			.result.success {
				font-weight: bold;
			}
			.media {
				display: inline-block;
				padding: 10px;
				margin: 5px;
				background: #fff;
			}
			.media-thumb {
				margin-top: 5px;
				border: 1px solid rgba(0,0,0,.07);
			}
		</style>
	</div>
	<?php
}
