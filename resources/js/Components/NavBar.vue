<template>
    <div class="relative w-full">
        <NavLink v-for="link in props.links" :key="link" :href="route(link.route)" :active="route().current(link.route)">
            {{link.name}}
        </NavLink>
    </div>
</template>

<script setup>
import NavLink from "@/Components/NavLink.vue";

const props = defineProps({
    links: {
        type: Array,
        default: []
    },
});
</script>

<style scoped>

</style>
