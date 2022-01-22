<template>
  <v-skeleton-loader v-if="isLoading" type="text" height="56" />
  <e-select v-else
            v-model="selectedCampCollaborations"
            :items="availableCampCollaborations"
            :loading="isSaving || isLoading ? 'secondary' : false"
            :name="$tc('entity.day.fields.dayResponsibles')"
            :error-messages="errorMessages"
            outlined
            :filled="false"
            multiple
            chips
            deletable-chips
            small-chips
            v-bind="$attrs"
            @input="onInput" />
</template>

<script>
import serverErrorToString from '@/helpers/serverErrorToString.js'

export default {
  name: 'DayResponsibles',
  props: {
    // current period
    period: {
      type: Object,
      required: true
    },

    // date of the DayEntity as ISO String
    date: {
      type: String,
      required: true
    }
  },
  data () {
    return {
      oldSelectedCampCollaborations: [],
      selectedCampCollaborations: [],
      errorMessages: [],
      isSaving: false,
      isLoading: true
    }
  },
  computed: {
    availableCampCollaborations () {
      return this.campCollaborations.items.filter(cc => {
        return (cc.status === 'established') || (this.currentCampCollaborationIRIs.includes(cc._meta.self))
      }).map(value => {
        const inactive = value.status === 'inactive'
        const text = value.user().displayName + (inactive ? (' (' + this.$tc('entity.campCollaboration.inactive')) + ')' : '')

        // following structure is defined by vuetify v-select items property
        return {
          value: value._meta.self,
          text
        }
      })
    },
    currentCampCollaborationIRIs () {
      return this.dayResponsibles.items.map(item => item.campCollaboration()._meta.self)
    },
    dayResponsibles () {
      return this.day.dayResponsibles()
    },
    campCollaborations () {
      return this.period.camp().campCollaborations()
    },

    // returns the day entity which corresponds to the provided date string
    day () {
      return this.period.days().items.find(day => {
        return this.$date.utc(this.date).isSame(this.$date.utc(day.start), 'day')
      })
    }
  },
  async mounted () {
    await Promise.all([
      this.period.camp().campCollaborations()._meta.load,
      this.period.days().$reload()
    ])

    this.isLoading = false

    this.oldSelectedCampCollaborations = [...this.currentCampCollaborationIRIs]
    this.selectedCampCollaborations = [...this.currentCampCollaborationIRIs]
  },
  methods: {
    onInput (value) {
      const promises = []
      this.errorMessages = []
      this.isSaving = true

      // add new items
      const newItems = this.selectedCampCollaborations.filter(item => !this.oldSelectedCampCollaborations.includes(item))
      newItems.forEach(campCollaborationIRI => {
        promises.push(this.dayResponsibles.$post({
          day: this.day._meta.self,
          campCollaboration: campCollaborationIRI
        }))
      })

      // delete removed items
      const removedItems = this.oldSelectedCampCollaborations.filter(item => !this.selectedCampCollaborations.includes(item))
      removedItems.forEach(campCollaborationIRI => {
        const dayResponsible = this.dayResponsibles.items.find(item => item.campCollaboration()._meta.self === campCollaborationIRI)
        if (dayResponsible !== undefined) {
          promises.push(dayResponsible.$del())
        }
      })

      // reset comparison value
      this.oldSelectedCampCollaborations = [...this.selectedCampCollaborations]

      Promise.all(promises).then(() => {
        this.dayResponsibles.$reload()
      }).catch(e => {
        this.errorMessages.push(serverErrorToString(e))
      }).finally(() => {
        this.isSaving = false
      })
    }
  }
}
</script>

<style lang="scss" scoped>
  ::v-deep .v-skeleton-loader__text {
    height: 40px;
    padding-left: 0 !important;
    padding-right: 0 !important;
  }
</style>
