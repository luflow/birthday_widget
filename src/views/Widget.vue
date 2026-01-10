<template>
	<div class="birthday-widget-container">
		<BirthdayCalendarWarnings
			:status="calendarStatus.status.value"
			:is-admin="calendarStatus.isAdmin"
			:enabling-global="calendarStatus.enablingGlobal.value"
			:enabling-user="calendarStatus.enablingUser.value"
			@enable-global="calendarStatus.enableGlobal"
			@enable-user="calendarStatus.enableUser" />

		<div v-if="state === 'loading'" class="loading">
			<NcLoadingIcon :size="44" />
		</div>

		<div v-else-if="isEmpty && isCalendarEnabled" class="empty-content">
			<NcEmptyContent :name="t('birthday_widget', 'No birthdays')">
				<template #icon>
					<CakeIcon />
				</template>
				<template #description>
					{{ t('birthday_widget', 'Add birthday dates to your contacts to see them here') }}
				</template>
			</NcEmptyContent>
		</div>

		<div v-else class="birthday-sections">
			<!-- Today's birthdays -->
			<div v-if="birthdays.today.length > 0" class="birthday-section birthday-section--today">
				<h3 class="section-header">
					<CakeIcon :size="18" />
					{{ t('birthday_widget', 'Today') }}
				</h3>
				<ul class="birthday-list">
					<li
						v-for="item in birthdays.today"
						:key="item.id"
						class="birthday-item birthday-item--today">
						<div class="birthday-item__content">
							<span class="birthday-item__name">{{ item.name }}</span>
							<span v-if="item.age !== null" class="birthday-item__age">
								{{ n('birthday_widget', 'Turns %n year old', 'Turns %n years old', item.age, { n: item.age }) }}
							</span>
						</div>
						<div class="birthday-item__badge">
							🎂
						</div>
					</li>
				</ul>
			</div>

			<!-- Past birthdays -->
			<div v-if="birthdays.past.length > 0" class="birthday-section">
				<h3 class="section-header">
					{{ t('birthday_widget', 'Recent') }}
				</h3>
				<ul class="birthday-list">
					<li
						v-for="item in birthdays.past"
						:key="item.id"
						class="birthday-item">
						<div class="birthday-item__content">
							<span class="birthday-item__name">{{ item.name }}</span>
							<span class="birthday-item__details">
								<span class="birthday-item__date">{{ formatDate(item.date) }}</span>
								<span v-if="item.age !== null" class="birthday-item__age">
									· {{ n('birthday_widget', 'turned %n year old', 'turned %n years old', item.age, { n: item.age }) }}
								</span>
							</span>
						</div>
					</li>
				</ul>
			</div>

			<!-- Upcoming birthdays -->
			<div v-if="birthdays.upcoming.length > 0" class="birthday-section">
				<h3 class="section-header">
					{{ t('birthday_widget', 'Upcoming') }}
				</h3>
				<ul class="birthday-list">
					<li
						v-for="item in birthdays.upcoming"
						:key="item.id"
						class="birthday-item">
						<div class="birthday-item__content">
							<span class="birthday-item__name">{{ item.name }}</span>
							<span class="birthday-item__details">
								<span class="birthday-item__date">{{ formatDate(item.date) }}</span>
								<span v-if="item.age !== null" class="birthday-item__age">
									· {{ n('birthday_widget', 'turns %n year old', 'turns %n years old', item.age, { n: item.age }) }}
								</span>
							</span>
						</div>
					</li>
				</ul>
			</div>
		</div>
	</div>
</template>

<script setup>
import { ref, computed } from 'vue'
import CakeIcon from 'vue-material-design-icons/CakeVariant.vue'
import { NcEmptyContent, NcLoadingIcon } from '@nextcloud/vue'
import { loadState } from '@nextcloud/initial-state'
import { isToday, isTomorrow, isYesterday, parseISO, differenceInDays } from 'date-fns'
import { getLocale } from '@nextcloud/l10n'
import BirthdayCalendarWarnings from '../components/BirthdayCalendarWarnings.vue'
import { useBirthdayCalendarStatus } from '../composables/useBirthdayCalendarStatus.js'

