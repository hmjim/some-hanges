<?php

namespace Voxel\Controllers;

if ( ! defined('ABSPATH') ) {
	exit;
}

class Db_Controller extends Base_Controller {

	protected function hooks() {
		$this->on( 'after_setup_theme', '@prepare_db', 0 );
		$this->on( 'after_setup_theme', '@create_recurring_dates_table', 0 );
		$this->on( 'after_setup_theme', '@create_orders_table', 0 );
		$this->on( 'after_setup_theme', '@create_timeline_table', 0 );
		$this->on( 'after_setup_theme', '@create_followers_table', 0 );
		$this->on( 'after_setup_theme', '@create_work_hours_table', 0 );
		$this->on( 'after_setup_theme', '@create_post_relations_table', 0 );
		$this->on( 'after_setup_theme', '@create_notifications_table', 0 );
		$this->on( 'after_setup_theme', '@create_messages_table', 0 );
		$this->on( 'after_setup_theme', '@modify_terms_table', 0 );
		$this->on( 'after_setup_theme', '@modify_posts_table', 0 );
		$this->on( 'after_setup_theme', '@modify_users_table', 0 );
	}

	protected function prepare_db() {
		$db_version = '0.15';
		$current_version = \Voxel\get( 'versions.db' );
		if ( $db_version === $current_version ) {
			return;
		}

		global $wpdb;

		// wp_posts must use InnoDB
		$wp_posts = $wpdb->get_row( $wpdb->prepare( "SHOW TABLE STATUS WHERE name = %s", $wpdb->posts ) );
		$wp_posts_engine = $wp_posts->Engine ?? null;
		if ( $wp_posts_engine !== 'InnoDB' ) {
			$wpdb->query( "ALTER TABLE {$wpdb->posts} ENGINE = InnoDB;" );
		}

		// wp_users must use InnoDB
		$wp_users = $wpdb->get_row( $wpdb->prepare( "SHOW TABLE STATUS WHERE name = %s", $wpdb->users ) );
		$wp_users_engine = $wp_users->Engine ?? null;
		if ( $wp_users_engine !== 'InnoDB' ) {
			$wpdb->query( "ALTER TABLE {$wpdb->users} ENGINE = InnoDB;" );
		}

		\Voxel\set( 'versions.db', $db_version );
	}

	protected function create_recurring_dates_table() {
		$table_version = '0.2';
		$current_version = \Voxel\get( 'versions.recurring_dates_table' );
		if ( $table_version === $current_version ) {
			return;
		}

		global $wpdb;
		require_once ABSPATH . 'wp-admin/includes/upgrade.php';

		// create events table
		$table_name = $wpdb->prefix . 'voxel_recurring_dates';
		$sql = <<<SQL
			CREATE TABLE IF NOT EXISTS $table_name (
				`id` BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
				`post_id` BIGINT(20) UNSIGNED NOT NULL,
				`post_type` VARCHAR(64) NOT NULL,
				`field_key` VARCHAR(64) NOT NULL,
				`start` DATETIME NOT NULL,
				`end` DATETIME NOT NULL,
				`frequency` SMALLINT UNSIGNED,
				`unit` ENUM('day','month'),
				`until` DATETIME,
				PRIMARY KEY (`id`),
					KEY (`post_id`),
					KEY (`post_type`),
					KEY (`field_key`),
					KEY (`start`),
					KEY (`end`),
					KEY (`frequency`),
					KEY (`unit`),
					KEY (`until`),
				FOREIGN KEY (`post_id`)
					REFERENCES {$wpdb->posts}(ID) ON DELETE CASCADE
			) ENGINE = InnoDB {$wpdb->get_charset_collate()};
		SQL;
		dbDelta( $sql );

		// migrate data from old events table
		if ( \Voxel\get('versions.events_table') === '0.14' && ( !! $wpdb->get_var( "SHOW TABLES LIKE '{$wpdb->prefix}voxel_events'" ) ) ) {
			add_action( 'init', function() {
				global $wpdb;
				$results = $wpdb->get_results( <<<SQL
					SELECT post_id, field_key, CONCAT( '[', GROUP_CONCAT( details SEPARATOR ',' ), ']' ) AS details
					FROM `{$wpdb->prefix}voxel_events`
					GROUP BY post_id, field_key
				SQL );

				foreach ( $results as $result ) {
					$value = json_decode( $result->details, ARRAY_A );
					if ( json_last_error() !== JSON_ERROR_NONE ) {
						continue;
					}

					$post = \Voxel\Post::get( $result->post_id );
					$field = $post ? $post->get_field( $result->field_key ) : null;
					if ( $field && $field->get_type() === 'recurring-date' ) {
						$field->update( $value );
					}
				}

				// $wpdb->query( "DROP TABLE IF EXISTS `{$wpdb->prefix}voxel_events`" );
				\Voxel\get('versions.events_table', null);
			} );
		}

		\Voxel\set( 'versions.recurring_dates_table', $table_version );
	}

