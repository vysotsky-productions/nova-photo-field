<template>
    <div>

        <modal @modal-close="closeModal">
            <card class="text-center m-2 bg-white rounded-lg shadow-lg overflow-hidden">
                <vue-cropper
                        ref="cropper"
                        :view-mode="1"
                        :rotatable="false"
                        :scalable="false"
                        :zoomable="false"
                        :movable="false"
                        :src="imgSrc"
                        alt="Source Image"
                        class="max-w-3xl"
                >
                </vue-cropper>

                <div class="actions m-8">
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
        props: ['imgSrc', 'extension'],

        methods: {
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

</style>