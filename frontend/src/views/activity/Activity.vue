<!--
Displays a single activity
-->

<template>
  <v-container fluid>
    <content-card toolbar>
      <template #title>
        <v-toolbar-title class="font-weight-bold">
          {{ scheduleEntry().number }}
          <v-chip v-if="!category._meta.loading" dark :color="category.color">{{ category.short }}</v-chip>
          {{ activity.title }}
        </v-toolbar-title>
      </template>
      <template #title-actions>
        <v-menu bottom left offset-y>
          <template #activator="{ on, attrs }">
            <v-btn color="success"
                   outlined
                   v-bind="attrs"
                   v-on="on">
              <template v-if="$vuetify.breakpoint.smAndUp">
                <v-icon left>mdi-plus-circle-outline</v-icon>
                {{ $tc('global.button.addContentDesktop') }}
              </template>
              <template v-else>{{ $tc('global.button.add') }}</template>
            </v-btn>
          </template>
          <v-list>
            <v-list-item v-for="act in availableContentTypes"
                         :key="act.contentType.id"
                         :disabled="!act.enabled"
                         @click="addActivityContent(act.contentType.id)">
              <v-list-item-icon>
                <v-icon>{{ $tc(act.contentTypeIconKey) }}</v-icon>
              </v-list-item-icon>
              <v-list-item-title>
                {{ $tc(act.contentTypeNameKey) }}
              </v-list-item-title>
            </v-list-item>
          </v-list>
        </v-menu>
      </template>
      <v-card-text>
        <v-skeleton-loader v-if="activity._meta.loading" type="article" />
        <template v-else>
          <!-- Header -->
          <v-card outlined>
            <div class="v-item-group v-expansion-panels">
              <div class="v-expansion-panel px-4 py-1">
                <v-row dense>
                  <v-col class="col col-sm-6 col-12">
                    <v-row v-if="$vuetify.breakpoint.smAndUp" dense>
                      <v-col cols="2">
                        {{ $tc('entity.scheduleEntry.fields.nr') }}
                      </v-col>
                      <v-col cols="10">
                        {{ $tc('entity.scheduleEntry.fields.time') }}
                      </v-col>
                    </v-row>
                    <v-row
                      v-for="scheduleEntryItem in scheduleEntries"
                      :key="scheduleEntryItem._meta.self" dense>
                      <v-col cols="2">
                        ({{ scheduleEntryItem.number }})
                      </v-col>
                      <v-col cols="10">
                        {{ $date.utc(scheduleEntryItem.startTime).format($tc('global.datetime.dateShort')) }} <b>
                          {{ $date.utc(scheduleEntryItem.startTime).format($tc('global.datetime.hourShort')) }} </b> - {{
                          $date.utc(scheduleEntryItem.startTime).format($tc('global.datetime.dateShort')) == $date.utc(scheduleEntryItem.endTime).format($tc('global.datetime.dateShort'))
                            ? ''
                            : $date.utc(scheduleEntryItem.endTime).format($tc('global.datetime.dateShort'))
                        }} <b> {{ $date.utc(scheduleEntryItem.endTime).format($tc('global.datetime.hourShort')) }} </b>
                      </v-col>
                    </v-row>
                  </v-col>
                  <v-col class="col col-sm-6 col-12">
                    <v-row dense>
                      <v-col>
                        <api-text-field
                          :name="$tc('entity.activity.fields.location')"
                          :uri="activity._meta.self"
                          fieldname="location"
                          dense />
                      </v-col>
                    </v-row>
                    <v-row dense>
                      <v-col>
                        <api-select
                          :name="$tc('entity.activity.fields.responsible')"
                          dense
                          multiple
                          chips
                          deletable-chips
                          small-chips
                          :uri="activity._meta.self"
                          fieldname="campCollaborations"
                          :items="availableCampCollaborations" />
                      </v-col>
                    </v-row>
                  </v-col>
                </v-row>
              </div>
            </div>
          </v-card>
          <activity-content-layout :activity="activity" />
        </template>
      </v-card-text>
    </content-card>
  </v-container>
</template>

<script>
import ContentCard from '@/components/layout/ContentCard'
import ApiTextField from '@/components/form/api/ApiTextField'
import ApiSelect from '@/components/form/api/ApiSelect'

import ActivityContentLayout from '@/components/activity/ActivityContentLayout'
import camelCase from 'lodash/camelCase'

export default {
  name: 'Activity',
  components: {
    ContentCard,
    ApiTextField,
    ApiSelect,
    ActivityContentLayout
  },
  props: {
    scheduleEntry: {
      type: Function,
      required: true
    }
  },
  computed: {
    availableCampCollaborations () {
      const currentCampCollaborationIds = this.activity.campCollaborations().items.map(cc => cc.id)
      return this.campCollaborations.filter(cc => {
        return (cc.status === 'established') || (currentCampCollaborationIds.includes(cc.id))
      }).map(value => {
        const left = value.status === 'left'
        const text = value.user().displayName + (left ? (' (' + this.$tc('entity.campCollaboration.campLeft')) + ')' : '')
        return {
          value,
          text
        }
      })
    },
    campCollaborations () {
      return this.activity.camp().campCollaborations().items
    },
    activity () {
      return this.scheduleEntry().activity()
    },
    category () {
      return this.activity.category()
    },
    scheduleEntries () {
      return this.activity.scheduleEntries().items.map((entry) => {
        return {
          ...entry,
          get startTime () {
            return Date.parse(this.period().start) + (this.periodOffset * 60000)
          },
          set startTime (value) {
            this.periodOffset = (value - this.period().start) / 60000
          },
          get endTime () {
            return this.startTime + (this.length * 60000)
          },
          set endTime (value) {
            this.length = (value - this.startTime) / 60000
          }
        }
      }
      )
    },
    activityContents () {
      return this.activity.activityContents()
    },
    availableContentTypes () {
      return this.category.categoryContentTypes().items.map(cct => ({
        id: cct.id,
        contentType: cct.contentType(),
        contentTypeNameKey: 'activityContent.' + camelCase(cct.contentType().name) + '.name',
        contentTypeIconKey: 'activityContent.' + camelCase(cct.contentType().name) + '.icon',
        contentTypeSort: parseInt(this.$tc('activityContent.' + camelCase(cct.contentType().name) + '.sort')),
        enabled: true // atct.contentType().allowMultiple || this.countActivityContents(atct.contentType()) === 0
      })).sort((a, b) => a.contentTypeSort - b.contentTypeSort)
    }
  },
  methods: {
    countActivityContents (contentType) {
      return this.activityContents.items.filter(ac => {
        return ac.contentType().id === contentType.id
      }).length
    },
    async addActivityContent (ctId) {
      await this.api.post('/activity-contents', {
        activityId: this.activity.id,
        contentTypeId: ctId
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
