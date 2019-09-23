<template>
    <div class="flex border-b border-40 media-field">
        <div class="w-1/5 py-6 px-8">
            <label class="inline-block text-80 pt-2 leading-tight">
                {{ field.name }}
            </label>
        </div>

        <input type="file" ref="photo" style="display: none;" @change="loadPhoto">

        <div class="py-6 px-8 w-4/5">
            <div v-if="original"
                 @click="openCropper"
                 style="max-width: 320px"
                 class="card relative card relative border border-lg border-50 overflow-hidden px-0 py-0"
            >
                <img class="block w-full" :src="preview || original">
            </div>

            <div v-else
                 @drop.prevent="loadPhoto" @dragover.prevent
                 class="border border-primary-30% flex hover:border-primary overflow-hidden rounded relative text-primary-30% hover:text-primary"
                 style="width: 250px; height: 250px"
                 @click="$refs.photo.click()">
                <icon type="add" width="50" height="50" class="m-auto"/>
            </div>
            <p v-if="preview || original" class="flex items-center text-sm mt-3">
                <a
                        :href="preview || original"
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
                <button
                        type="button"
                        @keydown.enter.prevent="deleteImage"
                        @click.prevent="deleteImage"
                        tabindex="0"
                        class="cursor-pointer dim btn btn-link inline-flex items-center text-danger ml-8"
                >
                    <icon type="delete" class="mr-2" view-box="0 0 20 20" width="16" height="16"/>
                    <span class="class mt-1">{{ __('Delete') }}</span>
                    <slot/>
                </button>
            </p>
        </div>


        <!--<div class="media-image_delete" @click.stop="deleteImage">-->
        <!--<icon type="delete" width="14" height="14"/>-->
        <!--</div>-->


        <!--<a :href="downloadPath" download class="media-image_download" @click.stop>-->
        <!--<icon type="download" width="12" height="13.5"/>-->
        <!--</a>-->

        <cropper v-if="showCropper && original && useCropper"
                 :img-src="original"
                 :extension="extension"
                 :aspectRatio="aspectRatio"
                 @cropped="saveNewCropData"
                 @close="showCropper = false"
        ></cropper>

        <div style="display: none;visibility: hidden;opacity: 0">
            <default-field :field="field" :errors="errors">
                <template slot="field">
                    <input :id="field.attribute" type="text"
                           class="w-full form-control form-input form-input-bordered"
                           :class="errorClasses"
                           :placeholder="field.name"
                           v-model="value"
                    />
                </template>
            </default-field>
        </div>
    </div>
</template>

<script>
    import Vue from 'vue';

    Vue.config.devtools = true;

    import {FormField, HandlesValidationErrors} from 'laravel-nova'

    import Cropper from "./Cropper"
    import {convertBlobToBase64} from "../utils/convertBlobToBase64";
    import getFileExtension from "../utils/getFileExtension";


    export default {
        components: {Cropper},

        mixins: [FormField, HandlesValidationErrors],

        props: ['resourceName', 'resourceId', 'field'],

        data() {
            return {
                mediaId: null,

                original: null,
                preview: null,

                value: false,
                name: false,

                useCropper: false,
                aspectRatio: null,

                newPhoto: null,
                cropData: null,
                showCropper: false,

                deleteId: null,
            }
        },
        methods: {
            download() {
                let link = document.createElement('a');
                link.href = this.preview || this.original;
                link.download = 'download';
                document.body.appendChild(link);
                link.click();
                document.body.removeChild(link);
            },
            openCropper() {
                this.showCropper = true;
            },
            saveNewCropData({cropBoxData, dataUrl}) {
                this.cropData = cropBoxData;
                this.preview = dataUrl;
            },
            loadPhoto(e) {
                let file = e.dataTransfer ? e.dataTransfer.files[0] : e.target.files[0];
                if(!file) return;

                convertBlobToBase64(file).then(img => {
                    this.original = img;
                    this.preview = img;
                    this.newPhoto = file;
                    this.extension = getFileExtension(file.name)
                    console.log(this.newPhoto)
                });

            },
            setMediaToDelete() {
                if (this.mediaId) {
                    this.deleteId = this.mediaId;
                    this.mediaId = null
                }
            },
            deleteImage() {

                this.setMediaToDelete();

                this.original = null;
                this.preview = null;

                this.newPhoto = null;
                this.cropData = null;
                this.extension = null;

                this.$refs.photo.value = null;

                if (!/safari/i.test(navigator.userAgent)) {
                    this.$refs.photo.type = '';
                    this.$refs.photo.type = 'file';
                }

            },
            /**
             * Fill the given FormData object with the field's internal value.
             */
            fill(formData) {
                formData.append(`${this.field.attribute}_crop_data`, this.requestData || '');
                formData.append(`${this.field.attribute}_file`, this.newPhoto || '');
                formData.append(`${this.field.attribute}_delete_id`, this.deleteId || '');
                formData.append(`${this.field.attribute}_update_id`, this.mediaId || '');
            },

            /**
             * Update the field's internal value.
             */
            handleChange(value) {
                this.value = value
            },
        },
        computed: {
            requestData() {
                return JSON.stringify(this.cropData)
            }

        },
        mounted() {
            console.log(this.field)

            const {value, previewFormUrl, previewUrl} = this.field;

            this.useCropper = this.field.useCropper;
            this.aspectRatio = this.field.aspectRatio;

            if (value) {
                this.mediaId = value.id;
                this.original = value[previewUrl] || null;
                this.preview = value[previewFormUrl] || value[previewUrl] || null;
            }
        }
    }


</script>
