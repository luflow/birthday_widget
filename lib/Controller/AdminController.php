<?php

declare(strict_types=1);

namespace OCA\BirthdayWidget\Controller;

use OCA\BirthdayWidget\Service\ConfigService;
use OCP\AppFramework\Controller;
use OCP\AppFramework\Http\Attribute\AuthorizedAdminSetting;
use OCP\AppFramework\Http\DataResponse;
use OCP\IRequest;

class AdminController extends Controller {
	public function __construct(
		string $appName,
		IRequest $request,
		private ConfigService $configService,
	) {
		parent::__construct($appName, $request);
	}

	/**
	 * Get admin settings.
	 */
	#[AuthorizedAdminSetting(settings: \OCA\BirthdayWidget\Settings\AdminSettings::class)]
	public function getSettings(): DataResponse {
		return new DataResponse($this->configService->getSettings());
	}

	/**
	 * Update admin settings.
	 */
	#[AuthorizedAdminSetting(settings: \OCA\BirthdayWidget\Settings\AdminSettings::class)]
	public function setSettings(int $days_past, int $days_future): DataResponse {
		$this->configService->setSettings([
			'days_past' => $days_past,
			'days_future' => $days_future,
		]);

		return new DataResponse($this->configService->getSettings());
	}
}
