import Vue from 'vue'
import CampDetails from './components/camp-details'

new Vue({
    el: '#camp-details',
    render(h) {
        return h(CampDetails, { props: this.$el.dataset } );
    },
});
