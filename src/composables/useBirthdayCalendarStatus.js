import { ref } from 'vue'
import { generateRemoteUrl, generateUrl } from '@nextcloud/router'
import { getCurrentUser } from '@nextcloud/auth'
import { showError } from '@nextcloud/dialogs'
import { translate as t } from '@nextcloud/l10n'
import axios from '@nextcloud/axios'

/**
 * Composable for managing birthday calendar status and enabling it.
 *
 * @param {object} initialStatus - Initial status object { globalEnabled: boolean, userEnabled: boolean }
 * @returns {object} Reactive state and methods for birthday calendar status
 */
export function useBirthdayCalendarStatus(initialStatus = { globalEnabled: true, userEnabled: true }) {
	const currentUser = getCurrentUser()
	const isAdmin = currentUser?.isAdmin ?? false

	const status = ref({ ...initialStatus })
	const enablingGlobal = ref(false)
	const enablingUser = ref(false)

	/**
	 * Enable birthday calendar globally (admin setting)
	 * Also disables user's calendar so they have a clean state
	 */
	const enableGlobal = async () => {
		enablingGlobal.value = true
		try {
			// Enable global setting
			await axios.post(generateUrl('/apps/dav/enableBirthdayCalendar'))

			// Delete user's birthday calendar to give them a clean state
			// They will need to re-enable it themselves
			if (currentUser?.uid) {
				const calendarUrl = generateRemoteUrl('dav/calendars/' + encodeURIComponent(currentUser.uid) + '/contact_birthdays/')
				try {
					await axios.delete(calendarUrl, {
						headers: {
							'Content-Type': 'application/xml; charset=utf-8',
							'Depth': '0',
							'X-NC-CalDAV-Webcal-Caching': 'On',
						},
					})
				} catch (e) {
					// Calendar might not exist, ignore error
				}
			}

			status.value = {
				...status.value,
				globalEnabled: true,
				userEnabled: false,
			}
			// Reload the page to show user enable button
			window.location.reload()
		} catch (error) {
			console.error('Failed to enable global birthday calendar:', error)
			showError(t('birthday_widget', 'Failed to enable birthday calendar'))
			throw error
		} finally {
			enablingGlobal.value = false
		}
	}

	/**
	 * Enable birthday calendar for current user (personal setting)
	 */
	const enableUser = async () => {
		if (!currentUser?.uid) {
			console.error('No user ID available')
			return
		}

		enablingUser.value = true
		try {
			const caldavUrl = generateRemoteUrl('dav/calendars/' + encodeURIComponent(currentUser.uid) + '/')
			await axios.post(caldavUrl, '<x3:enable-birthday-calendar xmlns:x3="http://nextcloud.com/ns"/>', {
				headers: {
					'Content-Type': 'application/xml; charset=UTF-8',
					'Depth': '0',
					'X-NC-CalDAV-Webcal-Caching': 'On',
				},
			})
			status.value = {
				...status.value,
				userEnabled: true,
			}
			// Reload the page to fetch birthdays
			window.location.reload()
		} catch (error) {
			console.error('Failed to enable user birthday calendar:', error)
			showError(t('birthday_widget', 'Failed to enable birthday calendar'))
			throw error
		} finally {
			enablingUser.value = false
		}
	}

	return {
		status,
		isAdmin,
		enablingGlobal,
		enablingUser,
		enableGlobal,
		enableUser,
	}
}
