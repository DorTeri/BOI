import { createApp } from 'vue'
import { router } from './router.js'
import VueApexCharts from "vue3-apexcharts";
import VueDatePicker from '@vuepic/vue-datepicker';
import '@vuepic/vue-datepicker/dist/main.css'
import Notifications from '@kyvg/vue3-notification'

import App from './App.vue'

const app = createApp(App)


app.use(router)
app.use(VueApexCharts);
app.use(Notifications)
app.component('VueDatePicker', VueDatePicker);


app.mount('#app')
