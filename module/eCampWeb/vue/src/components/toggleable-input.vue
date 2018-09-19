<!--
Displays a field as text or as an input field, depending on the editing prop.
You can two-way bind to the value using v-model.
-->

<template>
    <span>
        <span v-if="!editing">{{ fieldname }}: {{ value }}</span>
        <span v-if="editing">{{ fieldname }}: <input class="form-control" v-model="valueModel"></span>
    </span>
</template>

<script>
    export default {
        name: "toggleable-input",
        props: ['editing', 'fieldname', 'value'],
        computed: {
            valueModel: {
                get() {
                    return this.value;
                },
                set(newValue) {
                    this.$emit('input', newValue);
                },
            },
        },
    }
</script>

<style scoped>

</style>