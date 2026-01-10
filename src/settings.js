import { createApp } from 'vue'
import { translate, translatePlural } from '@nextcloud/l10n'
import AdminSettings from './views/AdminSettings.vue'

const app = createApp(AdminSettings)

app.config.globalProperties.t = translate
app.config.globalProperties.n = translatePlural

app.mount('#birthday-widget-admin-settings')
