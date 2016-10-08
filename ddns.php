<?php
//---config---
$token = '';  //dnspod token
$id = '';  //dnspod id
$host = 'www';  //host name
$domain = 'google.com';  //domain name

//@hxdyxd
$public_config = 'login_token='.$id.','.$token.'&format=json&land=cn';

/*
PHP POST
*/
function http_post($remote_server, $post_string){
	$len = strlen($post_string);
	$opts = array(
		'http'=>array(
			'method'=>"POST",
			'header'=>"Content-type: application/x-www-form-urlencoded\r\n"
					. "Content-length: $len\r\n"
					. "User-Agent: ddns.php/1.0(hxdyxd@gmail.com)",
			"content"=> $post_string
			)
		);
	return @file_get_contents($remote_server, false, stream_context_create($opts));
}

function get_public_ip(){
	$public_ip = @file_get_contents('http://www.3322.org/dyndns/getip');
	$public_ip = str_replace("\n", '', $public_ip);
	if($public_ip){
		return $public_ip;
	}else{
		return '0.0.0.0';
	}
}

function set_public_ip($ip){
	global $public_config,$record_id;
	$set = json_decode(http_post('https://dnsapi.cn/Record.Modify', $public_config.'&domain=sococo.ml&record_id='.$record_id.'&sub_domain=gxnu&record_type=A&record_line=默认&value='.$ip), true);
	if($set['status']['code']=='1'){
		return true;
	}else{
		echo $set['status']['code']." set ERROR\n";
		return false;
	}
}

$record_id = json_decode(http_post('https://dnsapi.cn/Record.List', $public_config.'&domain=sococo.ml&sub_domain=gxnu'), true);
if($record_id){
	if($record_id['status']['code']=='1'){
		$least_ip = $record_id['records'][0]['value'];
		$record_id = $record_id['records'][0]['id'];
	}
}

while(1){
	$ip = get_public_ip();
	if($ip != $least_ip && $ip != '0.0.0.0'){
		if(set_public_ip($ip)){
			$least_ip = $ip;
			echo "SET NEW IP $ip\n";
		}
	}else{
		echo "KEEP IP $ip\n";
	}
	sleep(30);
}
