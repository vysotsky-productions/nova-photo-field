<template>
    <div>

        <modal @modal-close="closeModal">
            <card class="text-center m-2 mb-0 bg-white rounded-lg max-w-2xl p-view shadow-lg overflow-hidden test">

                <vue-cropper class="cropper-wrapper"
                             ref="cropper"
                             :view-mode="1"
                             :rotatable="false"
                             :scalable="false"
                             :zoomable="false"
                             :movable="false"
                             :aspectRatio="aspectRatio"
                             :src="imgSrc"
                             :background="false"
                             alt="Source Image"
                >
                </vue-cropper>


                <div class="actions mt-8">
                    <button @click.prevent="handleCrop" class="btn btn-default btn-primary">Обрезать</button>
                    <button @click.prevent="exitFromCropper" class="btn btn-default btn-primary">Отмена</button>
                </div>
            </card>
        </modal>
    </div>
</template>
<script>
    import VueCropper from 'vue-cropperjs';
    import 'cropperjs/dist/cropper.css';
    //
    export default {
        components: {VueCropper},
        props: ['imgSrc', 'extension', 'aspectRatio', 'cropData'],
        mounted() {
            const handler = () => {
                this.handleSetCropData(this.$refs.cropper, this.cropData)
            };
            document.addEventListener('ready', handler);
            this.$on('hook:destroyed', () => {
                document.removeEventListener('ready', handler)
            })
        },
        methods: {
            handleSetCropData(cropper, data) {
                cropper.setData(data);
            },
            handleCrop() {

                let cropResult = {
                    cropBoxData: this.$refs.cropper.getData(true),
                    dataUrl: this.$refs.cropper.getCroppedCanvas().toDataURL(this.extension)
                };

                this.$emit('cropped', cropResult);
                this.closeModal();
            },
            exitFromCropper() {
                this.closeModal();
            },
            closeModal() {
                this.$emit('close')
            }

        }
    }
</script>
<style lang="scss">

    .cropper-wrapper {
        max-width:  80vw !important;
        max-height: 60vh !important;
    }

    img {
        max-width: 100%; /* This rule is very important, please do not ignore this! */
    }
</style>
