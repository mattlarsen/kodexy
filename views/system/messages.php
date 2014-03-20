<?php if(Kodexy::isMessages()): ?>
<div style="border:1px solid black;">
<ul>
	<?php foreach (Kodexy::getMessages() as $message): ?>
	<li><?php echo $message; ?></li>
	<?php endforeach; ?>
</ul>
</div>
<?php endif; ?>

<?php if(Kodexy::isErrorMessages()): ?>
<div style="border:1px solid red;color:red;">
<ul>
	<?php foreach (Kodexy::getErrorMessages() as $message): ?>
	<li><?php echo $message; ?></li>
	<?php endforeach; ?>
</ul>
</div>
<?php endif; ?>