<?php
require_once('functions.php');

$TK_PATTERN = '/^[0-9a-fA-F]{32}$/';
if(isset($_GET['qn']) && isset($_GET['token'])){
	$qn 	= $_GET['qn'];
	$token 	= $_GET['token'];
	if( !preg_match($TK_PATTERN, $token) ){
		echo "无效的token!!";
		exit;
	}
	switch ($qn) {
		case 'stat':
			$json = wpce_apiQuery($apiURLs[$qn]."?token=".$token);
		    $json_arr = json_decode($json, true);
		    if($json_arr["code"] == 0){
		    	//valid
		    }else{

		    }
			break;
		case 'hist':
	    		$json = wpce_apiQuery($apiURLs[$qn]."?token=".$token);
		        $json_arr = json_decode($json, true);
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
		        	echo $apiURLs[$qn]."?token=".$token;
		        }
			break;
	    case 'rAdd':
	    	if( isset($_GET['args']) ){
	    		$q = "";
	    		foreach ($_GET['args'] as $key => $value) {
	    			$q .= "&".$key."=".$value;
	       		}
	    		$json = wpce_apiQuery($apiURLs[$qn]."?token=".$token.$q);
		        $json_arr = json_decode($json, true);
		        if($json_arr["code"] == 0){
		        	echo "<h1>添加规则成功！</h1>";
		        }else{
		        	echo "<h1>".$json_arr["msg"]."</h1>";
		        	echo $apiURLs[$qn]."?token=".$token.$q;
		        }
	    	}else{
	    		echo "<h1>token && args needed!</h1>";
	    	}
	    	break;
	    case 'rDel':
	    	if( isset($_GET['ruleId']) ){
	    		$ruid = intval($_GET['ruleId']);
		        $json = wpce_apiQuery($apiURLs[$qn]."?token=".$token."&ruleid=".$ruid);
		        $json_arr = json_decode($json, true);
		        if($json_arr['code'] == 0){
		        	echo "0";
		        }else{
		        	echo $json_arr['msg'];
		        }

	    	}else{
	    		echo "<h1>token && ruleId needed!</h1>";
	    	}
	    	break;
	    case 'rList':
		        $json = wpce_apiQuery($apiURLs[$qn]."?token=".$token);
		        $json_arr = json_decode($json, true);
		        if($json_arr["rows"] > 0 && isset($_GET['p']) ){
		        		
		        	$p = $_GET['p'];
		        	if($p == 'man'){
		        ?>
						<table class="table table-bordered">

						      <thead>
						        <tr class="active">
						          <th>序号</th>
						          <th>名称</th>
						          <th>数据源</th>
						          <th>更多操作</th>
						        </tr>
						      </thead>
						      <tbody>
				        <?php
				        	$num=1;
				        	foreach ($json_arr["data"] as $rule) {
				        ?>

				        	<tr>
					          <th scope="row"><?php echo $num++; ?></th>
					          <td><?php echo $rule['rule_name']; ?></td>
					          <td><?php echo preg_replace('/\,/', ' ', $rule['sourceids']); ?></td>
					          <td>
					          	<!-- <button>查看</button>
					          	<button>编辑</button> -->
					          	<button class="del" value=<?php echo $rule['id']; ?> >删除</button>
					          </td>

				        	</tr>

				        	<?php
				        	}
				        	?>


						      </tbody>
						</table>
				<?php
		        	}elseif ($p == 'cor') {
				        $num=0;
				        $pa = array('text-left','text-center','text-right');
				        foreach ($json_arr["data"] as $rule) {
				        	$n = $num%4;
				        	if($n == 0){echo "<br>";}
				        	echo "<label for='rule$num'>".$rule['rule_name']."</label>";
				        	echo "<input type='checkbox' id='rule$num' value='".$rule['id']."'>";
				        	echo "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";

				        	$num++;
		        		}
		        	}elseif ($p == 'nav') {

		        		foreach ($json_arr["data"] as $item) {
		                	echo '<a href="#" id="'.$item["id"].'" class="list-group-item">'.$item["rule_name"]."</a>\n";
			            }
		        	}else{
		        		echo "<h1>[p]args error!!</h1>";
		        	}
		    	}else{
		    		echo "<h1>".$json_arr["msg"]."</h1>";
		    	}
	        break;
	    case 'sList':
		    	$start = 0;
		    	if(isset($_GET['start'])){
		    		$start = intval( $_GET['start'] );
		    	}
	    		$q='';
	    		if(isset($_GET['args'])){
		    		foreach ($_GET['args'] as $key => $value) {
		    			$q .= "&".$key."=".$value;
		       		}
	    		}
		        $json = wpce_apiQuery($apiURLs[$qn]."?token=".$token."&start=$start&rows=100".$q);
		        $json_arr = json_decode($json, true);
		        if($json_arr["rows"] > 0){

		        	$num=1;
		        	foreach ($json_arr["data"] as $source) {
				?>

			        <tr>
			          <th scope="row"><input name="item" value=<?php echo $source['sourceid']; ?> type="checkbox"></th>
			          <td><?php echo $num++; ?></td>
			          <td><?php echo $source['site']; ?></td>
			          <td><?php echo $source['channel']; ?></td>
			          <td><?php echo $source['column']; ?></td>
			          <td>--</td>
			        </tr>


					<?php
		        	}

			            $total = round($json_arr["total"]/20);
			            $total = ($total == floor($total))?$total-1:floor($total);
			            $currPage = floor($start/10);
			            $threshold = 3;
			            $pageNum = array();
			        	if($total > 10){
				        	if($total - $currPage > $threshold && $currPage - 1 > $threshold){
				        		$pages = range($currPage-$threshold, $currPage+$threshold);
				        		array_push($pageNum , "...");
				        		$pageNum = array_merge($pageNum, $pages);
				        		array_push($pageNum,"...");
				        	}elseif($total - $currPage > $threshold){
				        		$pageNum = range(0, 6);
				        		array_push($pageNum , "...");
				        	}elseif($currPage - 1 > $threshold){
				        		array_push($pageNum, "...");
				        		$pageNum = range(4, 10);
				        	}
			        	}else{
			        		$pageNum = range(0, $total<0?0:$total);
			        	}
					?>
		        	<tr>
		        	<td colspan="6">
		              <!-- <li class="list-group-item list-group-item-info"> -->
		              <input type="hidden" id="args" value = "<?php echo $q; ?>" />
		              <div class="row">
		                <div class="btn-toolbar col-md-9" role="toolbar">
		                 	<div class="btn-group" role="group">
		                		<button type="button" class="btn btn-default" value=0 >最前页</button>
		              		</div>
		                  	<div class="btn-group" role="group">

				                <?php
				                  foreach ($pageNum as $n){
				                    $ac = $n==$currPage?'active':'';
				                    $ac = $n==="..."?'':$ac;
				                    echo "<button type='button' class='btn btn-default $ac' value=$n>".($n==="..."?"...":$n+1)."</button>";
				                  }
				                ?>
		                  
		              		</div>
		                  	<div class="btn-group" role="group">
		                		<button type="button" class="btn btn-default" value=<?php echo $total; ?> >最后页</button>
		              		</div>
		            	</div>
					    <div class="col-md-3">
					      <button class="btn btn-default" id="selectAll">全选</button>
					      <button class="btn btn-default" id="reverse">反选</button>
					    </div>
					</div>
		              <!-- </li> -->
		        	</td>
		        	</tr>

			<?php
		        }else{
		    		echo "<h1>".$json_arr["msg"]."</h1>";
		        }

	        break;
		case 'aList':
		    $rid_pattern = '/^[\d,\s]*$/';
		    if(isset($_GET["ri"]) && preg_match($rid_pattern, trim($_GET["ri"])) ){
		    	$ri = trim($_GET["ri"]);
		    	$start = 0;
		    	if(isset($_GET['start'])){
		    		$start = intval( $_GET['start'] );
		    	}

		        $json = wpce_apiQuery($apiURLs[$qn]."?token=".$token."&ruleids=".$ri."&start=$start"/*."&rows=20"*/);
		        $json_arr = json_decode($json, true);

		        if($json_arr["total"/*rows*/] > 0){
			?>

				<div class="panel panel-default" id="accordion" role="tablist" aria-multiselectable="true">
				  <!-- Default panel contents -->
				  <div class="panel-heading">
				  	<h3>
				  		<button type="button" id="selectAll" class="btn btn-default">全选</button>&nbsp;
				  		<button type="button" id="reverse" class="btn btn-default">反选</button>&nbsp;&nbsp;&nbsp;&nbsp; 
				  		<?php
				  			if(isset($_GET['c'])){
			       				echo "<button id='SubaID' class='btn btn-default' value=".intval( $_GET['c'] ).">发布选中文章</button>";
			       			}else{
			       				echo "<button id='Submit' class='btn btn-default popup-trigger'>发布选中文章</button>";
			       			}
				  		?>
				  	</h3>
				  </div>
				  <div class="panel-body">
		        		<div class="row">
		        			<div class="col-md-1">
		        				<h4>ID</h4>
		        			</div>
		        			<div class="col-md-8">
		        				<h4>新闻标题</h4>
		        			</div>
		        			<div class="col-md-3">
		        				<h4>操作</h4>
		        			</div>
		        		</div>
		          </div>

		          <ul class="list-group al">
		        <?php
		        	$num=1;
		        	foreach ($json_arr["data"] as $article) {
		        ?>

		        		<li class="list-group-item">
		        			<?php echo "<form id='".$article['id']."'>"; ?>
			        		<div class="row">
			        			<div class="col-md-1">
			        				<input type='checkbox'><?php echo $num; ?>
			        			</div>
			        			<div class="col-md-8 title">
			        				<strong><a id="title"><?php echo $article['title']; ?></a></strong>
			        				<br>
			        				<small>
			        				来源：<?php echo $article['news_source_name']; ?>
			        				抓取时间：<?php echo date('Y-m-d H:i', $article['modified']) ?>
			        				</small>
			        			</div>
			        			<div class="col-md-3">
			        				<button class="btn btn-default btn-xs pp">预览</button>
			        				<button class="btn btn-default btn-xs pe">编辑</button>
		       						<?php 
			       						if(isset($_GET['c'])){
			       							echo "<button class='btn btn-default btn-xs' value=".intval( $_GET['c'] )." id='pub'>发布</button>";
			       						}else{
			       							echo "<button class='btn btn-default btn-xs popup-trigger' id='prePub'>发布</button>";
			       						}
		       						?>
		       						
			        			</div>
			        			<div id="article" class="col-md-12 bg-success" hidden="hidden">
			        				<div id="detail">
			        				<?php echo $article['detail']; ?>
			        				</div>
			        			</div>
					        </div>
					    	<?php echo "</form>"; ?>
		        		</li>


		        <?php
		        		$num++; 
		        	}
		        	$total = round($json_arr["total"]/20);
		        	$total = ($total == floor($total))?$total-1:floor($total);
		        	$currPage = floor($start/20);
		        	$threshold = 3;
		        	$pageNum = array();
		        	if($total > 10){
			        	if($total - $currPage > $threshold && $currPage - 1 > $threshold){
			        		$pages = range($currPage-$threshold, $currPage+$threshold);
			        		array_push($pageNum , "...");
			        		$pageNum = array_merge($pageNum, $pages);
			        		array_push($pageNum,"...");
			        	}elseif($total - $currPage > $threshold){
			        		$pageNum = range(0, 6);
			        		array_push($pageNum , "...");
			        	}elseif($currPage - 1 > $threshold){
			        		array_push($pageNum, "...");
			        		$pageNum = range(4, 10);
			        	}
		        	}else{
		        		$pageNum = range(0, $total<0?0:$total);
		        	}
		        ?>
		        		<li class="list-group-item list-group-item-info">
		        			<div class="btn-toolbar" role="toolbar">
			        			<div class="btn-group" role="group">
								  <button type="button" class="btn btn-default" value=0 >最前页</button>
								</div>
			        			<div class="btn-group" role="group">

			        			<?php
			        				foreach ($pageNum as $n){
			        					$ac = $n==$currPage?'active':'';
			        					$ac = $n==="..."?'':$ac;
			        					echo "<button type='button' class='btn btn-default $ac' value=$n>".($n==="..."?"...":$n+1)."</button>";
			        				}
			        			?>
			        			
								
								</div>
			        			<div class="btn-group" role="group">
								  <button type="button" class="btn btn-default" value=<?php echo $total; ?> >最后页</button>
								</div>
							</div>
		        		</li>

		          </ul>
		    	</div>

		        <?php
		    	}else{
		    		echo "<h2 class='text-danger'>尚无匹配文章</h2><br>";
		    		echo "<p>尝试重新创建一下规则吧</p>";
		    	}
		    }else{
		        // print_r($_GET);
		        echo "NO ri( RuleId ) !!!!!!!!";
		    }
	        break;
	    Default:
	    	echo "未设定的qn值！！";
	}

}else{
	echo "参数错误，未设定qn值！！";
}


?>