	protected function create_orders_table() {
		$table_version = '0.28';
		$current_version = \Voxel\get( 'versions.orders_table' );
		if ( $table_version === $current_version ) {
			return;
		}

		global $wpdb;
		require_once ABSPATH . 'wp-admin/includes/upgrade.php';

		// create orders table
		$table_name = $wpdb->prefix . 'voxel_orders';
		$sql = <<<SQL
			CREATE TABLE IF NOT EXISTS $table_name (
				id BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
				post_id BIGINT(20) UNSIGNED NOT NULL,
				product_type VARCHAR(64) NOT NULL,
				product_key VARCHAR(64) NOT NULL,
				customer_id BIGINT(20) UNSIGNED NOT NULL,
				vendor_id BIGINT(20) UNSIGNED NOT NULL,
				details MEDIUMTEXT NOT NULL,
				status VARCHAR(32) NOT NULL,
				session_id VARCHAR(128) NOT NULL,
				mode ENUM("payment", "subscription") NOT NULL,
				object_id VARCHAR(128),
				object_details MEDIUMTEXT,
				testmode BOOLEAN NOT NULL DEFAULT false,
				catalog_mode BOOLEAN NOT NULL DEFAULT false,
				created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
				checkin DATE GENERATED ALWAYS AS (
					CASE WHEN ( JSON_VALID( details ) AND JSON_VALID( JSON_EXTRACT( details, "$.booking.checkin" ) ) )
						THEN DATE( JSON_UNQUOTE( JSON_EXTRACT( details, "$.booking.checkin" ) ) )
						ELSE NULL END
				) VIRTUAL,
				checkout DATE GENERATED ALWAYS AS (
					CASE WHEN ( JSON_VALID( details ) AND JSON_VALID( JSON_EXTRACT( details, "$.booking.checkout" ) ) )
						THEN DATE( JSON_UNQUOTE( JSON_EXTRACT( details, "$.booking.checkout" ) ) )
						ELSE NULL END
				) VIRTUAL,
				timeslot VARCHAR(32) GENERATED ALWAYS AS (
					CASE WHEN ( JSON_VALID( details ) AND JSON_VALID( JSON_EXTRACT( details, "$.booking.timeslot" ) ) )
						THEN CONCAT_WS(
							'-',
							JSON_UNQUOTE( JSON_EXTRACT( details, "$.booking.timeslot.from" ) ),
							JSON_UNQUOTE( JSON_EXTRACT( details, "$.booking.timeslot.to" ) )
						)
						ELSE NULL END
				) VIRTUAL,
				PRIMARY KEY (id),
					KEY (post_id),
					KEY (product_type),
					KEY (product_key),
					KEY (customer_id),
					KEY (vendor_id),
					KEY (status),
					KEY (session_id),
					KEY (mode),
					KEY (object_id),
					KEY (testmode),
					KEY (catalog_mode),
					KEY (checkin),
					KEY (checkout),
					KEY (timeslot),
					KEY (created_at)
			) ENGINE = InnoDB {$wpdb->get_charset_collate()};
		SQL;
		dbDelta( $sql );

		// create order notes table
		$table_name = $wpdb->prefix . 'voxel_order_notes';
		$sql = <<<SQL
			CREATE TABLE IF NOT EXISTS $table_name (
				id BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
				order_id BIGINT(20) UNSIGNED NOT NULL,
				type VARCHAR(96) NOT NULL,
				details TEXT,
				created_at DATETIME NOT NULL,
				PRIMARY KEY (id),
					KEY (order_id),
					KEY (type),
					KEY (created_at),
				FOREIGN KEY (order_id)
					REFERENCES {$wpdb->prefix}voxel_orders(id) ON DELETE CASCADE
			) ENGINE = InnoDB {$wpdb->get_charset_collate()};
		SQL;
		dbDelta( $sql );

		// mariadb bugfix with generated columns in table version 0.26
		if ( $current_version === '0.26' ) {
			$wpdb->query( "ALTER TABLE {$wpdb->prefix}voxel_orders DROP `checkin`" );
			$wpdb->query( "ALTER TABLE {$wpdb->prefix}voxel_orders DROP `checkout`" );

			$wpdb->query( <<<SQL
				ALTER TABLE {$wpdb->prefix}voxel_orders ADD COLUMN `checkin` DATE GENERATED ALWAYS AS (
					CASE WHEN ( JSON_VALID( details ) AND JSON_VALID( JSON_EXTRACT( details, "$.booking.checkin" ) ) )
						THEN DATE( JSON_UNQUOTE( JSON_EXTRACT( details, "$.booking.checkin" ) ) )
						ELSE NULL END
				) VIRTUAL
			SQL );

			$wpdb->query( <<<SQL
				ALTER TABLE {$wpdb->prefix}voxel_orders ADD COLUMN `checkout` DATE GENERATED ALWAYS AS (
					CASE WHEN ( JSON_VALID( details ) AND JSON_VALID( JSON_EXTRACT( details, "$.booking.checkout" ) ) )
						THEN DATE( JSON_UNQUOTE( JSON_EXTRACT( details, "$.booking.checkout" ) ) )
						ELSE NULL END
				) VIRTUAL
			SQL );

			$wpdb->query( "ALTER TABLE {$wpdb->prefix}voxel_orders ADD INDEX(`checkin`)" );
			$wpdb->query( "ALTER TABLE {$wpdb->prefix}voxel_orders ADD INDEX(`checkout`)" );
		}

		// add vendor_id and catalog_mode columns
		if ( $current_version === '0.27' ) {
			$wpdb->query( "ALTER TABLE {$wpdb->prefix}voxel_orders ADD COLUMN `catalog_mode` BOOLEAN NOT NULL DEFAULT false AFTER `testmode`" );
			$wpdb->query( "ALTER TABLE {$wpdb->prefix}voxel_orders ADD COLUMN `vendor_id` BIGINT(20) UNSIGNED NOT NULL AFTER `customer_id`" );

			$wpdb->query( "ALTER TABLE {$wpdb->prefix}voxel_orders ADD INDEX(`catalog_mode`)" );
			$wpdb->query( "ALTER TABLE {$wpdb->prefix}voxel_orders ADD INDEX(`vendor_id`)" );

			// bulk assign vendor_id to orders created before the vendor_id column was present
			$wpdb->query( <<<SQL
				UPDATE {$wpdb->prefix}voxel_orders AS orders
					LEFT JOIN {$wpdb->posts} AS posts ON (orders.post_id = posts.ID)
					SET orders.vendor_id = posts.post_author
			SQL );
		}

		\Voxel\set( 'versions.orders_table', $table_version );
	}

