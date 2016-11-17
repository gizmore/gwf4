<div class="gwf_messages">
	<span class="gwf_msg_t"><?php echo $title; ?></span>
		<ul>
<?php foreach ($messages as $msg) <?php echo 
	printf('<li>%s</li><br/>', $msg);
; ?>
?>
		</ul>
</div>
<div class="cl"></div>
