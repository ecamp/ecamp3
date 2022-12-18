<template>
  <dialog-form
    v-model="showDialog"
    :loading="loading"
    :error="error"
    icon="mdi-package-variant"
    :title="period.description"
    :submit-action="update"
    submit-color="success"
    :cancel-action="close"
  >
    <template #activator="scope">
      <slot name="activator" v-bind="scope" />
    </template>
    <div v-if="modus == 'move'">
      <p>{{ $tc('components.campAdmin.dialogPeriodDateEdit.movePeriod') }}</p>
      <e-date-picker
        v-model="entityData.start"
        :name="$tc('entity.period.fields.start')"
        vee-rules="required"
        @input="startChanged"
      />
      <div class="e-form-container">
        <v-text-field
          :value="endString"
          :label="$tc('entity.period.fields.end')"
          hide-details
          filled
          disabled
        >
          <template #prepend><v-icon>mdi-calendar</v-icon></template>
        </v-text-field>
      </div>
    </div>
    <div v-if="modus == 'changeStart'">
      <p>{{ $tc('components.campAdmin.dialogPeriodDateEdit.periodChangeStart') }}</p>
      <e-date-picker
        v-model="entityData.start"
        :name="$tc('entity.period.fields.start')"
        :vee-rules="'required|lessThanOrEqual_date:' + endString"
        @input="startChanged"
      />
      <div class="e-form-container">
        <v-text-field
          :value="endString"
          :label="$tc('entity.period.fields.end')"
          hide-details
          filled
          disabled
        >
          <template #prepend>
            <v-icon>mdi-calendar</v-icon>
          </template>
        </v-text-field>
      </div>
    </div>
    <div v-if="modus == 'changeEnd'">
      <p>{{ $tc('components.campAdmin.dialogPeriodDateEdit.periodChangeEnd') }}</p>
      <div class="e-form-container">
        <v-text-field
          :value="startString"
          :label="$tc('entity.period.fields.start')"
          hide-details
          filled
          disabled
        >
          <template #prepend>
            <v-icon>mdi-calendar</v-icon>
          </template>
        </v-text-field>
      </div>
      <e-date-picker
        v-if="modus == 'changeEnd'"
        v-model="entityData.end"
        :name="$tc('entity.period.fields.end')"
        :vee-rules="'required|greaterThanOrEqual_date:' + startString"
      />
    </div>
  </dialog-form>
</template>

<script>
import DialogBase from '@/components/dialog/DialogBase.vue'
import DialogForm from '@/components/dialog/DialogForm.vue'

export default {
  name: 'DialogPeriodDateEdit',
  components: { DialogForm },
  extends: DialogBase,
  props: {
    period: { type: Object, required: true },
    modus: { type: String, required: true },
  },
  data() {
    return {
      entityProperties: ['start', 'end', 'moveScheduleEntries'],
    }
  },
  computed: {
    startString() {
      return this.$date.utc(this.entityData.start, 'YYYY-MM-DD').format('L')
    },
    endString() {
      return this.$date.utc(this.entityData.end, 'YYYY-MM-DD').format('L')
    },
  },
  watch: {
    // copy data whenever dialog is opened
    showDialog: function (showDialog) {
      if (showDialog) {
        this.loadEntityData(this.period._meta.self)
      }
    },
    loading: function (isLoading) {
      if (!isLoading) {
        this.entityData.moveScheduleEntries = this.modus == 'move'
      }
    },
  },
  methods: {
    startChanged() {
      if (this.modus == 'move') {
        let origStart = this.$date.utc(this.period.start, 'YYYY-MM-DD')
        let origEnd = this.$date.utc(this.period.end, 'YYYY-MM-DD')
        let origLength = origEnd - origStart
        let start = this.$date.utc(this.entityData.start, 'YYYY-MM-DD')
        let end = start.add(origLength)
        this.entityData.end = end.format('YYYY-MM-DD')
      }
    },
  },
}
</script>

<style scoped></style>
