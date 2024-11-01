<?php
if ( ! defined( 'ABSPATH' ) ) exit;
require_once(WPCE_PLUGIN_DIR . 'functions.php');
global $wpce_token, $NONCE_STR;;
global $gtoken, $apiURLs;
$gtoken 	  = get_option($wpce_token,"");
$queryUrl 	  = plugins_url('processQuery.php', __FILE__);

if(!current_user_can('edit_posts')){
  return;
}

if(isset($_POST['t']) && isset($_POST['d']) ){
	$nonce = $_REQUEST['_wpnonce'];
	if(!wp_verify_nonce($nonce, $NONCE_STR)){
		wp_die('非法操作！');
	}

	$title =  sanitize_text_field( strip_tags( $_POST['t'] ) );
	$content = sanitize_text_field( strip_tags( $_POST['d'] ) );

    // $ret = "[".$_POST['t']."][";
    $id = 0;
    $status = "publish";
    $category = array();
    if(isset($_POST['c']) && is_array($_POST['c'])){
        $category = $_POST['c'];
        global $FUNC_INTVAL;
        $category = array_map($FUNC_INTVAL, $category);
    }
    
    if(isset($_POST['action']) && $_POST['action']=='edit'){
        $status = "draft";
    }
    if(isset($_POST['id'])){
        $id = intval( $_POST['id'] );
    }

    $post = array(  'ID'            =>$id,
                    'post_title'    =>$title,
                    'post_content'  =>$content,
                    'post_category' =>empty($category) ? 1 : $category,
                    'post_status'   =>$status,
                    'post_type'     =>'post');
    $ret = wp_insert_post($post);

    if($ret != 0){
        $docid = "&docid=".intval( $_POST["f"] );
        $title = "&title=".urlencode( $title );
        $relationid = "&relationid="./*urlencode_deep*/implode(',', $category );
        $pubUrl = $apiURLs['pub']."?token=".$gtoken.$docid.$title.$relationid;
		$response = wp_remote_get($pubUrl, array('timeout' => 120,) );
        wp_remote_retrieve_body($response);

	    if(isset($_POST['action']) && $_POST['action'] == "edit"){
	        $adminUrl = admin_url();
	        echo "[editurl]".$adminUrl."post.php?post=$ret&action=edit";
	        exit;
	        
	    }else{
	    	//success
	        echo "success";
	        exit;
	    }
    }else{
    	//failure
    	echo "failure";
    	exit;
    }

}
?>

<?php wp_enqueue_style('sourceCss', plugins_url( 'css/source.css', __FILE__ ) ); ?>
<?php wp_enqueue_script('sourceJs', plugins_url( 'js/source.js', __FILE__ ), 'jquery' ); ?>

<input type="hidden" id="gtoken" value="<?php echo esc_attr( $gtoken ); ?>" />
<input type="hidden" id="queryUrl" value="<?php echo esc_attr( $queryUrl ); ?>" />
<input type="hidden" id="_wpnonce" name="_wpnonce" value="<?php echo wp_create_nonce( $NONCE_STR ); ?>" />


<?php wp_enqueue_script('navJs', plugins_url( 'js/nav.js', __FILE__ ), 'jquery' ); ?>
<?php wp_enqueue_script('queryJs', plugins_url( 'js/query.js', __FILE__ ), 'jquery' ); ?>

<div class="col-md-2 bg-info">

<?php
	function wpce_display_child_cats($catId, $depth = 1, $count = 1){
		$indent = "&nbsp;&nbsp;&nbsp;";
		$tags 	= "|--";
		$categories = get_categories(array('orderby'    => 'ID', 
										   'hide_empty' => 0,
										   'parent' 	=> $catId) );
		foreach ($categories as $category) {
			printf('<a href="#" id=%1$d class="list-group-item">', esc_html( $category->term_id ));
			print(str_repeat($indent, $count));
			print($tags);
			print(esc_html($category->cat_name));
			print('</a>');

			if(0 < --$depth){
				wpce_display_child_cats($category->term_id, $depth, $count+1);
			}
		}

	}


	$url = home_url(add_query_arg(array()));
	$paras = explode('=', substr($url, strpos($url, '?')+1));
	$paras = array_flip($paras);
	if(isset($paras['content'])){

?>

		<div class="row">
			<div class="col-md-12 text-center">
				<button id="corr" type="button" class="btn btn-info" <?php if(empty($gtoken)){echo 'disabled="disabled"';} ?> >频道关联表</button>
			</div>
		</div>
		<div class="row">
			<div class="col-md-12">
				<br/>
				<div id="cats" class="list-group">

				    <?php 

				    if(!empty($gtoken)){
					    $categories = get_categories( array('orderby'=>'ID',
					    									'hide_empty'=>0,
					    									'parent'=>0) ); 

					    foreach ( $categories as $category ) {
					    	printf( '<a href="#" id=%1$d class="list-group-item">%2$s</a>',
					    		esc_html( $category->term_id ),
					            esc_html( $category->cat_name ) );
					    	wpce_display_child_cats($category->term_id);

					    }
					}

				    ?>

				</div>

			</div>

		</div>

<?php
	}elseif (isset($paras['source']) ) { 
?>

		<div class="row">
			<div class="col-md-12 text-center">
				<button id="addTag" type="button" class="btn btn-info" <?php if(empty($gtoken)){echo 'disabled="disabled"';} ?> >新增内容源</button>
			</div>
		</div>
		<br/>
		<div class="row">
			<div class="col-md-12 text-center">
				<button id="manTag" type="button" class="btn btn-info" <?php if(empty($gtoken)){echo 'disabled="disabled"';} ?> >管理内容源</button>
			</div>
		</div>
		<div class="row">
			<div class="col-md-12 text-center">
				<br/>
				<div id="rules" class="list-group">

				    <?php 
				    //查询数据库内容源表，列出所有创建的内容源
				    global $apiURLs;
				    if(!empty($gtoken)){
						$response = wp_remote_get($apiURLs['rList']."?token=".$gtoken, array('timeout' => 120,
																 ) );
						$body = wp_remote_retrieve_body($response);
						$json_arr = json_decode($body, true);
				    	//判断返回的json是否有rules
				    	if($json_arr["rows"] > 0){

				            foreach ($json_arr["data"] as $item) {
				                echo '<a href="#" id="'.$item["id"].'" class="list-group-item">'.$item["rule_name"]."</a>\n";

				            }
				    	}
				    }
				    ?>

				</div>
			</div>

		</div>
<?php					
	}
?>
<div id="pp" class="popup">
    <div id="" class="popup-preview">
    </div>
</div>

</div>

