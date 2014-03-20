<?php

/**
 * Required variables:
 * $page - current page number
 * $perPage - number of results shown per page
 * $numRows - total number of results
 * $url - URL to use for links including "{page}" indicating the URL segment that holds the page number. E.g. BASE_URL.'index/{page}/new'
 */

$lastPage = ceil($numRows / $perPage) - 1;
if($lastPage == -1)
{
	$lastPage = 0;
}
?>

<?php if($page != 0): ?>
 <a href="<?php echo str_replace('{page}', 0, $url); ?>">&laquo;first</a> 
<?php else: ?>
 &laquo;first 
<?php endif; ?>

<?php 
$i = $page-2;
if($i < 0)
{
	$i = 0;
}
?>

<?php while($i <= ($page+2) && $i <= $lastPage): ?>
<?php if($i != $page): ?>
 <a href="<?php echo str_replace('{page}', $i, $url); ?>"><?php echo $i+1; ?></a> 
<?php else: ?>
 <?php echo $i+1; ?> 
<?php endif; ?>
<?php $i++; endwhile; ?>

<?php if($page != $lastPage): ?>
 <a href="<?php echo str_replace('{page}', $lastPage, $url); ?>">last&raquo;</a> 
<?php else: ?>
 last&raquo; 
<?php endif; ?>