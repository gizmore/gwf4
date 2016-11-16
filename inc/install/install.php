<?php
/**
 * The GWF-Installation-Wizard
 * @author spaceone
 * @author gizmore
 */
header('Content-Type: text/html; charset=UTF-8');
# Load Install-Core
require_once GWF_CORE_PATH.'inc/install/GWF_InstallWizard.php';
require_once GWF_CORE_PATH.'inc/install/GWF_InstallConfig.php';
require_once GWF_CORE_PATH.'inc/install/GWF_InstallFunctions.php';
require_once GWF_CORE_PATH.'inc/install/GWF_InstallWizardLanguage.php';

// define('GWF_INSTALLATION', true);
define('GWF_STEP', Common::getGetString('step', '0'));
define('GWF_LOGGING_PATH', getcwd().'/protected/installog');

$gwf = new GWF3(getcwd(), array(
	'website_init' => false,
	'autoload_modules' => false,
	'load_module' => false,
	'load_config' => false,
	'start_debug' => true,
	'get_user' => false,
	'do_logging' => true,
	'log_request' => true,
	'blocking' => false,
	'no_session' => true,
	'store_last_url' => false,
	'ignore_user_abort' => true,
));

GWF_Debug::setDieOnError(false);

# Website init
GWF_InstallWizardLanguage::init();
GWF_HTML::init();


# Set install language
$il = new GWF_LangTrans(GWF_CORE_PATH.'inc/lang/install/install');
GWF_InstallWizard::setGWFIL($il);

# Design init
GWF3::setDesign('install');
GWF_Website::addCSS(GWF_WEB_ROOT.'themes/install/css/install.css');
GWF_Website::addCSS(GWF_WEB_ROOT.'themes/install/css/design.css');
GWF_Website::setPageTitle('GWF Install Wizard');
$tVars = array(
	'gwfpath'=> GWF_PATH, 
	'gwfwebpath' => GWF_WWW_PATH, 
	'step' => GWF_STEP, 
	'il' => $il, 
	'steps' => 12,
	'timings' => GWF_DebugInfo::getTimings(),
);
// GWF_Template::addMainTvars($tVars);

if (false !== (Common::getPost('create_admin'))) {
	$page = GWF_InstallWizard::wizard_9_1();
}
elseif (false !== (Common::getPost('test_db'))) {
	$page = GWF_InstallWizard::wizard_1a();
}
elseif (false !== (Common::getPost('write_config'))) {
	$page = GWF_InstallWizard::wizard_1b();
}
elseif (false !== (Common::getPost('install_modules'))) {
	$page = GWF_InstallWizard::wizard_6_1();
}
else switch(GWF_STEP)
{
	case '1': $page = GWF_InstallWizard::wizard_1(); break; # Create Config
	case '2': $page = GWF_InstallWizard::wizard_2(); break; # Init Install
	case '3': $page = GWF_InstallWizard::wizard_3(); break; # Install CoreDB
	case '4': $page = GWF_InstallWizard::wizard_4(); break; # Choose Language
	case '4_1': $page = GWF_InstallWizard::wizard_4_1(); break; # Install Language
	case '4_2': $page = GWF_InstallWizard::wizard_4_2(); break; # Install Language+IP2C 
	case '5': $page = GWF_InstallWizard::wizard_5(); break; # Choose UA
	case '5_1': $page = GWF_InstallWizard::wizard_5_1(); break; # Install UA
	case '5_2': $page = GWF_InstallWizard::wizard_5_2(); break; # Skip UA
	case '6': $page = GWF_InstallWizard::wizard_6(); break; # Choose modules
	case '7': $page = GWF_InstallWizard::wizard_7(); break; # Create index.php
	case '8': $page = GWF_InstallWizard::wizard_8(); break; # Create htaccess
	case '9': $page = GWF_InstallWizard::wizard_9(); break; # Create admins
	case '10': $page = GWF_InstallWizard::wizard_10(); break; # Clear Caches
	case '11': $page = GWF_InstallWizard::wizard_11(); break; # Protect install folder
	case '12': $page = GWF_InstallWizard::wizard_12(); break; # Protect install folder
	
	default:
	case '0': $page = GWF_InstallWizard::wizard_0(); break; # List Status
}


echo $gwf->onDisplayPage($page, $tVars);
