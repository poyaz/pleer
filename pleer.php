<?php
class pleer {
	private $_username = '';
	private $_password = '';
	private $_tokenUrl = 'http://api.pleer.com/token.php';
	private $_apiUrl = 'http://api.pleer.com/index.php';
	private $_accessToken = '';
	private $_accessTime = 0;

	function __construct($username, $password) {
		$this->_username = $username;
		$this->_password = $password;
	}

	public function run() {
		$this->connectAccessToken(["grant_type" => "client_credentials"]);
	}

	/**
	* Search the list of music and song
	* @param $query(String)
	* @param $page(Integer)
	* @param $count(Integer)
	* @return Array of counter and tracker of music and song
	*/
	public function track_search($query, $page, $count = 100) {
		$result = $this->sendRequest('tracks_search', ['query' => $query, 'page' => $page, 'result_on_page' => $count]);

		if($result['success']) {
			print_r($result);
		} else {
			return 'Error: ' . $result['message'];
		}
	}

	/**
	* Get the information of music and song by track id
	* @param $track_id(String)
	* @return Array of tracker and artister and size, ... of music and song
	*/
	public function tracks_get_info($track_id) {
		$result = $this->sendRequest('tracks_get_info', ['track_id' => $track_id]);

		if($result['success']) {
			print_r($result);
		} else {
			return 'Error: ' . $result['message'];
		}
	}

	/**
	* Get the lyrics of music and song by track id
	* @param $track_id(String)
	* @return Array of string music lyrics(if exist lyrics)
	*/
	public function tracks_get_lyrics($track_id) {
		$result = $this->sendRequest('tracks_get_lyrics', ['track_id' => $track_id]);

		if($result['success']) {
			print_r($result);
		} else {
			return 'Error: ' . $result['message'];
		}
	}

	/**
	* Get the url of music
	* @param $track_id(String)
	* @param $reason(String) => ['save', 'listen']
	* @return Array of string url music selectet
	*/
	public function tracks_get_download_link($track_id, $reason = 'save') {
		$result = $this->sendRequest('tracks_get_download_link', ['track_id' => $track_id, 'reason' => $reason]);

		if($result['success']) {
			print_r($result);
		} else {
			return 'Error: ' . $result['message'];
		}
	}

	/**
	* Get the top list of music and song
	* @param $list_type(Integer) => [1 => 'Week', 2 => 'Month', 3 => '3 months', 4 => 'half a year', 5 => year]
	* @param $page(Integer)
	* @param $language(String)
	* @return Array of counter and tracker of music and song
	*/
	public function get_top_list($list_type, $page, $language = 'en') {
		$result = $this->sendRequest('get_top_list', ['list_type' => $list_type, 'page' => $page, 'language' => $language]);

		if($result['success']) {
			print_r($result);
		} else {
			return 'Error: ' . $result['message'];
		}
	}

	/**
	* Get access token for connect to pleer
	* @return token pleer
	*/
	public function getAccessToken() {
		return $this->_accessToken;
	}

	/**
	* Set token for connect to pleer
	* @param $token(String)
	*/
	public function setAccessToken($token) {
		$this->_accessToken = $token;
	}

	/**
	* Get access token for connect to pleer
	* @return token pleer
	*/
	public function getAccessTime() {
		return $this->_accessTime;
	}

	/**
	* Connection to pleer token page
	* @param $post(Array)
	* @param $header(Array)
	*/
	private function connectAccessToken($post, $header = []) {
		$ch = curl_init();

		curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
		curl_setopt($ch, CURLOPT_HEADER, 1);
		curl_setopt($ch, CURLOPT_URL, $this->_tokenUrl);
		curl_setopt($ch, CURLOPT_POST, 1);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $post);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_USERPWD, "{$this->_username}:{$this->_password}");

		$output = curl_exec($ch);
		curl_close ($ch);

		$output = json_decode(substr($output, strpos($output, '{')));
		$this->_accessToken = $output->access_token;
		$this->_accessTime = $output->expires_in;
	}

	/**
	* Connection to pleer api page for execute function
	* @param $method(String)
	* @param $params(Array)
	* @return json output excuted function in pleer
	*/
	private function sendRequest($method, $params = []) {
		$data = array_merge(['access_token' => $this->_accessToken, 'method' => $method], $params);
		$datavar = "";
		foreach ($data as $key => $value) {
			$datavar .= "{$key}={$value}&";
		}
		$datavar = rtrim($datavar, '&');

		$ch = curl_init();

		curl_setopt($ch, CURLOPT_URL, $this->_apiUrl);
		curl_setopt($ch, CURLOPT_POST, 1);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $datavar);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

		$output = curl_exec($ch);
		$rescode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
		curl_close ($ch);

		if($rescode == 200) {
			return json_decode($output, true);
		} else {
			exit('Invalid getting data from server.');
		}
	}
}
