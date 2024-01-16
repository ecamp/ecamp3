<template>
  <v-menu offset-y :close-on-content-click="!multiple">
    <template #activator="{ on, attrs }">
      <v-chip
        label
        variant="outlined"
        :color="active ? 'primary' : null"
        v-bind="attrs"
        v-on="on"
      >
        <span class="d-none d-sm-inline">{{
          labelValue ? `${label}: ${labelValue}` : label
        }}</span>
        <span class="d-sm-none">{{ labelValue || label }}</span>
        <v-icon end>mdi-chevron-down</v-icon>
      </v-chip>
    </template>
    <v-list density="compact">
      <v-list-item density="compact" color="primary" @click.prevent="clear()">
        <v-list-item-title class="d-flex align-center text-grey-darken-1">
          <span class="flex-grow-1">{{
            $tc('components.dashboard.selectFilter.clear')
          }}</span>
          <v-icon end class="d-flex text-grey">mdi-close</v-icon>
        </v-list-item-title>
      </v-list-item>
      <v-list-item
        v-for="(item, self) in processedItems"
        :key="self"
        density="compact"
        :input-value="item.selected"
        color="primary"
        @click.prevent="toggle(item.value, item.exclusiveNone)"
      >
        <v-list-item-title>
          <slot name="item" v-bind="{ item, self }">{{ item.text }}</slot>
        </v-list-item-title>
        <v-list-item-action v-if="multiple && !item.exclusiveNone">
          <v-checkbox v-model="item.selected" dense />
        </v-list-item-action>
        <v-list-item-action v-if="item.exclusiveNone">
          <v-radio-group v-model="item.selected">
            <v-radio dense :value="true" />
          </v-radio-group>
        </v-list-item-action>
      </v-list-item>
    </v-list>
  </v-menu>
</template>

<script>
import { get, keyBy } from 'lodash'

export default {
  name: 'SelectFilter',
  props: {
    label: { type: String, required: true },
    multiple: { type: Boolean, default: false },
    value: { type: [Array, String], default: null },
    items: { type: Object, default: () => ({}) },
    displayField: { type: [String, Function], required: true },
    valueField: { type: String, default: '_meta.self' },
    andFilter: { type: Boolean, default: false },
  },
  computed: {
    active() {
      return this.value?.length > 0
    },
    processedItems() {
      return keyBy(
        Object.values(this.items).map((item) => {
          const text = this.displayValue(item)
          const value = get(item, this.valueField)
          const exclusiveNone = get(item, 'exclusiveNone')
          const selected = this.multiple
            ? this.value?.includes(value)
            : this.value === value
          return { text, value, selected, exclusiveNone }
        }),
        'value'
      )
    },
    labelValue() {
      if (this.multiple) {
        const list = (this.value || []).map((item) => this.processedItems[item].text)
        const lang = this.$store.state.lang.language
        if ('Intl' in window && 'ListFormat' in Intl) {
          const listFormat = new Intl.ListFormat(lang, {
            type: this.andFilter ? 'conjunction' : 'disjunction',
          })
          return listFormat.format(list)
        }
        return list.join(', ')
      }
      return this.processedItems[this.value]?.text
    },
  },
  methods: {
    displayValue(item) {
      if (item.exclusiveNone) {
        return item.label
      }
      if (typeof this.displayField === 'function') {
        return this.displayField(item)
      }
      return get(item, this.displayField)
    },
    clear() {
      this.$emit('input', null)
    },
    toggle(item, none = false) {
      if (this.andFilter && none) {
        const newValue = this.value === item ? null : item
        this.$emit('input', this.multiple ? [newValue] : newValue)
      } else {
        if (this.multiple) {
          const filteredValue = this.andFilter
            ? this.value?.filter((value) => value !== 'none')
            : this.value
          const newValue = filteredValue?.includes(item)
            ? filteredValue.filter((value) => value !== item)
            : (filteredValue || []).concat([item])
          this.$emit('input', newValue)
        } else {
          const newValue = this.value === item ? null : item
          this.$emit('input', newValue)
        }
      }
    },
  },
}
</script>

<style scoped></style>
