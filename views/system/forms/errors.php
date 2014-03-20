<?php if(FormHandler::isErrors()): ?>
<div style="border:1px solid red;color:red;">
<ul>
	<?php foreach(FormHandler::getErrors() as $error): ?>
	<li><?php echo $error; ?></li>
	<?php endforeach; ?>
</ul>
</div>
<?php endif; ?>