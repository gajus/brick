<h1><?=$post['name']?></h1>
<p><?=$post['body']?></p>
<?php $template->extend('inheritence/blog', ['post' => $post])?>