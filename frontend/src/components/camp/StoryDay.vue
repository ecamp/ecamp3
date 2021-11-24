<template>
  <v-expansion-panel-content class="e-story-day">
    <h3 class="body-2 grey--text text--darken-2 e-story-day-title">
      {{ dayName }}
    </h3>
    <template v-if="entriesWithStory.length">
      <template v-for="{ scheduleEntry, storyChapters } in entriesWithStory">
        <div v-for="chapter in storyChapters" :key="chapter._meta.uri">
          <h4 class="mt-1 mb-2">
            <div class="d-flex">
              {{ scheduleEntry.number }}
              <v-chip v-if="!scheduleEntry.activity().category()._meta.loading"
                      small
                      dark
                      class="mx-1"
                      :color="scheduleEntry.activity().category().color">
                {{ scheduleEntry.activity().category().short }}
              </v-chip>
              {{ scheduleEntry.activity().title }}
              <template v-if="chapter.contentNode().instanceName">
                - {{ chapter.contentNode().instanceName }}
              </template>
              <v-spacer />
              <router-link :to="{ name: 'activity', params: { campId: day.period().camp().id, scheduleEntryId: scheduleEntry.id } }">
                <v-icon small>mdi-open-in-new</v-icon>
              </router-link>
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
import ApiForm from '@/components/form/api/ApiForm.vue'
import ApiTextarea from '@/components/form/api/ApiTextarea.vue'
import TiptapEditor from '@/components/form/tiptap/TiptapEditor.vue'

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
      return this.$date.utc(date).format(this.$tc('global.datetime.dateLong'))
    },
    sortedScheduleEntries () {
      return sortBy(this.day.scheduleEntries().items, scheduleEntry => scheduleEntry.periodOffset)
    },
    entries () {
      return this.sortedScheduleEntries.map(scheduleEntry => {
        return {
          scheduleEntry: scheduleEntry,
          storyChapters: (scheduleEntry.activity().contentNodes() || { items: [] })
            .items
            .filter(contentNode => contentNode.contentTypeName === 'Storycontext')
            .map(contentNode => contentNode.singleText())
        }
      })
    },
    entriesWithStory () {
      return this.entries.filter(({ storyChapters }) => storyChapters.length)
    }
  },
  mounted () {
    this.day.scheduleEntries().items.forEach(entry => this.api.reload(entry.activity().contentNodes()))
  },
  methods: {
    addDays (date, days) {
      return Date.parse(date) + days * 24 * 60 * 60 * 1000
    }
  }
}
</script>

<style>
.e-story-day + .e-story-day .e-story-day-title {
  border-top: 1px solid #eee;
  padding-top: 5px;
}
</style>
