<?php

function series_comp($url)
{
	$response = [
		['category_id' => '0', 'category_name' => 'Series [0-9]', 'parent_id' => 0]
	];

	foreach (range('A', 'Z') as $char) {
		$char_code = ord($char);
		$dados = ['category_id' => (string) $char_code, 'category_name' => 'Series [' . $char . ']', 'parent_id' => 0];
		array_push($response, $dados);
	}

	return json_encode($response);
}

function series_get($url)
{
	$category_id = $_GET['category_id'];
	$url = $GLOBALS['dns_base'] . '/player_api.php?username=' . $_GET['usuario'] . '&password=' . $_GET['senha'] . '&action=get_series';
	$data = json_decode(getData($url));
	$response = [];

	if ($category_id == '0') {
		foreach ($data as $series) {
			if (is_numeric(substr($serie->name, 0, 1))) {
				if ($serie->category_id == $series_censored_category_id) {
				}
				else {
					array_push($response, $serie);
				}
			}
		}
	}
	else {
		foreach ($data as $serie) {
			if (substr($serie->name, 0, 1) == chr($category_id)) {
				if ($serie->category_id == $GLOBALS['series_censored_category_id']) {
				}
				else {
					array_push($response, $serie);
				}
			}
		}
	}

	return json_encode($response);
}

function censurar($url)
{
	$data = json_decode(getData($url));
	$response = [];

	if ($_GET['action'] == 'get_live_streams') {
		foreach ($data as $canal_o) {
			if (isset($canal_o->is_adult)) {
				unset($canal_o->is_adult);
			}

			array_push($response, (array) $canal_o + ['is_adult' => '1']);
		}
	}
	else {
		foreach ($data as $movie) {
			if (isset($movie->is_adult)) {
				unset($movie->is_adult);
			}

			array_push($response, (array) $movie + ['is_adult' => '1']);
		}
	}

	return json_encode($response);
}

function pesquisa($url)
{
	$termo = $_GET['search'];
	$url = $GLOBALS['dns_base'] . '/player_api.php?username=' . $_GET['usuario'] . '&password=' . $_GET['senha'] . '&action=' . $_GET['action'];
	$data = json_decode(getData($url));
	$response = [];

	foreach ($data as $item) {
		if (strstr(strtolower($item->name), strtolower($termo))) {
			if ($_GET['action'] == 'get_live_streams') {
				$censored = ($item->category_id == $GLOBALS['tv_censored_category_id'] ? '1' : '0');
			}
			else if ($_GET['action'] == 'get_vod_streams') {
				$censored = ($item->category_id == $GLOBALS['movie_censored_category_id'] ? '1' : '0');
			}
			else if ($_GET['action'] == 'get_series') {
				$censored = ($item->category_id == $GLOBALS['series_censored_category_id'] ? '1' : '0');
			}
			else {
				$censored = '0';
			}

			array_push($response, (array) $item + ['is_adult' => $censored]);
		}
	}

	return json_encode($response);
}

function removeNulos($data)
{
	$data = json_decode($data);

	if (isset($_GET['action'])) {
		print_r($data);
		exit();
	}

	return $data;
}

function debugFeed($data)
{
	$data = json_decode($data);

	if (isset($_GET['action'])) {
		$action = $_GET['action'];

		if ($action == 'get_series') {
			$series = [];

			foreach ($data as $serie) {
				$serie->name = NULL;
				$serie->cover = NULL;
				$serie->plot = NULL;
				$serie->cast = NULL;
				$serie->director = NULL;
				$serie->releaseDate = NULL;
				$serie->last_modified = NULL;
				$serie->rating = NULL;
				$serie->rating_5based = NULL;
				$serie->backdrop_path = NULL;
				$serie->youtube_trailer = NULL;
				$serie->episode_run_time = NULL;
				$serie->category_id = NULL;
				array_push($series, $serie);
			}

			return json_encode($series);
		}
		else if ($action == 'get_series_info') {
			print_r($data);
			exit();
		}
	}

	return json_encode($data);
}

