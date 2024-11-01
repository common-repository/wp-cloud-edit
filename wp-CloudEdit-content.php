<?php
/*内容发布页面
 *
 *
 */ 
if ( ! defined( 'ABSPATH' ) ) exit;
?>
<?php require_once(WPCE_PLUGIN_DIR . 'header.php'); ?>
<?php require_once(WPCE_PLUGIN_DIR . 'nav-side.php'); ?>
<?php global $gtoken, $TK_PATTERN, $FUNC_INTVAL, $NONCE_STR; 
if(!current_user_can('edit_posts')){
  return;
}

if(isset($_GET['cm']) && $_GET['cm']=='del'){

    $nonce = $_REQUEST['_wpnonce'];
    if (!wp_verify_nonce($nonce, $NONCE_STR) ) {
      wp_die("非法操作");
    }

    if(isset($_GET['c']) && isset($_GET['token'])){
        $cat = intval($_GET['c']);
        if(!preg_match($TK_PATTERN, $_GET['token'])){
          echo '2';
          exit;
        }
        global $wpdb;
        $t = $wpdb->prefix . 'wpce_correlation';
        $q = "delete from $t where cat_id=".$cat." and token='".$_GET['token']."';";
        $r = $wpdb->query( $wpdb->prepare($q) );
        if($r){
            echo '0';
            exit;
        }else{
            echo '1';
            exit;
        }
    }
}


if(isset($_POST['cor'])&& isset($_POST['cor_name']) && isset($_POST['c']) && isset($_POST['token'])){

    $nonce = $_REQUEST['_wpnonce'];
    if (!wp_verify_nonce($nonce, $NONCE_STR) ) {
      wp_die("非法操作");
    }

    global $wpdb;
    $cor = array_map($FUNC_INTVAL, $_POST['cor']);
    $cat = intval($_POST['c']);
    $cor_name = array_map('strip_tags', $_POST['cor_name']);
    $cor_name = array_map('sanitize_text_field', $cor_name);
    if(!preg_match($TK_PATTERN, $_POST['token'])){
      echo '2';
      exit;
    }

    $t = $wpdb->prefix . 'wpce_correlation';
    $r = $wpdb->insert($t, 
            array('cat_id'=>$cat, 'value'=>implode(',', $cor), 'cor_name'=>implode(',', $cor_name), 'token'=>$_POST['token'] ), 
            array('%d', '%s', '%s', '%s'));
    if($r){
        echo '0';
        exit;
    }else{
        echo '1';
        exit;
    }
}

if(isset($_GET['c']) && isset($_GET['token'])){

    $nonce = $_REQUEST['_wpnonce'];
    if (!wp_verify_nonce($nonce, $NONCE_STR) ) {
      wp_die("非法操作");
    }
    
    $cat = intval($_GET['c']);
    if(!preg_match($TK_PATTERN, $_GET['token'])){
      echo '[tokenerror]1';
      exit;
    }

    global $wpdb;
    $t = $wpdb->prefix . 'wpce_correlation';
    $q = "select value from $t where cat_id = ".$cat." and token='".$_GET['token']."';";
    $r = $wpdb->get_results( $wpdb->prepare($q) );
    if(count($r) != 0){
        //根据规则查询服务
        // foreach ($r as $item) {
        //     echo $item->value;
        // }
        echo esc_html("[ruleorcat]".$r[0]->value);
        exit;
    }else{
        $cat = get_category($cat);
        $cat_name = "当前频道(不用填)";
        if($cat != null){
            $cat_name = $cat->name;
        }
        echo esc_html("[ruleorcat]".$cat_name);
        exit;
    }
}
?>
<div id="contentBoard" class="col-md-10">
	<div id="manCorr"  class="col-md-12 hidden">
		<h1>内容发布 >> 管理频道关联规则关系</h1>
		<hr>
		<div class="col-md-8">
        <table class="table table-bordered">

        	<thead>
        	  <tr>
        	    <th class="active">序号</th>
        	    <th class="active">频道名称</th>
        	    <th class="active">已关联规则</th>
        	    <th class="active">操作</th>
        	  </tr>
        	</thead>
        	<tbody id="trs">

            <?php
            // if(isset($_GET['cl']) && isset($_GET['token'])){
                global $wpdb;
                $t = $wpdb->prefix . 'wpce_correlation';
                $q = "select * from $t where token='".$gtoken/*$_GET['token']*/."';";
                $r = $wpdb->get_results( $wpdb->prepare($q) );
                if(count($r) != 0){
                        $num = 1;
                        foreach ($r as $i) {
                    ?>
                        <tr>
                          <td><?php echo esc_html($num++); ?></td>
                          <td>
                          <?php  
                            $c = get_category($i->cat_id);
                            if($c!=null){
                                echo  esc_html($c->name);
                            }else{
                                echo  esc_html($i->cat_id);
                            }
                          ?>    
                          </td>
                          <td><?php echo esc_html($i->cor_name); ?></td>
                          <td>
                            <!-- <button>编辑</button> -->
                            <button value="<?php echo esc_html($i->cat_id);?>" class='cancel'>取消</button>
                          </td>
                        </tr>
                    <?php
                        }//end foreach
                }else{
                    echo "<h2 class='text-danger'>请先添加关联关系！</h2>";
                }
            // }
            ?>
            <!-- end tbody -->
          	</tbody>
        </table>
        </div>
	</div>
  <div id="catContent"  class="col-md-12 hidden">
  </div>
  <div id="addCorr"  class="col-md-12 hidden">
        <h1>内容发布 >> 频道关联内容源</h1>
        <hr>
        <div class="col-md-8">
            <h3  class="text-danger">此频道尚未完成关联，赶快完成下边的关联吧！</h3>
          <table class="table table-bordered">
                <thead>
                </thead>
                <tbody>
                  <tr>
                    <th class="active">频道名称</th>
                    <td><input type='text' id='cat' value='' placeholder='placeholder' readonly='readonly'></td>
                  </tr>
                  <tr>
                    <th class="active">选择内容源</th>
                    <td>
                      <div class= 'rules'>
                      </div>
                    </td>
                  </tr>
                  <tr>
                    <th class="active"></th>
                    <td><button class='subCor'>提交</button></td>
                  </tr>
           
                </tbody>
          </table>
        </div>
  </div>
  <div id="infoSecc"  class="col-md-12 hidden">
    <h2 class='text-danger'>关联成功！</h2>
  </div>
  <div id="infoFail"  class="col-md-12 hidden">
    <h2 class='text-danger'>关联失败，请稍后重试或联系技术人员！</h2>
  </div>
</div>
<?php wp_enqueue_script('addCorrJs', plugins_url( 'js/add-corr.js', __FILE__ ), 'jquery' ); ?>
<?php include_once(WPCE_PLUGIN_DIR . 'footer.php'); ?>
