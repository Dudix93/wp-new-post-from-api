<?php
/*
Plugin Name: post-from-api
Description: Creates posts from API calls.
Version:     1.0
Author:      Michal D
*/
function addPost($title, $content) {
	global $user_ID;
	$new_post = array(
	'post_title' => $title,
	'post_content' => $content,
	'post_status' => 'publish',
	'post_date' => date('Y-m-d H:i:s'),
	'post_author' => $user_ID,
	'post_type' => 'post',
	'post_category' => array(0)
	);
	$post_id = wp_insert_post($new_post);
}

$url = 'http://127.0.0.1:8000/api/posts';
$curl = curl_init($url);
curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
$data = curl_exec($curl);
curl_close($curl);
$posts = json_decode($data, true);

foreach($posts as $post) {
	echo $post['title'].PHP_EOL;
	addPost($post['title'],
		$post['content']);
}