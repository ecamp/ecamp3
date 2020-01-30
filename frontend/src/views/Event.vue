<!--
Displays a single event
-->

<template>
  <v-card>
    <v-toolbar dense>
      <v-btn icon @click="$router.go(-1)">
        <v-icon>mdi-arrow-left</v-icon>
      </v-btn>
      <v-toolbar-title class="pl-2">
        1.1
        <v-chip v-if="!category.loading" dark :color="category.color">{{ category.short }}</v-chip>
        {{ event.title }}
      </v-toolbar-title>
    </v-toolbar>
    <v-card-text>
      <v-skeleton-loader v-if="event.loading" type="article" />
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
      <v-list v-if="!event.loading">
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
    </v-card-text>
  </v-card>
</template>

<script>
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
  computed: {
    event () {
      return this.api.get(this.$route.params.eventUri)
    },
    category () {
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
