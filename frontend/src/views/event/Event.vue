<!--
Displays a single event
-->

<template>
  <v-container fluid>
    <content-card>
      <v-toolbar dense>
        <button-back />
        <v-toolbar-title class="pl-2">
          {{ eventInstance().number }}
          <v-chip v-if="!category._meta.loading" dark :color="category.color">{{ category.short }}</v-chip>
          {{ event.title }}
        </v-toolbar-title>
      </v-toolbar>
      <v-card-text>
        <v-skeleton-loader v-if="event._meta.loading" type="article" />
        <template v-else>
          <api-text-field
            :value="event.title"
            :uri="event._meta.self"
            fieldname="title"
            :auto-save="false"
            label="Titel"
            required />
          <api-text-field
            :value="event.title"
            :uri="event._meta.self"
            fieldname="title"
            :auto-save="true"
            label="Titel"
            required />
          <v-list>
            <v-label>Instanzen</v-label>
            <v-list-item
              v-for="instance in instances.items"
              :key="instance._meta.self"
              two-line>
              <v-list-item-content>
                1. Montag<br> {{ instance.start_time }} bis {{ instance.end_time }}
              </v-list-item-content>
            </v-list-item>
          </v-list>
        </template>
      </v-card-text>
    </content-card>
  </v-container>
</template>

<script>
import ButtonBack from '@/components/buttons/ButtonBack'
import ContentCard from '@/components/layout/ContentCard'
import ApiTextField from '@/components/form/api/ApiTextField'

export default {
  name: 'Event',
  components: {
    ButtonBack,
    ContentCard,
    ApiTextField
  },
  props: {
    eventInstance: { type: Function, required: true }
  },
  computed: {
    event () {
      return this.eventInstance().event()
    },
    category () {
      return this.event.event_category()
    },
    instances () {
      return this.event.event_instances()
    }
  }
}
</script>

<style scoped>

  .v-card .v-list-item {
    padding-left: 0;
  }

  .event_title input {
    font-size: 28px;
  }
</style>
