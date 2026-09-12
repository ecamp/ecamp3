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
    path="dayResponsibles"
    :error-messages="errorMessages"
    :menu-props="{ closeOnClick: true, closeOnContentClick: true, overflowY: true }"
    variant="underlined"
    multiple
    chips
    single-line
    small-chips
    persistent-placeholder
    v-bind="$attrs"
    :readonly="readonly"
    class="e-day-responsible-dropdown rounded-0"
    :class="{ 'e-day-responsible--readonly': readonly }"
    @update:model-value="onInput"
  >
    <template #prepend-item>
      <v-list-subheader>{{ $t('entity.day.fields.dayResponsibles') }}</v-list-subheader>
    </template>
  </e-select>
</template>

<script>
import { serverErrorToString } from '@/helpers/serverError.js'
import campCollaborationDisplayName from '@/common/helpers/campCollaborationDisplayName.js'

export default {
  name: 'DayResponsibles',
  provide() {
    return {
      entityName: 'day',
    }
  },
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
            text: campCollaborationDisplayName(value, this.$t.bind(this)),
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
  watch: {
    // reset selectedCampCollaborations when date changes
    date() {
      this.oldSelectedCampCollaborations = [...this.currentCampCollaborationIRIs]
      this.selectedCampCollaborations = [...this.currentCampCollaborationIRIs]
    },
  },
  async mounted() {
    // reload days before reading dayResponsibles below (fixes #9756)
    await this.period.days().$reload()

    await Promise.all([
      this.period.camp().campCollaborations()._meta.load,
      this.dayResponsibles?._meta.load,
    ])

    this.isLoading = false

    this.oldSelectedCampCollaborations = [...this.currentCampCollaborationIRIs]
    this.selectedCampCollaborations = [...this.currentCampCollaborationIRIs]
  },
  methods: {
    async onInput() {
      const promises = []
      this.errorMessages = []
      this.isSaving = true

      // add new items
      const dayReponsiblesIri = await this.api.href(this.api.get(), 'dayResponsibles')
      const newItems = this.selectedCampCollaborations.filter(
        (item) => !this.oldSelectedCampCollaborations.includes(item)
      )
      newItems.forEach((campCollaborationIRI) => {
        promises.push(
          this.api.post(dayReponsiblesIri, {
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

.e-day-responsible--readonly {
  :deep(.v-field__input) {
    justify-content: center !important;
  }
  :deep(.v-field__overlay) {
    opacity: 0;
  }
  &:deep(.v-field),
  &:deep(.v-field__input) {
    cursor: auto;
  }
}

.e-day-responsible-dropdown :deep(.v-field__input) {
  justify-content: start;
}

.e-day-responsible-dropdown :deep(.v-field) {
  &:not(:hover) .v-field__overlay {
    opacity: 0;
  }
  .v-field__append-inner {
    align-items: baseline;
  }
  --v-input-control-height: auto;
  --v-field-padding-start: 2px;
  --v-field-input-padding-top: 2px;
  --v-field-input-padding-bottom: 2px;
  --v-field-padding-end: 2px;
}

.e-day-responsible-dropdown :deep(.v-field--appended) {
  -webkit-padding-end: 0;
  padding-inline-end: 0;
}

:deep(.v-select__selections) input {
  display: none;
}
</style>
