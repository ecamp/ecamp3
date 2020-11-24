<template>
  <v-expansion-panel-content>
    <h3 class="grey--text">
      {{ dayName }}
    </h3>
    <template v-if="entriesWithStory.length">
      <template v-for="{ scheduleEntry, storyChapters } in entriesWithStory">
        <div v-for="chapter in storyChapters" :key="chapter._meta.uri">
          <h4 class="mt-5">
            <div class="d-flex">
              {{ scheduleEntry.number }}
              <v-chip v-if="!scheduleEntry.activity().activityCategory()._meta.loading"
                      small
                      dark
                      class="mx-1"
                      :color="scheduleEntry.activity().activityCategory().color">
                {{ scheduleEntry.activity().activityCategory().short }}
              </v-chip>
              {{ scheduleEntry.activity().title }}
              <template v-if="chapter.activityContent().instanceName">
                - {{ chapter.activityContent().instanceName }}
              </template>
              <v-spacer />
              <!--Add Content Button Start-->
              <v-menu offset-y>
                <template v-slot:activator="{ on, attrs }">
                  <v-btn v-bind="attrs" v-on="on">
                    <v-icon>mdi-dots-vertical</v-icon>
                  </v-btn>
                </template>
                <v-card>
                  <v-item-group>
                    <v-list-item-action>
                      <dialog-activity-category-edit :entity="activityCategory">
                        hello world
                      </dialog-activity-category-edit>
                    </v-list-item-action>
                  </v-item-group>
                </v-card>
              </v-menu>
              <!--Add Content Button End-->
            </div>
          </h4>
          <api-form v-show="editing"
                    :entity="chapter">
            <api-textarea
              fieldname="text"
              label=""
              auto-grow
              :outlined="false"
              :solo="false"
              dense />
          </api-form>
          <tiptap-editor v-show="!editing"
                         :value="chapter.text"
                         :editable="false"
                         class="mt-1 v-input" />
        </div>
      </template>
    </template>
    <div v-else class="grey--text">
      {{ $tc('components.camp.storyDay.noStory') }}
    </div>
  </v-expansion-panel-content>
</template>
<script>
import { sortBy } from 'lodash'
import ApiForm from '@/components/form/api/ApiForm'
import ApiTextarea from '@/components/form/api/ApiTextarea'
import TiptapEditor from '@/components/form/tiptap/TiptapEditor'

export default {
  name: 'StoryDay',
  components: { TiptapEditor, ApiForm, ApiTextarea },
  props: {
    day: { type: Object, required: true },
    editing: { type: Boolean, default: false }
  },
  computed: {
    dayName () {
      const date = this.addDays(this.day.period().start, this.day.dayOffset)
      return this.$moment.utc(date).format(this.$tc('global.moment.dateLong'))
    },
    sortedScheduleEntries () {
      return sortBy(this.day.scheduleEntries().items, scheduleEntry => scheduleEntry.periodOffset)
    },
    entries () {
      return this.sortedScheduleEntries.map(scheduleEntry => {
        return {
          scheduleEntry: scheduleEntry,
          storyChapters: (scheduleEntry.activity().activityContents() || { items: [] })
            .items
            .filter(activityContent => activityContent.contentTypeName === 'Storycontext')
            .map(activityContent => activityContent.singleText())
        }
      })
    },
    entriesWithStory () {
      return this.entries.filter(({ storyChapters }) => storyChapters.length)
    }
  },
  methods: {
    addDays (date, days) {
      return Date.parse(date) + days * 24 * 60 * 60 * 1000
    }
  }
}
</script>
