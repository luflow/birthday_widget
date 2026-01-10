<?php

declare(strict_types=1);

namespace OCA\BirthdayWidget\Settings;

use OCP\IL10N;
use OCP\IURLGenerator;
use OCP\Settings\IIconSection;

class AdminSection implements IIconSection {
	public function __construct(
		private IL10N $l,
		private IURLGenerator $urlGenerator,
	) {
	}

	/**
	 * Returns the ID of the section.
	 */
	public function getID(): string {
		return 'birthday_widget';
	}

	/**
	 * Returns the translated name as it should be displayed.
	 */
	public function getName(): string {
		return $this->l->t('Birthday widget');
	}

	/**
	 * Returns the priority for ordering in the navigation.
	 */
	public function getPriority(): int {
		return 80;
	}

	/**
	 * Returns the relative path to an icon describing the section.
	 */
	public function getIcon(): string {
		return $this->urlGenerator->imagePath('birthday_widget', 'app.svg');
	}
}
