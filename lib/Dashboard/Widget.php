<?php

declare(strict_types=1);

namespace OCA\BirthdayWidget\Dashboard;

use OCA\BirthdayWidget\AppInfo\Application;
use OCA\BirthdayWidget\Service\BirthdayService;
use OCP\AppFramework\Services\IInitialState;
use OCP\Dashboard\IAPIWidget;
use OCP\IL10N;
use OCP\Util;

class Widget implements IAPIWidget {
	public function __construct(
		private IL10N $l10n,
		private BirthdayService $birthdayService,
		private IInitialState $initialStateService,
		private ?string $userId,
	) {
	}

	/**
	 * @inheritDoc
	 */
	public function getId(): string {
		return 'birthday-widget';
	}

	/**
	 * @inheritDoc
	 */
	public function getTitle(): string {
		return $this->l10n->t('Birthdays');
	}

	/**
	 * @inheritDoc
	 */
	public function getOrder(): int {
		return 10;
	}

	/**
	 * @inheritDoc
	 */
	public function getIconClass(): string {
		return 'icon-cake';
	}

	/**
	 * @inheritDoc
	 */
	public function getUrl(): ?string {
		return null;
	}

	/**
	 * @inheritDoc
	 */
	public function load(): void {
		if ($this->userId !== null) {
			$items = $this->getItems($this->userId);
			$this->initialStateService->provideInitialState('birthdays', $items);
		}

		// Check birthday calendar status for this user
		$birthdayCalendarStatus = $this->userId !== null
			? $this->birthdayService->getBirthdayCalendarStatus($this->userId)
			: ['globalEnabled' => false, 'userEnabled' => false];
		$this->initialStateService->provideInitialState('birthdayCalendarStatus', $birthdayCalendarStatus);

		Util::addScript(Application::APP_ID, Application::APP_ID . '-dashboard');
		Util::addStyle(Application::APP_ID, Application::APP_ID . '-dashboard');
		Util::addStyle(Application::APP_ID, 'icon');
	}

	/**
	 * @inheritDoc
	 */
	public function getItems(string $userId, ?string $since = null, int $limit = 10): array {
		return $this->birthdayService->getBirthdays($userId);
	}
}
