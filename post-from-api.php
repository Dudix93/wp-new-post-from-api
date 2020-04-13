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
	$post_title   = wp_unslash( sanitize_post_field( 'post_title', $title, 0, 'db' ) );
    $post_content = wp_unslash( sanitize_post_field( 'post_content', $content, 0, 'db' ) );
 
    $query = "SELECT ID FROM $wpdb->posts WHERE 1=1";
    $args  = array();
 
    if ( ! empty( $title ) ) {
        $query .= ' AND post_title = %s';
        $args[] = $post_title;
    }
 
    if ( ! empty( $content ) ) {
        $query .= ' AND post_content = %s';
        $args[] = $post_content;
    }
 
    return 0;
}

function addPostsFromAPI() {
	global $user_ID;
	global $wpdb;
	$url = 'http://127.0.0.1:8000/api/posts';
	$curl = curl_init($url);
	curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
	$data = curl_exec($curl);
	curl_close($curl);
	$posts = json_decode($data, true);

	foreach($posts as $post) {
		if (!ifPostExists($post['title'], $post['content'])) {
			addPost($post['title'],	$post['content']);
		}
	}
}
add_action('init', 'addPostsFromAPI');
