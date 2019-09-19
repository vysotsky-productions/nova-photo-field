<template>
    <div class="flex border-b border-40 media-field">
        <div class="w-1/5 py-6 px-8">
            <label class="inline-block text-80 pt-2 leading-tight">
                {{ field.name }}
            </label>
        </div>

        <input type="file" ref="photo" style="display: none;" @change="loadPhoto">

        <div class="py-6 px-8 w-4/5">
            <div class="media-image_wrap mod_field-edit" v-if="path || tmpUrl">
                <img class="media-image" :src="path || tmpUrl">

                <div class="media-image_delete" @click.stop="deleteImage">
                    <icon type="delete" width="14" height="14"/>
                </div>


                <a :href="downloadPath" download class="media-image_download" @click.stop>
                    <icon type="download" width="12" height="13.5"/>
                </a>

                <media-loading :loading="loading"></media-loading>
            </div>

            <div class="avatar-uploader add-image" v-else>


                <div class="add-image_container" @click="$refs.photo.click()">
                    <span class="add-image_plus">+</span>
                </div>
            </div>
        </div>


        <!--<Cropper :cropperName="cropperName" :typeResource="typeResource"></Cropper>-->

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
    import MediaLoading from "./MediaLoading";

    const convertBlobToBase64 = blob => new Promise((resolve, reject) => {
        const reader = new FileReader;
        reader.onerror = reject;
        reader.onload = () => {
            resolve(reader.result);
        };
        reader.readAsDataURL(blob);
    });

    export default {
        components: {MediaLoading},

        mixins: [FormField, HandlesValidationErrors],

        props: ['resourceName', 'resourceId', 'field'],

        data() {
            return {
                path: null,
                tmpUrl: null,
                croppedMedia: false,
                loading: false,
                typeResource: 'mediaField',
                value: false,
                name: false
            }
        },
        methods: {
            loadPhoto(event) {
                console.log(1);

                convertBlobToBase64(event.target.files[0]).then(img => {
                    console.log(img);
                    this.tmpUrl = img;
                });

            },
            deleteImage() {
                this.path = null;
                this.tmpUrl = null;

                console.log(this.$refs);

                this.$refs.photo.value = null;

                if (!/safari/i.test(navigator.userAgent)) {
                    this.$refs.photo.type = '';
                    this.$refs.photo.type = 'file';
                }

            },
            upload(file) {  // upload file && move data to cropper
                if (file) {
                    let reader = new FileReader();
                    reader.onload = (event) => {
                        Nova.$emit(this.cropperName, {
                            fileRead: event.target.result, // dataURl new image
                            originalFile: file.file, // original new image
                            ratio: this.field.params.ratio,
                            width: 1920,
                            height: 1080,
                            path: this.field.params.path,
                            croppedMediaParams: null,
                            croppedMediaId: false,
                            title: '',
                            alt: ''
                        });
                    };
                    reader.readAsDataURL(file.file);
                }
            },
            changeCrop() { // move cropped file && data to cropper
                Nova.$emit(this.cropperName, {
                    fileRead: '/storage/' + this.croppedMedia.path + '/' + this.croppedMedia.name, // old original file, has in storage
                    originalFile: false,
                    ratio: this.field.params.ratio,
                    width: 1920,
                    height: 1080,
                    path: this.field.params.path,
                    croppedMediaParams: JSON.parse(this.croppedMedia.cropped_params),
                    croppedMediaId: this.croppedMedia.id,
                    title: this.croppedMedia.title,
                    alt: this.croppedMedia.alt
                });
            },

            /**
             * Fill the given FormData object with the field's internal value.
             */
            fill(formData) {
                formData.append(this.field.attribute, this.value || '');
            },

            /**
             * Update the field's internal value.
             */
            handleChange(value) {
                this.value = value
            },
        },
        computed: {
            cropperName() {
                return 'cropper_MediaField_' + this.field.params.relation +
                    this.field.params.resourceModelName +
                    this.resourceId;
            },
            thumbPath() {
                return `/thumbs/${this.field.params.thumbs.adminThumbFolder}/`;
            },
            downloadPath() {
                return '/storage/' + this.croppedMedia.path + '/' + this.croppedMedia.name;
            }
        },
        mounted() {
            this.path = this.field.value[this.field.previewFormUrl] || this.field.value[this.field.previewUrl] || null;

            Nova.$on(this.typeResource + '-save_' + this.cropperName, (data) => {
                this.loading = true;
                this.handleChange(data.media.id);
                this.croppedMedia = data.media;
                this.loading = false;
            });
        }
    }


</script>
