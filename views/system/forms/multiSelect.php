<?php

/**
 * Required variables:
 * $name - HTML field name
 * $options - array of options. Keys = values, values = text.
 *
 * Optional variables:
 * $id - HTML element id
 * $defaultValues
 * $attributes - array of HTML attributes
 * $errorClass - class name if errors are logged
 */

$attributes = isset($attributes) ? $attributes : array();
$errorClass = isset($errorClass) ? $errorClass : 'error';
$id = isset($id) ? $id : kodexy()->formHandler->convertNameToId($name);
$attributes['id'] = $id;

if (kodexy()->formHandler->isErrors($name))
{
    $attributes['class'] = isset($attributes['class']) ? $attributes['class'] : '';
    $attributes['class'] = ' '.$errorClass;
}

$values = kodexy()->formHandler->getPost($name);
$defaultValues = isset($defaultValues) ? $defaultValues : array();

if (!count($_POST))
{
    $values = $defaultValues;
}

if (!is_array($values))
{
    $values = array($values);
}

?><select name="<?php echo $name; ?>" multiple="multiple" <?php echo renderHtmlAttributes(unxss($attributes)); ?>>
    <?php foreach ($options as $optValue => $optText): ?>
    <option value="<?php echo $optValue; ?>" <?php echo in_array($optValue, $values) ? 'selected="selected"' : ''; ?>><?php echo $optText; ?></option>
    <?php endforeach; ?>
</select>