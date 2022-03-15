<?php

function getSslPage($url, $method = 'get', $data = []) {
	$ch = curl_init();
	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
	curl_setopt($ch, CURLOPT_HEADER, false);
	curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
	curl_setopt($ch, CURLOPT_URL, $url);
	curl_setopt($ch, CURLOPT_REFERER, $url);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);

	if(count($data)){
		curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));
	}
	if($method == 'post'){
		curl_setopt($ch, CURLOPT_POST, true);
	}

	$result = curl_exec($ch);
	curl_close($ch);
	return $result;
}

function dd($v){
	die(var_dump($v));
}