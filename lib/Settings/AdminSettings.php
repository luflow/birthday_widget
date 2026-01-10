<?php

declare(strict_types=1);

namespace OCA\BirthdayWidget\Settings;

use OCA\BirthdayWidget\AppInfo\Application;
use OCA\BirthdayWidget\Service\ConfigService;
use OCP\AppFramework\Http\TemplateResponse;
use OCP\AppFramework\Services\IInitialState;
use OCP\Settings\ISettings;
use OCP\Util;

class AdminSettings implements ISettings {
	public function __construct(
		private IInitialState $initialState,
		private ConfigService $configService,
		private ?string $userId,
	) {
	}

	public function getForm(): TemplateResponse {
		$this->initialState->provideInitialState('admin-settings', $this->configService->getSettings($this->userId));

		Util::addScript(Application::APP_ID, Application::APP_ID . '-settings');
		Util::addStyle(Application::APP_ID, Application::APP_ID . '-settings');

		return new TemplateResponse(Application::APP_ID, 'admin-settings', []);
	}

	public function getSection(): string {
		return 'birthday_widget';
	}

	public function getPriority(): int {
		return 90;
	}
}
