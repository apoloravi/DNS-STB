<?php
/**
*
* @ This file is created by http://DeZender.Net
* @ deZender (PHP7 Decoder for SourceGuardian Encoder)
*
* @ Version			:	4.1.0.1
* @ Author			:	DeZender
* @ Release on		:	29.08.2020
* @ Official site	:	http://DeZender.Net
*
*/

include 'config.php';
$dns = $dns_base;

if (isset($_GET['dns'])) {
	$dns_tmp = multidns($_GET['dns']);

	if (!empty($dns_tmp)) {
		$dns = $dns_tmp;
	}
}

$url = $dns . '/' . $_GET['type'] . '/' . $_GET['data'];
header('Location: ' . $url);

?>