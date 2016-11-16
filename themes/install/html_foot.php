		</div>
		<div id="gwf_inst_progress">
			<?php echo $il->lang('foot_progress', array(100 / $steps * $step)); ?>
			<br/>
			[#<?php echo str_repeat('#', (int)(100 / $steps * $step/2)) . str_repeat('-', 100-100 / $steps * $step); ?>]<br/>
			<!-- Progressbar with CSS by steps / percent-->
			GWF_PATH: <?php echo GWF_PATH; ?>}<br/>
			GWF_WWW_PATH: <?php echo GWF_WWW_PATH; ?><br/>
			GWF_WEB_ROOT: <?php echo GWF_WEB_ROOT; ?>
		</div>
		<div id="gwf_inst_foot">
			<div><?php echo $il->lang('license'); ?></div>
			<div><?php echo $il->lang('pagegen', $timings['t_total']); ?></div>
		</div>
	</div>
</body>
</html>