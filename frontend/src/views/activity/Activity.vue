<!--
Displays a single activity
-->

<template>
  <v-container fluid>
    <content-card>
      <v-toolbar dense>
        <button-back />
        <!-- Activity-Titel -->
        <v-toolbar-title class="pl-2">
          {{ scheduleEntry().number }}
          <v-chip v-if="!category._meta.loading" dark :color="category.color">{{ category.short }}</v-chip>
          {{ activity.title }}
        </v-toolbar-title>
        <v-spacer />

        <!-- AddContent-Menu -->
        <v-menu bottom left offset-y>
          <template v-slot:activator="{ on, attrs }">
            <v-btn color="success"
                   outlined
                   v-bind="attrs"
                   v-on="on">
              <v-icon left>mdi-plus-circle-outline</v-icon>
              Add Content
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
      </v-toolbar>
      <v-card-text>
        <v-skeleton-loader v-if="activity._meta.loading" type="article" />
        <template v-else>
          <v-list>
            <v-label>Instanzen</v-label>
            <v-list-item
              v-for="scheduleEntry in scheduleEntries"
              :key="scheduleEntry._meta.self"
              two-line>
              <v-list-item-content>
                {{ $moment(scheduleEntry.startTime) }} bis {{ $moment(scheduleEntry.endTime) }}
              </v-list-item-content>
            </v-list-item>
          </v-list>
          <component :is="'ActivityLayout' + activityType.template" v-if="!activityType._meta.loading" :activity="activity" />
        </template>
      </v-card-text>
    </content-card>
  </v-container>
</template>

<script>
import ButtonBack from '@/components/buttons/ButtonBack'
import ContentCard from '@/components/layout/ContentCard'
import ApiTextField from '@/components/form/api/ApiTextField'

import ActivityLayoutGeneral from '@/components/activity/layouts/General'
import camelCase from 'lodash/camelCase'

export default {
  name: 'Activity',
  components: {
    ButtonBack,
    ContentCard,
    ApiTextField,
    ActivityLayoutGeneral
  },
  props: {
    scheduleEntry: {
      type: Function,
      required: true
    }
  },
  computed: {
    activity () {
      return this.scheduleEntry().activity()
    },
    category () {
      return this.activity.activityCategory()
    },
    scheduleEntries () {
      return this.activity.scheduleEntries().items.map((entry) => {
        return {
          ...entry,
          get startTime () {
            return this.period().start + (this.periodOffset * 60000)
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
    activityType () {
      return this.category.activityType()
    },
    activityContents () {
      return this.activity.activityContents()
    },
    activityTypeContentTypes () {
      return this.activityType.activityTypeContentTypes()
    },
    availableContentTypes () {
      return this.activityTypeContentTypes.items.map(atct => ({
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
