Vue.component('camp-details', {
    props: ['campId'],
    data: function() {
        return {
            editing: false,
            campDetails: {},
        }
    },
    created: function() {
        this.campDetails = this.fetchFromAPI();
    },
    computed: {
        ownerName: function () {
            return this.campDetails._embedded.owner.name;
        },
        periods: function () {
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
            // fetch API). Therefore, to prove our point, we use a good old-fashioned XMLHttpRequest here to query our
            // eCamp API.
            var request = new XMLHttpRequest();
            request.open("GET", "/api/camp/" + this.campId, false);
            request.send();

            if (request.status !== 200) {
                alert('can not get camp details');
                return {};
            }

            return JSON.parse(request.responseText);
        },
        saveToAPI: function() {
            var request = new XMLHttpRequest();
            request.open("PUT", "/api/camp/" + this.campId, false);
            request.send(this.campDetails);

            if (request.status !== 200) {
                console.log('can not save camp details');
                return this.campDetails;
            }

            console.log(request.responseText);
            return JSON.parse(request.responseText);
        },
        toggleEdit: function() {
            if (this.editing) {
                this.campDetails = this.saveToAPI();
            }
            this.editing = !this.editing;
        },
    },
    template: '\
        <div class="card" style="margin-bottom: 10px">\
            <div class="card-body">\
                <button style="float: right" class="btn btn-sm" v-bind:class="{ \'btn-primary\': editing, \'btn-outline-primary\': !editing }" v-on:click="toggleEdit">{{ buttonText }}</button>\
                Vue.js Infos zu genau einem Lager, id {{ campId }}\
                <ul v-bind:style="{display: editing ? \'none\' : \'\'}">\
                    <li>Name: {{ campDetails.name }}</li>\
                    <li>Titel: {{ campDetails.title }}</li>\
                    <li>Motto: {{ campDetails.motto }}</li>\
                    <li>Besitzer Name: {{ ownerName }}</li>\
                    <li>Lager-Perioden:\
                        <ul><li v-for="period in periods">{{ period.description }} ({{ period.start }} - {{ period.end }})</li></ul>\
                    </li>\
                </ul>\
                <ul v-bind:style="{display: editing ? \'\' : \'none\'}">\
                    <li>Name: {{ campDetails.name }}</li>\
                    <li>Titel: <input class="form-control" v-model="campDetails.title"></li>\
                    <li>Motto: <input class="form-control" v-model="campDetails.motto"></li>\
                    <li>Besitzer Name: {{ ownerName }}</li>\
                    <li>Lager-Perioden:\
                        <ul><li v-for="period in periods">{{ period.description }} ({{ period.start }} - {{ period.end }})</li></ul>\
                    </li>\
                </ul>\
            </div>\
        </div>\
        ',
});

new Vue({ el: '#camp-details' });
