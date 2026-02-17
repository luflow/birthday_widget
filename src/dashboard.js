import './dashboard-icon.css'
import { createApp } from 'vue'
import { translate, translatePlural } from '@nextcloud/l10n'
import BirthdayWidget from './views/Widget.vue'

document.addEventListener('DOMContentLoaded', () => {
	OCA.Dashboard.register('birthday-widget', (el, { widget }) => {
		const app = createApp(BirthdayWidget, {
			title: widget.title,
		})

		app.config.globalProperties.t = translate
		app.config.globalProperties.n = translatePlural

		app.mount(el)
	})
})
