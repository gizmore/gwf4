<?php
printf('<md-content layout="column" flex class="gwf-messages">');
printf('<h3>%s</h3>', $title);
foreach ($messages as $message)
{
	printf('<div class="gwf-message"><i class="material-icons">done</i>%s</div>', $message);
}
echo "</md-content>\n";
