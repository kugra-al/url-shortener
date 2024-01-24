<script setup>
import { Head, Link } from '@inertiajs/vue3';

defineProps({
    canLogin: {
        type: Boolean,
    },
    canRegister: {
        type: Boolean,
    },
});

</script>

<template>
    <Head title="Welcome" />

    <div class="bg-slate-700 h-screen px-10 py-20 w-full">
        <div v-if="canLogin" class="sm:fixed sm:top-0 sm:right-0 p-6 text-end">
            <Link
                v-if="$page.props.auth.user"
                :href="route('dashboard')"
                class="font-semibold text-gray-600 hover:text-gray-900 dark:text-gray-400 dark:hover:text-white focus:outline focus:outline-2 focus:rounded-sm focus:outline-red-500"
                >Dashboard</Link
            >

            <template v-else>
                <Link
                    :href="route('login')"
                    class="font-semibold text-gray-600 hover:text-gray-900 dark:text-gray-400 dark:hover:text-white focus:outline focus:outline-2 focus:rounded-sm focus:outline-red-500"
                    >Log in</Link
                >

                <Link
                    v-if="canRegister"
                    :href="route('register')"
                    class="ms-4 font-semibold text-gray-600 hover:text-gray-900 dark:text-gray-400 dark:hover:text-white focus:outline focus:outline-2 focus:rounded-sm focus:outline-red-500"
                    >Register</Link
                >
            </template>
        </div>
        <form v-on:submit.prevent>
            <div class="w-1/2 mx-auto py-20">
                <div class="w-full">
                    <div class="flex rounded-md overflow-hidden w-full">
                       <input
                            id="url"
                            name="url"
                            v-model="url"v-on:submit.prevent
                            class="w-full rounded-md rounded-r-none"
                            type="text"
                            placeholder="Submit url.."
                        >
                        <button @click="submit()" class="bg-indigo-600 text-white px-6 text-lg font-semibold py-4 rounded-r-md">Submit</button>
                    </div>
                </div>

                <div class="w-full block text-red-600">
                    <span v-if="errors?.url">{{ errors.url[0] }}</span>
                    <span class="text-white" v-if="success?.url">{{ success.status }}
                        <span class="text-green-600" v-if="success.safe == 1">URL is safe: <a class="text-blue-600" :href="success.short" target="_blank">{{route('url.show', success.hash)}}</a></span>
                        <span class="text-red-600" v-else-if="success.safe == 0">URL is unsafe</span>
                    </span>
                </div>
            </div>
        </form>
    </div>

</template>

<script>
    export default {
        data: function() {
            return {
                url: "",
                hash: "",
                errors: {},
                success: {}
            }
        },
        methods: {
            submit() {
                this.errors = {};
                this.success = {};
                axios.post('/url', {
                    url: this.url,
                })
                .then(response => {
                    if (response.status == 201) {
                        this.success = response.data;
                        this.hash = response.data.hash;
                        if (response.data.safe == null) {
                            setTimeout(()=>{this.check();},1000);
                        }
                    }
                })
                .catch(error => {
                    console.log(error);
                    if (error.response.status === 422) {
                        this.errors = error.response.data.errors;
                    }
                });
            },
            check() {
                axios.post('/url/check', {
                    hash: this.hash
                })
                .then(response => {
                    if (response.status == 200) {
                        if (response.data.safe == null) {
                            setTimeout(()=>{this.check();},2000);
                            this.success.status = this.success.status + ".";
                        }
                        else {
                            this.success = response.data;
                            console.log(response);
                        }
                    }
                })
                .catch(error => {
                    this.errors = error.response.data.errors;
                });
            }
        },
        mounted() {
            window.Echo.channel('url-checker')
            .listen('.UrlChecked', (e) => {
                console.log(e);
            })
        }
    };
</script>
