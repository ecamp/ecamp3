<template>
  <v-skeleton-loader
    v-if="isLoading"
    type="heading"
    height="24"
    class="mx-2 d-flex justify-center my-1"
  />
  <e-select
    v-else
    v-model="selectedCampCollaborations"
    :items="availableCampCollaborations"
    :loading="isSaving || isLoading ? 'secondary' : false"
    :name="$tc('entity.day.fields.dayResponsibles')"
    :error-messages="errorMessages"
    :menu-props="{ closeOnClick: true, closeOnContentClick: true, overflowY: true }"
    :filled="false"
    multiple
    chips
    single-line
    small-chips
    persistent-placeholder
    v-bind="$attrs"
    :readonly="readonly"
    :class="{ 'ec-day-responsible--readonly': readonly }"
    @input="onInput"
  >
    <template #prepend-item>
      <v-subheader>{{ $tc('entity.day.fields.dayResponsibles') }}</v-subheader>
    </template>
  </e-select>
</template>

<script>
import { serverErrorToString } from '@/helpers/serverError.js'
import campCollaborationDisplayName from '@/common/helpers/campCollaborationDisplayName.js'

export default {
  name: 'DayResponsibles',
  props: {
    // current period
    period: {
      type: Object,
      required: true,
    },

    // date of the DayEntity as ISO String
    date: {
      type: String,
      required: true,
    },

    readonly: {
      type: Boolean,
      default: false,
    },
  },
  data() {
    return {
      oldSelectedCampCollaborations: [],
      selectedCampCollaborations: [],
      errorMessages: [],
      isSaving: false,
      isLoading: true,
    }
  },
  computed: {
    availableCampCollaborations() {
      return this.campCollaborations.items
        .filter((cc) => {
          return (
            cc.status !== 'inactive' ||
            this.currentCampCollaborationIRIs.includes(cc._meta.self)
          )
        })
        .map((value) => {
          // following structure is defined by vuetify v-select items property
          return {
            value: value._meta.self,
            text: campCollaborationDisplayName(value, this.$tc.bind(this)),
          }
        })
    },
    currentCampCollaborationIRIs() {
      return (
        this.dayResponsibles?.items.map((item) => item.campCollaboration()._meta.self) ??
        []
      )
    },
    dayResponsibles() {
      return this.day?.dayResponsibles()
    },
    campCollaborations() {
      return this.period.camp().campCollaborations()
    },

    // returns the day entity which corresponds to the provided date string
    day() {
      return this.period.days().items.find((day) => {
        return this.$date.utc(this.date).isSame(this.$date.utc(day.start), 'day')
      })
    },
  },
  async mounted() {
    await Promise.all([
      this.period.camp().campCollaborations()._meta.load,
      this.period.days().$reload(),
    ])

    this.isLoading = false

    this.oldSelectedCampCollaborations = [...this.currentCampCollaborationIRIs]
    this.selectedCampCollaborations = [...this.currentCampCollaborationIRIs]
  },
  methods: {
    onInput() {
      const promises = []
      this.errorMessages = []
      this.isSaving = true

      // add new items
      const newItems = this.selectedCampCollaborations.filter(
        (item) => !this.oldSelectedCampCollaborations.includes(item)
      )
      newItems.forEach((campCollaborationIRI) => {
        promises.push(
          this.dayResponsibles.$post({
            day: this.day._meta.self,
            campCollaboration: campCollaborationIRI,
          })
        )
      })

      // delete removed items
      const removedItems = this.oldSelectedCampCollaborations.filter(
        (item) => !this.selectedCampCollaborations.includes(item)
      )
      removedItems.forEach((campCollaborationIRI) => {
        const dayResponsible = this.dayResponsibles.items.find(
          (item) => item.campCollaboration()._meta.self === campCollaborationIRI
        )
        if (dayResponsible !== undefined) {
          promises.push(dayResponsible.$del())
        }
      })

      // reset comparison value
      this.oldSelectedCampCollaborations = [...this.selectedCampCollaborations]

      Promise.all(promises)
        .then(() => {
          this.dayResponsibles.$reload()
        })
        .catch((e) => {
          this.errorMessages.push(serverErrorToString(e))
        })
        .finally(() => {
          this.isSaving = false
        })
    },
  },
}
</script>

<style lang="scss" scoped>
:deep(.v-skeleton-loader__text) {
  height: 40px;
  padding-left: 0 !important;
  padding-right: 0 !important;
}

:deep(.v-input) {
  margin-top: 0;
  padding-top: 0;
}

.ec-day-responsible--readonly:deep(.v-input__append-inner) {
  display: none;
}
</style>
