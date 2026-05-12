<template>
  <v-menu :close-on-content-click="!multiple" :multiple="null">
    <template #activator="{ props }">
      <v-chip
        :color="active ? 'primary' : 'surface'"
        :border="active ? null : 'sm'"
        label
        :variant="active ? 'outlined' : 'flat'"
        v-bind="props"
        append-icon="mdi-chevron-down"
      >
        <span class="d-none d-sm-inline">{{
          labelValue ? `${label}: ${labelValue}` : label
        }}</span>
        <span class="d-sm-none">{{ labelValue || label }}</span>
      </v-chip>
    </template>
    <v-list :lines="false" density="compact" tag="ul">
      <v-list-item color="primary" density="comfortable" @click.prevent="clear()">
        <v-list-item-title class="d-flex align-center text-body-2 text-surface-variant">
          <span class="flex-grow-1">{{
            $t('components.dashboard.selectFilter.clear')
          }}</span>
          <v-icon class="d-flex text-grey" end>mdi-close</v-icon>
        </v-list-item-title>
      </v-list-item>
      <v-list-item
        v-for="(item, self) in processedItems"
        :key="self"
        density="compact"
        min-height="50"
        :active="item.selected"
        active-class="text-primary"
        class="mb-0"
        color="primary"
        tag="li"
        @click.prevent="toggle(item.value, item.exclusiveNone)"
      >
        <v-list-item-title class="text-body-2">
          <slot name="item" v-bind="{ item, self }"
            ><span>{{ item.text }}</span></slot
          >
          <CountBadge v-if="item.resultCount !== null" :count="item.resultCount" />
        </v-list-item-title>
        <template #append>
          <v-list-item-action v-if="multiple && !item.exclusiveNone" end>
            <v-checkbox-btn v-model="item.selected" dense />
          </v-list-item-action>
          <v-list-item-action v-if="item.exclusiveNone" end>
            <v-radio-group v-model="item.selected" hide-details>
              <v-radio dense :value="true" />
            </v-radio-group>
          </v-list-item-action>
        </template>
      </v-list-item>
    </v-list>
  </v-menu>
</template>

<script>
import { get, keyBy } from 'lodash-es'
import CountBadge from '@/components/dashboard/CountBadge.vue'

export default {
  name: 'SelectFilter',
  components: { CountBadge },
  inheritAttrs: false,
  props: {
    label: { type: String, required: true },
    multiple: { type: Boolean, default: false },
    modelValue: { type: [Array, String], default: null },
    items: { type: Object, default: () => ({}) },
    displayField: { type: [String, Function], required: true },
    valueField: { type: String, default: '_meta.self' },
    andFilter: { type: Boolean, default: false },
  },
  emits: ['update:modelValue'],
  data() {
    return {
      open: false,
    }
  },
  computed: {
    active() {
      return this.modelValue?.length > 0
    },
    processedItems() {
      return keyBy(
        Object.values(this.items).map((item) => {
          const text = this.displayValue(item)
          const value = get(item, this.valueField)
          const resultCount = get(item, 'resultCount', null)
          const exclusiveNone = get(item, 'exclusiveNone')
          const selected = this.multiple
            ? this.modelValue?.includes(value)
            : this.modelValue === value
          return { text, resultCount, value, selected, exclusiveNone }
        }),
        'value'
      )
    },
    labelValue() {
      if (this.multiple) {
        const list = (this.modelValue || [])
          .map((item) => this.processedItems[item]?.text)
          .filter(Boolean)
        const lang = this.$store.state.lang.language
        if ('Intl' in window && 'ListFormat' in Intl) {
          const listFormat = new Intl.ListFormat(lang, {
            type: this.andFilter ? 'conjunction' : 'disjunction',
          })
          return listFormat.format(list)
        }
        return list.join(', ')
      }
      return this.processedItems[this.modelValue]?.text
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
      this.$emit('update:modelValue', null)
    },
    toggle(item, none = false) {
      if (this.andFilter && none) {
        const newValue = this.modelValue === item ? null : item
        this.$emit('update:modelValue', this.multiple ? [newValue] : newValue)
      } else {
        if (this.multiple) {
          const filteredValue = this.andFilter
            ? this.modelValue?.filter((value) => value !== 'none')
            : this.modelValue
          const newValue = filteredValue?.includes(item)
            ? filteredValue.filter((value) => value !== item)
            : (filteredValue || []).concat([item])
          this.$emit('update:modelValue', newValue)
        } else {
          const newValue = this.modelValue === item ? null : item
          this.$emit('update:modelValue', newValue)
        }
      }
    },
  },
}
</script>
