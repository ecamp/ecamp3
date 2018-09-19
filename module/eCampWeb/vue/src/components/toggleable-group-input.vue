<!--
Displays a group as text or as an dropdown selection field, depending on the editing prop.
You can two-way bind to the value using v-model.
TODO: Fix bug causing the dropdown to be blank when closed
-->

<template>
    <span>
        <span v-if="!editing">{{ fieldname }}: {{ value.name }}</span>
        <span v-if="editing">{{ fieldname }}: <select class="form-control" v-model="valueModel"><option v-for="group in this.allGroups" v-bind:value="group.id" v-bind:selected="group.id == valueModel.id">{{ group.name }}</option></select></span>
    </span>
</template>

<script>
    export default {
        name: "toggleable-group-input",
        props: ['editing', 'fieldname', 'value'],
        data: function() {
            return {
                allGroups: this.fetchFromAPI(),
            }
        },
        computed: {
            valueModel: {
                get: function() {
                    return this.getGroup(this.value.id);
                },
                set: function(newValue) {
                    let $this = this;
                    this.$emit('input', this.getGroup(newValue));
                },
            },
        },
        methods: {
            fetchFromAPI: function() {
                let $this = this;
                axios.get('/api/group')
                    .then(function (response) {
                        $this.allGroups = response.data._embedded.items;
                    })
                    .catch(function (error) {
                        console.log('Could get group list.');
                        $this.$emit('error', [ { type: 'danger', text: 'Could get group list. ' + error } ] );
                    });
            },
            getGroup: function(id) {
                return this.allGroups.find(function(group) { return group.id === id; });
            }
        },
    }
</script>

<style scoped>

</style>