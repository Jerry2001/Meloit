<?php
	function console($txt){
		echo '<script>';
		echo 'console.log('. json_encode($txt) .')';
		echo '</script>';
	}
	$text = file_get_contents('http://stuboard.beloit.edu/stumail/');
	$cur = 0;
	$page = 0;
	$updateTime = "";
	$a = array();
	if(isset($_GET["page"])){
		$page = $_GET["page"];
	}
	/*echo '<script>';
	echo 'console.log('. json_encode($text) .')';
	echo '</script>';*/
	function debugg($cur){
		global $text;
		echo $text[$cur].$text[$cur + 1].$text[$cur + 2].$text[$cur + 3].$text[$cur + 4];
	}
	function findNextInstance($idx, $pattern){
		global $text, $cur;
		$j = 0;
		for($i = $idx; $i < strlen($text); $i++){
			if($text[$i] == $pattern[$j]) $j++; else $j = 0;
			++$cur;
			if($j == strlen($pattern)) break; 
		}
		return $cur;
	}
	function createMessageList($idx, $pattern){
		global $text, $cur, $a, $page, $updateTime;
		$num = 1;
		$check = "";
		$count_news = 0;
		$a = array();
		while($check != "/ul" && count($a) < 10){
			$count_news++;
			$j = 0;
			$ul_count = 0;
			$object = new stdClass();
			$begin = findNextInstance($cur ,"\"");
			$end = findNextInstance($cur ,"\"");
			$object->url = "";
			for($i = $begin; $i < $end - 1; $i++){
				$object->url = $object->url . $text[$i];
			}
			$object->tag = "";
			$begin = findNextInstance($cur ,"[");
			$end = findNextInstance($cur ,"]");
			for($i = $begin; $i < $end - 1; $i++){
				$object->tag = $object->tag . $text[$i];
			}
			$object->tag = strtoupper($object->tag);
			$begin = findNextInstance($cur ," ");
			$end = findNextInstance($cur ,"<");
			$object->title = "";
			for($i = $begin; $i < $end - 1; $i++){
				$object->title = $object->title . $text[$i];
			}
			$object->title = ucfirst($object->title);
			$begin = findNextInstance($cur ,"em>");
			$end = findNextInstance($cur ,"</e");
			$object->author = "";
			for($i = $begin; $i < $end - 1; $i++){
				$object->author = $object->author . $text[$i];
			}
			$begin = findNextInstance($cur ,"(");
			$end = findNextInstance($cur ,")");
			$object->time = "";
			for($i = $begin; $i < $end - 1; $i++){
				$object->time = $object->time . $text[$i];
			}
			findNextInstance($cur, "</li>");
			findNextInstance($cur, "<");
			$check = $text[$cur] . $text[$cur + 1] . $text[$cur + 2];
			if($count_news == 1) {
				$updateTime = $object->time;
			}
			if($count_news > $page * 10) array_push($a, $object);
		}
	}
	function createContent(){
		global $a, $text, $cur, $updateTime;
		for($i = 0; $i < count($a); $i++){
			$cur = 0;
			$text = file_get_contents('http://stuboard.beloit.edu/stumail/' . $a[$i]->url);
			$begin = findNextInstance($cur, "<!-- body=\"start\" -->");
			$end = findNextInstance($cur, "<!-- body=\"end\" -->") - strlen("<!-- body=\"end\" -->");
			$a[$i]->content = "";
			//for($j = $begin; $j < $end - 1; $j++){
				$a[$i]->content = substr($text, $begin, $end - $begin);
			//}
		}
	}
	findNextInstance($cur, "messages-list");
	findNextInstance($cur, "ul");
?>

<!DOCTYPE html>
<html lang="en">
	<meta charset="utf-8">
	<head>
		<title>Meloit</title>

		<meta name="viewport" content="width=device-width, initial-scale=1.0, shrink-to-fit=no">

		<link href="vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">
		
		<link href="vendor/fontawesome-free/css/all.min.css" rel="stylesheet" type="text/css">

		<link href='https://fonts.googleapis.com/css?family=Lora:400,700,400italic,700italic' rel='stylesheet' type='text/css'>

	  	<link href='https://fonts.googleapis.com/css?family=Open+Sans:300italic,400italic,600italic,700italic,800italic,400,300,600,700,800' rel='stylesheet' type='text/css'>

	  	<link href="css/clean-blog.min.css" rel="stylesheet">
	</head>
	<body>
		<header class="masthead" style="background-image: url('img/hone.jpg');">
			<div class="overlay"></div>
			<div class="container">
				<div class="row">
					<div class="col mx-auto">
						<div class="site-heading">
							<h1>Meloit</h1>
							<span class="subheading">Beloit announcements updated on <?php
								createMessageList($cur, "ul");
								createContent();
								echo $updateTime;
							?></span> 
						</div>
					</div>
				</div>
			</div>
		</header>

		<div class="container">
		    <div class="row">
		      	<div class="col-lg-8 col-md-10 mx-auto">
			      	<?php
				      	for($i = 0; $i < count($a); $i++){
					      	$title = $a[$i]->title;
					     	$author = $a[$i]->title;
							$date = $a[$i]->title;
							echo "<div class='post-preview'>";
							
							echo "<div class='accordion' id='accordionExample". $i . "'>";
								echo "<div class='card'>";
									echo "<div class='card-header' id='heading". $i . "'>";
										echo "<h2 class='mb-0'>";
											echo "<button class='btn' type='button' data-toggle='collapse' data-target='#collapse". $i ."'aria-expanded='true' aria-controls='collapse". $i ."'>";
												echo "<h2 class='post-title'>";
												echo "[". $a[0]->tag . "] ";
												echo $title;;
												echo"</h2>";
											echo"</button>";
          								echo "</h2>";
          							echo "</div>";

          							echo "<div id='collapse". $i ."'class='collapse' aria-labelledby='heading". $i ."'data-parent='#accordionExample". $i . "'>";
          								echo "<div class='card-body'>";
          									echo $a[$i]->content;
          								echo "</div>";
          							echo "</div>";
          						echo "</div>";
          					echo "</div>";
							        echo "<p class='post-meta'>";
							        echo "Posted by ";
							        echo $author . " " . "on ";
							        echo $a[$i]->time;
							        echo "</p>";
							    echo "</div>";
							    echo "<hr>";
						}
				    	
			        ?>
		        	<!-- Pager--> 
			        <div class="clearfix">
			        	<?php
			        		if($page > 0){
				        		$page -= 1;
				        		echo"<a class='btn btn-primary float-left'  href='index.php?page=".$page."'>";
				        		$page += 1;
				        		echo"&larr;Page ". $page ."</a>";
				        	}
			        	?>
			        	<?php
			        		$page += 1;
			        		echo"<a class='btn btn-primary float-right'  href='index.php?page=".$page."'>";
			        		$page += 1;
			        		echo"Page ". $page ."&rarr;</a>";
			        		$page -= 2;
			        	?>
			        </div>
		      	</div>
		    </div>
	  </div>

	<footer>
    <div class="container">
    	<div class="row">
    		<div class="col-lg-8 col-md-10 mx-auto">
          		<p class="copyright text-muted">Copyright &copy; ngohongphuc2001</p>
       		</div>
      	</div>
    </div>
  	</footer>

	<hr>
		<script src="vendor/jquery/jquery.min.js"></script>
		<script src="vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
		<script src="js/clean-blog.min.js"></script>
	</body>
</html>