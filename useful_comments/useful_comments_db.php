<?php


/*
for uc~
http://www.ll19.com/
*/

require_once ('../../../wp-config.php');

$wp_uc_action = (string) $_POST["ucaction"];

if ($wp_uc_action == "insert") {
	wp_insert_uc($table_prefix);
}
if ($wp_uc_action == "delete") {
	wp_delete_uc($table_prefix);
}
if ($wp_uc_action == "select") {
	wp_uc_list($table_prefix);
}
if ($wp_uc_action == "delete_uc") {
	wp_uc_delete($table_prefix);
}

/*
select table useful_comments
*/
function wp_uc_list($table_prefix) {
	global $wpdb;
	$uc_postID = (string) $_POST["uc_postID"];
	$sql = "SELECT " . $table_prefix . "comments.* FROM " . $table_prefix . "comments LEFT OUTER JOIN " . $table_prefix . "posts ON (" . $table_prefix . "comments.comment_post_ID = " . $table_prefix . "posts.ID) LEFT OUTER JOIN " . $table_prefix . "useful_comments ON (" . $table_prefix . "comments.comment_post_ID = " . $table_prefix . "useful_comments.postid) WHERE comment_approved = '1' AND comment_type = '' AND post_password = '' AND " . $table_prefix . "useful_comments.postid = '" . $uc_postID . "' AND " . $table_prefix . "useful_comments.commentid in (" . $table_prefix . "comments.comment_ID) ORDER BY comment_date_gmt ";
	echo "<?xml version=\"1.0\" encoding=\"UTF-8\"?>";
	echo "<root>";
	$content = $wpdb->get_results($sql);
	global $comment;
	foreach ($content as $comment)
		: echo "<comment>";
	echo "<comment_author>";
	echo $comment->comment_author;
	echo "</comment_author>";
	echo "<comment_author_url>";
	echo $comment->comment_author_url;
	echo "</comment_author_url>";
	echo "<comment_ID>";
	echo $comment->comment_ID;
	echo "</comment_ID>";
	echo "<comment_post_ID>";
	echo $comment->comment_post_ID;
	echo "</comment_post_ID>";
	echo "<comment_excerpt>";
	//echo trim(dhtmlspecialchars($comment->comment_content));
	//echo $comment->comment_content;
	$apply_filters_comment_text = apply_filters('comment_text', $comment->comment_content);
	if (empty ($apply_filters_comment_text))
		$apply_filters_comment_text = $comment->comment_content;
	echo dhtmlspecialchars($apply_filters_comment_text);
	echo "</comment_excerpt>";
	echo "<comment_date>";
	echo $comment->comment_date;
	echo "</comment_date>";
	echo "</comment>";
	endforeach;
	echo "</root>";
}
function dhtmlspecialchars($string) {
	if (is_array($string)) {
		foreach ($string as $key => $val) {
			$string[$key] = dhtmlspecialchars($val);
		}
	} else {
		$string = preg_replace('/&amp;((#(\d{3,5}|x[a-fA-F0-9]{4})|[a-zA-Z][a-z0-9]{2,5});)/', '&\\1', str_replace(array (
			'&',
			'"',
			'<',
			'>'
		), array (
		//不是标准的替代形式 仅仅为提供FLASH中转换
			'&amp_19;',
			'&quot_19;',
			'&lt_19;',
			'&gt_19;'
		), $string));
	}
	return $string;
}

/*
insert table useful_comments
*/
function wp_insert_uc($table_prefix) {
	$wp_uc_cid = (string) $_POST["c"];
	$wp_uc_pid = (string) $_POST["p"];
	global $wpdb, $user_ID;
	if (empty ($wp_uc_cid) || empty ($wp_uc_pid)) {
		echo ("Error! comment_ID or comment_post_ID is null!");
		exit;
	}
	// If the user is logged in
	if ($user_ID) {
		$query = "INSERT INTO " . $table_prefix . "useful_comments (commentid, postid) VALUES (" . $wp_uc_cid . ", " . $wp_uc_pid . ")";
		$wp_insert_uc_callback = $wpdb->query($query);
		if (empty ($wp_insert_uc_callback)) {
			echo "InsertError";
		} else {
			echo "InsertDone";
		}
	} else {
		echo ('Sorry, you must be logged in to set a useful comment.');
	}
}

/*
delete form useful_comments
*/
function wp_delete_uc($table_prefix) {

	$wp_uc_cid = (string) $_POST["c"];
	global $wpdb, $user_ID;
	if (empty ($wp_uc_cid)) {
		echo ("Error! comment_ID is null!");
		exit;
	}
	// If the user is logged in
	if ($user_ID) {
		$query = "DELETE FROM " . $table_prefix . "useful_comments WHERE commentid = '" . $wp_uc_cid . "'";
		$wp_delete_uc_callback = $wpdb->query($query);
		if (empty ($wp_delete_uc_callback)) {
			echo "DeleteError";
		} else {
			echo "DeleteDone";
		}
	} else {
		echo ('Sorry, you must be logged in to set a useful comment.');
	}
}

function wp_uc_delete($table_prefix) {
	global $wpdb, $user_ID;

	if ($user_ID) {
		$uc_delete_sql = "DELETE FROM `" . $table_prefix . "useful_comments`  WHERE `" . $table_prefix . "useful_comments`.`commentid` not in (SELECT `comment_ID` FROM `" . $table_prefix . "comments`)";
		$uc_delete_back = $wpdb->query($uc_delete_sql);
		echo ("DELETE ROWS: <h2>" . $uc_delete_back . "</h2>DELETE COMPLETE.");
	} else {
		echo ('Sorry, you must be logged in.');
	}
}
?>
