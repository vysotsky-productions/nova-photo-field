<template>
    <div class="flex border-b border-40 media-field">
        <div class="w-1/4 py-4"><label class="inline-block text-80 pt-2 leading-tight">
            {{field.name}}
        </label></div>
        <div class="w-3/4 py-4">
            <div class="media-image_wrap mod_field-detail" v-if="croppedMedia">
                <img class="media-image" :src="path" v-if="path">
                <a :href="downloadPath" download class="media-image_download" @click.stop>
                    <i class="el-icon-download"></i>
                </a>
            </div>
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
                croppedMedia: false,
            }
        },
        methods: {
            loadImage() {
                if (this.resourceId) {
                    let data = {
                        resourceId: this.resourceId,
                        path: this.field.params.path,
                        resourceModelName: this.field.params.resourceModelName,
                        relation: this.field.params.relation
                    };

                    axios.post('/api/media-field/get', data).then((response) => {
                        this.croppedMedia = response.data;
                    });
                }
            },
        },
        created() {
            this.loadImage();
        },
        mounted() {
            this.path = this.field.value[this.field.previewDetailUrl] || this.field.value[this.field.previewUrl] || null;
        },
        computed: {
            thumbPath() {
                return `/thumbs/${this.field.params.thumbs.adminThumbFolder}/`;
            },
            downloadPath() {
                return '/storage/' + this.croppedMedia.path + '/' + this.croppedMedia.name;
            }
        }
    }
</script>
