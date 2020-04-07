<!--
Displays a field as a date picker (can be used with v-model)
-->

<template>
  <v-menu
    ref="menu"
    v-model="showPicker"
    :disabled="disabled || readonly"
    :close-on-content-click="false"
    transition="scale-transition"
    offset-y
    offset-overflow
    min-width="290px"
    max-width="290px">
    <template v-slot:activator="{on}">
      <e-text-field
        v-model="localValue"
        v-bind="$attrs"
        readonly
        :disabled="disabled"
        outlined
        :filled="false"
        v-on="on">
        <template v-if="icon" v-slot:prepend>
          <v-icon :color="iconColor" @click="on.click">
            {{ icon }}
          </v-icon>
        </template>

        <!-- passing the append slot through -->
        <template v-slot:append>
          <slot name="append" />
        </template>
      </e-text-field>
    </template>
    <slot :localValue="localValue"
          :showPicker="showPicker"
          :on="eventHandlers" />
  </v-menu>
</template>

<script>

export default {
  name: 'BasePicker',
  inheritAttr: false,
  props: {
    value: { type: String, required: true },
    icon: { type: String, required: false, default: null },
    iconColor: { type: String, required: false, default: null },
    readonly: { type: Boolean, required: false, default: false },
    disabled: { type: Boolean, required: false, default: false }
  },
  data () {
    return {
      localValue: this.value,
      showPicker: false,
      eventHandlers: {
        save: this.save,
        close: this.close,
        input: this.input
      }
    }
  },
  watch:
  {
    value () {
      this.localValue = this.value
    },
    showPicker () {
      // save value on menu closing
      if (!this.showPicker) {
        if (this.localValue !== this.value) {
          this.$emit('input', this.localValue)
        }
      }
    }
  },
  methods: {
    close () {
      // reset local value
      this.localValue = this.value
      this.showPicker = false
    },
    save () {
      this.showPicker = false
    },
    input (value) {
      this.localValue = value
    }
  }
}
</script>
