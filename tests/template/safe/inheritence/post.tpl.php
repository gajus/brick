<?php ob_start()?>
<h1><?=$post['name']?></h1>
<p><?=$post['body']?></p>
<?=$template->render('inheritence/blog.inc', ['post' => $post, 'body' => ob_get_clean()])?>