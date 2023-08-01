<?php

namespace Voxel\Dynamic_Tags;

if ( ! defined('ABSPATH') ) {
	exit;
}

class Message_Group extends Base_Group {

	public $key = 'message';
	public $label = 'Message';

	public $message;

	protected function properties(): array {
		return [
			'sender' => [
				'label' => 'Sender',
				'type' => \Voxel\T_OBJECT,
				'properties' => [
					'name' => [
						'label' => 'Name',
						'type' => \Voxel\T_STRING,
						'callback' => function() {
							return $this->message->get_sender_name();
						},
					],
					'link' => [
						'label' => 'Link',
						'type' => \Voxel\T_STRING,
						'callback' => function() {
							return $this->message->get_sender_link();
						},
					],
					'avatar' => [
						'label' => 'Avatar',
						'type' => \Voxel\T_NUMBER,
						'callback' => function() {
							return $this->message->get_sender()->get_avatar_id();
						},
					],
					'chat_link' => [
						'label' => 'Chat link',
						'type' => \Voxel\T_URL,
						'callback' => function() {
							return add_query_arg( 'chat', join( '', [
								$this->message->get_sender_type() === 'post' ? $this->message->get_sender_id() : '',
								$this->message->get_receiver_type() === 'post' ? 'p' : 'u',
								$this->message->get_receiver_id()
							] ), get_permalink( \Voxel\get('templates.inbox') ) ?: home_url('/') );
						},
					],
				],
			],
			'receiver' => [
				'label' => 'Receiver',
				'type' => \Voxel\T_OBJECT,
				'properties' => [
					'name' => [
						'label' => 'Name',
						'type' => \Voxel\T_STRING,
						'callback' => function() {
							return $this->message->get_receiver_name();
						},
					],
					'link' => [
						'label' => 'Link',
						'type' => \Voxel\T_STRING,
						'callback' => function() {
							return $this->message->get_receiver_link();
						},
					],
					'avatar' => [
						'label' => 'Avatar',
						'type' => \Voxel\T_NUMBER,
						'callback' => function() {
							return $this->message->get_receiver()->get_avatar_id();
						},
					],
					'chat_link' => [
						'label' => 'Chat link',
						'type' => \Voxel\T_URL,
						'callback' => function() {
							return add_query_arg( 'chat', join( '', [
								$this->message->get_receiver_type() === 'post' ? $this->message->get_receiver_id() : '',
								$this->message->get_sender_type() === 'post' ? 'p' : 'u',
								$this->message->get_sender_id()
							] ), get_permalink( \Voxel\get('templates.inbox') ) ?: home_url('/') );
						},
					],
				],
			],
			'content' => [
				'label' => 'Content',
				'type' => \Voxel\T_STRING,
				'callback' => function() {
					return $this->message->get_content_for_display();
				},
			],
		];
	}
}
