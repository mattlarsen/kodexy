<?php

/**
 * Required variables:
 * $name - HTML field name
 * $value - checkbox value
 *
 * Optional variables:
 * $id - HTML element id
 * $default - checked by default?
 * $attributes - array of HTML attributes
 * $errorClass - class name if errors are logged
 */

$attributes = isset($attributes) ? $attributes : array();
$errorClass = isset($errorClass) ? $errorClass : 'error';
$id = isset($id) ? $id : FormHandler::convertNameToId($name, $value);
$attributes['id'] = $id;

if(FormHandler::isErrors($name))
{
	$attributes['class'] = isset($attributes['class']) ? $attributes['class'] : '';
	$attributes['class'] = ' '.$errorClass;
}

$checked = FALSE;
$postedValue = FormHandler::getPost($name);
if(!count($_POST))
{
	$checked = isset($default) && $default;
}
else
{
	if(!is_array($postedValue))
	{
		$postedValue = array($postedValue);
	}
	
	if(in_array($value, $postedValue))
	{
		$checked = TRUE;
	}
}

?><input type="checkbox" name="<?php echo $name; ?>" value="<?php echo $value; ?>" <?php echo $checked ? 'checked="checked"' : ''; ?> <?php echo renderHtmlAttributes(unxss($attributes)); ?> />