<?php

declare(strict_types=1);

namespace OCA\BirthdayWidget\Service;

use DateTime;
use DateTimeImmutable;
use DateTimeZone;
use OCP\Calendar\IManager as ICalendarManager;

/**
 * Service for reading birthdays from Nextcloud's contact_birthdays calendar.
 */
class BirthdayService {
	private const CALENDAR_URI = 'contact_birthdays';

	public function __construct(
		private ICalendarManager $calendarManager,
		private ConfigService $configService,
	) {
	}

	/**
	 * Check if birthday calendar is enabled for a user.
	 */
	public function isBirthdayCalendarEnabled(string $userId): bool {
		return $this->configService->isBirthdayCalendarEnabledForUser($userId);
	}

	/**
	 * Get birthday calendar status for a user.
	 * Returns an array with both global and user status.
	 */
	public function getBirthdayCalendarStatus(string $userId): array {
		return $this->configService->getBirthdayCalendarStatus($userId);
	}

	/**
	 * Get birthdays within the configured date range.
	 *
	 * @param string $userId
	 * @return array Array of birthday items for the widget, grouped by status
	 */
	public function getBirthdays(string $userId): array {
		$principal = 'principals/users/' . $userId;

		$daysPast = $this->configService->getDaysPast();
		$daysFuture = $this->configService->getDaysFuture();

		$now = new DateTimeImmutable('now', new DateTimeZone('UTC'));
		$start = new DateTimeImmutable("-{$daysPast} days", new DateTimeZone('UTC'));
		$end = new DateTimeImmutable('+' . ($daysFuture + 1) . ' days', new DateTimeZone('UTC'));

		$query = $this->calendarManager->newQuery($principal);
		$query->setTimerangeStart($start);
		$query->setTimerangeEnd($end);

		$results = $this->calendarManager->searchForPrincipal($query);

		$birthdays = [
			'past' => [],
			'today' => [],
			'upcoming' => [],
		];
		$today = $now->format('Y-m-d');
		$currentYear = (int) $now->format('Y');

		foreach ($results as $result) {
			// Filter by birthday calendar
			$calendarUri = $result['calendar-uri'] ?? '';
			if ($calendarUri !== self::CALENDAR_URI) {
				continue;
			}

			$birthday = $this->parseBirthdayEvent($result, $today, $currentYear);
			if ($birthday !== null) {
				$birthdays[$birthday['status']][] = $birthday;
			}
		}

		// Sort each group by date
		usort($birthdays['past'], fn($a, $b) => strcmp($b['date'], $a['date'])); // Most recent first
		usort($birthdays['today'], fn($a, $b) => strcmp($a['name'], $b['name'])); // Alphabetical
		usort($birthdays['upcoming'], fn($a, $b) => strcmp($a['date'], $b['date'])); // Earliest first

		return $birthdays;
	}

	/**
	 * Parse a birthday calendar event into a widget item.
	 *
	 * @param array $searchResult
	 * @param string $today
	 * @param int $currentYear
	 * @return array|null
	 */
	private function parseBirthdayEvent(array $searchResult, string $today, int $currentYear): ?array {
		$objects = $searchResult['objects'] ?? [];

		foreach ($objects as $object) {
			$summary = $this->extractProperty($object, 'SUMMARY');
			$dtstart = $this->extractProperty($object, 'DTSTART');

			if ($summary === null || $dtstart === null) {
				continue;
			}

			// Format the date
			$date = $this->formatDate($dtstart);
			if ($date === null) {
				continue;
			}

			// Extract birth year from summary (format: "🎂 Name (Year)")
			$birthYear = null;
			$age = null;
			if (preg_match('/\((\d{4})\)$/', $summary, $matches)) {
				$birthYear = (int) $matches[1];
				// Calculate age - the year in parentheses is the year of birth
				// The event date shows when the birthday occurs this year
				$eventYear = (int) substr($date, 0, 4);
				$age = $eventYear - $birthYear;
			}

			// Clean up the name
			$name = $summary;

			// Remove cake emoji prefix
			$name = preg_replace('/^🎂\s*/', '', $name);

			// Remove year suffix like " (2026)"
			$name = preg_replace('/\s*\(\d{4}\)$/', '', $name);

			// Remove " (Birthday)" suffix (older format)
			$birthdaySuffix = ' (Birthday)';
			if (str_ends_with($name, $birthdaySuffix)) {
				$name = substr($name, 0, -strlen($birthdaySuffix));
			}
			// Also handle translated versions
			$birthdaySuffixDe = ' (Geburtstag)';
			if (str_ends_with($name, $birthdaySuffixDe)) {
				$name = substr($name, 0, -strlen($birthdaySuffixDe));
			}

			// Determine status
			$status = 'upcoming';
			if ($date === $today) {
				$status = 'today';
			} elseif ($date < $today) {
				$status = 'past';
			}

			return [
				'id' => md5($searchResult['uri'] ?? $summary . $date),
				'name' => $name,
				'date' => $date,
				'status' => $status,
				'age' => $age,
				'birthYear' => $birthYear,
			];
		}

		return null;
	}

	/**
	 * Extract a property value from VEVENT data.
	 *
	 * @param array $vevent
	 * @param string $property
	 * @return mixed
	 */
	private function extractProperty(array $vevent, string $property): mixed {
		$value = $vevent[$property] ?? null;

		if ($value === null) {
			return null;
		}

		// Handle array of values (take first)
		if (is_array($value)) {
			$value = $value[0] ?? null;
		}

		return $value;
	}

	/**
	 * Format a date value to Y-m-d string.
	 *
	 * @param mixed $dt
	 * @return string|null
	 */
	private function formatDate(mixed $dt): ?string {
		if ($dt instanceof DateTimeImmutable || $dt instanceof DateTime) {
			return $dt->format('Y-m-d');
		}

		if (is_string($dt)) {
			try {
				// Handle date-only strings like "20260107"
				if (preg_match('/^\d{8}$/', $dt)) {
					$date = DateTime::createFromFormat('Ymd', $dt);
					if ($date !== false) {
						return $date->format('Y-m-d');
					}
				}

				// Try to parse as datetime
				$date = new DateTime($dt);
				return $date->format('Y-m-d');
			} catch (\Exception $e) {
				return null;
			}
		}

		return null;
	}
}
