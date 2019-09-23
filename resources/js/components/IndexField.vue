<template>
    <div class="media-image_wrap mod_field-index" v-if="path">
        <img class="media-image-list" :src="path">
    </div>
</template>

<script>
    export default {
        props: ['resourceName', 'field'],

        data() {
            return {
                path: null
            }
        },

        mounted() {
            const {value, previewIndexUrl, previewUrl} = this.field;
            if (value) {
                this.path = value[previewIndexUrl] || value[previewUrl];
            }
        },

        computed: {}
    }
</script>
