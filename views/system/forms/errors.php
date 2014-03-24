<?php if (kodexy()->formHandler->isErrors()): ?>
<div style="border:1px solid red;color:red;">
<ul>
    <?php foreach (kodexy()->formHandler->getErrors() as $error): ?>
    <li><?php echo $error; ?></li>
    <?php endforeach; ?>
</ul>
</div>
<?php endif; ?>