<!--
Displays a field as a time picker (can be used with v-model)
Allows 15min steps only
-->

<template>
  <v-menu
    ref="menu"
    v-model="showTimePicker"
    :close-on-content-click="false"
    transition="scale-transition"
    offset-y
    min-width="290px">
    <template v-slot:activator="{on}">
      <v-text-field
        v-model="time"
        v-bind="$attrs"
        prepend-icon="mdi-clock-outline"
        readonly
        v-on="on">
        <!-- passing the append slot through -->
        <template v-slot:append>
          <slot name="append" />
        </template>
      </v-text-field>
    </template>
    <v-time-picker
      v-model="time"
      :allowed-minutes="allowedStep"
      format="24hr"
      scrollable>
      <v-spacer />
      <v-btn text color="primary" @click="close">Cancel</v-btn>
      <v-btn text color="primary" @click="save">OK</v-btn>
    </v-time-picker>
  </v-menu>
</template>

<script>

export default {
  name: 'TimePicker',
  inheritAttr: false,
  props: {
    value: { type: String, required: true }
  },
  data () {
    return {
      time: this.value,
      showTimePicker: false
    }
  },
  watch:
  {
    value () {
      this.time = this.value
    }
  },
  methods: {
    allowedStep: m => m % 15 === 0,
    close () {
      this.showTimePicker = false
      this.time = this.value
    },
    save () {
      this.showTimePicker = false
      this.$emit('input', this.time)
    }
  }
}
</script>

<style scoped>
</style>
