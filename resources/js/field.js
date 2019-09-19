Nova.booting((Vue, router) => {
    Vue.component('index-NovaPhotoField', require('./components/IndexField'));
    Vue.component('detail-NovaPhotoField', require('./components/DetailField'));
    Vue.component('form-NovaPhotoField', require('./components/FormField'));
});
