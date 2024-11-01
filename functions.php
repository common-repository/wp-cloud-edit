<?php
function wpce_apiQuery($url){
	$ch = curl_init();

	curl_setopt($ch, CURLOPT_URL, $url);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	$ret = curl_exec($ch);
	curl_close($ch);

	return $ret;
}

$apiURLs = array(
		"sList" => "http://api.ycb.solr.com.cn/0.2/source_list",
		"rAdd" 	=> "http://api.ycb.solr.com.cn/0.2/rule_add",
		"rDel" 	=> "http://api.ycb.solr.com.cn/0.2/rule_del",
		"rShow" => "http://api.ycb.solr.com.cn/0.2/rule_show",
		"rList" => "http://api.ycb.solr.com.cn/0.2/rule_list",
		"aOne" 	=> "http://api.ycb.solr.com.cn/0.2/doc",
		"aList" => "http://api.ycb.solr.com.cn/0.2/list",
		"pub" 	=> "http://api.ycb.solr.com.cn/0.2/publish",
		"hist" 	=> "http://api.ycb.solr.com.cn/0.2/history",
		"stat" 	=> "http://api.ycb.solr.com.cn/0.2/status");

?>
