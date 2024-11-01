<?php
/*内容源页面
 *
 *
 */
if ( ! defined( 'ABSPATH' ) ) exit;
require_once(WPCE_PLUGIN_DIR . 'functions.php'); 
?>

<?php require_once(WPCE_PLUGIN_DIR . 'header.php'); ?>
<?php require_once(WPCE_PLUGIN_DIR . 'nav-side.php'); ?>
<?php //require_once(WPCE_PLUGIN_DIR . 'processQuery.php'); ?>

<?php wp_enqueue_script('addSourceJs', plugins_url( 'js/add-source.js', __FILE__ ), 'jquery' ); ?>

<?php //wp_enqueue_script('queryJs', plugins_url( 'js/query.js', __FILE__ ), 'jquery' ); ?>

<div id="sourceBoard" class="col-md-10">

	<div class="aList">
		<div id="addSource" hidden="hidden">
			<h1>内容源 >> 新增内容源</h1>
			<hr>
			<div class="col-md-8">

			<div id="add" class = "popup">
			  <div class="panel panel-default popup-container" >
			    <!-- Default panel contents -->
			    <div class="panel-heading"><strong>检索</strong></div>
			    <div class="panel-body">
			      <div>
			        <input type="checkbox" value=1 checked="true" >互联网数据&nbsp;&nbsp;
			        <input type="checkbox" value=2 disabled="true" >APP数据&nbsp;&nbsp;
			        <input type="checkbox" value=3 disabled="true" >公众号数据&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
			        关键词：<input type="text" placeholder="输入关键词">&nbsp;&nbsp;<button id="filter">筛选</button>
			      </div>
			    </div>

			    <div id="source">
			    <!-- Table -->
			    <table class="table table-bordered table-striped table-condensed">

			        <thead>
			          <tr>
			            <th>#</th>
			            <th>ID</th>
			            <th>内容来源</th>
			            <th>频道</th>
			            <th>栏目</th>
			            <th>更新时间</th>
			          </tr>
			        </thead>
			        
			        <tbody class="sourceList">

			        </tbody>
			    </table>
			    </div>

			    <div id="pagination">

			    </div>

			    <button id="sSubmit">提交</button><button id="cancel" class="popup-close">取消</button>
			  </div>
			</div>

			<table class="table table-bordered">

			    <tbody>
			    <div id="ruleForm">
			        <tr>
			          <th>新增内容源名称</th>
			          <td><input name="ruleName" type="text"> </td>
			        </tr>
			        <tr>
			          <th>来源选择</th>
			          <td>
			            <input name="sourceIds" type="text" placeholder="请点击检索" readonly="readonly">
			            <button  class="showSources">检索</button>
			          </td>
			        </tr>
			        <tr>
			          <th>匹配关键词</th>
			          <td>
			          	<input name="keywords" type="text"><br>&nbsp;
			          	<input name="matchType" type="radio" checked="checked" value="1" />&nbsp;<span class="v-middle">标题</span>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
			          	<input name="matchType" type="radio" value="2" />&nbsp;<span class="v-middle">全文</span>
			          </td>
			        </tr>
			        <tr>
			          <th>不匹配关键词</th>
			          <td>
			          	<input name="noKeywords" type="text"><br>&nbsp;
			          	<input name="nomatchType" type="radio" checked="checked" value="1" />&nbsp;<span class="v-middle">标题</span>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
			          	<input name="nomatchType" type="radio" value="2" />&nbsp;<span class="v-middle">全文</span>
			          </td>
			        </tr>
			        <tr>
			          <th>排序</th>
			          <td><input name="order" type="text" placeholder=1></td>
			        </tr>
			    </div>
			    </tbody>
			</table>
			<button id="rSubmit">提交</button>

			</div>
		</div>
		<div id="manSource">
		</div>
		<div id="catContent">
		</div>
	</div>

	<div id="cats" class="popup">
		<div id="" class="popup-container">
		<table class="table">
			<tr><th>请选择要发布到的类目</th><th></th</tr>
	<?php
		function wpce_cats_list($catId, $depth = 1, $count = 1){
			$indent = "&nbsp;&nbsp;&nbsp;";
			$tags 	= "|--";
			$categories = get_categories(array('orderby'    => 'ID', 
											   'hide_empty' => 0,
											   'parent' 	=> $catId) );
			foreach ($categories as $category) {
				echo "<tr><td>";
				print(str_repeat($indent, $count));
				print($tags);
				print(esc_html($category->cat_name));
				echo "</td><td>";
				echo "<input type='checkbox' value='".esc_attr( $category->term_id )."'>";
				echo "</td></tr>";
				
				if(0 < --$depth){
					wpce_cats_list($category->term_id, $depth, $count+1);
				}
			}
		}
	    $categories = get_categories( array('orderby'=>'ID',
	    									'hide_empty'=>0,
	    									'parent'=>0) ); 

	    foreach ( $categories as $category ) {
	    	echo "<tr><td>";
	    	echo esc_html( $category->cat_name );
	    	echo "</td><td>";
	    	echo "<input type='checkbox' value='".esc_attr( $category->term_id )."'>";
	    	echo "</td></tr>";
	    	wpce_cats_list($category->term_id);
	    }
	?>
		<tr><td><button class="pub"> 发布到所选分类 </button></td><td><button class="popup-close">取消</button></td></tr>
		</table>
		</div>
	</div>

</div>

<?php include_once(WPCE_PLUGIN_DIR . 'footer.php'); ?>



