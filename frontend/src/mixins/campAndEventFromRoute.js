import campFromRoute from '@/mixins/campFromRoute'
import Vue from 'vue'

export default Vue.util.mergeOptions(campFromRoute, {
  computed: {
    event_instances () {
      if (!this.camp) return []
      return this.camp.periods().items.flatMap(period => period.event_instances().items)
    },
    events () {
      return this.event_instances.map(instance => instance.event())
    },
    event () {
      return this.events.find(event => event.id === this.$route.params.eventId)
    }
  }
})
