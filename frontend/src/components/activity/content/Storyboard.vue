<template>
  <card-content-node v-resizeobserver.debounce="onResize" v-bind="$props">
    <component :is="variant === 'default' ? 'table' : 'div'">
      <thead v-if="variant === 'default'">
        <tr>
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
              {{ $tc('contentNode.storyboard.entity.section.controls') }}
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
        :variant="variant"
        @sort="updateSections"
      />
      <template v-if="!layoutMode && !disabled">
        <tfoot v-if="variant === 'default'">
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
        <v-btn
          v-else
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
      </template>
    </component>
  </card-content-node>
</template>

<script>
import ApiForm from '@/components/form/api/ApiForm.vue'
import CardContentNode from '@/components/activity/CardContentNode.vue'
import { contentNodeMixin } from '@/mixins/contentNodeMixin.js'
import ApiSortable from '@/components/form/api/ApiSortable.vue'

import { v4 as uuidv4 } from 'uuid'
import { errorToMultiLineToast } from '@/components/toast/toasts'
import StoryboardRow from '@/components/activity/content/StoryboardRow.vue'
import StoryboardSortable from '@/components/activity/content/StoryboardSortable.vue'

export default {
  name: 'Storyboard',
  components: {
    StoryboardSortable,
    StoryboardRow,
    CardContentNode,
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
    variant() {
      return this.clientWidth <= 910 ? 'dense' : 'default'
    },
  },
  mounted() {
    this.clientWidth = this.$el.clientWidth
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

<style scoped lang="scss">
:deep {
  .v-text-field.v-text-field--enclosed.v-text-field--outlined:not(.v-input--dense)
    .editor {
    margin-top: 10px;
    padding-top: 5px;
    margin-bottom: 10px;
  }
}
</style>
