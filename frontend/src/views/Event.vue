<!--
Displays a single event
-->

<template>
  <div>
    <div v-if="event.loading">
      <b-spinner label="Loading..." />
    </div>

    <div v-else>
      <p class="event_title">
        <api-input
          :value="event.title"
          :uri="event._meta.self"
          fieldname="title"
          label="Titel"
          required />
      </p>

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
    </div>
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
