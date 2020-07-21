<template>
  <v-card
    outlined>
    <dialog-entity-delete :entity="activityContent">
      <template v-slot:activator="{ on }">
        <v-btn
          color="error"
          class="float-right delete-button"
          icon
          v-on="on">
          <v-icon>mdi-delete</v-icon>
        </v-btn>
      </template>
    </dialog-entity-delete>

    <v-card-title class="card-title">
      <div class="overline mb-4">
        {{ instanceName }}
      </div>
    </v-card-title>
    <v-card-text class="card-content">
      <component :is="activityContent.contentTypeName" :activity-content="activityContent" />
    </v-card-text>
  </v-card>
</template>

<script>

import Storycontext from '@/components/activity/content/Storycontext'
import Storyboard from '@/components/activity/content/Storyboard'
import Notes from '@/components/activity/content/Notes'
import DialogEntityDelete from '@/components/dialog/DialogEntityDelete'
import ButtonDelete from '@/components/buttons/ButtonDelete'
import camelCase from 'lodash/camelCase'

export default {
  name: 'ActivityContent',
  components: {
    Storycontext,
    Storyboard,
    Notes,
    DialogEntityDelete,
    ButtonDelete
  },
  props: {
    activityContent: { type: Object, required: true }
  },
  data () {
    return {
      isDeleting: false
    }
  },
  computed: {
    instanceName () {
      if (this.activityContent.instanceName) {
        return this.activityContent.instanceName
      }
      return this.$t(`activityContent.${camelCase(this.activityContent.contentTypeName)}.name`)
    }
  },
  methods: {
    async removeActivityContent () {
      this.api.del(this.activityContent)
    }
  }
}
</script>

<style scoped>
  .card-title {
    padding-bottom:4px;
  }

  .card-content {
    padding-bottom:4px;
  }

  .delete-button {
    margin-right:5px;
    margin-top:5px;
  }
</style>
