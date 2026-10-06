<template>
  <DetailPane
    v-if="isContributor"
    v-model="dialogOpen"
    max-width="900px"
    :title="$tc('components.category.dialogApplyCategoryLayoutToActivities.title')"
    icon="mdi-file-replace-outline"
    :cancel-action="close"
    :submit-action="apply"
    submit-color="error"
    submit-icon="mdi-bomb"
    :submit-label="
      $tc(
        'components.category.dialogApplyCategoryLayoutToActivities.confirmApply',
        selectedActivities.length
      )
    "
    :submit-enabled="selectedActivities.length > 0"
  >
    <template #activator="{ on, attrs }">
      <v-btn elevation="0" color="danger" :disabled="!isManager" v-bind="attrs" v-on="on">
        <v-icon left>mdi-file-replace-outline</v-icon>
        {{
          $tc(
            'components.category.dialogApplyCategoryLayoutToActivities.replaceActivityContents'
          )
        }}
      </v-btn>
    </template>

    <p>
      {{ $tc('components.category.dialogApplyCategoryLayoutToActivities.description') }}
    </p>
    <p>
      <v-icon color="grey lighten-1">mdi-alert</v-icon>
      {{ $tc('components.category.dialogApplyCategoryLayoutToActivities.risk') }}
    </p>

    <v-list two-line subheader>
      <v-list-item>
        <v-list-item-content>
          <v-list-item-title>
            <i18n
              class="font-weight-medium"
              path="components.category.dialogApplyCategoryLayoutToActivities.templateForNewActivities"
            >
              <template #categoryShort
                ><CategoryChip :category="category" dense
              /></template>
            </i18n>
          </v-list-item-title>
          <v-list-item-subtitle class="whitespace-normal">
            {{ contentsSummary(category) }}
          </v-list-item-subtitle>
        </v-list-item-content>
        <v-list-item-action class="justify-start">
          <v-list-item-action-text>
            {{
              $tc('components.category.dialogApplyCategoryLayoutToActivities.lastChanged')
            }}
          </v-list-item-action-text>
          <v-list-item-action-text>{{ updateTime(category) }}</v-list-item-action-text>
        </v-list-item-action>
      </v-list-item>
    </v-list>

    <v-divider></v-divider>

    <v-list two-line>
      <v-subheader class="justify-center black--text">
        <v-icon>mdi-arrow-down-thick</v-icon>
        {{
          $tc(
            'components.category.dialogApplyCategoryLayoutToActivities.applyToActivities'
          )
        }}
        <v-icon>mdi-arrow-down-thick</v-icon>
      </v-subheader>
    </v-list>

    <v-divider></v-divider>

    <v-list two-line>
      <v-list-item-group v-model="selectedActivities" active-class="red--text" multiple>
        <template v-for="activity in activities">
          <v-list-item :key="activity._meta.self">
            <template #default="{ active }">
              <v-list-item-action>
                <v-checkbox :input-value="active" color="primary"></v-checkbox>
              </v-list-item-action>
              <v-list-item-content>
                <v-list-item-title>
                  <CategoryChip
                    :category="activity.category()"
                    class="mr-1 flex-shrink-0"
                    dense
                  />
                  {{ scheduleEntryNumbers(activity) }}
                  {{ activity.title }}
                </v-list-item-title>
                <v-list-item-subtitle class="whitespace-normal">
                  {{ contentsSummary(activity) }}
                </v-list-item-subtitle>
              </v-list-item-content>

              <v-list-item-action class="justify-start">
                <v-list-item-action-text>
                  {{
                    $tc(
                      'components.category.dialogApplyCategoryLayoutToActivities.lastChanged'
                    )
                  }}
                </v-list-item-action-text>
                <v-list-item-action-text>
                  {{ updateTime(activity) }}
                </v-list-item-action-text>
              </v-list-item-action>
            </template>
          </v-list-item>
        </template>
      </v-list-item-group>
    </v-list>

    <template #actions>
      <v-progress-linear
        :active="progress !== null"
        :value="progress"
        absolute
        bottom
        color="red"
      ></v-progress-linear>
    </template>
  </DetailPane>
</template>
<script>
import DetailPane from '@/components/generic/DetailPane.vue'
import { campRoleMixin } from '../../mixins/campRoleMixin.js'
import CategoryChip from '../generic/CategoryChip.vue'
import camelCase from 'lodash-es/camelCase.js'
import { errorToMultiLineToast } from '../toast/toasts.js'

export default {
  name: 'DialogApplyCategoryLayoutToActivities',
  components: { CategoryChip, DetailPane },
  mixins: [campRoleMixin],
  props: {
    category: { type: Object, required: true },
    camp: { type: Object, required: true },
  },
  data() {
    return {
      dialogOpen: false,
      selectedActivities: [],
      progress: null,
    }
  },
  computed: {
    activities() {
      return this.camp
        .activities()
        .items.filter(
          (activity) => activity.category()._meta.self === this.category._meta.self
        )
    },
  },
  methods: {
    camelCase,
    scheduleEntryNumbers(activity) {
      if (this.category.numberingStyle === '-') return ''
      return activity
        .scheduleEntries()
        .items.map((scheduleEntry) => scheduleEntry.number)
        .join(', ')
    },
    contentsSummary(activityOrCategory) {
      return (
        activityOrCategory
          .contentNodes()
          .items.map((item) =>
            this.$tc('contentNode.' + camelCase(item.contentTypeName) + '.name')
          )
          .join(', ') ||
        this.$tc('components.category.dialogApplyCategoryLayoutToActivities.noContent')
      )
    },
    updateTime(activityOrCategory) {
      return this.$date().format(this.$tc('global.datetime.dateTimeLong'))
    },
    async apply() {
      console.log('you did it!', this.selectedActivities)
      this.progress = 0
      const step = 100 / this.selectedActivities.length
      const selected = [...this.selectedActivities]
      for (const index of selected) {
        const activity = this.activities[index]

        await this.api
          .href(this.api.get(), 'activities', {
            id: activity.id,
            action: 'reset_contents',
          })
          .then((url) => this.api.patch(url, {}))
          .catch((e) => {
            this.$toast.error(errorToMultiLineToast(e))
          })
          .finally(() => {
            this.progress += step
            this.selectedActivities = this.selectedActivities.splice(1)
            activity.$reload()
          })
      }
    },
    close() {
      this.dialogOpen = false
      this.selectedActivities = []
    },
  },
}
</script>
<style scoped>
:deep(.v-chip__content) {
  width: 100%;
}
</style>
