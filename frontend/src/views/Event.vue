<!--
Displays a single event
-->

<template>
  <div>
    <div
      v-if="event.loading"
      class="d-flex justify-content-center m-5">
      <v-progress-circular
        :size="50"
        indeterminate
        color="primary" />
    </div>

    <v-card
      v-else
      class="m-3">
      <v-card-title class="event_title">
        <div class="col-sm-12">
          <api-input
            :value="event.title"
            :uri="event._meta.self"
            fieldname="title"
            label="Titel"
            required />
        </div>
      </v-card-title>
      <v-divider />
      <v-card-text>
        <div>
          <b>Kategorie:</b>
          <div
            class="category_box"
            :style="{ backgroundColor: '#' + category.color }" />
          {{ category.name }} ({{ category.short }})
          <br>
          <small><i>(EventType: {{ category.event_type().name }})</i><small /></small>
        </div>

        <p />

        <p>
          Findet statt am:
          <ul>
            <li
              v-for="eventInstance in event.event_instances().items"
              :key="eventInstance._meta.self">
              {{ eventInstance.start_time }} bis {{ eventInstance.end_time }}
            </li>
          </ul>
        </p>
      </v-card-text>
    </v-card>
  </div>
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

<style>
.category_box{
  display:inline-block;
  position:relative;
  margin-left:5px;
  top:3px;
  height:20px;
  width:20px;
  border:1px black solid;
}

.event_title input{
  font-size:28px;
}
</style>
