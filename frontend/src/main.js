import { createApp } from 'vue'
import { router } from './router.js'
import VueApexCharts from "vue3-apexcharts";
import VueDatePicker from '@vuepic/vue-datepicker';
import '@vuepic/vue-datepicker/dist/main.css'

import App from './App.vue'

const app = createApp(App)


app.use(router)
app.use(VueApexCharts);
app.component('VueDatePicker', VueDatePicker);


app.mount('#app')
