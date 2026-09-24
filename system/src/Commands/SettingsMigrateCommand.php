<?php

namespace MyAAC\Commands;

use MyAAC\Models\Settings as SettingsModel;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class SettingsMigrateCommand extends Command
{
	private array $migrated = [];

	private array $ignored = ['admin_panel_modules', 'multiworld'];

	private array $renamed = [
		'visitors_counter' => 'footer_visitors_counter',
		'views_counter' => 'footer_views_counter',
		'account_change_character_name_points' => 'account_change_character_name_price',
		'account_change_character_sex_points' => 'account_change_character_sex_price',
		'smtp_secure' => 'smtp_security',
		'generate_new_reckey' => 'account_generate_new_reckey',
		'generate_new_reckey_price' => 'account_generate_new_reckey_price',
		'send_mail_when_change_password' => 'mail_send_when_change_password',
		'send_mail_when_generate_reckey' => 'mail_send_when_generate_reckey',
		'character_name_min_length' => 'create_character_name_min_length',
		'character_name_max_length' => 'create_character_name_max_length',
		'team_display_status' => 'team_status',
		'team_display_lastlogin' => 'team_lastlogin',
		'team_display_world' => 'team_world',
		'team_display_outfit' => 'team_outfit',
		'highscores_length' => 'highscores_per_page',
		'footer_show_load_time' => 'footer_load_time',
		'experiencetable_columns' => 'experience_table_columns',
		'experiencetable_rows' => 'experience_table_rows',
		'email_lai_sec_interval' => 'mail_lost_account_interval',
		'smtp_enabled' => 'mail_option',
	];

	private array $arrays = [
		'characters_level' => ['characters', 'level'],
		'characters_experience' => ['characters', 'experience'],
		'characters_magic_level' => ['characters', 'magic_level'],
		'characters_balance' => ['characters', 'balance'],
		'characters_marriage' => ['characters', 'marriage_info'], // only 0.3
		'characters_outfit' => ['characters', 'outfit'],
		'characters_creation_date' => ['characters', 'creation_date'],
		'characters_quests' => ['characters', 'quests'],
		'characters_skills' => ['characters', 'skills'],
		'characters_equipment' => ['characters', 'equipment'],
		'characters_frags' => ['characters', 'frags'],
		'characters_deleted' => ['characters', 'deleted'],
		'mail_signature_plain' => ['mail_signature', 'plain'],
		'mail_signature_html' => ['mail_signature', 'html'],
	];

	protected function configure(): void
	{
		$this->setName('settings:migrate')
			->setDescription('Migrates $config from config.php to Admin Panel settings')
			->addOption('dry-run', null, InputOption::VALUE_NONE, 'Show settings that would be migrated without changing anything');
	}

	protected function execute(InputInterface $input, OutputInterface $output): int
	{
		require SYSTEM . 'init.php';
		$this->migrated = [];

		$io = new SymfonyStyle($input, $output);
		$configFile = BASE . 'config.php';

		if (!is_file($configFile)) {
			$io->error('Config file not found: ' . $configFile);
			return Command::FAILURE;
		}

		$legacyConfig = $this->loadConfig($configFile);
		$settingsFile = require SYSTEM . 'settings.php';

		foreach ($settingsFile['settings'] as $key => $definition) {
			if (!is_string($key) || !is_array($definition) || !array_key_exists($key, $legacyConfig)) {
				continue;
			}

			if (in_array($key, $this->ignored, true)) {
				continue;
			}

			if (!empty($definition['is_config'])) {
				continue;
			}

			$this->parseValue($legacyConfig[$key], $definition['type'] ?? 'text', $key);
		}

		foreach ($this->renamed as $oldKey => $newKey) {
			if (array_key_exists($oldKey, $legacyConfig)) {
				$this->parseValue($legacyConfig[$oldKey], $settingsFile['settings'][$newKey]['type'] ?? 'text', $newKey);
			}
		}

		foreach ($this->arrays as $newKey => $old) {
			if (array_key_exists($old[0], $legacyConfig) && array_key_exists($old[1], $legacyConfig[$old[0]])) {
				$this->parseValue($legacyConfig[$old[0]][$old[1]], $settingsFile['settings'][$newKey]['type'] ?? 'text', $newKey);
			}
		}

		if ($this->migrated === []) {
			$io->success('No database-backed settings found in config.php.');
			return Command::SUCCESS;
		}

		if ($input->getOption('dry-run')) {
			$io->table(['Setting', 'Value'], $this->tableRows($this->migrated));
			return Command::SUCCESS;
		}

		global $db;
		$transactionStarted = false;

		try {
			$db->beginTransaction();
			$transactionStarted = true;
			foreach ($this->migrated as $key => $value) {
				SettingsModel::updateOrCreate(
					['name' => 'core', 'key' => $key],
					['value' => $value]
				);
			}

			$db->commit();
			$transactionStarted = false;
		}
		catch (\Throwable $error) {
			if ($transactionStarted) {
				$db->rollBack();
			}

			$io->error($error->getMessage());
			return Command::FAILURE;
		}

		clearCache();

		$io->success(count($this->migrated) . ' setting(s) migrated to Admin Panel settings.');
		//$io->table(['Setting', 'Value'], $this->tableRows($this->migrated));

		if ($legacyConfig['recaptcha_enabled'] ?? false) {
			$io->warning('Cannot migrate ReCaptcha settings. It needs to be installed as a plugin and re-configured.');
		}

		$impossibleToConvert = $this->getImpossibleToConvert($settingsFile['settings']);
		if ($impossibleToConvert !== '') {
			$io->warning('Following configs are impossible to convert, they need to be manually copied into config.local.php: ' . $impossibleToConvert);
		}

		return Command::SUCCESS;
	}

	private function loadConfig(string $filename): array
	{
		$config = [];
		require $filename;

		return $config;
	}

	private function parseValue($value, string $type, string $key = ''): void
	{
		if ($key === 'smtp_security') {
			if ($value == 'ssl') {
				$value = SMTP_SECURITY_SSL;
			}
			else if ($value == 'tls') {
				$value = SMTP_SECURITY_TLS;
			}
			else {
				$value = SMTP_SECURITY_NONE;
			}
		}
		elseif ($key === 'mail_option') {
			if ($value) {
				$value = MAIL_SMTP;
			}
			else {
				$value = MAIL_MAIL;
			}
		}

		if ($type === 'boolean' || $type === 'bool') {
			$this->migrated[$key] =	getBoolean($value) ? 'true' : 'false';
			return;
		}

		if (in_array($type, ['number', 'integer', 'int'], true)) {
			$this->migrated[$key] = (string)(int)$value;
			return;
		}

		if (is_array($value)) {

			if ($key === 'account_mail_confirmed_reward') {
				$this->migrated[$key . '_premium_days'] = $value['premium_days'];
				$this->migrated[$key . '_premium_points'] = $value['premium_points'];
				$this->migrated[$key . '_coins'] = $value['coins'];
				$this->migrated[$key . '_message'] = $value['message'];
				return;
			}

			if ($key === 'character_samples' || $key == 'quests' || $key == 'towns') {
				$entries = [];
				foreach ($value as $_key => $_value) {
					$entries[] = $_key . '=' . $_value;
				}

				$this->migrated[$key] = implode(PHP_EOL, $entries);
				return;
			}

			$this->migrated[$key] = implode(',', $value);
			return;
		}

		$this->migrated[$key] = (string)$value;
		return;
	}

	private function tableRows(array $settings): array
	{
		$rows = [];
		foreach ($settings as $key => $value) {
			$rows[] = [$key, is_scalar($value) ? (string)$value : json_encode($value)];
		}

		return $rows;
	}

	private function getImpossibleToConvert(array $settings): string
	{
		$keys = [];
		foreach ($settings as $key => $value) {
			if (!empty($value['is_config']) && $value['is_config']) {
				$keys[] = $key;
			}
		}

		return implode(', ', $keys);
	}
}
