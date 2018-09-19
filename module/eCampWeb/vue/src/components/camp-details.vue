<!--
Displays details on a single camp (with id specified as prop campId) and allows to edit them.
-->

<template>
    <div>
        <div v-for="message in messages" role="alert" class="alert" v-bind:class="'alert-' + message.type">{{ message.text }}
        </div>
        <div class="card camp-detail-card">
            <div class="card-body">
                <form v-on:submit.prevent="toggleEdit">
                    <button type="submit" class="btn btn-sm camp-detail-submit-button"
                            v-bind:class="{ 'btn-primary': editing, 'btn-outline-primary': !editing }">{{ buttonText }}
                    </button>
                    Vue.js Infos zu genau einem Lager, id {{ campId }}
                    <ul>
                        <li>Name: {{ campDetails.name }}</li>
                        <li>
                            <toggleable-input v-bind:editing="editing" fieldname="Titel"
                                              v-model="campDetails.title"></toggleable-input>
                        </li>
                        <li>
                            <toggleable-input v-bind:editing="editing" fieldname="Motto"
                                              v-model="campDetails.motto"></toggleable-input>
                        </li>
                        <li>
                            <toggleable-group-input v-if="campDetails._embedded" v-bind:editing="editing"
                                                    fieldname="Besitzer"
                                                    v-model="campDetails._embedded.owner"></toggleable-group-input>
                        </li>
                        <li>Lager-Perioden:
                            <ul>
                                <li v-for="period in periods">{{ period.description }} ({{ period.start }} - {{ period.end }})
                                </li>
                            </ul>
                        </li>
                    </ul>
                </form>
            </div>
        </div>
    </div>
</template>

<script>
    import ToggleableInput from './toggleable-input.vue';
    import ToggleableGroupInput from './toggleable-group-input.vue';
    import axios from 'axios';

    export default {
        name: 'camp-details',
        components: {
            ToggleableInput,
            ToggleableGroupInput,
        },
        props: ['campId'],
        data() {
            return {
                editing: false,
                campDetails: {},
                messages: this.fetchFromAPI(),
            }
        },
        computed: {
            periods() {
                if (this.campDetails._embedded == null) return [];
                return this.campDetails._embedded.periods;
            },
            buttonText() {
                return this.editing ? 'Speichern' : 'Bearbeiten';
            },
        },
        methods: {
            fetchFromAPI() {
                // TODO: Use an NPM plugin for REST interfaces instead of raw axios?
                axios.get('/api/camp/' + this.campId)
                    .then((response) => this.campDetails = response.data)
                    .catch((error) => this.messages = [{type: 'danger', text: 'Could get camp details. ' + error}] );
            },
            saveToAPI() {
                axios.patch('/api/camp/' + this.campId, this.campDetails)
                    .then((response) => {
                        this.messages = [{type: 'success', text: 'Successfully saved'}];
                        this.campDetails = response.data;
                    })
                    .catch((error) => this.messages = [{type: 'danger', text: 'Could not save camp details. ' + error}] );
            },
            toggleEdit() {
                if (this.editing) {
                    this.saveToAPI();
                }
                this.editing = !this.editing;
            },
        },
    }
</script>

<style scoped>
    .camp-detail-card {
        margin-bottom: 10px;
    }
    .camp-detail-submit-button {
        float: right;
    }
</style>