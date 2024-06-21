<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * To perform a task, override the task method
 *
 * Class EXMAGE_Background_Process
 */
abstract class EXMAGE_Background_Process extends WP_Background_Process {

	/**
	 * @return bool
	 */
	public function is_downloading() {
		return $this->is_process_running();
	}

	/**
	 * Complete
	 *
	 * Override if applicable, but ensure that the below actions are
	 * performed, or, call parent::complete().
	 */
	protected function complete() {
		if ( ! $this->is_process_running() && $this->is_queue_empty() ) {
			set_transient( $this->action . '_complete', time() );
		}
		// Show notice to user or perform some other arbitrary task...
		parent::complete();
	}

	/**
	 * Delete all batches.
	 *
	 * @return EXMAGE_Background_Process
	 */
	public function delete_all_batches() {
		global $wpdb;

		$table  = $wpdb->options;
		$column = 'option_name';

		if ( is_multisite() ) {
			$table  = $wpdb->sitemeta;
			$column = 'meta_key';
		}

		$key = $wpdb->esc_like( $this->identifier . '_batch_' ) . '%';

		$wpdb->query( $wpdb->prepare( "DELETE FROM {$table} WHERE {$column} LIKE %s", $key ) ); // @codingStandardsIgnoreLine.

		return $this;
	}

	/**
	 * Kill process.
	 *
	 * Stop processing queue items, clear cronjob and delete all batches.
	 */
	public function kill_process() {
		if ( ! $this->is_queue_empty() ) {
			$this->delete_all_batches();
			wp_clear_scheduled_hook( $this->cron_hook_identifier );
			update_site_option( $this->action . '_kill_process', true );
		}
	}

	/**
	 * Is queue empty
	 *
	 * @return bool
	 */
	public function is_queue_empty() {
		return parent::is_queue_empty();
	}

	/**
	 * Return all batches
	 *
	 * @return array|object|null
	 */
	public function get_all_batches() {
		global $wpdb;

		$table  = $wpdb->options;
		$column = 'option_name';

		if ( is_multisite() ) {
			$table  = $wpdb->sitemeta;
			$column = 'meta_key';
		}

		$key = $wpdb->esc_like( $this->identifier . '_batch_' ) . '%';

		return $wpdb->get_results( $wpdb->prepare( "SELECT * FROM {$table} WHERE {$column} LIKE %s", $key ), ARRAY_A ); // @codingStandardsIgnoreLine.;
	}

	/**
	 * Return number of items which are currently in the queue
	 *
	 * @return int|void
	 */
	public function get_items_left() {
		$batches    = $this->get_all_batches();
		$items_left = 0;
		if ( $batches ) {
			foreach ( $batches as $batch ) {
				if ( ! empty( $batch['option_value'] ) ) {
					$items_left += count( maybe_unserialize( $batch['option_value'] ) );
				}
			}
		}

		return $items_left;
	}

	/**
	 * @return mixed|string
	 */
	public function get_identifier() {
		return $this->identifier;
	}

	/**
	 * Considered as late if cron is triggered more than 300s late
	 *
	 * @return bool
	 */
	public function is_cron_late() {
		$is_late = false;
		$cron    = $this->get_identifier() . '_cron';
		$next    = wp_next_scheduled( $cron );
		if ( $next ) {
			$late = $next - time();
			if ( $late < - 300 ) {
				$is_late = true;
			}
		}

		return $is_late;
	}

	protected function handle() {
		if ( get_site_option( $this->action . '_kill_process' ) ) {
			delete_site_option( $this->action . '_kill_process' );
			$this->delete_all_batches();
			$this->complete();
			wp_die();
		}

		$this->lock_process();

		do {
			$batch = $this->get_batch();

			foreach ( $batch->data as $key => $value ) {
				$task = $this->task( $value );

				if ( false !== $task ) {
					$batch->data[ $key ] = $task;
				} else {
					unset( $batch->data[ $key ] );
				}

				if ( $this->time_exceeded() || $this->memory_exceeded() ) {
					// Batch limits reached.
					break;
				}
			}

			// Update or delete current batch.
			if ( ! empty( $batch->data ) ) {
				$this->update( $batch->key, $batch->data );
			} else {
				$this->delete( $batch->key );
			}
		} while ( ! $this->time_exceeded() && ! $this->memory_exceeded() && ! $this->is_queue_empty() );

		$this->unlock_process();

		// Start next batch or complete process.
		if ( ! $this->is_queue_empty() ) {
			$this->dispatch();
		} else {
			$this->complete();
		}

		wp_die();
	}

}