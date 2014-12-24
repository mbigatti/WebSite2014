<?php
	spl_autoload_register(function($class){
		require preg_replace('{\\\\|_(?!.*\\\\)}', DIRECTORY_SEPARATOR, ltrim($class, '\\')).'.php';
	});
	
	use \Michelf\Markdown;
	
	function readPost($resource_id) {
		$resource_file = "posts/" . $resource_id . ".md";
		
		if (file_exists($resource_file)) {
			$contents = file($resource_file);
		
			$title = array_shift($contents);
			$title = substr($title, strlen('title: '));
		
			$pubdate = array_shift($contents);
			$pubdate = substr($pubdate, strlen('pubdate: '));
		
			$article = Markdown::defaultTransform(join($contents));	
		
			return array(
				'uri' => 'http://bigatti.it/blog/'.$resource_id,
				'title' => $title,
				'pubdate' => $pubdate,
				'article' => $article
			);
		} else {
			return null;
		}
	}
			
	$uri = $_SERVER['REQUEST_URI'];
	$elements = split("/", $uri);
	$resource_id = $elements[2];
	
	$blog_main_page = true;
	$page_title = 'Massimiliano Bigatti - Blog';
	$posts = array();
	
	if (strlen($resource_id) != 0) {
		$post = readPost($resource_id);
		
		if ($post == null || count($elements) > 3) {
			header("HTTP/1.0 404 Not Found");
			include('/var/www/html/404.html');
			exit;
		}
		
		$blog_main_page = false;
		$page_title .= ' - ' . $post['title'];		
		array_push($posts, $post);
		
	} else {
		$list = file("posts/list");
		
		$records = array();
		foreach ($list as $record) {
			$elements = split(";", $record);
			array_push($records, array(
				'resource_id' => $elements[0],
				'pubdate' => $elements[1]
			));
		}
		
		function cmp($a, $b) {
			return $b['pubdate'] - $a['pubdate'];
		}	
		uasort($records, 'cmp');		
		
		foreach ($records as $record) {
			$post = readPost($record['resource_id']);
			array_push($posts, $post);
		}
	}
?>		
<html>
<head>
	<meta name="keywords" content="" />
	<meta name="description" content="Personal weblog" />
	<meta name="author" content="Massimiliano Bigatti" />
	<meta name="copyright" content="&copy; Copyright 2014 Massimiliano Bigatti" />
	<meta name="viewport" content="width=device-width">
	
	<title><?=$page_title?></title>

	<link href="http://fonts.googleapis.com/css?family=Montserrat:400|Open+Sans:300,400,600" rel="stylesheet" type="text/css">
	<link href="../css/blog.css" rel="stylesheet" type="text/css">
	<link href="../fonts/social-icon-font.css" rel="stylesheet" type="text/css">
	
	<link rel="shortcut icon" href="/images/favicon/favicon.ico" type="image/x-icon" />
	<link rel="apple-touch-icon" href="/images/favicon/apple-touch-icon.png" />
	<link rel="apple-touch-icon" sizes="57x57" href="/images/favicon/apple-touch-icon-57x57.png" />
	<link rel="apple-touch-icon" sizes="72x72" href="/images/favicon/apple-touch-icon-72x72.png" />
	<link rel="apple-touch-icon" sizes="76x76" href="/images/favicon/apple-touch-icon-76x76.png" />
	<link rel="apple-touch-icon" sizes="114x114" href="/images/favicon/apple-touch-icon-114x114.png" />
	<link rel="apple-touch-icon" sizes="120x120" href="/images/favicon/apple-touch-icon-120x120.png" />
	<link rel="apple-touch-icon" sizes="144x144" href="/images/favicon/apple-touch-icon-144x144.png" />
	<link rel="apple-touch-icon" sizes="152x152" href="/images/favicon/apple-touch-icon-152x152.png" />
</head>
<body>
	<nav>
		<ul>
			<li><a href="../">About</a></li>
<?php
	if ($blog_main_page) {
		echo '<li class="active">Blog</li>';
	} else {
		echo '<li><a href="./">Blog</a></li>';
	}
?>		
			<li><a href="../apps/">Apps</a></li>
			<li><a href="../books/">Books</a></li>
		</ul>
	</nav>
	
<?php
	foreach ($posts as $post) {
		$post_uri = $post['uri'];
		$title = $post['title'];
		$pubdate = $post['pubdate'];
		$article = $post['article'];
?>
	<section>
		<header>
			<h1><a href="<?=$post_uri?>"><?=$title?></a></h1>
			<time pubdate><?=$pubdate?></time>
		</header>
		<article>
			<?=$article?>
		</article>
	</section>
<?php
	}
?>
	
	<footer>
		<ul>
			<li><a href="https://twitter.com/mbigatti" target="_blank"><i class="icon-twitter"></i></a></li>
			<li><a href="https://github.com/mbigatti" target="_blank"><i class="icon-github"></i></a></li>
			<li><a href="https://www.linkedin.com/in/maxbigatti" target="_blank"><i class="icon-linkedin"></i></a></li>
			<li><a href="https://www.behance.net/_mxb" target="_blank"><i class="icon-behance"></i></a></li>
		</ul>
	</footer>
	
	<script>
	  (function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
	  (i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
	  m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
	  })(window,document,'script','//www.google-analytics.com/analytics.js','ga');

	  ga('create', 'UA-34001024-1', 'bigatti.it');
	  ga('send', 'pageview');
	</script>
</body>
</html>