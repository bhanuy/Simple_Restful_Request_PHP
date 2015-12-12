<?php
class Restful_Request {

	public function get_session($apikey, $appid, $email, $passwd){
		$params = http_build_query(array(
			'email' => $email,
			'password'=> $passwd,
			'application_id' => $appid,
			'signature' => sha1("$email$passwd$appid$apikey"),
			'token_version'=>2,
			'response_format' => 'json'
			));

		//initialize cURl
		$ch_post =curl_init();
		curl_setopt($ch_post, CURLOPT_URL,"https://www.mediafire.com/api/user/get_session_token.php?$params");
		curl_setopt($ch_post, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch_post, CURLOPT_SSL_VERIFYHOST,false);
		curl_setopt($ch_post, CURLOPT_SSL_VERIFYPEER,false);
		//execute the request
		$output = curl_exec($ch_post);
		curl_close($ch_post);

		if($output){

			$response_array = (json_decode($output, true));
			if(array_key_exists('response', $response_array)){
				return $response_array['response'];
			}
			
		}

		return false;
	}



	public function get_info($session_token = null){

		$md5 = md5(($session_token['secret_key']%256).$session_token['time'].'/api/1.4/user/get_info.php?session_token='.$session_token['session_token'].'&response_format=json');
		
		$params = http_build_query(array(
			'session_token' => $session_token['session_token'],
			'response_format' => 'json',   
			'signature'=>$md5,
			));

		$ch_get = curl_init();
		curl_setopt($ch_get, CURLOPT_URL,"http://www.mediafire.com/api/1.4/user/get_info.php?$params");
		curl_setopt($ch_get, CURLOPT_RETURNTRANSFER , true);
		curl_setopt($ch_get, CURLOPT_SSL_VERIFYHOST,false);
		curl_setopt($ch_get, CURLOPT_SSL_VERIFYPEER,false);
		$output = curl_exec($ch_get);			
		curl_close($ch_get);

		return $output;

	}
}


$api_call = new Restful_Request();

$session_token = $api_call->get_session(API_key, APP_id,email, password);
echo $api_call->get_info($session_token);


?>