	protected function create_timeline_table() {
		$table_version = '0.20';
		$current_version = \Voxel\get( 'versions.timeline_table' );
		if ( $table_version === $current_version ) {
			return;
		}

		global $wpdb;
		require_once ABSPATH . 'wp-admin/includes/upgrade.php';

		// create statuses table
		$table_name = $wpdb->prefix . 'voxel_timeline';
		$sql = <<<SQL
			CREATE TABLE IF NOT EXISTS $table_name (
				`id` BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
				`user_id` BIGINT(20) UNSIGNED,
				`published_as` BIGINT(20) UNSIGNED,
				`post_id` BIGINT(20) UNSIGNED,
				`content` TEXT,
				`details` TEXT,
				`review_score` DECIMAL(3,2),
				`created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
				`edited_at` DATETIME,
				PRIMARY KEY (`id`),
					KEY (`user_id`),
					KEY (`post_id`),
					KEY (`published_as`),
					FULLTEXT (`content`),
					KEY (`review_score`),
					KEY (`created_at`),
				FOREIGN KEY (`user_id`) REFERENCES {$wpdb->users}(ID) ON DELETE CASCADE,
				FOREIGN KEY (`published_as`) REFERENCES {$wpdb->posts}(ID) ON DELETE CASCADE,
				FOREIGN KEY (`post_id`) REFERENCES {$wpdb->posts}(ID) ON DELETE CASCADE
			) ENGINE = InnoDB {$wpdb->get_charset_collate()};
		SQL;
		dbDelta( $sql );

		// create status likes table
		$table_name = $wpdb->prefix . 'voxel_timeline_likes';
		$sql = <<<SQL
			CREATE TABLE IF NOT EXISTS $table_name (
				`user_id` BIGINT(20) UNSIGNED NOT NULL,
				`status_id` BIGINT(20) UNSIGNED NOT NULL,
				PRIMARY KEY (`user_id`,`status_id`),
					KEY (`user_id`),
					KEY (`status_id`),
				FOREIGN KEY (`user_id`) REFERENCES {$wpdb->users}(ID) ON DELETE CASCADE,
				FOREIGN KEY (`status_id`) REFERENCES {$wpdb->prefix}voxel_timeline(id) ON DELETE CASCADE
			) ENGINE = InnoDB {$wpdb->get_charset_collate()};
		SQL;
		dbDelta( $sql );

		// create replies table
		$table_name = $wpdb->prefix . 'voxel_timeline_replies';
		$sql = <<<SQL
			CREATE TABLE IF NOT EXISTS $table_name (
				`id` BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
				`user_id` BIGINT(20) UNSIGNED,
				`published_as` BIGINT(20) UNSIGNED,
				`status_id` BIGINT(20) UNSIGNED NOT NULL,
				`parent_id` BIGINT(20) UNSIGNED,
				`content` TEXT,
				`details` TEXT,
				`created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
				`edited_at` DATETIME,
				PRIMARY KEY (`id`),
					KEY (`user_id`),
					KEY (`status_id`),
					KEY (`parent_id`),
					FULLTEXT (`content`),
					KEY (`created_at`),
				FOREIGN KEY (`user_id`) REFERENCES {$wpdb->users}(ID) ON DELETE CASCADE,
				FOREIGN KEY (`published_as`) REFERENCES {$wpdb->posts}(ID) ON DELETE CASCADE,
				FOREIGN KEY (`status_id`) REFERENCES {$wpdb->prefix}voxel_timeline(id) ON DELETE CASCADE,
				FOREIGN KEY (`parent_id`) REFERENCES {$wpdb->prefix}voxel_timeline_replies(id) ON DELETE CASCADE
			) ENGINE = InnoDB {$wpdb->get_charset_collate()};
		SQL;
		dbDelta( $sql );

		// create reply likes table
		$table_name = $wpdb->prefix . 'voxel_timeline_reply_likes';
		$sql = <<<SQL
			CREATE TABLE IF NOT EXISTS $table_name (
				`user_id` BIGINT(20) UNSIGNED NOT NULL,
				`reply_id` BIGINT(20) UNSIGNED NOT NULL,
				PRIMARY KEY (`user_id`,`reply_id`),
					KEY (`user_id`),
					KEY (`reply_id`),
				FOREIGN KEY (`user_id`) REFERENCES {$wpdb->users}(ID) ON DELETE CASCADE,
				FOREIGN KEY (`reply_id`) REFERENCES {$wpdb->prefix}voxel_timeline_replies(id) ON DELETE CASCADE
			) ENGINE = InnoDB {$wpdb->get_charset_collate()};
		SQL;
		dbDelta( $sql );

		\Voxel\set( 'versions.timeline_table', $table_version );
	}

