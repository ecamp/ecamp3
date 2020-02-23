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
        {{ eventInstance().number }}
        <v-chip v-if="!category._meta.loading" dark :color="category.color">{{ category.short }}</v-chip>
        {{ event.title }}
      </v-toolbar-title>
    </v-toolbar>
    <v-card-text>
      <v-skeleton-loader v-if="event._meta.loading" type="article" />
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
  </v-card>
</template>

<script>
export default {
  name: 'Event',
  components: {
    ApiInput: () => import('@/components/form/ApiInput.vue')
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
