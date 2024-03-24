import { defineStore } from "pinia";
import fetchJSON from "../fetchJSON.js";

export const useUserStore = defineStore("user", {
    state: () => ({
        user: null,
    }),
    actions: {
        async login(email, password) {
            const { ok, data } = await fetchJSON("/login", {
                method: "POST",
                body: { email, password },
            });

            if (ok) {
                this.user = data;
            }

            return { ok };
        },
        async logout() {
            const { ok } = await fetchJSON("/logout", {
                method: "POST",
            });

            if (ok) {
                this.user = null;
            }

            return { ok };
        },
    },
});
