<!--
Admin screen of a camp: Displays details & periods of a single camp and allows to edit them.
-->

<template>
  <content-card>
    <v-toolbar>
      <v-card-title>{{ $tc('views.camp.story.title') }}</v-card-title>
      <v-spacer />
      <v-btn icon
             :href="previewUrl"
             class="mr-4"
             target="_blank">
        <v-icon>mdi-printer</v-icon>
      </v-btn>
      <e-switch v-model="editing" :label="editing ? $tc('global.button.editModeOn') : $tc('global.button.editModeOff')" />
    </v-toolbar>
    <v-card-text>
      <v-expansion-panels v-model="openPeriods" multiple>
        <!--Add Content Button Start-->
        <v-menu bottom left offset-y>
          <template v-slot:activator="{ on, attrs }">
            <v-btn color="success"
                   outlined
                   v-bind="attrs"
                   v-on="on">
              <template v-if="$vuetify.breakpoint.smAndUp"><v-icon left>mdi-plus-circle-outline</v-icon> {{ $tc('global.button.addContentDesktop') }}</template>
              <template v-else>{{ $tc('global.button.add') }}</template>
            </v-btn>
          </template>
          <v-list>
            <v-list-item v-for="act in availableContentTypes"
                         :key="act.contentType.id"
                         :disabled="!act.enabled"
                         @click="addActivityContent(act.id)">
              <v-list-item-icon>
                <v-icon>{{ $tc(act.contentTypeIconKey) }}</v-icon>
              </v-list-item-icon>
              <v-list-item-title>
                {{ $tc(act.contentTypeNameKey) }}
              </v-list-item-title>
            </v-list-item>
          </v-list>
        </v-menu>
        <!--Add Content Button End-->
        <story-period v-for="period in camp().periods().items"
                      :key="period._meta.self"
                      :period="period"
                      :editing="editing" />
      </v-expansion-panels>
    </v-card-text>
  </content-card>
</template>

<script>
import ContentCard from '@/components/layout/ContentCard'
import StoryPeriod from '@/components/camp/StoryPeriod'
import ESwitch from '@/components/form/base/ESwitch'
import ApiTextField from '@/components/form/api/ApiTextField'
import ApiSelect from '@/components/form/api/ApiSelect'
import ActivityLayoutGeneral from '@/components/activity/layouts/General'
import camelCase from 'lodash/camelCase'

const PRINT_SERVER = window.environment.PRINT_SERVER

export default {
  name: 'Story',
  components: {
    ESwitch,
    StoryPeriod,
    ContentCard
  },
  props: {
    camp: { type: Function, required: true },
    scheduleEntry: { type: Function, required: true }
  },
  data () {
    return {
      editing: false,
      openPeriods: []
    }
  },
  computed: {
    previewUrl () {
      const config = {
        showStoryline: true
      }
      const configGetParams = Object.entries(config).map(([key, val]) => `${key}=${val}`).join('&')
      return `${PRINT_SERVER}/?camp=${this.camp().id}&pagedjs=true&${configGetParams}`
    }
  },
  mounted () {
    this.camp().periods()._meta.load.then(periods => {
      this.openPeriods = periods.items
        .map((period, idx) => Date.parse(period.end) >= new Date() ? idx : null)
        .filter(idx => idx !== null)
    })
  },
  activityType () {
      return this.category.activityType()
    },
    activityContents () {
      return this.activity.activityContents()
    },
    activityTypeContentTypes () {
      return this.activityType.activityTypeContentTypes()
    },
    ableContentTypes () {
    avail  return this.activityTypeContentTypes.items.map(atct => ({
        id: atct.id,
        contentType: atct.contentType(),
        contentTypeNameKey: 'activityContent.' + camelCase(atct.contentType().name) + '.name',
        contentTypeIconKey: 'activityContent.' + camelCase(atct.contentType().name) + '.icon',
        contentTypeSort: parseInt(this.$tc('activityContent.' + camelCase(atct.contentType().name) + '.sort')),
        enabled: atct.contentType().allowMultiple || this.countActivityContents(atct.contentType()) === 0
      })).sort((a, b) => a.contentTypeSort - b.contentTypeSort)
    }
},
  methods: {
    countActivityContents (contentType) {
      return this.activityContents.items.filter(ac => {
        return ac.contentType().id === contentType.id
      }).length
    },
    async addActivityContent (atctId) {
      await this.api.post('/activity-contents', {
        activityId: this.activity.id,
        activityTypeContentTypeId: atctId
      })
      await this.refreshActivity()
    },
    async refreshActivity () {
      await this.api.reload(this.activity._meta.self)
    }
  }
}
</script>
<style scoped>
.v-card .v-list-item {
  padding-left: 0;
}

.activity_title input {
  font-size: 28px;
}
</style>
