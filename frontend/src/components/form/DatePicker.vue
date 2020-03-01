<!--
Displays a field as a date picker (can be used with v-model)
-->

<template>
  <v-menu
    ref="menu"
    v-model="showDatePicker"
    :close-on-content-click="false"
    transition="scale-transition"
    offset-y
    min-width="290px">
    <template v-slot:activator="{on}">
      <v-text-field
        v-model="date"
        v-bind="$attrs"
        prepend-icon="mdi-calendar"
        readonly
        v-on="on">
        <!-- passing the append slot through -->
        <template v-slot:append>
          <slot name="append" />
        </template>
      </v-text-field>
    </template>
    <v-date-picker v-model="date" no-title scrollable>
      <v-spacer />
      <v-btn text color="primary" @click="close">Cancel</v-btn>
      <v-btn text color="primary" @click="save">OK</v-btn>
    </v-date-picker>
  </v-menu>
</template>

<script>

export default {
  name: 'DatePicker',
  inheritAttr: false,
  props: {
    value: { type: String, required: true }
  },
  data () {
    return {
      date: this.value,
      showDatePicker: false
    }
  },
  watch:
  {
    value () {
      this.date = this.value
    }
  },
  methods: {
    close () {
      this.showDatePicker = false
      this.date = this.value
    },
    save () {
      this.showDatePicker = false
      this.$emit('input', this.date)
    }
  }
}
</script>

<style scoped>
</style>
