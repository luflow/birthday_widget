<template>
	<div v-if="!status.globalEnabled || !status.userEnabled" class="birthday-calendar-warnings">
		<!-- Warning if birthday calendar is disabled globally -->
		<NcNoteCard v-if="!status.globalEnabled" type="warning">
			{{ t('birthday_widget', 'The birthday calendar is disabled globally by the administrator.') }}
			<div v-if="isAdmin" class="warning-actions">
				<NcButton
					type="secondary"
					:disabled="enablingGlobal"
					@click="enableGlobal">
					<template #icon>
						<NcLoadingIcon v-if="enablingGlobal" :size="20" />
					</template>
					{{ t('birthday_widget', 'Enable') }}
				</NcButton>
			</div>
		</NcNoteCard>

		<!-- Warning if birthday calendar is disabled for user -->
		<NcNoteCard v-if="!status.userEnabled" type="warning">
			{{ t('birthday_widget', 'Your personal birthday calendar is disabled.') }}
			<div class="warning-actions">
				<NcButton
					type="secondary"
					:disabled="enablingUser"
					@click="enableUser">
					<template #icon>
						<NcLoadingIcon v-if="enablingUser" :size="20" />
					</template>
					{{ t('birthday_widget', 'Enable') }}
				</NcButton>
			</div>
		</NcNoteCard>
	</div>
</template>

<script setup>
import { NcButton, NcLoadingIcon, NcNoteCard } from '@nextcloud/vue'

defineProps({
	status: {
		type: Object,
		required: true,
	},
	isAdmin: {
		type: Boolean,
		default: false,
	},
	enablingGlobal: {
		type: Boolean,
		default: false,
	},
	enablingUser: {
		type: Boolean,
		default: false,
	},
})

const emit = defineEmits(['enable-global', 'enable-user'])

const enableGlobal = () => {
	emit('enable-global')
}

const enableUser = () => {
	emit('enable-user')
}
</script>

<style scoped lang="scss">
.birthday-calendar-warnings {
	display: flex;
	flex-direction: column;
	gap: 8px;
}

.warning-actions {
	margin-top: 8px;
}
</style>