	protected function create_followers_table() {
		$table_version = '0.3';
		$current_version = \Voxel\get( 'versions.followers_table' );
		if ( $table_version === $current_version ) {
			return;
		}

		global $wpdb;
		require_once ABSPATH . 'wp-admin/includes/upgrade.php';

		// create followers table
		$table_name = $wpdb->prefix . 'voxel_followers';
		$sql = <<<SQL
			CREATE TABLE IF NOT EXISTS $table_name (
				`object_type` ENUM('user','post') NOT NULL,
				`object_id` BIGINT(20) UNSIGNED NOT NULL,
				`follower_type` ENUM('user','post') NOT NULL,
				`follower_id` BIGINT(20) UNSIGNED NOT NULL,
				`status` TINYINT NOT NULL,
				PRIMARY KEY (`object_type`, `object_id`, `follower_type`, `follower_id`),
					KEY (`object_type`, `object_id`),
					KEY (`follower_type`, `follower_id`),
					KEY (`status`)
			) ENGINE = InnoDB {$wpdb->get_charset_collate()};
		SQL;
		dbDelta( $sql );

		// migrate data from voxel_followers_post and voxel_followers_user table
		if ( \Voxel\get('versions.followers_table') === '0.1' ) {
			if ( !! $wpdb->get_var( "SHOW TABLES LIKE '{$wpdb->prefix}voxel_followers_user'" ) ) {
				add_action( 'init', function() {
					global $wpdb;
					$wpdb->query( <<<SQL
						INSERT INTO {$wpdb->prefix}voxel_followers (`object_type`, `object_id`, `follower_type`, `follower_id`, `status`)
							SELECT 'user' AS `object_type`, user_id AS `object_id`, 'user' AS `follower_type`, `follower_id`, `status`
							FROM {$wpdb->prefix}voxel_followers_user
					SQL );

					// $wpdb->query( "DROP TABLE IF EXISTS `{$wpdb->prefix}voxel_followers_user`" );
					\Voxel\get('versions.followers_table', null);
				} );
			}

			if ( !! $wpdb->get_var( "SHOW TABLES LIKE '{$wpdb->prefix}voxel_followers_post'" ) ) {
				add_action( 'init', function() {
					global $wpdb;
					$wpdb->query( <<<SQL
						INSERT INTO {$wpdb->prefix}voxel_followers (`object_type`, `object_id`, `follower_type`, `follower_id`, `status`)
							SELECT 'post' AS `object_type`, post_id AS `object_id`, 'user' AS `follower_type`, `follower_id`, `status`
							FROM {$wpdb->prefix}voxel_followers_post
					SQL );

					// $wpdb->query( "DROP TABLE IF EXISTS `{$wpdb->prefix}voxel_followers_post`" );
					\Voxel\get('versions.followers_table', null);
				} );
			}
		}

		\Voxel\set( 'versions.followers_table', $table_version );
	}

