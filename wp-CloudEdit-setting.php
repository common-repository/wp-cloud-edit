<?php

/*判断option表是否有wpce-token字段，
 *存在并且不为空说明已经成功设置过，跳转到内容发布页，
 *否则为初次使用，初次使用必需先设置wpce-token选项。
 */
if ( ! defined( 'ABSPATH' ) ) exit;
// require_once('functions.php');
global $wpce_token, $apiURLs, $NONCE_STR;
global $TK_PATTERN;

if(!current_user_can('edit_posts')){
  return;
}
?>

<?php require_once(WPCE_PLUGIN_DIR . 'header.php'); ?>

<div class="col-md-10 text-center">
	<br/>

<?php
$pHolder = "token";
if( isset($_POST["token"]) ){
	$token = $_POST["token"];

	$nonce = $_REQUEST['_wpnonce'];
	if (!wp_verify_nonce($nonce, $NONCE_STR) ) {
		wp_die("非法操作");
	}

	if( !preg_match($TK_PATTERN, $token) ){
		wp_die( "无效的token!!" );
	}

	$response = wp_remote_get($apiURLs['stat']."?token=".$token,array('timeout' => 120,
																  ) );
	$body = wp_remote_retrieve_body($response);
	$json_arr = json_decode($body, true);

	if($json_arr["code"] == 0){
		//valid

		if( update_option($wpce_token, $token) ){
			//更新token成功。
			$pHolder = $token;
			?>
			<h3>您已成功更新token，当前token为<br/><span class="text-danger"><?php echo $token; ?></span></h3>

			<?php
		}else{
			?>
			<h3>更新token失败，请重新尝试或联系相关技术人员!!</h3>
			<?php
		}
	}else{
		//invalid
		?>
		<h3>无效的token，请联系技术人员！！</h3>
		<?php
	}

}else{
	$token = get_option($wpce_token);
	if (empty($token) || false == $token){
?>
		<h3>您尚未设置token，<br/>请先从服务商处获得token并设置成功，<br/>才能进行使用本产品！</h3>
	<?php
	}else{
		$pHolder = $token;
	?>
		<h3>您当前token为<br/><span class="text-danger"><?php echo $token; ?></span></h3>
	<?php
	}
}

?>
	<br/>
	<form id="tokenForm" action="#" method="post" name="tokenForm">
		<?php wp_nonce_field($NONCE_STR); ?>
		<input name="token" placeholder="<?php echo $pHolder; ?>" onkeyup="value=value.replace(/[\W]/g,'') " onbeforepaste="clipboardData.setData('text',clipboardData.getData('text').replace(/[^\d]/g,''))" maxlength=64 size=65> 
		<input type="submit" value="设置" />
	</form>
</div>
<?php include_once(WPCE_PLUGIN_DIR . 'footer.php'); ?>
