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
                path: ""
            }
        },

        mounted() {
            this.path = this.field.value[this.field.previewIndexUrl] || this.field.value[this.field.previewUrl] || null;
        },

        computed: {}
    }
</script>