	protected function create_work_hours_table() {
		$table_version = '0.1';
		$current_version = \Voxel\get( 'versions.work_hours_table' );
		if ( $table_version === $current_version ) {
			return;
		}

		global $wpdb;
		require_once ABSPATH . 'wp-admin/includes/upgrade.php';

		$table_name = $wpdb->prefix . 'voxel_work_hours';
		$sql = <<<SQL
			CREATE TABLE IF NOT EXISTS $table_name (
				`id` BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
				`post_id` BIGINT(20) UNSIGNED NOT NULL,
				`post_type` VARCHAR(64) NOT NULL,
				`field_key` VARCHAR(64) NOT NULL,
				`start` SMALLINT(5) UNSIGNED NOT NULL,
				`end` SMALLINT(5) UNSIGNED NOT NULL,
				PRIMARY KEY (`id`),
					KEY (`post_id`),
					KEY (`post_type`),
					KEY (`field_key`),
					KEY (`start`),
					KEY (`end`),
				FOREIGN KEY (`post_id`) REFERENCES {$wpdb->posts}(ID) ON DELETE CASCADE
			) ENGINE = InnoDB {$wpdb->get_charset_collate()};
		SQL;
		dbDelta( $sql );

		\Voxel\set( 'versions.work_hours_table', $table_version );
	}

