import "../css/app.css";
import "floating-vue/dist/style.css";
import "./bootstrap";
import App from "./App.vue";
import { createApp } from "vue";
import { createRouter, createWebHashHistory } from "vue-router";
import { createPinia } from "pinia";
import { useUserStore } from "./stores/user.js";
import { autoAnimatePlugin } from "@formkit/auto-animate/vue";

const app = createApp(App);

const router = createRouter({
    history: createWebHashHistory(),
    routes: [
        {
            path: "/",
            component: () => import("./layouts/AuthenticatedLayout.vue"),
            children: [
                {
                    path: "/",
                    component: () => import("./pages/Chat.vue"),
                },
                {
                    path: "/about",
                    component: () => import("./pages/About.vue"),
                },
            ],
        },
        {
            path: "/auth",
            component: () => import("./layouts/GuestLayout.vue"),
            children: [
                {
                    path: "/auth/login",
                    name: "login",
                    component: () => import("./pages/Login.vue"),
                },
                {
                    path: "/auth/register",
                    name: "register",
                    component: () => import("./pages/Register.vue"),
                },
            ],
        },
    ],
});

router.beforeEach((to) => {
    const userStore = useUserStore();

    if (to.name === "login" || to.name === "register") {
        return;
    }

    if (!userStore.isLogin) {
        return { name: "login" };
    }
});

app.use(router);

app.use(createPinia());

app.use(autoAnimatePlugin);

app.mount("#app");
