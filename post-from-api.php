<?php
/*
Plugin Name: post-from-api
Description: Creates posts from API calls.
Version:     1.0
Author:      Michal D
*/
function addPost($title, $content) {
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

function ifPostExists( $title, $content = '') {
    global $wpdb;
	$query = $wpdb->prepare(
        'SELECT ID FROM ' . $wpdb->posts . '
        WHERE post_title = %s
		AND post_content = %s',
        $title,
		$content
    );
    $wpdb->query( $query );
	return $wpdb->num_rows;
}

function addPostsFromAPI() {
	global $user_ID;
	$url = 'http://127.0.0.1:8000/api/posts';
	$curl = curl_init($url);
	curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
	$data = curl_exec($curl);
	curl_close($curl);
	$posts = json_decode($data, true);
	if (isset($posts)) {
		foreach($posts as $post) {
		if (!ifPostExists($post['title'], $post['content'])) {
			addPost($post['title'],	$post['content']);
		}
	}
	}
}
add_action('init', 'addPostsFromAPI');
