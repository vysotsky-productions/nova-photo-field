<template>
    <div class="flex border-b border-40 media-field">
        <div class="w-1/5 py-6 px-8">
            <label class="inline-block text-80 pt-2 leading-tight">
                {{ field.name }}
            </label>
        </div>

        <input type="file" ref="photo" style="display: none;" @change="loadPhoto">

        <div class="py-6 px-8 w-4/5">
            <div @click.self="openCropper" class="media-image_wrap mod_field-edit" v-if="original">
                <img class="media-image" :src="preview || original">

                <div class="media-image_delete" @click.stop="deleteImage">
                    <icon type="delete" width="14" height="14"/>
                </div>


                <a :href="downloadPath" download class="media-image_download" @click.stop>
                    <icon type="download" width="12" height="13.5"/>
                </a>

            </div>

            <div class="avatar-uploader add-image" v-else>


                <div class="add-image_container" @click="$refs.photo.click()">
                    <span class="add-image_plus">+</span>
                </div>
            </div>
        </div>

        <cropper v-if="showCropper && original && useCropper"
                 :img-src="original"
                 :extension="extension"
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

                newPhoto: null,
                cropData: null,
                showCropper: false,

                deleteId: null,
            }
        },
        methods: {
            openCropper() {
                this.showCropper = true;
            },
            saveNewCropData({cropBoxData, dataUrl}) {
                this.cropData = cropBoxData;
                this.preview = dataUrl;
            },
            loadPhoto({target}) {
                let file = target.files[0];

                convertBlobToBase64(file).then(img => {
                    this.original = img;
                    this.preview = img;
                    this.newPhoto = file;
                    this.extension = getFileExtension(file.name)
                    console.log(this.newPhoto)
                });

            },
            setMediaToDelete() {
                if (this.mediaId) this.deleteId = this.mediaId;
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

            if (value) {
                this.mediaId = value.id;
                this.original = value[previewUrl] || null;
                this.preview = value[previewFormUrl] || value[previewUrl] || null;
            }
        }
    }


</script>
