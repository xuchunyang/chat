import "../css/app.css";
// import "./bootstrap";
import App from "./App.vue";
import { createApp } from "vue";
import { createRouter, createWebHistory } from "vue-router";
import { createPinia } from "pinia";
import { useUserStore } from "./stores/user.js";

const app = createApp(App);

const router = createRouter({
    history: createWebHistory(),
    routes: [
        {
            path: "/",
            component: () => import("./layouts/AuthenticatedLayout.vue"),
            children: [
                {
                    path: "/",
                    component: () => import("./pages/Home.vue"),
                },
                {
                    path: "/about",
                    component: () => import("./pages/About.vue"),
                },
            ],
        },
        {
            path: "/login",
            component: () => import("./layouts/GuestLayout.vue"),
            children: [
                {
                    path: "",
                    component: () => import("./pages/Login.vue"),
                },
            ],
        },
    ],
});

router.beforeEach((to, from) => {
    const userStore = useUserStore();

    if (to.path !== "/login" && !userStore.user) {
        return "/login";
    }
});

app.use(router);

app.use(createPinia());

app.mount("#app");