function emAlta($url)
{
	$filmes_em_alta_categoria = $GLOBALS['filmes_em_alta_categoria'];
	$series_em_alta_categoria = $GLOBALS['series_em_alta_categoria'];

	if (preg_match('/category_id=900001/', $url)) {
		$url = preg_replace('/900001/', $filmes_em_alta_categoria, $url);
	}
	else {
		$url = preg_replace('/900002/', $series_em_alta_categoria, $url);
	}

	$items = json_decode(getData($url));
	//print_r($items);
	$items_resp = [];
	
	if(sizeof($items) == 0){
		/*$p = array("num"=>1,"name"=>"Don Oscar","stream_type"=>"movie","stream_id"=>97585,"stream_icon"=>"http=>\/\/b5.vc=>80\/images\/rtX6TPhoNACLQOTOZmTxc5r4R67_big.jpg","rating"=>"6.2","rating_5based"=>3.1,"added"=>"1615488054","is_adult"=>"0","category_id"=>"33","container_extension"=>"mp4","custom_sid"=>"","direct_source"=>"");
		for($i=0; $i<9; $i++){
			array_push($items_resp,$p);
		}*/
		return json_encode($items_resp);
	}

	for ($i = 0; $i <= 9; $i++) {
		if (!(($_GET['xcategory_id'] == '900002') && ($items[$i]->category_id == $GLOBALS['series_censored_category_id']))) {
			array_push($items_resp, $items[$i]);
		}
	}

	return json_encode($items_resp);
}

function encode($str)
{
	logger($str);

	if (count(json_decode($str)) <= 0) {
		$str = '{}';
	}

	$encoded = base64_encode(strrev($str));
	return strrev(base64_encode($encoded . '|' . ceil(strlen($encoded) / 7)));
}

function getData($url)
{
	try {
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
		curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
		curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (SMART-TV; LINUX; Tizen 3.0) AppleWebKit/538.1 (KHTML, like Gecko) Version/3.0 TV Safari/538.1');
		curl_setopt($ch, CURLOPT_IPRESOLVE, CURL_IPRESOLVE_V4);
		curl_setopt($ch, CURLOPT_TIMEOUT, 30);
		curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10);
		$resp = curl_exec($ch);

		if (!curl_errno($ch)) {
			$info = curl_getinfo($ch);

			if ($info['http_code'] == 404) {
				logger('Servidor Retornou 404');
				return '{"user_info":{"auth":0}}';
			}
		}

		curl_close($ch);
	}
	catch (Execption $e) {
		logger($e);
		return '{"user_info":{"auth":0}}';
	}

	logger($resp);
	return $resp;
}

function logger($data)
{
	if ($GLOBALS['logs']) {
		$log = 'Usuario: ' . $_GET['usuario'] . ' - ' . date('d:m:Y H:m') . PHP_EOL . 'Recebeu durante a requisição: ' . http_build_query($_GET) . PHP_EOL . 'Resposta: ' . PHP_EOL . $data . PHP_EOL . '-----------------' . PHP_EOL;
		file_put_contents('./logs/' . date('d.m.Y') . '.log', $log, FILE_APPEND);
	}
}

//error_reporting(0);
include 'config.php';

if (!empty($_GET['usuario']) & !empty($_GET['senha'])) {
	$url = $dns_base . '/player_api.php?' . http_build_query($_GET);
	$url = preg_replace('/usuario/', 'username', $url);
	$url = preg_replace('/senha/', 'password', $url);

	if (preg_match('/category_id=900001|category_id=900002/', $url)) {
		$response = emAlta($url);
	}
	else if (preg_match('/search=/', $url)) {
		$response = pesquisa($url);
	}
	else if ((preg_match('/get_live_streams/', $url) && ($_GET['category_id'] == $tv_censored_category_id)) || (preg_match('/get_vod_streams/', $url) && ($_GET['category_id'] == $movie_censored_category_id))) {
		$response = censurar($url);
	}
	else if ($compatibility && preg_match('/get_series_categories/', $url)) {
		$response = series_comp($url);
	}
	else if ($compatibility && preg_match('/get_series/', $url) && !preg_match('/get_series_info/', $url)) {
		$response = series_get($url);
	}
	else {
		$response = getData($url);
	}

	if ($debug) {
		$response = debugFeed($response);
	}

	echo encode($response);
}

?>
