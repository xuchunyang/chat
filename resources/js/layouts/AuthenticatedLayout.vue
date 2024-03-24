<script setup>
import { useUserStore } from "../stores/user.js";
import { useRouter } from "vue-router";

const router = useRouter();
const userStore = useUserStore();

const logout = () => {
    userStore.logout();

    // Redirect to login page
    router.push("/login");
};
</script>

<template>
    <h1>this is authenticated layout</h1>
    <pre>{{ { user: userStore.user } }}</pre>

    <div>
        <nav>
            <router-link class="p-2" to="/">Home</router-link>
            <router-link class="p-2" to="/about">About</router-link>
            <button @click="logout">Logout</button>
        </nav>

        <router-view />
    </div>
</template>
