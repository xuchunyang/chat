<script setup>
import { useUserStore } from "../stores/user.js";
import { useRouter } from "vue-router";
import { ChevronDownIcon } from "@heroicons/vue/24/outline";

const router = useRouter();
const userStore = useUserStore();

const logout = () => {
    userStore.logout();
    router.push({ name: "login" });
};
</script>

<template>
    <div
        class="fixed right-2 top-2 z-10 bg-white/90 text-sm leading-none text-gray-700"
    >
        <div class="group relative">
            <div
                class="flex items-center gap-1 rounded px-4 py-3 transition group-hover:bg-gray-100"
            >
                <span>{{ userStore.user.name }}</span>
                <ChevronDownIcon class="size-4" />
            </div>
            <div
                class="invisible absolute right-0 top-full w-max min-w-full group-hover:visible"
            >
                <div
                    class="mt-1 rounded border border-gray-200 bg-white shadow"
                >
                    <button
                        class="block w-full px-4 py-3 transition hover:bg-gray-100 hover:text-blue-600"
                        @click="logout"
                    >
                        退出登陆
                    </button>
                </div>
            </div>
        </div>
    </div>

    <router-view />
</template>
