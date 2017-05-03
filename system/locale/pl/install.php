<?php
/**
 * polish language file
 * install.php
 *
 * @author Slawkens <slawkens@gmail.com>
 */
$locale['installation'] = 'Instalacja';
$locale['steps'] = 'Kroki';

$locale['previous'] = 'Poprzedni';
$locale['next'] = 'Następny';

$locale['on'] = 'Włączone';
$locale['off'] = 'Wyłączone';

$locale['loaded'] = 'Załadowane';
$locale['not_loaded'] = 'Nie załadowane';

$locale['please_fill_all'] = 'Proszę wypełnić wszystkie pola!';
$locale['already_installed'] = 'MyAAC został już zainstalowany. Proszę usunąć katalog <b>install/</b>.';

// welcome
$locale['step_welcome'] = 'Witamy';
$locale['step_welcome_title'] = 'Witamy w instalatorze';
$locale['step_welcome_desc'] = 'Wybierz język w którym chciałbyś przeprowadzić instalację';

// license
$locale['step_license'] = 'Licencja';
$locale['step_license_title'] = 'Licencja GNU/GPL';

// requirements
$locale['step_requirements'] = 'Wymagania';
$locale['step_requirements_title'] = 'Sprawdzanie wymagań';
$locale['step_requirements_php_version'] = 'Wersja PHP';
$locale['step_requirements_write_perms'] = 'Uprawnienia do zapisu';
$locale['step_requirements_failed'] = 'Instalacja zostanie zablokowana dopóki te wymagania nie zostaną spełnione.</b><br/>Po więcej informacji zasięgnij do pliku <b>README</b>.';
$locale['step_requirements_extension'] = 'Rozszerzenie PHP - $EXTENSION$';

// config
$locale['step_config'] = 'Konfiguracja';
$locale['step_config_title'] = 'Podstawowa konfiguracja';
$locale['step_config_server_path'] = 'Ścieżka do serwera';
$locale['step_config_server_path_desc'] = 'Ścieżka do Twojego folderu z TFS, gdzie znajduje się plik config.lua.';
$locale['step_config_account'] = 'Konto administratora';
$locale['step_config_account_desc'] = 'Nazwa twojego konta admina, która będzie używana do logowania na stronę i do serwera.';
$locale['step_config_password'] = 'Hasło do konta admina';
$locale['step_config_password_desc'] = 'Hasło do Twojego konta administratora.';

$locale['step_config_mail_admin'] = 'E-Mail admina';
$locale['step_config_mail_admin_desc'] = 'Na ten adres będą dostarczane E-Maile z formularza kontaktowego , przykładowo <i>admin@gmail.com</i>';
$locale['step_config_mail_admin_error'] = 'E-Mail admina jest niepoprawny.';
$locale['step_config_mail_address'] = 'E-Mail serwera';
$locale['step_config_mail_address_desc'] = 'Ten adres będzie używany do wysyłanych wiadomości z serwera (from:), przykładowo <i>no-reply@twój-serwer.org</i>';
$locale['step_config_mail_address_error'] = 'E-Mail serwera jest niepoprawny.';
$locale['step_config_client'] = 'Wersja klienta';
$locale['step_config_client_desc'] = 'Używana do strony pobieranie klienta oraz kilku szablonów';

// database
$locale['step_database'] = 'Baza';
$locale['step_database_title'] = 'Baza MySQL';
$locale['step_database_importing'] = 'Twoja baza to MySQL. Importowanie schematu...';
$locale['step_database_error_path'] = 'Proszę podać ścieżkę do serwera.';
$locale['step_database_error_config'] = 'Nie można znaleźć pliku config. Jest Twoja ścieżka do katalogu serwera poprawna? Wróć się i sprawdź ponownie.';
$locale['step_database_error_only_mysql'] = 'Ten AAC wspiera tylko bazy danych MySQL. Z Twojego pliku config wynika, że Twój serwera używa bazy: $DATABASE_TYPE$. Proszę zmienić typ bazy na MySQL i ponownie przystąpić do instalacji.';
$locale['step_database_error_table'] = 'Tabela $TABLE$ nie istnieje. Proszę najpierw zaimportować schemat bazy danych serwera OTS.';
$locale['step_database_error_table_exist'] = 'Tabela $TABLE$ już istnieje. Wygląda na to, że AAC został już zainstalowany. Schemat MySQL nie zostanie zaimportowany..';
$locale['step_database_error_schema'] = 'Błąd podczas importowania struktury bazy danych:';
$locale['step_database_success_schema'] = 'Pomyślnie zainstalowano tabele $PREFIX$.';
$locale['step_database_error_file'] = '$FILE$ nie mógł zostać otwarty. Proszę skopiować zawartość pola tekstowego i wkleić do tego pliku:';
$locale['step_database_adding_field'] = 'Dodawanie pola';
$locale['step_database_modifying_field'] = 'Modyfikacja pola';
$locale['step_database_changing_field'] = 'Zmiana $FIELD$ na $FIELD_NEW$...';
$locale['step_database_imported_players'] = 'Importowanie schematów graczy...';
$locale['step_database_created_account'] = 'Utworzono konto admina...';

// finish
$locale['step_finish_admin_panel'] = 'Panelu Admina';
$locale['step_finish_homepage'] = 'stronę główną';
$locale['step_finish'] = 'Koniec';
$locale['step_finish_title'] = 'Instalacja zakończona!';
$locale['step_finish_desc'] = 'Gratulacje! <b>MyAAC</b> jest gotowy do użycia!<br/>Możesz się teraz zalogować do $ADMIN_PANEL$, albo odwiedzić $HOMEPAGE$.<br/><br/>
<font color="red">Proszę usunąć katalog <b>install/</b>.</font><br/><br/>
Wrzuć błędy i sugestie na $LINK$, dzięki!';
?>
