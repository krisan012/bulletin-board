<template>
    <div>
        <!-- Include the Navbar -->
        <Navbar />

        <!-- Main content will go here -->
        <router-view></router-view>
    </div>
</template>

<script setup>
import Navbar from './Navbar.vue';
import {ref} from "vue";

const user = ref(window.Laravel.user);

</script>

<style>
.container {
    max-width: 800px;
}
</style>
