Vue.component('camp-details', {
    props: ['campId'],
    computed: {
        campDetails: function() {
            return {
                name: 'So-La',
                title: 'Sommerlager',
                motto: 'Römer',
                _embedded: {
                    owner: {
                        name: 'Pfadi Bewegung Schweiz'
                    },
                    periods: [ { description: 'Vorweekend', start: '2018-05-19', end: '2018-05-20' }, { description: 'Lager', start: '2018-07-14', end: '2018-07-28' } ]
                },
            }
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