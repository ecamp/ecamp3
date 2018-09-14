Vue.component('camp-details', {
    props: ['campId'],
    data: function() {
        return {
            editing: false,
            campDetails: {},
            messages: this.fetchFromAPI(),
        }
    },
    computed: {
        ownerName: function () {
            if (this.campDetails._embedded == null) return '';
            return this.campDetails._embedded.owner.name;
        },
        periods: function () {
            if (this.campDetails._embedded == null) return [];
            return this.campDetails._embedded.periods;
        },
        buttonText: function () {
            return this.editing ? 'Speichern' : 'Bearbeiten';
        },
    },
    methods: {
        fetchFromAPI: function() {
            // Since we don't yet include vue.js with NPM and webpack, we cannot use npm modules such as vue-fetch, and
            // also cannot use advanced javascript features such as async / await (which are required for the
            // fetch API). Therefore, to prove our point, we use axios here to query our eCamp API.
            let $this = this;
            axios.get('/api/camp/' + this.campId)
                .then(function (response) {
                    $this.campDetails = response.data;
                })
                .catch(function (error) {
                    $this.messages = [ { type: 'danger', text: 'Could get camp details. ' + error } ];
                });
        },
        saveToAPI: function() {
            let $this = this;
            axios.put('/api/camp/' + this.campId, this.campDetails)
                .then(function (response) {
                    $this.messages = [ { type: 'success', text: 'Successfully saved' } ];
                    $this.campDetails = response.data;
                })
                .catch(function (error) {
                    $this.messages = [ { type: 'danger', text: 'Could not save camp details' } ];
                });
        },
        toggleEdit: function() {
            if (this.editing) {
                this.saveToAPI();
            }
            this.editing = !this.editing;
        },
    },
    template: '\
    <div>\
        <div v-for="message in messages" role="alert" class="alert" v-bind:class="\'alert-\' + message.type">{{ message.text }}</div>\
        <div class="card" style="margin-bottom: 10px">\
            <div class="card-body">\
                <form v-on:submit.prevent="toggleEdit">\
                    <button type="submit" style="float: right" class="btn btn-sm" v-bind:class="{ \'btn-primary\': editing, \'btn-outline-primary\': !editing }">{{ buttonText }}</button>\
                    Vue.js Infos zu genau einem Lager, id {{ campId }}\
                    <ul>\
                        <li>Name: {{ campDetails.name }}</li>\
                        <li><toggleable-input v-bind:editing="editing" fieldname="Titel" v-model="campDetails.title"></toggleable-input></li>\
                        <li><toggleable-input v-bind:editing="editing" fieldname="Motto" v-model="campDetails.motto"></toggleable-input></li>\
                        <li>Besitzer Name: {{ ownerName }}</li>\
                        <li>Lager-Perioden:\
                            <ul><li v-for="period in periods">{{ period.description }} ({{ period.start }} - {{ period.end }})</li></ul>\
                        </li>\
                    </ul>\
                </form>\
            </div>\
        </div>\
    </div>\
    ',
});

// A component that displays a field as text or as an input field, depending on the editing prop.
// You can two-way bind to the value using v-model.
Vue.component('toggleable-input', {
    props: ['editing', 'fieldname', 'value'],
    computed: {
        valueModel: {
            get: function() {
                return this.value;
            },
            set: function(newValue) {
                this.$emit('input', newValue);
            },
        },
    },
    template: '\
        <span>\
            <span v-bind:style="{display: editing ? \'none\' : \'\'}">{{ fieldname }}: {{ value }}</span>\
            <span v-bind:style="{display: editing ? \'\' : \'none\'}">{{ fieldname }}: <input class="form-control" v-model="valueModel"></span>\
        </span>\
    ',
});

new Vue({ el: '#camp-details' });
