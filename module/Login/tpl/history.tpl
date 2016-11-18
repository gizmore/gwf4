<?php

# Login History and clear form.

echo GWF_Box::box($cleared);

echo $pagemenu;

echo GWF_Table::start();

echo $tablehead;

foreach ($history as $h)
{
	echo GWF_Table::rowStart();
	echo GWF_Table::column($h->displayDate(), 'gwf-date');
	echo GWF_Table::column($h->displayIP(), 'gwf-ip');
	echo GWF_Table::column($h->displayHostname(), 'gwf-name');
	echo GWF_Table::rowEnd();
}

echo GWF_Table::end();

echo $pagemenu;

echo $form;