	protected function create_post_relations_table() {
		$table_version = '0.4';
		$current_version = \Voxel\get( 'versions.post_relations_table' );
		if ( $table_version === $current_version ) {
			return;
		}

		global $wpdb;
		require_once ABSPATH . 'wp-admin/includes/upgrade.php';

		$table_name = $wpdb->prefix . 'voxel_relations';
		$sql = <<<SQL
			CREATE TABLE IF NOT EXISTS $table_name (
				`id` BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
				`parent_id` BIGINT(20) UNSIGNED NOT NULL,
				`child_id` BIGINT(20) UNSIGNED NOT NULL,
				`relation_key` varchar(96) NOT NULL,
				`order` INT(10) UNSIGNED NOT NULL,
				PRIMARY KEY (`id`),
				KEY (`parent_id`),
				KEY (`child_id`),
				KEY (`relation_key`),
				FOREIGN KEY (`parent_id`) REFERENCES {$wpdb->posts}(ID) ON DELETE CASCADE,
				FOREIGN KEY (`child_id`) REFERENCES {$wpdb->posts}(ID) ON DELETE CASCADE
			) ENGINE = InnoDB {$wpdb->get_charset_collate()};
		SQL;
		dbDelta( $sql );

		\Voxel\set( 'versions.post_relations_table', $table_version );
	}

	protected function create_notifications_table() {
		$table_version = '0.1';
		$current_version = \Voxel\get( 'versions.notifications_table' );
		if ( $table_version === $current_version ) {
			return;
		}

		global $wpdb;
		require_once ABSPATH . 'wp-admin/includes/upgrade.php';

		// create events table
		$table_name = $wpdb->prefix . 'voxel_notifications';
		$sql = <<<SQL
			CREATE TABLE IF NOT EXISTS $table_name (
				`id` BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
				`user_id` BIGINT(20) UNSIGNED NOT NULL,
				`type` VARCHAR(96) NOT NULL,
				`details` TEXT,
				`seen` TINYINT NOT NULL,
				`created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
				PRIMARY KEY (`id`),
					KEY (`user_id`),
					KEY (`type`),
					KEY (`seen`),
					KEY (`created_at`),
				FOREIGN KEY (`user_id`) REFERENCES {$wpdb->users}(ID) ON DELETE CASCADE
			) ENGINE = InnoDB {$wpdb->get_charset_collate()};
		SQL;
		dbDelta( $sql );

		\Voxel\set( 'versions.notifications_table', $table_version );
	}


	protected function create_messages_table() {
		$table_version = '0.3';
		$current_version = \Voxel\get( 'versions.messages_table' );
		if ( $table_version === $current_version ) {
			return;
		}

		global $wpdb;
		require_once ABSPATH . 'wp-admin/includes/upgrade.php';

		// create chats table
		// @todo: maybe index p1_cleared_below, p2_cleared_below
		$table_name = $wpdb->prefix . 'voxel_chats';
		$sql = <<<SQL
			CREATE TABLE IF NOT EXISTS $table_name (
				`id` BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
				`p1_type` ENUM('user','post') NOT NULL,
				`p1_id` BIGINT(20) UNSIGNED NOT NULL,
				`p1_last_message_id` BIGINT(20) UNSIGNED NOT NULL DEFAULT 0,
				`p1_cleared_below` BIGINT(20) UNSIGNED NOT NULL DEFAULT 0,
				`p2_type` ENUM('user','post') NOT NULL,
				`p2_id` BIGINT(20) UNSIGNED NOT NULL,
				`p2_last_message_id` BIGINT(20) UNSIGNED NOT NULL DEFAULT 0,
				`p2_cleared_below` BIGINT(20) UNSIGNED NOT NULL DEFAULT 0,
				`last_message_id` BIGINT(20) UNSIGNED NOT NULL DEFAULT 0,
				`details` TEXT,
				PRIMARY KEY (`id`),
					KEY (`p1_type`, `p1_id`),
					KEY (`p2_type`, `p2_id`),
					KEY (`p1_last_message_id`),
					KEY (`p2_last_message_id`),
					KEY (`last_message_id`)
			) ENGINE = InnoDB {$wpdb->get_charset_collate()};
		SQL;
		dbDelta( $sql );

		// create messages table
		$table_name = $wpdb->prefix . 'voxel_messages';
		$sql = <<<SQL
			CREATE TABLE IF NOT EXISTS $table_name (
				`id` BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
				`sender_type` ENUM('user','post') NOT NULL,
				`sender_id` BIGINT(20) UNSIGNED NOT NULL,
				`sender_deleted` TINYINT NOT NULL DEFAULT 0,
				`receiver_type` ENUM('user','post') NOT NULL,
				`receiver_id` BIGINT(20) UNSIGNED NOT NULL,
				`receiver_deleted` TINYINT NOT NULL DEFAULT 0,
				`content` TEXT,
				`details` TEXT,
				`seen` TINYINT NOT NULL DEFAULT 0,
				`created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
				PRIMARY KEY (`id`),
					KEY (`sender_type`, `sender_id`),
					KEY (`sender_deleted`),
					KEY (`receiver_type`, `receiver_id`),
					KEY (`receiver_deleted`),
					KEY (`seen`),
					KEY (`created_at`)
			) ENGINE = InnoDB {$wpdb->get_charset_collate()};
		SQL;
		dbDelta( $sql );

		\Voxel\set( 'versions.messages_table', $table_version );
	}

