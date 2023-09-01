<?php
/*
Plugin Name: Bulk Change Media Author
Plugin URI: http://www.mikhno.org/articles/en/files/wp_bulk_change_media_author
Description: This simple plugin allows you to bulk change author for your media items.
Version: 1.3.2
Author: Ruslan Mikhno
Author URI: http://www.mikhno.org
Text Domain: bulk-change-media-author
*/

defined('ABSPATH') or die('Direct access is not allowed.');


/* Register the new bulk actions. */
function bulk_change_media_author_register_actions($bulk_actions) {
	$bulk_actions['bulk_change_media_author_action'] = __( 'Change Author', 'bulk-change-media-author');
	return $bulk_actions;
}
add_filter('bulk_actions-upload', 'bulk_change_media_author_register_actions');


/* Handle the new bulk actions. */
function bulk_change_media_author_action_handler($redirect_to, $action_name, $media_ids) {
	if ('bulk_change_media_author_action' === $action_name) {
		$redirect_to = add_query_arg('media', urlencode(json_encode($media_ids)), 'options.php?page=bulk-change-media-author-edit-page' );
	}

	return $redirect_to;
}
add_filter('handle_bulk_actions-upload', 'bulk_change_media_author_action_handler', 10, 3);


/* Register the author change page. */
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


/* Display the author change page. */
function bulk_change_media_author_edit_page_callback() {
	$author = (isset($_REQUEST['author'])) ? $_REQUEST['author'] : false;
	$for = (isset($_REQUEST['for'])) ? $_REQUEST['for'] : false;
	$media = false;
	$media_ids = array();
	if (isset($_REQUEST['media'])) {
		$media = urldecode(stripslashes($_REQUEST['media']));
		$media_ids = json_decode($media);
	}
	$redirectToMediaLibrary = false;
	if ($for && empty($media_ids)) {
		$query = new WP_Query(array(
		    'author' => $for,
		    'post_type' => 'attachment',
				'post_status' => [
					'any',
					'inherit',
					'trash',
					'auto-draft',
				],
				'fields' => 'ids',
				'nopaging' => true,
				'posts_per_page' => -1,
		));
		$media_ids = $query->get_posts();
		$media = json_encode($media_ids);
	}
	?>
	<div class="wrap">
		<h1><?php _e('Bulk change author for media', 'bulk-change-media-author'); ?></h1>
		<?php
		if (empty($media_ids)) {
			?>
			<hr />
			<div>
				<p class="result"><?php _e( 'No media items selected.', 'bulk-change-media-author') ?></p>
				<p><?php _e( 'You can filter media by its author here, or go back to Media Library and select media.', 'bulk-change-media-author') ?></p>
			</div>
			<form action="<?php echo $_SERVER['REQUEST_URI']; ?>">
				<input type="hidden" name="page" value="bulk-change-media-author-edit-page" />
				<div style="margin-bottom: 15px;">
					<span style="font-weight: bold;"><?php _e('Select author to filter media:', 'bulk-change-media-author'); ?></span>
					<select name="for"><?php
					$users = get_users();
					foreach ($users as $user):
						echo '<option value="' . esc_html($user->ID) . '">' . esc_html($user->user_login) . '</option>';
					endforeach;
					?></select>
					<input type="submit" class="button-primary" value="<?php _e('Filter By Author', 'bulk-change-media-author'); ?>"> <a href="<?php echo admin_url('upload.php'); ?>" class="button-secondary" ><?php _e('Cancel', 'bulk-change-media-author'); ?></a>
				</div>
			</form>
			<?php
		} else if ($author) {
			bulk_change_media_author_update_author($author, $media_ids);

			echo '<hr /><div class="result">';
			_e('Updated! New author: ', 'bulk-change-media-author');
			 echo get_the_author_meta('display_name', $author) . '. ';
			_e('Redirecting back to Media Library...', 'bulk-change-media-author');
			echo '</div>';

			$redirectToMediaLibrary = true;
		}

		if ($redirectToMediaLibrary) {
			echo '<script type="text/javascript">';
			echo 'setTimeout(function(){ window.location = "' . admin_url('upload.php') . '" }, 1000);';
			echo '</script>';
		}

		if (!empty($media_ids) && empty($author)): ?>
			<hr />
			<form action="<?php echo $_SERVER['REQUEST_URI']; ?>">
				<p style="font-weight: bold;"><?php _e('Select a new author for the media:', 'bulk-change-media-author'); ?></p>
				<input type="hidden" name="page" value="bulk-change-media-author-edit-page" />
				<input type="hidden" name="media" value="<?php echo urlencode($media); ?>" />
				<div>
					<select name="author"><?php
					$users = get_users();
					foreach ($users as $user):
						echo '<option value="' . esc_html($user->ID) . '">' . esc_html($user->user_login) . '</option>';
					endforeach;
					?></select>
					<input type="submit" class="button-primary" value="<?php _e('Change', 'bulk-change-media-author'); ?>"> <a href="<?php echo admin_url('upload.php'); ?>" class="button-secondary" ><?php _e('Cancel', 'bulk-change-media-author'); ?></a>
				</div>
			</form>
			<hr />
			<p><?php _e('Selected media items (the author will be changed for the items below)', 'bulk-change-media-author'); ?>:</p>
			<div><?php
			foreach ($media_ids as $media_id):
				$media_name = get_the_title($media_id);
				$media_name = (strlen($media_name) > 13) ? substr($media_name, 0, 10) . '...' : $media_name;
				$media_file = basename(get_attached_file($media_id));
				$media_file = (strlen($media_file) > 10) ? substr($media_file, 0, 7) . '...' : $media_file;
				$media_title = $media_name . ' (' . $media_file . ')';

				echo '<div class="media">';
				echo '<a href="'. get_edit_post_link($media_id) . '" target="_blank">';
				echo '<div class="media-author">' . get_the_author_meta('display_name', get_post_field ('post_author', $media_id)) . '</div>';
				echo '<div>' . $media_title . '</div>';
				echo '<div class="media-thumb">' . wp_get_attachment_image($media_id) . '</div>';
				echo '</a>';
				echo '</div>';
			endforeach;
			?></div>
		<?php endif; ?>
		<hr />
		<style>
			.result {
				font-weight: bold;
			}
			.media {
				display: inline-block;
				padding: 10px;
				margin: 10px;
				background: #fff;
			}
			.media a {
				text-decoration: none;
				color: #000;
			}
			.media-author {
				font-weight: bold;
			}
			.media-thumb {
				margin-top: 5px;
				border: 1px solid rgba(0,0,0,.07);
				max-width: 150px;
		    max-height: 150px;
			}
		</style>
	</div>
	<?php
}


/* Load translation (textmodain) files. */
function bulk_change_media_author_load_textdomain() {
	load_plugin_textdomain('bulk-change-media-author', false, dirname(plugin_basename(__FILE__)) . '/languages/');
}
add_action('plugins_loaded', 'bulk_change_media_author_load_textdomain');
