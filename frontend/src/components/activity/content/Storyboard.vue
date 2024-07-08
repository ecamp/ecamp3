<template>
  <ContentNodeCard v-resizeobserver.debounce="onResize" v-bind="$props">
    <template #outer>
      <component
        :is="isDefaultVariant ? 'table' : 'div'"
        class="w-full"
        :class="{
          'flex-grow-1': !isDefaultVariant,
          'pointer-events-none px-3 pb-3': layoutMode,
        }"
      >
        <thead v-if="isDefaultVariant" :class="{ 'opacity-60': layoutMode }">
          <tr>
            <th v-if="!layoutMode">
              <span class="d-sr-only">
                {{ $tc('components.activity.content.storyboard.reorder') }}
              </span>
            </th>
            <th scope="col" class="text-left">
              {{ $tc('contentNode.storyboard.entity.section.fields.column1') }}
            </th>
            <th scope="col" class="text-left">
              {{ $tc('contentNode.storyboard.entity.section.fields.column2Html') }}
            </th>
            <th scope="col" class="text-left">
              {{ $tc('contentNode.storyboard.entity.section.fields.column3') }}
            </th>
            <th>
              <span class="d-sr-only">
                {{ $tc('components.activity.content.storyboard.controls') }}
              </span>
            </th>
          </tr>
        </thead>
        <StoryboardSortable
          :entity="contentNode"
          :disabled="layoutMode || disabled"
          :items="sections"
          :layout-mode="layoutMode"
          :is-last-section="isLastSection"
          :variant="isDefaultVariant ? 'default' : 'dense'"
          @sort="updateSections"
        />
        <template v-if="!layoutMode && !disabled">
          <tfoot v-if="isDefaultVariant">
            <tr>
              <td colspan="4">
                <v-btn
                  block
                  icon
                  class="button-add"
                  color="success"
                  rounded
                  :loading="isAdding"
                  @click="addSection"
                >
                  <v-icon>mdi-plus</v-icon>
                </v-btn>
              </td>
            </tr>
          </tfoot>
          <div v-else class="d-flex mx-1 mb-3">
            <v-btn
              block
              icon
              class="button-add"
              color="success"
              rounded
              :loading="isAdding"
              @click="addSection"
            >
              <v-icon>mdi-plus</v-icon>
            </v-btn>
          </div>
        </template>
      </component>
    </template>
  </ContentNodeCard>
</template>
<script>
import ApiForm from '@/components/form/api/ApiForm.vue'
import ContentNodeCard from '@/components/activity/content/layout/ContentNodeCard.vue'
import { contentNodeMixin } from '@/mixins/contentNodeMixin.js'
import ApiSortable from '@/components/form/api/ApiSortable.vue'

import { v4 as uuidv4 } from 'uuid'
import { errorToMultiLineToast } from '@/components/toast/toasts'
import StoryboardSortable from '@/components/activity/content/storyboard/StoryboardSortable.vue'

export default {
  name: 'Storyboard',
  components: {
    ContentNodeCard,
    StoryboardSortable,
    ApiForm,
    ApiSortable,
  },
  mixins: [contentNodeMixin],
  props: {
    contentNode: { type: Object, required: true },
  },
  data() {
    return {
      isAdding: false,
      clientWidth: 1000,
    }
  },
  computed: {
    sections() {
      const sections = this.api.get(this.contentNode).data.sections

      // workaround because our API return an empty array instead of empty object, if there is no section
      // this could be removed later, when no empty sections objects are in the DB anymore
      if (Array.isArray(sections) && sections.length === 0) {
        return {}
      }

      return sections
    },
    isLastSection() {
      return Object.keys(this.sections).length === 1
    },
    isDefaultVariant() {
      return this.clientWidth > 910
    },
  },
  mounted() {
    this.clientWidth = this.$el.getBoundingClientRect().width
  },
  methods: {
    onResize({ width }) {
      this.clientWidth = width
    },
    async addSection() {
      this.isAdding = true

      const sectionId = uuidv4()
      try {
        // TODO: consider adding item to ApiSortable eagerly (should be easy, now that uuid is generated locally)
        await this.contentNode.$patch({
          data: {
            sections: {
              [sectionId]: {
                column1: '',
                column2Html: '',
                column3: '',
                position: Object.keys(this.sections).length + 1,
              },
            },
          },
        })
      } catch (error) {
        this.$toast.error(errorToMultiLineToast(error))
      }

      this.isAdding = false
    },

    async updateSections(payload) {
      try {
        await this.contentNode.$patch({
          data: {
            sections: payload,
          },
        })
      } catch (error) {
        this.$toast.error(errorToMultiLineToast(error))
      }
    },
  },
}
</script>

<style scoped lang="scss"></style>