	protected function modify_terms_table() {
		$table_version = '0.2';
		$current_version = \Voxel\get( 'versions.terms_table' );
		if ( $table_version === $current_version ) {
			return;
		}

		global $wpdb;

		$order_col_exists = $wpdb->query( "SHOW COLUMNS FROM {$wpdb->terms} LIKE 'voxel_order'" );
		if ( ! $order_col_exists ) {
			$wpdb->query( "ALTER TABLE {$wpdb->terms} ADD COLUMN `voxel_order` INT NOT NULL DEFAULT 0" );
		}

		$fulltext_exists = $wpdb->query( "SHOW INDEX FROM {$wpdb->terms} WHERE Key_name = 'vx_fulltext'" );
		if ( ! $fulltext_exists ) {
			$wpdb->query( "ALTER TABLE {$wpdb->terms} ADD FULLTEXT vx_fulltext (name)" );
		}

		\Voxel\set( 'versions.terms_table', $table_version );
	}

	protected function modify_posts_table() {
		$table_version = '0.1';
		$current_version = \Voxel\get( 'versions.posts_table' );
		if ( $table_version === $current_version ) {
			return;
		}

		global $wpdb;

		$fulltext_exists = $wpdb->query( "SHOW INDEX FROM {$wpdb->posts} WHERE Key_name = 'vx_post_title'" );
		if ( ! $fulltext_exists ) {
			$wpdb->query( "ALTER TABLE {$wpdb->posts} ADD FULLTEXT vx_post_title (post_title)" );
		}

		\Voxel\set( 'versions.posts_table', $table_version );
	}

	protected function modify_users_table() {
		$table_version = '0.2';
		$current_version = \Voxel\get( 'versions.users_table' );
		if ( $table_version === $current_version ) {
			return;
		}

		global $wpdb;

		$fulltext_exists = $wpdb->query( "SHOW INDEX FROM {$wpdb->users} WHERE Key_name = 'vx_display_name'" );
		if ( ! $fulltext_exists ) {
			$wpdb->query( "ALTER TABLE {$wpdb->users} ADD FULLTEXT vx_display_name (display_name)" );
		}

		\Voxel\set( 'versions.users_table', $table_version );
	}
}
