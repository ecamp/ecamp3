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
        v-model="localValueFormatted"
        v-bind="$attrs"
        readonly
        :disabled="disabled"
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
    disabled: { type: Boolean, required: false, default: false },
    format: { type: Function, required: false, default: null },
    parse: { type: Function, required: false, default: null }
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
  computed: {
    localValueFormatted: {
      get () {
        if (this.format != null) {
          return this.format(this.localValue)
        } else {
          return this.localValue
        }
      },
      set (val) {
        if (this.parse != null) {
          this.localValue = this.parse(val)
        } else {
          this.localValue = val
        }
      }
    }
  },
  watch: {
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
