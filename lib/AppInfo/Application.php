<?php

declare(strict_types=1);

namespace OCA\BirthdayWidget\AppInfo;

use OCA\BirthdayWidget\Dashboard\Widget;
use OCP\AppFramework\App;
use OCP\AppFramework\Bootstrap\IBootContext;
use OCP\AppFramework\Bootstrap\IBootstrap;
use OCP\AppFramework\Bootstrap\IRegistrationContext;

class Application extends App implements IBootstrap {
	public const APP_ID = 'birthday_widget';

	public function __construct(array $urlParams = []) {
		parent::__construct(self::APP_ID, $urlParams);
	}

	public function register(IRegistrationContext $context): void {
		$context->registerDashboardWidget(Widget::class);
	}

	public function boot(IBootContext $context): void {
		// No additional boot logic needed
	}
}
