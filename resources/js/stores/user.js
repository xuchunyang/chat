import { defineStore } from "pinia";
import fetchJSON from "../fetchJSON.js";
import { useLocalStorage } from "@vueuse/core";

export const useUserStore = defineStore("user", {
    state: () => ({
        isLogin: useLocalStorage("isLogin", false),
        user: useLocalStorage("user", {}),
    }),
    actions: {
        async register(name, password) {
            const { ok, data, errors } = await fetchJSON("/register", {
                method: "POST",
                body: { name, password },
                showError: false,
            });

            if (ok) {
                this.isLogin = true;
                this.user = data;
            }

            return { ok, errors };
        },
        async login(name, password) {
            const { ok, data, errors } = await fetchJSON("/login", {
                method: "POST",
                body: { name, password },
                showError: false,
            });

            if (ok) {
                this.isLogin = true;
                this.user = data;
            }

            return { ok, errors };
        },
        async logout() {
            const { ok } = await fetchJSON("/logout", {
                method: "POST",
            });

            if (ok) {
                this.isLogin = false;
                this.user = {};
            }

            return { ok };
        },
    },
});
