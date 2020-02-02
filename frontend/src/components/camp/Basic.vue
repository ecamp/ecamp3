<!--
Displays details on a single camp and allows to edit them.
-->

<template>
  <div>
    <v-card class="mb-4">
      <v-toolbar dense color="blue-grey lighten-5">
        <v-icon left>
          mdi-cogs
        </v-icon>
        <v-toolbar-title>
          Einstellungen
        </v-toolbar-title>
      </v-toolbar>
      <v-card-text v-if="campDetails.loaded">
        <v-skeleton-loader type="text" max-width="100" class="mt-2" />
        <v-skeleton-loader type="heading" class="mb-2" />
        <v-skeleton-loader type="text" max-width="100" class="mt-7" />
        <v-skeleton-loader type="heading" class="mb-2" />
        <v-skeleton-loader type="text" max-width="100" class="mt-7" />
        <v-skeleton-loader type="heading" class="mb-5" />
      </v-card-text>
      <v-card-text v-if="!campDetails.loaded">
        <v-alert
          v-for="(message, index) in messages"
          :key="index"
          :type="message.type">
          {{ message.text }}
        </v-alert>
        <v-form>
          <v-text-field
            :value="campDetails.name"
            readonly
            label="Name" />
          <v-text-field
            :value="campDetails.title"
            :readonly="editing"
            label="Titel" />
          <v-text-field
            :value="campDetails.motto"
            :readonly="editing"
            label="Motto" />
        </v-form>
      </v-card-text>
    </v-card>
    <v-card class="mb-4">
      <v-toolbar dense color="blue-grey lighten-5">
        <v-icon left>
          mdi-calendar-plus
        </v-icon>
        <v-toolbar-title>
          Periods
        </v-toolbar-title>
      </v-toolbar>
      <v-card-text v-if="campDetails.loaded">
        <v-skeleton-loader type="article" />
      </v-card-text>
      <v-card-text v-if="!campDetails.loaded">
        <v-list>
          <v-list-item
            v-for="period in periods"
            :key="period.id">
            <v-list-item-content>
              <v-list-item-title>{{ period.description }}</v-list-item-title>
              <v-list-item-subtitle>{{ period.start }} - {{ period.end }}</v-list-item-subtitle>
            </v-list-item-content>
            <v-list-item-action style="display: inline">
              <v-item-group>
                <v-btn
                  small
                  color="primary"
                  class="mr-1"
                  @click="startEditPeriod(period)">
                  <i class="v-icon v-icon--left mdi mdi-pencil" />
                  Edit
                </v-btn>
                <v-btn
                  small
                  color="error"
                  @click="periodDelete=period, showPeriodDelete=true">
                  <i class="v-icon v-icon--left mdi mdi-delete" />
                  Delete
                </v-btn>
              </v-item-group>
            </v-list-item-action>
          </v-list-item>
          <v-list-item>
            <v-list-item-content />
            <v-list-item-action>
              <v-btn
                small
                color="success"
                class="mb-1"
                @click="startCreatePeriod()">
                <i class="v-icon v-icon--left mdi mdi-plus" />
                Create Period
              </v-btn>
            </v-list-item-action>
          </v-list-item>
        </v-list>
      </v-card-text>
    </v-card>

    <v-dialog
      v-model="showPeriodEdit"
      max-width="600px">
      <v-card v-if="periodEdit != null">
        <v-card-title>
          <span class="headline">User Profile</span>
        </v-card-title>
        <v-card-text>
          <v-container>
            <v-row>
              <v-col cols="12">
                <v-text-field
                  v-model="periodEdit.description"
                  label="Description"
                  required />
              </v-col>
              <v-col cols="12">
                <v-text-field
                  v-model="periodEdit.start"
                  label="Start"
                  required />
              </v-col>
              <v-col cols="12">
                <v-text-field
                  v-model="periodEdit.end"
                  label="End"
                  required />
              </v-col>
            </v-row>
          </v-container>
        </v-card-text>
        <v-card-actions>
          <v-spacer />
          <v-btn color="secundary" @click="cancelEditPeriod()">Close</v-btn>
          <v-btn v-if="periodEdit.id == null" color="success" @click="createPeriod">
            <i class="v-icon mdi mdi-plus" />
            Create
          </v-btn>
          <v-btn v-if="periodEdit.id != null" color="success" @click="updatePeriod">
            <i class="v-icon mdi mdi-check" />
            Save
          </v-btn>
        </v-card-actions>
      </v-card>
    </v-dialog>

    <v-dialog v-model="showPeriodDelete" max-width="290">
      <v-card>
        <v-card-title class="headline">
          Delete Period?
        </v-card-title>
        <v-card-text>
          Are you sure?
        </v-card-text>
        <v-card-actions>
          <v-spacer />
          <v-btn color="error" @click="deletePeriod(periodDelete)">Delete</v-btn>
          <v-btn color="" @click="showPeriodDelete=false">Cancel</v-btn>
        </v-card-actions>
      </v-card>
    </v-dialog>
  </div>
</template>

<script>
export default {
  name: 'Basic',
  props: {
    campUri: { type: String, required: true }
  },
  data () {
    return {
      periodEdit: null,
      periodEditUri: null,
      showPeriodEdit: false,
      periodDelete: null,
      showPeriodDelete: false,
      messages: []
    }
  },
  computed: {
    campDetails () {
      return this.api.get(this.campUri)
    },
    periods () {
      return this.campDetails.periods().items
    }
  },
  methods: {
    startCreatePeriod () {
      this.periodEdit = {
        id: null,
        camp_id: this.campDetails.id,
        description: '',
        start: null,
        end: null
      }
      this.periodEditUri = '/period'
      this.showPeriodEdit = true
    },
    startEditPeriod (period) {
      this.periodEdit = {
        id: period.id,
        camp_id: period.camp_id,
        description: period.description,
        start: period.start,
        end: period.end
      }
      this.periodEditUri = period._meta.self
      this.showPeriodEdit = true
    },
    async createPeriod () {
      await this.api.post(this.periodEditUri, this.periodEdit)
      this.cancelEditPeriod()
      this.api.reload(this.campUri)
    },
    async updatePeriod () {
      const data = {
        description: this.periodEdit.description,
        start: this.periodEdit.start,
        end: this.periodEdit.end
      }
      await this.api.patch(this.periodEditUri, data)
      this.cancelEditPeriod()
    },
    async deletePeriod (period) {
      await this.api.del(period._meta.self)
      this.showPeriodDelete = false
    },
    cancelEditPeriod () {
      this.periodEdit = null
      this.periodEditUri = null
      this.showPeriodEdit = false
    }
  }
}
</script>

<style scoped>
  .v-list-item {
    padding-left: 0;
  }
</style>
