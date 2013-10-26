<?php
include('phpquery.php');

$QSC_BBS_NOTICE_URL	= 'http://www.qsc.zju.edu.cn/apps/editor_bbs/notice.php';
$EMAIL_ADDRESS		= 'mail@clarkok.com';
$EMAIL_SUBJECT		= '【QSC后台】您有新的消息';
$EMAIL_HEADER		= '您有以下新提醒：\n';
$EMAIL_TEMPLETE		= '';
$EMAIL_FOOTER		= '';

// Get cookies from anyway you like
// Return cookies as a string
function get_cookies(){
	$f	= fopen('cookie.txt', 'r');
	$res	= fgets($f);
	fclose($f);
	return	$res;
}

// Set and execult curl
// Return response without header as a string
function get_remote_data($url){
	$curl	= curl_init();
	curl_setopt($curl, CURLOPT_URL, $url);
	curl_setopt($curl, CURLOPT_HEADER, 1);
	curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
	curl_setopt($curl, CURLOPT_MAXREDIRS, 10020);
	curl_setopt($curl, CURLOPT_FOLLOWLOCATION, true);
	curl_setopt($curl, CURLOPT_COOKIE, get_cookies());
	curl_setopt($curl, CURLOPT_HTTPHEADER, array('User-Agent: Mozilla/5.0 (Macintosh; Intel Mac OS X 10_8_5) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/30.0.1599.69 Safari/537.36'));
	$res	= curl_exec($curl);
	echo	curl_getinfo($curl, CURLINFO_HTTP_CODE);
	curl_close($curl);

	return	$res;
}

// Return a DOMObject of QSC bbs notice
function get_dom_object(){
	global	$QSC_BBS_NOTICE_URL;
	$res	= get_remote_data($QSC_BBS_NOTICE_URL);
//	echo	$res;
	return	phpQuery::newDocument($res);
}

// Send email
function send_email($msg){
	global	$EMAIL_ADDRESS,	$EMAIL_SUBJECT;
	mail($EMAIL_ADDRESS, $EMAIL_SUBJECT, $msg);
}

// Main
$need_re	= false;
$email_message	= '';

$d	= get_dom_object();
foreach($d['.feed']->children('li.s_clear') as $re){
	if (pq($re)->find('img[alt=NEW]')){
		$need_re	= true;
		$email_message	.= pq($re)->html();
	}
	echo	pq($re)->html();
}

if ($need_re){
	send_email($email_message);
}


?>

