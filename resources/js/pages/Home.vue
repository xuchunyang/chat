<script setup>
import fetchJSON from "../fetchJSON.js";

const hello = async () => {
    const data = await fetchJSON("/rooms");
    console.log(data);
};

// hello();
</script>

<template>
    <h1>This is the home page!</h1>
</template>
