<?php
/**
 * english language file
 * install.php
 *
 * @author Slawkens <slawkens@gmail.com>
 */
$locale['installation'] = 'Installation';
$locale['steps'] = 'Steps';

$locale['previous'] = 'Previous';
$locale['next'] = 'Next';

$locale['on'] = 'On';
$locale['off'] = 'Off';

$locale['loaded'] = 'Loaded';
$locale['not_loaded'] = 'Not loaded';

$locale['please_fill_all'] = 'Please fill all inputs!';
$locale['already_installed'] = 'MyAAC has been already installed. Please delete <b>install/<b/> directory.';

// welcome
$locale['step_welcome'] = 'Welcome';
$locale['step_welcome_title'] = 'Welcome to the installer';
$locale['step_welcome_desc'] = 'Choose the language you would like to view the installer with';

// license
$locale['step_license'] = 'License';
$locale['step_license_title'] = 'GNU/GPL License';

// requirements
$locale['step_requirements'] = 'Requirements';
$locale['step_requirements_title'] = 'Requirements check';
$locale['step_requirements_php_version'] = 'PHP Version';
$locale['step_requirements_write_perms'] = 'Write permissions';
$locale['step_requirements_failed'] = 'Installation will be disabled until these requirements will be passed.</b><br/>For more informations see <b>README</b> file.';
$locale['step_requirements_extension'] = '$EXTENSION$ PHP extension';

// config
$locale['step_config'] = 'Configuration';
$locale['step_config_title'] = 'Basic configuration';
$locale['step_config_server_path'] = 'Server path';
$locale['step_config_server_path_desc'] = 'Path to your TFS main directory, where you have config.lua located.';

$locale['step_config_mail_admin'] = 'Admin E-Mail';
$locale['step_config_mail_admin_desc'] = 'Address where emails from contact form will be delivered, for example <i>admin@gmail.com</i>';
$locale['step_config_mail_admin_error'] = 'Admin E-Mail is not correct.';
$locale['step_config_mail_address'] = 'Server E-Mail';
$locale['step_config_mail_address_desc'] = 'Address which will be used for outgoing emails (from:), for example <i>no-reply@your-server.org</i>';
$locale['step_config_mail_address_error'] = 'Server E-Mail is not correct.';
$locale['step_config_client'] = 'Client version';
$locale['step_config_client_desc'] = 'Used for download page and some templates';

// database
$locale['step_database'] = 'Import schema';
$locale['step_database_title'] = 'Import MySQL schema';
$locale['step_database_importing'] = 'Your database is MySQL. Importing schema now...';
$locale['step_database_error_path'] = 'Please specify server path.';
$locale['step_database_error_config'] = 'Cannot find config file. Is your server path correct? Go back and check again.';
$locale['step_database_error_database_empty'] = 'Cannot determine database type from config.lua. Your OTS is unsupported by this AAC.';
$locale['step_database_error_only_mysql'] = 'This AAC supports only MySQL. From your config file it seems that your OTS is using: $DATABASE_TYPE$ database. Please change your database to MySQL and then follow the installation again.';
$locale['step_database_error_table'] = 'Table $TABLE$ doesn\'t exist. Please import your OTS database schema first.';
$locale['step_database_error_table_exist'] = 'Table $TABLE$ already exist. Seems AAC is already installed. Skipping importing MySQL schema..';
$locale['step_database_error_schema'] = 'Error while importing schema:';
$locale['step_database_success_schema'] = 'Succesfully installed $PREFIX$ tables.';
$locale['step_database_error_file'] = '$FILE$ couldn\'t be opened. Please copy this content and paste there:';
$locale['step_database_adding_field'] = 'Adding field';
$locale['step_database_modifying_field'] = 'Modifying field';
$locale['step_database_changing_field'] = 'Changing $FIELD$ to $FIELD_NEW$...';
$locale['step_database_imported_players'] = 'Imported player samples...';
$locale['step_database_created_account'] = 'Created admin account...';

// admin account
$locale['step_admin'] = 'Admin Account';
$locale['step_admin_title'] = 'Create Admin Account';
$locale['step_admin_account'] = 'Admin account name';
$locale['step_admin_account_desc'] = 'Name of your admin account, which will be used to login to website and server.';
$locale['step_admin_account_id'] = 'Admin account id';
$locale['step_admin_account_id_desc'] = 'ID of your admin account, which will be used to login to website and server.';
$locale['step_admin_password'] = 'Admin account password';
$locale['step_admin_password_desc'] = 'Password to your admin account.';

// finish
$locale['step_finish_admin_panel'] = 'Admin Panel';
$locale['step_finish_homepage'] = 'homepage';
$locale['step_finish'] = 'Finish';
$locale['step_finish_title'] = 'Installation finished!';
$locale['step_finish_desc'] = 'Congratulations! <b>MyAAC</b> is ready to use!<br/>You can now login to $ADMIN_PANEL$, or visit $HOMEPAGE$.<br/><br/>
<font color="red">Please delete install/ directory.</font><br/><br/>
Post bugs and suggestions at $LINK$, thanks!';
?>
