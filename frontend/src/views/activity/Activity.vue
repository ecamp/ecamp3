<!--
Displays a single activity
-->

<template>
  <v-container fluid>
    <content-card>
      <v-toolbar dense>
        <button-back />
        <v-toolbar-title class="pl-2">
          {{ scheduleEntry().number }}
          <v-chip v-if="!category._meta.loading" dark :color="category.color">{{ category.short }}</v-chip>
          {{ activity.title }}
        </v-toolbar-title>
      </v-toolbar>
      <v-card-text>
        <v-skeleton-loader v-if="activity._meta.loading" type="article" />
        <template v-else>
          <api-text-field
            :value="activity.title"
            :uri="activity._meta.self"
            fieldname="title"
            :auto-save="false"
            label="Titel"
            required />
          <v-list>
            <v-label>Instanzen</v-label>
            <v-list-item
              v-for="instance in instances.items"
              :key="instance._meta.self"
              two-line>
              <v-list-item-content>
                {{ instance.startTime }} bis {{ instance.endTime }}
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

export default {
  name: 'Activity',
  components: {
    ButtonBack,
    ContentCard,
    ApiTextField,
    ActivityLayoutGeneral
  },
  props: {
    scheduleEntry: { type: Function, required: true }
  },
  computed: {
    activity () {
      return this.scheduleEntry().activity()
    },
    category () {
      return this.activity.activityCategory()
    },
    instances () {
      return this.activity.scheduleEntries()
    },
    activityType () {
      return this.category.activityType()
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
