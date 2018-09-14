Vue.component('camp-details', {
    props: ['campId'],
    computed: {
        campDetails: function() {
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
        ownerName: function() {
            return this.campDetails._embedded.owner.name;
        },
        periods: function() {
            return this.campDetails._embedded.periods;
        },
    },
    template:
        '        <div class="card" style="margin-bottom: 10px">\n' +
        '            <div class="card-body">\n' +
        '                Vue.js Infos zu genau einem Lager, id {{ campId }}\n' +
        '                <ul>\n' +
        '                    <li>Name: {{ campDetails.name }}</li>\n' +
        '                    <li>Titel: {{ campDetails.title }}</li>\n' +
        '                    <li>Motto: {{ campDetails.motto }}</li>\n' +
        '                    <li>Besitzer Name: {{ ownerName }}</li>\n' +
        '                    <li>Lager-Perioden:' +
        '                        <ul><li v-for="period in periods">{{ period.description }} ({{ period.start }} - {{ period.end }})</li></ul>' +
        '                    </li>\n' +
        '                </ul>\n' +
        '            </div>\n' +
        '        </div>'
});

new Vue({ el: '#camp-details' });
