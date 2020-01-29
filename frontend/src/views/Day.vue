<template>
  <div>
    <v-overflow-btn class="mt-0 event-number" single-line
                    label="Day" hide-details
                    :menu-props="{&quot;contentClass&quot;:&quot;ec-menu-left&quot;}"
                    value="1" :items="[{text:'(1.0) 2019-11-24',value:'1'}]" />
    <template  v-if="camp">
      <v-list
        v-for="period in periods"
        :key="period._meta.self"
        class="pt-0">
        <!-- wait for all events to be loaded => avoid each eventInstance to load separately -->
        <template>
          <v-list-item
            v-for="eventInstance in period.event_instances().items"
            :key="eventInstance._meta.self"
            two-line
            :to="{ name: 'event', params: { eventUri: eventInstance.event()._meta.self } }">
            <v-list-item-content class="event-number-style mr-1">
              <v-list-item-title class="event-number">
                ({{ eventInstance.number }})&nbsp;
              </v-list-item-title>
              <v-list-item-subtitle><br></v-list-item-subtitle>
            </v-list-item-content>
            <v-list-item-content>
              <v-list-item-title>{{ eventInstance.event().title }}</v-list-item-title>
              <v-list-item-subtitle>{{ eventInstance.start_time }}</v-list-item-subtitle>
            </v-list-item-content>
            <v-skeleton-loader v-if="eventInstance.event().event_category().loading" type="chip" />
            <v-chip v-else dark
                    :color="eventInstance.event().event_category().color" class="ml-1 px-2"
                    :title="eventInstance.event().event_category().name">
              {{ eventInstance.event().event_category().short }}
            </v-chip>
          </v-list-item>
        </template>
      </v-list>
    </template>
  </div>
</template>

<script>
import campFromRoute from '@/mixins/campFromRoute'

export default {
  name: 'Day',
  data () {
    return {
      editing: false,
      messages: []
    }
  },
  mixins: [campFromRoute],
  computed: {
    periods () {
      return this.camp.periods().items
    },
    buttonText () {
      return this.editing ? 'Speichern' : 'Bearbeiten'
    },
    events () {
      return this.camp.events()
    }
  }
}
</script>

<style scoped>
  .event-number {
    font-feature-settings: 'tnum';
  }

  .event-number-style {
    flex: 0 auto;
  }
</style>
