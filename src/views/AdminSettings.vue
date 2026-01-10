<template>
	<div class="birthday-widget-admin-settings">
		<NcSettingsSection
			:name="t('birthday_widget', 'Birthday widget')"
			:description="t('birthday_widget', 'Configure the date range for displaying birthdays in the dashboard widget.')">
			<BirthdayCalendarWarnings
				:status="calendarStatus.status.value"
				:is-admin="calendarStatus.isAdmin"
				:enabling-global="calendarStatus.enablingGlobal.value"
				:enabling-user="calendarStatus.enablingUser.value"
				@enable-global="calendarStatus.enableGlobal"
				@enable-user="calendarStatus.enableUser" />

			<div class="settings-form">
				<NcInputField
					v-model.number="settings.days_past"
					type="number"
					:label="t('birthday_widget', 'Days in the past')"
					:helper-text="t('birthday_widget', 'Show birthdays that occurred up to this many days ago')"
					:input-props="{ min: 0, max: 365 }" />

				<NcInputField
					v-model.number="settings.days_future"
					type="number"
					:label="t('birthday_widget', 'Days in the future')"
					:helper-text="t('birthday_widget', 'Show birthdays that will occur within this many days')"
					:input-props="{ min: 0, max: 365 }" />

				<div class="button-row">
					<NcButton
						variant="primary"
						:disabled="saving || !hasChanges"
						@click="saveSettings">
						<template v-if="saving" #icon>
							<NcLoadingIcon :size="20" />
						</template>
						{{ t('birthday_widget', 'Save') }}
					</NcButton>
				</div>
			</div>
		</NcSettingsSection>
	</div>
</template>

<script setup>
import { ref, computed } from 'vue'
import { NcSettingsSection, NcInputField, NcButton, NcLoadingIcon } from '@nextcloud/vue'
import { showError, showSuccess } from '@nextcloud/dialogs'
import { loadState } from '@nextcloud/initial-state'
import axios from '@nextcloud/axios'
import { generateUrl } from '@nextcloud/router'
import BirthdayCalendarWarnings from '../components/BirthdayCalendarWarnings.vue'
import { useBirthdayCalendarStatus } from '../composables/useBirthdayCalendarStatus.js'

// Load initial settings from backend (already numbers from PHP)
const loadedSettings = loadState('birthday_widget', 'admin-settings', {
	days_past: 14,
	days_future: 14,
	birthday_calendar_status: { globalEnabled: true, userEnabled: true },
})

const initialSettings = {
	days_past: loadedSettings.days_past,
	days_future: loadedSettings.days_future,
}

const calendarStatus = useBirthdayCalendarStatus(loadedSettings.birthday_calendar_status)

const settings = ref({ ...initialSettings })
const originalSettings = ref({ ...initialSettings })
const saving = ref(false)

const hasChanges = computed(() => {
	return settings.value.days_past !== originalSettings.value.days_past
		|| settings.value.days_future !== originalSettings.value.days_future
})

const saveSettings = async () => {
	saving.value = true

	try {
		const response = await axios.post(
			generateUrl('/apps/birthday_widget/api/admin/settings'),
			{
				days_past: settings.value.days_past,
				days_future: settings.value.days_future,
			}
		)

		settings.value = { ...response.data }
		originalSettings.value = { ...response.data }
		showSuccess(t('birthday_widget', 'Settings saved'))
	} catch (error) {
		console.error('Failed to save settings:', error)
		showError(t('birthday_widget', 'Failed to save settings'))
	} finally {
		saving.value = false
	}
}
</script>

<style scoped lang="scss">
.birthday-widget-admin-settings {
	.settings-form {
		max-width: 400px;
	}

	.input-field {
		margin-bottom: 24px;
	}

	.button-row {
		margin-top: 24px;
	}
}
</style>