// Get user's locale for date formatting
const locale = getLocale().replace('_', '-')

// Load initial state
let initialBirthdays = { past: [], today: [], upcoming: [] }
let initialState = 'ok'
let initialBirthdayCalendarStatus = { globalEnabled: true, userEnabled: true }

try {
	initialBirthdays = loadState('birthday_widget', 'birthdays')
} catch (error) {
	console.error('Error loading birthdays:', error)
	initialState = 'error'
}

try {
	initialBirthdayCalendarStatus = loadState('birthday_widget', 'birthdayCalendarStatus')
} catch (error) {
	// Default to enabled if state not available
}

// State
const birthdays = ref(initialBirthdays)
const state = ref(initialState)
const calendarStatus = useBirthdayCalendarStatus(initialBirthdayCalendarStatus)

// Computed
const isEmpty = computed(() => {
	return birthdays.value.past.length === 0
		&& birthdays.value.today.length === 0
		&& birthdays.value.upcoming.length === 0
})

const isCalendarEnabled = computed(() => {
	return calendarStatus.status.value.globalEnabled && calendarStatus.status.value.userEnabled
})

// Methods
const formatDate = (dateStr) => {
	const date = parseISO(dateStr)
	const today = new Date()

	if (isToday(date)) {
		return t('birthday_widget', 'Today')
	}
	if (isTomorrow(date)) {
		return t('birthday_widget', 'Tomorrow')
	}
	if (isYesterday(date)) {
		return t('birthday_widget', 'Yesterday')
	}

	const daysDiff = differenceInDays(date, today)

	if (daysDiff > 0 && daysDiff <= 7) {
		// Within a week - show weekday (e.g., "Montag" or "Monday")
		return new Intl.DateTimeFormat(locale, { weekday: 'long' }).format(date)
	}

	// Show date with day and month (e.g., "28. Dez." or "Dec 28")
	return new Intl.DateTimeFormat(locale, { day: 'numeric', month: 'short' }).format(date)
}
</script>

<style scoped lang="scss">
.birthday-widget-container {
	display: flex;
	flex-direction: column;
	height: 100%;
	max-height: 450px;
	overflow-y: auto;
	padding: 0 8px;
}

.loading {
	display: flex;
	justify-content: center;
	align-items: center;
	height: 100%;
	min-height: 100px;
}

.empty-content {
	display: flex;
	justify-content: center;
	align-items: center;
	height: 100%;
	min-height: 100px;
}

.birthday-sections {
	display: flex;
	flex-direction: column;
	gap: 16px;
}

.birthday-section {
	.section-header {
		display: flex;
		align-items: center;
		gap: 6px;
		font-size: 0.85em;
		font-weight: 600;
		color: var(--color-text-maxcontrast);
		text-transform: uppercase;
		letter-spacing: 0.5px;
		margin: 0 0 8px 0;
		padding: 0 4px;
	}

	&--today .section-header {
		color: var(--color-primary-element);
	}
}

.birthday-list {
	list-style: none;
	margin: 0;
	padding: 0;
}

.birthday-item {
	display: flex;
	align-items: center;
	justify-content: space-between;
	padding: 8px 12px;
	border-radius: var(--border-radius-large);
	margin-bottom: 4px;

	&--today {
		background-color: var(--color-primary-element-light);

		.birthday-item__name {
			font-weight: 600;
		}

		.birthday-item__age {
			color: var(--color-primary-element);
		}
	}

	&__content {
		flex: 1;
		min-width: 0;
		display: flex;
		flex-direction: column;
		gap: 2px;
	}

	&__name {
		font-weight: 500;
		overflow: hidden;
		text-overflow: ellipsis;
		white-space: nowrap;
	}

	&__details {
		display: flex;
		align-items: center;
		gap: 4px;
		font-size: 0.9em;
		color: var(--color-text-lighter);
	}

	&__date {
		color: var(--color-text-lighter);
	}

	&__age {
		color: var(--color-text-lighter);
		font-size: 0.9em;
	}

	&__badge {
		flex-shrink: 0;
		font-size: 1.2em;
	}
}
</style>
