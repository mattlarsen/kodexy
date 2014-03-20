<?php

/**
 * Required variables:
 * $name - HTML field name
 *
 * Optional variables:
 * $id - HTML element id
 * $defaultValue
 * $attributes - array of HTML attributes
 * $errorClass - class name if errors are logged
 */

$attributes = isset($attributes) ? $attributes : array();
$errorClass = isset($errorClass) ? $errorClass : 'error';
$id = isset($id) ? $id : FormHandler::convertNameToId($name);
$attributes['id'] = $id;

if(FormHandler::isErrors($name))
{
	$attributes['class'] = isset($attributes['class']) ? $attributes['class'] : '';
	$attributes['class'] = ' '.$errorClass;
}

$value = FormHandler::getPost($name);
if(!count($_POST))
{
	$value = isset($defaultValue) ? $defaultValue : '';
}

?><textarea name="<?php echo $name; ?>" <?php echo renderHtmlAttributes(unxss($attributes)); ?>><?php echo $value; ?></textarea>