<script setup>
import { ExclamationCircleIcon } from "@heroicons/vue/24/outline";

defineProps({
    errors: Array,
});
</script>

<template>
    <template v-if="errors">
        <div class="space-y-1">
            <div
                v-for="error in errors"
                role="alert"
                class="flex items-center gap-1 text-sm text-rose-600 bg-rose-50 p-2 rounded-md"
            >
                <ExclamationCircleIcon class="size-4" />
                <span>{{ error }}</span>
            </div>
        </div>
    </template>
</template>
