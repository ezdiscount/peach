<?php

$storeId = 1776843727;
$productId = 577880881721;
$currentPageNum = 1;

$apiUrl = "http://rate.taobao.com/feedRateList.htm?callback=jsonp_reviews_list&userNumId=$storeId&auctionNumId=$productId&currentPageNum=$currentPageNum";

echo $apiUrl, PHP_EOL;

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $apiUrl);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($ch, CURLOPT_HEADER, 0);
$result = trim(curl_exec($ch));

$json = trim(mb_substr($result, mb_strlen('jsonp_reviews_list')), '()');

echo $json, PHP_EOL;
