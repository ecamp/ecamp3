<!--
Displays a single event
-->

<template>
  <v-card>
    <v-toolbar dense>
      <v-btn icon @click="$router.go(-1)">
        <v-icon>mdi-arrow-left</v-icon>
      </v-btn>
      <v-toolbar-title class="pl-2" v-if="event">
        1.1
        <v-chip v-if="!category.loading" dark :color="category.color">{{ category.short }}</v-chip>
        {{ event.title }}
      </v-toolbar-title>
    </v-toolbar>
    <v-card-text>
      <v-skeleton-loader v-if="!event || event.loading" type="article" />
      <template v-else>
        <api-input
          :value="event.title"
          :uri="event._meta.self"
          fieldname="title"
          :auto-save="false"
          label="Titel"
          required />
        <api-input
          :value="event.title"
          :uri="event._meta.self"
          fieldname="title"
          :auto-save="true"
          label="Titel"
          required />
        <v-list>
          <v-label>Instanzen</v-label>
          <v-list-item
            v-for="eventInstance in event.event_instances().items"
            :key="eventInstance._meta.self"
            two-line>
            <v-list-item-content>
              1. Montag<br> {{ eventInstance.start_time }} bis {{ eventInstance.end_time }}
            </v-list-item-content>
          </v-list-item>
        </v-list>
      </template>
    </v-card-text>
  </v-card>
</template>

<script>
import campAndEventFromRoute from '@/mixins/campAndEventFromRoute'

export default {
  name: 'Event',
  components: {
    ApiInput: () => import('@/components/form/ApiInput.vue')
  },
  data () {
    return {
      editing: false,
      messages: []
    }
  },
  mixins: [campAndEventFromRoute],
  computed: {
    category () {
      if (!this.event) return undefined
      return this.event.event_category()
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
