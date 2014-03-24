<?php if (kodexy()->isMessages()): ?>
<div style="border:1px solid black;">
<ul>
    <?php foreach (kodexy()->getMessages() as $message): ?>
    <li><?php echo $message; ?></li>
    <?php endforeach; ?>
</ul>
</div>
<?php endif; ?>

<?php if (kodexy()->isErrorMessages()): ?>
<div style="border:1px solid red;color:red;">
<ul>
    <?php foreach (kodexy()->getErrorMessages() as $message): ?>
    <li><?php echo $message; ?></li>
    <?php endforeach; ?>
</ul>
</div>
<?php endif; ?>