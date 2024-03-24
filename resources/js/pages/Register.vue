<script setup>
import { useRouter } from "vue-router";
import { useUserStore } from "../stores/user.js";
import { ref } from "vue";
import ShowErrors from "../components/ShowErrors.vue";

const router = useRouter();
const userStore = useUserStore();

const form = ref({
    name: "John Doe",
    password: "top-secret",
});
const errors = ref({
    // name: ["The name field is required."],
});

const register = async () => {
    const { ok, errors: registerErrors } = await userStore.register(
        form.value.name,
        form.value.password,
    );

    if (!ok) {
        errors.value = registerErrors;
        return;
    }

    await router.push("/");
};
</script>

<template>
    <div>
        <form
            @submit.prevent="register"
            class="mx-auto mt-[100px] space-y-4 rounded-md border p-8 shadow-xl sm:max-w-[500px]"
        >
            <h1 class="text-2xl font-bold">注册</h1>
            <div class="space-y-2">
                <label class="block" for="name">用户名</label>
                <input
                    class="w-full rounded"
                    id="name"
                    type="text"
                    placeholder="用户名"
                    v-model="form.name"
                    required
                    :class="{ 'border-red-500': errors.name }"
                />
                <ShowErrors :errors="errors.name" />
            </div>
            <div class="space-y-2">
                <label class="block" for="password">密码</label>
                <input
                    class="w-full rounded"
                    id="password"
                    type="password"
                    placeholder="密码"
                    v-model="form.password"
                    required
                    :class="{ 'border-red-500': errors.password }"
                />
                <ShowErrors :errors="errors.password" />
            </div>
            <div>
                <button class="w-full rounded-md bg-blue-500 py-2 text-white">
                    注册
                </button>
            </div>
            <div>
                <router-link to="/auth/login" class="text-blue-500"
                    >登陆
                </router-link>
            </div>
        </form>
    </div>
</template>
