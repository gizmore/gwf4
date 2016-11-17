<div id="GWF4_footmenu">
	<?php include 'menu_foot.php'; ?>
</div>

<div id="GWF4_debug_foot">
<div class="fl">
	<div id="oos_GWF4">GWF-4</div>
	<div>Logged in as <?php echo $user->display('user_name'); ?></div>
	<div>&copy;2009-2017 C.Busch</div>
</div>

<?php $timings = GWF_DebugInfo::getTimings(); ?>
<div class="fl">
	<div>SQL: <?php printf('%.03f', $timings['t_sql']); ?>s (<?php echo $timings['queries']; ?> Queries)</div>
	<div>PHP: <?php printf('%.03f', $timings['t_php']); ?>s</div>
	<div>TOTAL: <?php printf('%.03f', $timings['t_total']); ?>s</div>
</div>

<div class="fl">
	<div>MEM PHP: <?php echo GWF_Upload::humanFilesize($timings['mem_php']); ?></div>
	<div>MEM USER: <?php echo GWF_Upload::humanFilesize($timings['mem_user']); ?></div>
	<div>MEM TOTAL: <?php echo GWF_Upload::humanFilesize($timings['mem_total']); ?></div>
</div>

<?php $db = gdo_db(); ?>
<div class="fl">
	<div>SQL_OPENED: <?php echo $db->getQueriesOpened(); ?></div>
	<div>SQL_CLOSED: <?php echo $db->getQueriesClosed(); ?></div>
	<div>MODULES LOADED: <?php echo GWF_Module::getModulesLoaded(); ?></div>
</div>
<div class="fl">
	<div>PAGE SIZE: Unknown</div>
	<div>PAGES SERVED: <?php echo GWF_Counter::getAndCount('GWF4_pagecount'); ?></div>
</div>

</div>
