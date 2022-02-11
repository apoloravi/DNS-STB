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

function multidns($dns)
{
	$lista_dns = ['Padrao' => 'http://run.carbotv.xyz', 'DNS2' => 'http://run.carbotv.xyz'];
	return $lista_dns[$dns];
}

$debug = false;
$logs = false;
$dns_base = 'http://run.carbotv.xyz';
$compatibility = false;
$tv_censored_category_id = 332;
$movie_censored_category_id = 188;
$series_censored_category_id = 520;
$filmes_em_alta_categoria = 841;
$series_em_alta_categoria = 250;

?>
