<template>
  <v-menu offset-y :close-on-content-click="!multiple">
    <template #activator="{ on, attrs }">
      <v-chip
        label
        outlined
        :color="active || value.length !== 0 ? 'primary' : null"
        v-bind="attrs"
        v-on="on"
      >
        <template v-if="hasSelection">
          <span class="d-none d-sm-inline">{{
            labelValue ? `${label}: ${labelValue}` : label
          }}</span>
          <span class="d-sm-none">{{ labelValue || label }}</span>
        </template>
        <template v-else>
          {{ label }}
        </template>
        <v-icon right>mdi-chevron-down</v-icon>
      </v-chip>
    </template>
    <v-list dense>
      <v-list-item
        dense
        :input-value="value.length === 0"
        color="primary"
        @click.prevent="clear()"
      >
        <v-list-item-title>Alle</v-list-item-title>
      </v-list-item>
      <v-list-item
        v-for="(item, index) in processedItems"
        :key="index"
        dense
        :input-value="item.selected"
        color="primary"
        @click.prevent="toggle(item.value)"
      >
        <v-list-item-title>
          <slot name="item" v-bind="{ item, index }">{{ item.text }} </slot>
        </v-list-item-title>
        <v-list-item-action v-if="multiple">
          <v-checkbox v-model="item.selected" dense />
        </v-list-item-action>
      </v-list-item>
    </v-list>
  </v-menu>
</template>

<script>
export default {
  name: 'SelectFilter',
  props: {
    active: Boolean,
    label: { type: String, required: true },
    multiple: { type: Boolean, default: false },
    value: { type: [Array, String], default: () => [] },
    items: { type: Array, default: () => [] },
  },
  data: () => ({
    changed: false,
    internalSelectedArray: [],
    internalSelectedString: '',
  }),
  computed: {
    processedItems() {
      return this.items.map((item) => ({
        text: item.text ?? item,
        value: item.value ?? item,
        selected: this.multiple
          ? this.value.includes(item.value ?? item) ||
            this.internalSelectedArray.includes(item.value ?? item)
          : this.value === (item.value ?? item),
      }))
    },
    hasSelection() {
      return (
        this.value &&
        (this.multiple
          ? this.internalSelectedArray && this.value === this.internalSelectedArray
          : this.internalSelectedString && this.value === this.internalSelectedString)
      )
    },
    labelValue() {
      return this.multiple
        ? [
            this.internalSelectedArray.slice(0, -1).join(', '),
            this.internalSelectedArray.at(-1),
          ]
            .filter((i) => i !== '')
            .join(' & ')
        : this.internalSelectedString
    },
  },
  watch: {
    value: {
      handler(newValue) {
        if (this.multiple) {
          this.internalSelectedArray = newValue
        } else {
          this.internalSelectedString = newValue
        }
      },
      immediate: true,
    },
  },
  methods: {
    clear() {
      this.changed = false
      if (this.multiple) {
        this.internalSelectedArray = []
        this.$emit('input', this.internalSelectedArray)
      } else {
        this.internalSelectedString = ''
        this.$emit('input', '')
      }
    },
    toggle(item) {
      this.changed = true
      if (this.multiple) {
        this.internalSelectedArray.includes(item)
          ? (this.internalSelectedArray = this.internalSelectedArray.filter(
              (selected) => selected !== item
            ))
          : this.internalSelectedArray.push(item)
        this.$emit('input', this.internalSelectedArray)
      } else {
        this.internalSelectedString = item
        this.$emit('input', item)
      }
    },
  },
}
</script>

<style scoped></style>
