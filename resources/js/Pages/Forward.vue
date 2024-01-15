<script setup>
import { Head, Link } from '@inertiajs/vue3';

const props = defineProps({
    url : {
        type: Text
    },
    short : {
        type: Text
    },
    safe : {
        type: Boolean
    }
});
if (props.safe) {
    setTimeout(() => {
        window.location.href = props.url
    }, 3000);
}
</script>

<template>
    <Head title="Forward" />

    <div class="bg-slate-700 px-10 py-20 w-full">
        <div v-if="safe">
            <span class="text-gray-300">Url has been checked and is safe. Redirecting to <a :href="url" class="text-blue-600">{{ url }}</a> in 3 seconds</span>
        </div>
        <div v-else>
            <span class="text-red-600">Url hasn't been checked, or is unsafe.</span>
        </div>
    </div>

</template>


