<?php
/*历史发布记录
 *
 *
 */
?>

<?php 
if ( ! defined( 'ABSPATH' ) ) exit;
global $wpce_token, $apiURLs;
$gtoken 	= get_option($wpce_token,"");
require_once(WPCE_PLUGIN_DIR . 'header.php'); 
?>

<div id="historyBoard" class="col-md-10">
	<?php
	if( !empty($gtoken) ){
		$response = wp_remote_get($apiURLs['hist']."?token=".$gtoken, array('timeout' => 120,
																	  ) );
		$body = wp_remote_retrieve_body($response);
		$json_arr = json_decode($body, true);

		if($json_arr["code"] == 0){
		?>
						<h1>发布历史记录</h1>
						<hr>
			        	<table class="table table-striped">
			        		<tr>
			        			<th>##</th>
			        			<th>源文章ID</th>
			        			<th>文章标题</th>
			        			<th>发布类别</th>
			        		</tr>

			        	<?php
			        	$c = 1;
			        	foreach ($json_arr["data"] as $record) {
			        		echo "<tr><td>".$c++."</td><td>".$record['docid']."</td><td>".$record['title']."</td><td>".$record['relationid']."</td></tr>";
			        	}
			        	?>
						</table>
			        	<?php
			        	echo "<strong><em>共".$json_arr["total"]."条记录！</em></strong>";
		}else{
			echo "<h1>".$json_arr["msg"]."</h1>";
			// echo $apiURLs[$qn]."?token=".$token;
		}
	}else{
		echo '<p class="text-danger"> <span class="glyphicon glyphicon-bell" style = "font-size:40px;">请先设置TOKEN!!</span></p>';
	}
	?>
</div>

<?php include_once(WPCE_PLUGIN_DIR . 'footer.php'); ?>
