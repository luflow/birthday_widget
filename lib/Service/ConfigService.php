<?php

declare(strict_types=1);

namespace OCA\BirthdayWidget\Service;

use OCA\BirthdayWidget\AppInfo\Application;
use OCP\IConfig;

class ConfigService {
	private const DEFAULT_DAYS_PAST = 14;
	private const DEFAULT_DAYS_FUTURE = 14;

	public function __construct(
		private IConfig $config,
	) {
	}

	/**
	 * Get number of days in the past to show birthdays.
	 */
	public function getDaysPast(): int {
		return (int) $this->config->getAppValue(
			Application::APP_ID,
			'days_past',
			(string) self::DEFAULT_DAYS_PAST
		);
	}

	/**
	 * Set number of days in the past to show birthdays.
	 */
	public function setDaysPast(int $days): void {
		$this->config->setAppValue(
			Application::APP_ID,
			'days_past',
			(string) max(0, $days)
		);
	}

	/**
	 * Get number of days in the future to show birthdays.
	 */
	public function getDaysFuture(): int {
		return (int) $this->config->getAppValue(
			Application::APP_ID,
			'days_future',
			(string) self::DEFAULT_DAYS_FUTURE
		);
	}

	/**
	 * Set number of days in the future to show birthdays.
	 */
	public function setDaysFuture(int $days): void {
		$this->config->setAppValue(
			Application::APP_ID,
			'days_future',
			(string) max(0, $days)
		);
	}

	/**
	 * Check if the birthday calendar is enabled globally in DAV settings.
	 */
	public function isBirthdayCalendarEnabledGlobally(): bool {
		return $this->config->getAppValue('dav', 'generateBirthdayCalendar', 'yes') === 'yes';
	}

	/**
	 * Check if the birthday calendar is enabled for a specific user.
	 */
	public function isBirthdayCalendarEnabledForUser(string $userId): bool {
		// First check global setting
		if (!$this->isBirthdayCalendarEnabledGlobally()) {
			return false;
		}
		// Then check user setting
		return $this->config->getUserValue($userId, 'dav', 'generateBirthdayCalendar', 'yes') === 'yes';
	}

	/**
	 * Get birthday calendar status for a user.
	 * Returns an array with both global and user status.
	 */
	public function getBirthdayCalendarStatus(string $userId): array {
		return [
			'globalEnabled' => $this->isBirthdayCalendarEnabledGlobally(),
			'userEnabled' => $this->config->getUserValue($userId, 'dav', 'generateBirthdayCalendar', 'yes') === 'yes',
		];
	}

	/**
	 * Get all settings as array (for admin settings page).
	 */
	public function getSettings(?string $userId = null): array {
		$settings = [
			'days_past' => $this->getDaysPast(),
			'days_future' => $this->getDaysFuture(),
		];

		if ($userId !== null) {
			$settings['birthday_calendar_status'] = $this->getBirthdayCalendarStatus($userId);
		} else {
			// When no user ID is provided, use global status only
			$settings['birthday_calendar_status'] = [
				'globalEnabled' => $this->isBirthdayCalendarEnabledGlobally(),
				'userEnabled' => true, // Assume enabled when no user context
			];
		}

		return $settings;
	}

	/**
	 * Update settings from array.
	 */
	public function setSettings(array $settings): void {
		if (isset($settings['days_past'])) {
			$this->setDaysPast((int) $settings['days_past']);
		}
		if (isset($settings['days_future'])) {
			$this->setDaysFuture((int) $settings['days_future']);
		}
	}
}
