<?php
printf('<md-content layout="column" flex class="gwf-error">');
foreach ($tVars['errors'] as $error)
{
	printf('<div><i class="material-icons">error</i>%s</div>', $error);
}
echo "</md-content>\n";
