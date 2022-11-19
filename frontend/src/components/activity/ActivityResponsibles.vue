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
    @input="onInput"
  >
    <template #selection="{ item }">
      <v-chip :key="item.value" small class="mx-0">
        <UserAvatar
          :user="item.info.user && item.info.user()"
          :camp-collaboration="item.info.campCollaboration"
          left
          size="20"
          class="ml-n3"
        />
        <span>{{ item.text }}</span>
      </v-chip>
    </template>
  </e-select>
</template>

<script>
import { serverErrorToString } from '@/helpers/serverError.js'
import campCollaborationDisplayName from '@/common/helpers/campCollaborationDisplayName.js'
import { isEqual, sortBy } from 'lodash'
import UserAvatar from '@/components/user/UserAvatar.vue'

export default {
  name: 'ActivityResponsibles',
  components: { UserAvatar },
  props: {
    activity: {
      type: Object,
      required: true,
    },
  },
  data() {
    return {
      oldSelectedCampCollaborations: [],
      selectedCampCollaborations: [],
      errorMessages: [],
      isSaving: false,
      dirty: false,
    }
  },
  computed: {
    isLoading() {
      return (
        this.campCollaborations._meta.loading || this.activityResponsibles._meta.loading
      )
    },
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
            info: value,
            text: campCollaborationDisplayName(value, this.$tc.bind(this)),
          }
        })
    },
    currentCampCollaborationIRIs() {
      return this.activityResponsibles.items.map(
        (item) => item.campCollaboration()._meta.self
      )
    },
    activityResponsibles() {
      return this.activity.activityResponsibles()
    },
    campCollaborations() {
      return this.activity.camp().campCollaborations()
    },
  },
  watch: {
    activity: {
      async handler(newActivity, oldActivity) {
        // Activity changed; reset SelectedCampCollaboration-Arrays
        if (oldActivity?._meta.self != newActivity._meta.self) {
          // Set Array to empty until collection is loaded
          this.selectedCampCollaborations = []
          this.oldSelectedCampCollaborations = []
          await this.activityResponsibles._meta.load
          this.resetLocalData()
        }
      },
      immediate: true,
    },
    currentCampCollaborationIRIs: {
      async handler(newIRIs, oldIRIs) {
        if (isEqual(sortBy(newIRIs), sortBy(oldIRIs))) {
          return
        }

        // copy incoming data if not dirty or if incoming data is the same as local data
        if (
          !this.dirty ||
          isEqual(sortBy(newIRIs), sortBy(this.selectedCampCollaborations))
        ) {
          this.resetLocalData()
        }
      },
      immediate: true,
    },
  },
  methods: {
    resetLocalData() {
      this.selectedCampCollaborations = [...this.currentCampCollaborationIRIs]
      this.oldSelectedCampCollaborations = [...this.currentCampCollaborationIRIs]
      this.dirty = false
    },
    onInput() {
      const promises = []
      this.errorMessages = []
      this.isSaving = true
      this.dirty = true

      // add new items
      const newItems = this.selectedCampCollaborations.filter(
        (item) => !this.oldSelectedCampCollaborations.includes(item)
      )

      newItems.forEach((campCollaborationIRI) => {
        promises.push(
          this.activity.activityResponsibles().$post({
            activity: this.activity._meta.self,
            campCollaboration: campCollaborationIRI,
          })
        )
      })

      // delete removed items
      const removedItems = this.oldSelectedCampCollaborations.filter(
        (item) => !this.selectedCampCollaborations.includes(item)
      )
      removedItems.forEach((campCollaborationIRI) => {
        const activityResponsible = this.activityResponsibles.items.find(
          (item) => item.campCollaboration()._meta.self === campCollaborationIRI
        )
        if (activityResponsible !== undefined) {
          promises.push(activityResponsible.$del())
        }
      })

      this.oldSelectedCampCollaborations = [...this.selectedCampCollaborations]

      Promise.all(promises)
        .then(async () => {
          await this.activityResponsibles.$reload()
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

<style scoped lang="scss">
::v-deep(.v-select__selections) {
  gap: 4px;
  padding-top: 8px !important;

  & input {
    padding: 0;
  }
}
</style>
