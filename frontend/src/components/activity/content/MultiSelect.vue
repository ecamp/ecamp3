<template>
  <ContentNodeCard class="ec-multi-select grow-v-slot" v-bind="$props">
    <e-select
      :model-value="localSelection"
      path="localSelection"
      :items="items"
      :placeholder="placeholder"
      :error-messages="errorMessages"
      :loading="savingRequestCount > 0"
      :disabled="layoutMode || disabled"
      density="compact"
      multiple
      variant="outlined"
      persistent-placeholder
      :menu-props="{
        contentProps: { style: { maxWidth: 'min(290px, calc(100vw - 32px))' } },
      }"
      @update:model-value="onInput"
    >
      <template #selection="{ index }">
        <v-list v-if="index === 0" class="flex-grow-1 bg-transparent">
          <v-list-item
            v-for="item in selectedItems"
            :key="item.value"
            :title="item.text"
            :subtitle="item.description"
            :lines="
              item.description ? (selectedItems.length <= 3 ? 'three' : 'two') : 'one'
            "
            :ripple="false"
            class="px-0 ec-multi-select__item"
          />
        </v-list>
      </template>
      <template #item="{ item, props }">
        <v-list-item
          v-bind="props"
          :subtitle="item.raw.description"
          :lines="item.raw.description ? 'three' : 'one'"
          class="ec-multi-select__item"
          active-class="text-blue-darken-2"
        >
          <template #prepend>
            <v-list-item-action start>
              <v-checkbox-btn
                :model-value="localSelection.includes(item.value)"
                :ripple="false"
                class="pointer-events-none"
              />
            </v-list-item-action>
          </template>
        </v-list-item>
      </template>
    </e-select>
  </ContentNodeCard>
</template>

<script>
import ESelect from '@/components/form/base/ESelect.vue'
import ContentNodeCard from '@/components/activity/content/layout/ContentNodeCard.vue'
import { contentNodeMixin } from '@/mixins/contentNodeMixin.js'
import { debounce, isEqual, sortBy } from 'lodash-es'
import { serverErrorToString } from '@/helpers/serverError.js'

export default {
  name: 'MultiSelect',
  components: { ContentNodeCard, ESelect },
  mixins: [contentNodeMixin],
  props: {
    items: { type: Array, required: true },
    placeholder: { type: String, default: '' },
  },
  data() {
    return {
      localSelection: [],
      savingRequestCount: 0,
      dirty: false,
      errorMessages: [],
      debounceSave: () => null,
    }
  },
  computed: {
    dataOptions() {
      return Object.entries(this.contentNode.data.options)
    },
    serverSelection() {
      return this.dataOptions.filter(([_, option]) => option.checked).map(([key]) => key)
    },
    selectedItems() {
      return this.items.filter((item) => this.localSelection.includes(item.value))
    },
  },
  watch: {
    serverSelection: {
      async handler(newOptions, oldOptions) {
        if (isEqual(sortBy(newOptions), sortBy(oldOptions))) {
          return
        }

        // copy incoming data if not dirty or if incoming data is the same as local data
        if (!this.dirty || isEqual(sortBy(newOptions), sortBy(this.localSelection))) {
          this.resetLocalData()
        }
      },
      immediate: true,
    },
  },
  created() {
    const DEBOUNCE_WAIT = 500
    this.debounceSave = debounce(this.save, DEBOUNCE_WAIT)
  },
  beforeUnmount() {
    this.debounceSave.flush()
  },
  methods: {
    onInput(newValue) {
      this.dirty = true
      this.localSelection = newValue
      this.errorMessages = []

      this.debounceSave()
    },
    save() {
      this.savingRequestCount++
      this.contentNode
        .$patch({
          data: {
            options: Object.fromEntries(
              this.dataOptions.map(([key]) => [
                key,
                { checked: this.localSelection.includes(key) },
              ])
            ),
          },
        })
        .catch((e) => this.errorMessages.push(serverErrorToString(e)))
        .finally(() => this.savingRequestCount--)
    },
    resetLocalData() {
      this.localSelection = [...this.serverSelection]
      this.dirty = false
    },
  },
}
</script>

<style scoped>
.ec-multi-select :deep(.v-select__selection) {
  width: 100%;
  margin-inline-end: 0;
}

.ec-multi-select :deep(.v-list-item + .v-list-item) {
  margin-top: 10px;
}

.ec-multi-select__item {
  min-height: 0 !important;

  :deep(.v-list-item-title) {
    white-space: normal;
  }
}
</style>
