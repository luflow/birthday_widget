<?php

declare(strict_types=1);

namespace OCA\BirthdayWidget\Controller;

use OCA\BirthdayWidget\Service\BirthdayService;
use OCP\AppFramework\Controller;
use OCP\AppFramework\Http\Attribute\NoAdminRequired;
use OCP\AppFramework\Http\DataResponse;
use OCP\IRequest;
use OCP\IUserSession;

class BirthdayController extends Controller {
	public function __construct(
		string $appName,
		IRequest $request,
		private BirthdayService $birthdayService,
		private IUserSession $userSession,
	) {
		parent::__construct($appName, $request);
	}

	/**
	 * Get birthdays for the widget.
	 */
	#[NoAdminRequired]
	public function widget(): DataResponse {
		$user = $this->userSession->getUser();
		if ($user === null) {
			return new DataResponse(['error' => 'Not authenticated'], 401);
		}

		$birthdays = $this->birthdayService->getBirthdays($user->getUID());
		return new DataResponse($birthdays);
	}
}
