<?php

return [
	'routes' => [
		// API for widget data refresh
		['name' => 'birthday#widget', 'url' => '/api/birthdays/widget', 'verb' => 'GET'],

		// Admin settings API
		['name' => 'admin#getSettings', 'url' => '/api/admin/settings', 'verb' => 'GET'],
		['name' => 'admin#setSettings', 'url' => '/api/admin/settings', 'verb' => 'POST'],
	]
];
