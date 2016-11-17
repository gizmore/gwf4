<?php
foreach ($tVars['messages'] as $type => $msgs)
{
	printf('<md-content layout="row" flex class="gwf_%s">', $type);

	foreach ($msgs as $title => $msg)
	{
		printf('<h3>%s</h3>', $title);
		foreach ($msg as $message)
		{
			printf('<div><i class="material-icons">error</i>%s</div>', $message);
		}
		echo "</li>\n";
	}
	
	echo "</md-content>\n";
}
