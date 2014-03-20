<?php

/**
 * Required variables:
 * $name - HTML field name
 * $options - array of options. Keys = values, values = text.
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
$defaultValue = isset($defaultValue) ? $defaultValue : NULL;

if(!count($_POST))
{
	$value = $defaultValue;
}

?><select name="<?php echo $name; ?>" <?php echo renderHtmlAttributes($attributes); ?>>
	<?php foreach ($options as $optValue => $optText): ?>
	<option value="<?php echo $optValue; ?>" <?php echo $value !== NULL && $optValue == $value ? 'selected="selected"' : ''; ?>><?php echo $optText; ?></option>
	<?php endforeach; ?>
</select>