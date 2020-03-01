<!--
Displays a field as a color picker (can be used with v-model)
-->

<template>
  <div>
    <v-menu
      ref="menu"
      v-model="showColorPicker"
      :close-on-content-click="false"
      transition="scale-transition"
      offset-y
      min-width="290px">
      <template v-slot:activator="{on}">
        <v-text-field
          v-model="color"
          v-bind="$attrs"
          readonly
          v-on="on">
          <template v-slot:prepend>
            <v-icon :color="color">
              mdi-palette
            </v-icon>
          </template>

          <!-- passing the append slot through -->
          <template v-slot:append>
            <slot name="append" />
          </template>
        </v-text-field>
      </template>
      <v-card>
        <v-color-picker v-if="showColorPicker" v-model="color" flat />
        <v-spacer />
        <v-btn text color="primary" @click="close">Cancel</v-btn>
        <v-btn text color="primary" @click="save">OK</v-btn>
      </v-card>
    </v-menu>
  </div>
</template>

<script>

export default {
  name: 'ColorPicker',
  inheritAttr: false,
  props: {
    value: { type: String, required: true }
  },
  data () {
    return {
      color: this.value,
      showColorPicker: false
    }
  },
  watch:
  {
    value () {
      this.color = this.value
    }
  },
  methods: {
    close () {
      this.showColorPicker = false
      this.color = this.value
    },
    save () {
      this.showColorPicker = false
      this.$emit('input', this.color)
    }
  }
}
</script>

<style scoped>
</style>
