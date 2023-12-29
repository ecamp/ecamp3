<template>
  <card-content-node class="ec-la-thematic-area" v-bind="$props">
    <e-select
      v-model="localSelection"
      item-value="value"
      :items="items"
      multiple
      :filled="false"
      outlined
      persistent-placeholder
      :error-messages="errorMessages"
      :loading="savingRequestCount > 0"
      :disabled="layoutMode || disabled"
      :menu-props="{
        maxWidth: 'min(290px, calc(100vw - 32px))',
        contentClass: 'ec-la-thematic-area',
      }"
      :placeholder="$tc('components.activity.content.lAThematicArea.placeholder')"
      @input="onInput"
    >
      <template #selection="{ index, parent }">
        <template v-if="index === 0">
          <v-list
            v-if="selectionCount > 0"
            :two-line="selectionCount > 3"
            :three-line="selectionCount <= 3"
            class="flex-grow-1 transparent"
          >
            <template v-for="[key] in dataOptions">
              <v-list-item
                v-if="localSelection.includes(key)"
                :key="key"
                :ripple="false"
                inactive
                class="px-0 ec-lta-item"
                @click.stop="parent.isMenuActive = !parent.isMenuActive"
              >
                <v-list-item-content class="py-0">
                  <v-list-item-title>
                    {{ $tc(`contentNode.laThematicArea.entity.option.${key}.name`) }}
                  </v-list-item-title>
                  <v-list-item-subtitle>
                    {{
                      $tc(`contentNode.laThematicArea.entity.option.${key}.description`)
                    }}
                  </v-list-item-subtitle>
                </v-list-item-content>
              </v-list-item>
            </template>
          </v-list>
          <v-skeleton-loader v-else type="sentences" class="mt-2" width="100px" />
        </template>
      </template>
      <template #item="{ item, parent, on, attrs }">
        <v-list-item three-line class="ec-lta-item" v-bind="attrs" v-on="on">
          <v-list-item-action>
            <v-simple-checkbox
              class="pointer-events-none"
              :value="parent.hasItem(item)"
              :color="parent.color"
              :ripple="false"
            />
          </v-list-item-action>
          <v-list-item-content>
            <v-list-item-title>{{ item.text }}</v-list-item-title>
            <v-list-item-subtitle>{{ item.description }}</v-list-item-subtitle>
          </v-list-item-content>
        </v-list-item>
      </template>
    </e-select>
  </card-content-node>
</template>

<script>
import CardContentNode from '@/components/activity/CardContentNode.vue'
import { contentNodeMixin } from '@/mixins/contentNodeMixin.js'
import { debounce, isEqual, sortBy } from 'lodash'
import { serverErrorToString } from '@/helpers/serverError.js'

export default {
  name: 'LAThematicArea',
  components: { CardContentNode },
  mixins: [contentNodeMixin],
  data() {
    return {
      localSelection: [],
      savingRequestCount: 0,
      dirty: false,
      errorMessages: [],
      debouncedSave: () => null,
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
        text: this.$tc(`contentNode.laThematicArea.entity.option.${key}.name`),
        description: this.$tc(
          `contentNode.laThematicArea.entity.option.${key}.description`
        ),
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
  methods: {
    onInput() {
      this.dirty = true
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
.ec-la-thematic-area :deep(.v-input__slot) {
  flex-grow: 1;
}

.ec-la-thematic-area :deep(.v-select .v-select__selections) {
  align-self: start;
  padding: 10px 0;
}

.ec-la-thematic-area :deep(.v-select .v-select__selections .v-list) {
  padding: 6px 0;
  width: 0;
}

.ec-la-thematic-area :deep(.v-select .v-list-item + .v-list-item) {
  margin-top: 10px;
}

.ec-la-thematic-area :deep(.v-select__selections .v-list + input) {
  flex-grow: 0;
}

.ec-la-thematic-area .ec-lta-item {
  min-height: 0 !important;

  .v-list-item__subtitle {
    -webkit-line-clamp: initial !important;
  }
}
</style>
