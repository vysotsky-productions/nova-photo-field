<template>
    <div class="flex border-b border-40 media-field">
        <div class="w-1/4 py-4"><label class="inline-block text-80 pt-2 leading-tight">
            {{field.name}}
        </label></div>
        <div class="w-3/4 py-4">
            <div class="media-image_wrap mod_field-detail rounded overflow-hidden">
                <img class="media-image" :src="path" v-if="path">
                <!--<a :href="downloadPath" download class="media-image_download" @click.stop>-->
                    <!--<icon type="download" width="14" height="14"/>-->
                <!--</a>-->
            </div>
            <p v-if="path" class="flex items-center text-sm mt-3">
                <a
                        :href="path"
                        v-if="field.downloadable"
                        @keydown.enter.prevent="download"
                        @click.prevent="download"
                        tabindex="0"
                        class="cursor-pointer dim btn btn-link text-primary inline-flex items-center"
                >
                    <icon
                            class="mr-2"
                            type="download"
                            view-box="0 0 24 24"
                            width="16"
                            height="16"
                    ></icon>
                    <span class="class mt-1">{{ __('Download') }}</span>
                </a>
            </p>
        </div>
    </div>
</template>

<script>
    import axios from 'axios';

    export default {
        props: ['resource', 'resourceName', 'resourceId', 'field'],
        data() {
            return {
                path: null,
            }
        },
        mounted() {
            const {value, previewDetailUrl, previewUrl} = this.field;
            if (value) {
                console.log(this.field)
                this.path = value[previewDetailUrl] || value[previewUrl];
            }
        },
        methods: {
            download() {
                let link = document.createElement('a');
                link.href = this.path;
                link.download = 'download';
                document.body.appendChild(link);
                link.click();
                document.body.removeChild(link);
            },
        },
        computed: {
            // thumbPath() {
            //     return `/thumbs/${this.field.params.thumbs.adminThumbFolder}/`;
            // },
            // downloadPath() {
            //     return '/storage/' + this.croppedMedia.path + '/' + this.croppedMedia.name;
            // }
        }
    }
</script>
