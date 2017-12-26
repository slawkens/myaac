<?php
/**
 * german language file
 * install.php
 *
 * @author Slawkens <slawkens@gmail.com>
 */
$locale['installation'] = 'Installation';
$locale['steps'] = 'Schritte';

$locale['previous'] = 'Zurück';
$locale['next'] = 'Weiter';

$locale['on'] = 'Ein';
$locale['off'] = 'Aus';

$locale['loaded'] = 'Geladen';
$locale['not_loaded'] = 'Nicht geladen';

$locale['please_fill_all'] = 'Bitte alle Felder ausfüllen!';
$locale['already_installed'] = 'MyAAC wurde bereits installiert. Bitte löschen <b>install/<b/> Verzeichnis.';

// welcome
$locale['step_welcome'] = 'Willkommen';
$locale['step_welcome_title'] = 'Willkommen beim Installer';
$locale['step_welcome_desc'] = 'Wählen Sie die Sprache, mit der Sie das Installationsprogramm anzeigen möchten';

// license
$locale['step_license'] = 'Lizenz';
$locale['step_license_title'] = 'GNU/GPL Lizenz';

// requirements
$locale['step_requirements'] = 'Anforderungen';
$locale['step_requirements_title'] = 'Anforderungen überprüfen';
$locale['step_requirements_php_version'] = 'PHP Version';
$locale['step_requirements_write_perms'] = 'Schreibberechtigungen';
$locale['step_requirements_failed'] = 'Die Installation wird deaktiviert, bis diese Anforderungen erfüllt sind.</b><br/>Für weitere Informationen siehe <b>README</b> Datei.';
$locale['step_requirements_extension'] = '$EXTENSION$ PHP Erweiterung';

// config
$locale['step_config'] = 'Konfiguration';
$locale['step_config_title'] = 'Grundkonfiguration';
$locale['step_config_server_path'] = 'Serverpfad';
$locale['step_config_server_path_desc'] = 'Pfad zu Ihrem TFS-Hauptverzeichnis, in dem Sie sich config.lua befinden.';
$locale['step_config_mail_admin'] = 'Admin E-Mail';
$locale['step_config_mail_admin_desc'] = 'Adresse, an die E-Mails aus dem Kontaktformular gesendet werden, z. B. admin@gmail.com';
$locale['step_config_mail_admin_error'] = 'Admin E-Mail ist nicht korrekt.';
$locale['step_config_mail_address'] = 'Server E-Mail';
$locale['step_config_mail_address_desc'] = 'Adresse, die für ausgehende E-Mails (von :) verwendet wird, zB no-reply@your-server.org';
$locale['step_config_mail_address_error'] = 'Server E-Mail ist nicht korrekt.';
$locale['step_config_timezone'] = 'Zeitzone';
$locale['step_config_timezone_desc'] = 'Wird für Datumsfunktionen verwendet';
$locale['step_config_timezone_error'] = 'Zeitzone ist nicht korrekt.';
$locale['step_config_client'] = 'Client Version';
$locale['step_config_client_desc'] = 'Wird für die Downloadseite und einige Vorlagen verwendet';
$locale['step_config_client_error'] = 'Client ist nicht korrekt.';
$locale['step_config_usage'] = 'Nutzungsstatistiken';
$locale['step_config_usage_desc'] = 'MyAAC erlauben, anonyme Nutzungsstatistiken zu melden? Die Daten werden nur einmal alle 30 Tage gesendet und sind vollständig vertraulich.';
$locale['step_config_note'] = 'Der nächste Schritt dauert einige Zeit. Bitte aktualisieren Sie die Seite nicht und warten Sie, bis sie geladen ist.';

