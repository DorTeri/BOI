import { createRouter, createWebHashHistory } from 'vue-router'

import Home from './views/Home.vue'
import Stats from './views/Stats.vue'

const routes = [
    {
        path: '/',
        name: 'Home',
        component: Home
    },
    {
        path: '/stats',
        name: 'Stats',
        component: Stats
    }
]

export const router = createRouter({
    routes,
    history: createWebHashHistory()
    // base: process.env.BASE_URL,
})