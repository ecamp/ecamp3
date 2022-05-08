<template>
  <e-select
    v-model="selectedCampCollaborations"
    :items="availableCampCollaborations"
    :loading="isSaving || isLoading ? 'secondary' : false"
    :name="$tc('entity.activity.fields.responsible')"
    :error-messages="errorMessages"
    outlined
    :filled="false"
    dense
    multiple
    chips
    deletable-chips
    small-chips
    v-bind="$attrs"
    @input="onInput" />
</template>

<script>
import serverErrorToString from '@/helpers/serverErrorToString.js'
import campCollaborationDisplayName from '@/common/helpers/campCollaborationDisplayName.js'

export default {
  name: 'ActivityResponsibles',
  props: {
    activity: {
      type: Object,
      required: true
    }
  },
  data () {
    return {
      oldSelectedCampCollaborations: [],
      selectedCampCollaborations: [],
      errorMessages: [],
      isSaving: false
    }
  },
  computed: {
    isLoading () {
      return this.campCollaborations._meta.loading || this.activityResponsibles._meta.loading
    },
    availableCampCollaborations () {
      return this.campCollaborations.items.filter(cc => {
        return (cc.status !== 'inactive') || (this.currentCampCollaborationIRIs.includes(cc._meta.self))
      }).map(value => {
        // following structure is defined by vuetify v-select items property
        return {
          value: value._meta.self,
          text: campCollaborationDisplayName(value)
        }
      })
    },
    currentCampCollaborationIRIs () {
      return this.activityResponsibles.items.map(item => item.campCollaboration()._meta.self)
    },
    activityResponsibles () {
      return this.activity.activityResponsibles()
    },
    campCollaborations () {
      return this.activity.camp().campCollaborations()
    }
  },
  async mounted () {
    await this.activityResponsibles._meta.load
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
        promises.push(this.activity.activityResponsibles().$post({
          activity: this.activity._meta.self,
          campCollaboration: campCollaborationIRI
        }))
      })

      // delete removed items
      const removedItems = this.oldSelectedCampCollaborations.filter(item => !this.selectedCampCollaborations.includes(item))
      removedItems.forEach(campCollaborationIRI => {
        const activityResponsible = this.activityResponsibles.items.find(item => item.campCollaboration()._meta.self === campCollaborationIRI)
        if (activityResponsible !== undefined) {
          promises.push(activityResponsible.$del())
        }
      })

      // reset comparison value
      this.oldSelectedCampCollaborations = [...this.selectedCampCollaborations]

      Promise.all(promises).then(() => {
        this.activityResponsibles.$reload()
      }).catch(e => {
        this.errorMessages.push(serverErrorToString(e))
      }).finally(() => {
        this.isSaving = false
      })
    }
  }
}
</script>
