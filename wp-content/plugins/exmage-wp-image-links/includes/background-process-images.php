<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * To perform a task, override the task method
 *
 * Class EXMAGE_Background_Process_Images
 */
class EXMAGE_Background_Process_Images extends EXMAGE_Background_Process {
	protected $action = 'exmage_background_process_image';

	/**
	 * Task
	 *
	 * Override this method to perform any actions required on each
	 * queue item. Return the modified item for further processing
	 * in the next pass through. Or, return false to remove the
	 * item from the queue.
	 *
	 * @param mixed $item Queue item to iterate over
	 *
	 * @return mixed
	 */
	protected function task( $item ) {
		$url     = isset( $item['url'] ) ? $item['url'] : '';
		$post_id = isset( $item['post_id'] ) ? $item['post_id'] : '';
		try {
			if ( $url ) {
				$result = EXMAGE_WP_IMAGE_LINKS::add_image( $url, $image_id, '', $post_id );
				if ( apply_filters( 'exmage_background_process_log_result', true ) ) {
					error_log( 'EXMAGE background process log: ' . var_export( $result, true ) );
				}
			}
		} catch ( Error $e ) {
			error_log( 'EXMAGE background process error: ' . $e->getMessage() );

			return false;
		} catch ( Exception $e ) {
			error_log( 'EXMAGE background process error: ' . $e->getMessage() );

			return false;
		}

		return false;
	}
}