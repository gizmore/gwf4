<?php
foreach ($tVars['messages'] as $type => $msgs)
{
	printf('<ul class="gwf_%s">', $type);

	foreach ($msgs as $title => $msg)
	{
		printf('<li><span class="gwf_%s_t">%s</span>', $type, $title);
		foreach ($msg as $message)
		{
			echo $message.PHP_EOL;
		}
		echo "</li>\n";
	}
	
	echo "</ul>\n";
}
