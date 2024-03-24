<script setup>
import { useRouter } from "vue-router";
import { useUserStore } from "../stores/user.js";

const router = useRouter();
const userStore = useUserStore();

const login = async () => {
    const ok = await userStore.login("test@example.com", "top-secret");

    if (!ok) {
        return;
    }

    await router.push("/");
};
</script>

<template>
    <!--    Add goto / -->
    <router-link to="/">Home</router-link>

    <h1>This is the login page!</h1>

    <button @click="login">Login</button>
</template>
