<?php

define('TM_URL', 'https://api.telegram.org/');
define("API_KEY", "5282880118:AAEPEzwjYmo03oa8zciSOS8PNgk4UCtZrk8");

function get_updates($offset = 0){
	$url = TM_URL . API_KEY . '/getUpdates';
	$resp = getSslPage($url, 'get', ['offset' => $offset]);
	return json_decode($resp, true);
}

function get_message_types($message){
	if(isset($message['reply_to_message'])){
		return 'reply_to_message';
	}

	if(isset($message['text'])){
		return 'text';
	}

	if(isset($message['photo'])){
		return 'photo';
	}

	if(isset($message['voice'])){
		return 'voice';
	}

	if(isset($message['document'])){
		return 'document';
	}

	if(isset($message['video_note'])){
		return 'video_note';
	}

	if(isset($message['sticker'])){
		return 'sticker';
	}

	return NULL;
}

function get_text_from_mess($mess){
	$text = '';

	if($mess['type'] == 'text'){
		$text = $mess['content']['text'];
	}elseif($mess['type'] == 'photo' and isset($mess['content']['caption'])){
		$text = $mess['content']['caption'];
	}

	return $text;
}

function send($chat_id, $message){
	$data = [
		'chat_id' => $chat_id,
		'text' => $message
	];

	$url = TM_URL . API_KEY . '/sendMessage';
	$resp = getSslPage($url, 'post', $data);

	return json_decode($resp, true);
}

function get_messages($update){
	$messages = [];
	$result = $update['result'];
	foreach ($result as $i => $message) {
		$from = $message['message']['from'];
		$id = $message['update_id'];
		$message = $message['message'];
		$type = get_message_types($message);
		if(!$type)
			continue;

		$mr = [
			'id' => $id,
			'timestamp' => $message['date'],
			'from_who' => [
				'chat_id' => $message['chat']['id'],
				'first_name' => (isset($from['first_name']) and $from['first_name']) ? $from['first_name'] : '',
				'last_name' => (isset($from['last_name']) and $from['last_name']) ? $from['last_name'] : '',
				'username' => (isset($from['username']) and $from['username']) ? $from['username'] : ''
			],
			'type' => $type
		];

		switch($mr['type']){
			case 'text': $mr['content'] = $message['text']; break;
			case 'photo': 
				$mr['content'] = $message['photo'][count($message['photo']) - 1];
				$mr['content']['caption'] = $message['caption'];
			break;
			case 'voice': $mr['content'] = $message['voice']; break;
			case 'document': $mr['content'] = $message['document']; break;
			case 'video_note': $mr['content'] = $message['video_note']; break;
			case 'sticker': 
				$mr['content'] = [
					'emoji' => $message['sticker']['emoji'],
					'file_id' => $message['sticker']['file_id']
				];
			break;
			case 'reply_to_message':
				$message['reply_to_message']['type'] = get_message_types($message['reply_to_message']);
				$mr['content'] = [
					'text' => $message['text'],
					'reply_to_message' => $message['reply_to_message']
				];
			break; 
		}

		$messages[] = $mr;
	}

	return $messages;
}

function get_recipients(){
	return json_decode(file_get_contents(__DIR__ . '/recipients.json'), true);
}

function save_recipients_list($recipients){
	return file_put_contents(__DIR__ . '/recipients.json', json_encode($recipients, JSON_PRETTY_PRINT));
}

function subscribe($chat_id){
	$recipients = get_recipients();
	if(array_search($chat_id, $recipients) !== false){
		return false;
	}
	$recipients[] = $chat_id;
	save_recipients_list($recipients);
	return true;
}

function unsubscribe($chat_id){
	$recipients = get_recipients();
	$inx = array_search($chat_id, $recipients);
	if($inx === false){
		return false;
	}
	unset($recipients[$inx]);
	save_recipients_list($recipients);
	return true;
}