<template>
  <ContentNodeCard class="ec-ys-activity-characteristics grow-v-slot" v-bind="$props">
    <e-select
      :model-value="localSelection"
      path="localSelection"
      :items="items"
      density="compact"
      multiple
      variant="outlined"
      persistent-placeholder
      :error-messages="errorMessages"
      :loading="savingRequestCount > 0"
      :disabled="layoutMode || disabled"
      :menu-props="{
        contentProps: { style: { maxWidth: 'min(290px, calc(100vw - 32px))' } },
      }"
      :placeholder="
        $t('components.activity.content.ysActivityCharacteristics.placeholder')
      "
      @update:model-value="onInput"
    >
      <template #selection="{ index, item: parent }">
        <template v-if="index === 0">
          <v-list
            v-if="selectionCount > 0"
            lines="two"
            class="flex-grow-1 bg-transparent"
          >
            <template v-for="[key] in dataOptions">
              <v-list-item
                v-if="localSelection.includes(key)"
                :key="key"
                :ripple="false"
                inactive
                class="px-0 ec-ysac-item"
                @click.stop="parent.isMenuActive = !parent.isMenuActive"
              >
                <v-list-item-title>
                  {{
                    $t(`contentNode.ysActivityCharacteristics.entity.option.${key}.name`)
                  }}
                </v-list-item-title>
              </v-list-item>
            </template>
          </v-list>
          <v-skeleton-loader v-else type="sentences" class="mt-2" width="100px" />
        </template>
      </template>
      <template #item="{ item, props }">
        <v-list-item
          class="ec-ysac-item"
          lines="two"
          v-bind="props"
          active-class="text-blue-darken-2"
        >
          <template #prepend>
            <v-list-item-action start>
              <v-checkbox-btn
                :model-value="localSelection.includes(item.value)"
                :ripple="false"
                class="pointer-events-none"
              ></v-checkbox-btn>
            </v-list-item-action>
          </template>
        </v-list-item>
      </template>
    </e-select>
  </ContentNodeCard>
</template>

<script>
import ContentNodeCard from '@/components/activity/content/layout/ContentNodeCard.vue'
import { contentNodeMixin } from '@/mixins/contentNodeMixin.js'
import { debounce, isEqual, sortBy } from 'lodash-es'
import { serverErrorToString } from '@/helpers/serverError.js'

export default {
  name: 'YSActivityCharacteristics',
  components: { ContentNodeCard },
  mixins: [contentNodeMixin],
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
    selectionCount() {
      return this.localSelection.length
    },
    items() {
      return this.dataOptions.map(([key, option]) => ({
        text: this.$t(`contentNode.ysActivityCharacteristics.entity.option.${key}.name`),
        value: key,
        checked: option.checked,
      }))
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
.ec-ys-activity-characteristics :deep(.v-input__slot) {
  flex-grow: 1;
}

.ec-ys-activity-characteristics :deep(.v-select .v-select__selections) {
  align-self: start;
  padding: 10px 0;
}

.ec-ys-activity-characteristics :deep(.v-select .v-select__selection .v-list-item) {
  padding: 0;
}

.ec-ys-activity-characteristics :deep(.v-select .v-list-item + .v-list-item) {
  margin-top: 10px;
}

.ec-ys-activity-characteristics :deep(.v-select__selections .v-list + input) {
  flex-grow: 0;
}

.ec-ys-activity-characteristics .ec-ysac-item {
  min-height: 0 !important;

  :deep(.v-list-item-title) {
    white-space: normal;
  }
}
</style>