// database
$locale['step_database'] = 'Schema importieren';
$locale['step_database_title'] = 'MySQL schema importieren';
$locale['step_database_importing'] = 'Ihre Datenbank ist MySQL. Schema wird jetzt importiert...';
$locale['step_database_error_path'] = 'Bitte geben Sie den Serverpfad an.';
$locale['step_database_error_config'] = 'Datei config.lua kann nicht gefunden werden. Ist der Serverpfad korrekt? Geh zurück und überprüfe noch einmal.';
$locale['step_database_error_database_empty'] = 'Der Datenbanktyp kann nicht aus config.lua ermittelt werden. Ihr OTS wird von diesem AAC nicht unterstützt.';
$locale['step_database_error_only_mysql'] = 'Dieser AAC unterstützt nur MySQL. Aus Ihrer Konfigurationsdatei scheint Ihr OTS die Datenbank $DATABASE_TYPE$ zu verwenden. Bitte ändern Sie Ihre Datenbank in MySQL und folgen Sie dann der Installation erneut.';
$locale['step_database_error_table'] = 'Die Tabelle $TABLE$ existiert nicht. Bitte importieren Sie zuerst Ihr OTS-Datenbankschema.';
$locale['step_database_error_table_exist'] = 'Die Tabelle $TABLE$ existiert bereits. Scheint, dass AAC bereits installiert ist. Das Importieren des MySQL-Schemas wird übersprungen..';
$locale['step_database_error_schema'] = 'Fehler beim Importieren des Schemas:';
$locale['step_database_success_schema'] = '$PREFIX$ Tabellen wurden erfolgreich installiert.';
$locale['step_database_error_file'] = '$FILE$ konnte nicht geöffnet werden. Bitte kopieren Sie diesen Inhalt und fügen Sie ihn dort ein:';
$locale['step_database_adding_field'] = 'Feld hinzufügen';
$locale['step_database_modifying_field'] = 'Ändern Feld';
$locale['step_database_changing_field'] = 'Ändern $FIELD$ zu $FIELD_NEW$...';
$locale['step_database_imported_players'] = 'Importierte Spielerproben...';
$locale['step_database_loaded_monsters'] = 'Geladen Monsters...';
$locale['step_database_error_monsters'] = 'Beim Laden der Datei monsters.xml sind einige Probleme aufgetreten. Bitte überprüfen Sie $LOG$ für weitere Informationen.';
$locale['step_database_loaded_spells'] = 'Geladen Zauber...';
$locale['step_database_created_account'] = 'Administratorkonto erstellt...';
$locale['step_database_created_news'] = 'Erstellt newses...';

// admin account
$locale['step_admin'] = 'Administratorkonto';
$locale['step_admin_title'] = 'Administratorkonto erstellen';
$locale['step_admin_account'] = 'Name des Administratorkontos';
$locale['step_admin_account_desc'] = 'Name Ihres Admin-Accounts, der für die Anmeldung an der Website und dem Server verwendet wird.';
$locale['step_admin_account_error_format'] = 'Ungültiges Kontonamensformat. Verwenden Sie nur a-Z und Ziffern 0-9. Mindestens 3, maximal 32 Zeichen.';
$locale['step_admin_account_error_same'] = 'Das Passwort darf nicht mit dem Kontonamen übereinstimmen.';
$locale['step_admin_account_id'] = 'Administratorkontonummer';
$locale['step_admin_account_id_desc'] = 'Nummer Ihres Admin-Accounts, der für die Anmeldung bei der Website und dem Server verwendet wird.';
$locale['step_admin_account_id_error_format'] = 'Ungültiges Kontonummernformat. Bitte benutzen Sie nur die Nummern 0-9. Mindestens 6, maximal 10 Zeichen.';
$locale['step_admin_account_id_error_same'] = 'Das Passwort darf nicht mit dem Kontonummer übereinstimmen';
$locale['step_admin_password'] = 'Administratorkontokennwort';
$locale['step_admin_password_desc'] = 'Passwort für Ihr Administratorkonto.';
$locale['step_admin_password_error_empty'] = 'Bitte geben Sie das Passwort für Ihr neues Konto ein.';
$locale['step_admin_password_error_format'] = 'Ungültiges Passwortformat. Verwenden Sie nur a-Z und Ziffern 0-9. Mindestens 8, maximal 30 Zeichen.';

// finish
$locale['step_finish_admin_panel'] = 'Admin-Panel';
$locale['step_finish_homepage'] = 'Homepage';
$locale['step_finish'] = 'Finish';
$locale['step_finish_title'] = 'Installation beendet!';
$locale['step_finish_desc'] = 'Herzliche Glückwünsche! <b>MyAAC</b> ist bereit zu verwenden!<br/>Sie können sich jetzt im $ADMIN_PANEL$ anmelden, oder die $HOMEPAGE$ besuchen.<br/><br/>
<font color="red">Bitte lösche install/ Verzeichnis.</font><br/><br/>
Sende Fehler und Vorschläge bei $LINK$, danke!';
?>
