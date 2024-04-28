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
    <e-form v-if="mode == 'move'" class="e-form-container" name="period">
      <p>{{ $tc('components.campAdmin.dialogPeriodDateEdit.movePeriod') }}</p>
      <e-date-picker
        v-model="entityData.start"
        path="start"
        icon="mdi-calendar-start"
        vee-rules="required"
        @input="startChanged"
      />
      <e-text-field
        :value="endString"
        path="end"
        hide-details
        readonly
        prepend-icon="mdi-calendar-end"
      />
    </e-form>
    <e-form v-if="mode == 'changeStart'" class="e-form-container" name="period">
      <p>{{ $tc('components.campAdmin.dialogPeriodDateEdit.periodChangeStart') }}</p>
      <e-date-picker
        v-model="entityData.start"
        path="start"
        icon="mdi-calendar-start"
        :vee-rules="'required|lessThanOrEqual_date:' + endString"
        @input="startChanged"
      />
      <e-text-field
        :value="endString"
        path="end"
        hide-details
        readonly
        prepend-icon="mdi-calendar-end"
      />
    </e-form>
    <e-form v-if="mode == 'changeEnd'" class="e-form-container" name="period">
      <p>{{ $tc('components.campAdmin.dialogPeriodDateEdit.periodChangeEnd') }}</p>
      <e-text-field
        :value="startString"
        path="start"
        hide-details
        readonly
        prepend-icon="mdi-calendar-start"
      />
      <e-date-picker
        v-if="mode == 'changeEnd'"
        v-model="entityData.end"
        path="end"
        icon="mdi-calendar-end"
        :vee-rules="'required|greaterThanOrEqual_date:' + startString"
      />
    </e-form>
    <e-text-field
      :value="periodDurationInDays"
      :label="$tc('components.campAdmin.dialogPeriodDateEdit.periodDuration')"
      hide-details
      prepend-icon="mdi-calendar-expand-horizontal"
      readonly
    />
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
    mode: { type: String, required: true },
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
    periodDurationInDays() {
      const start = this.$date.utc(this.entityData.start, 'YYYY-MM-DD')
      const end = this.$date.utc(this.entityData.end, 'YYYY-MM-DD')
      return 1 + end.diff(start, 'day')
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
        this.entityData.moveScheduleEntries = this.mode == 'move'
      }
    },
  },
  methods: {
    startChanged() {
      if (this.mode == 'move') {
        const origStart = this.$date.utc(this.period.start, 'YYYY-MM-DD')
        const origEnd = this.$date.utc(this.period.end, 'YYYY-MM-DD')
        const origLength = origEnd - origStart
        const start = this.$date.utc(this.entityData.start, 'YYYY-MM-DD')
        const end = start.add(origLength)
        this.entityData.end = end.format('YYYY-MM-DD')
      }
    },
  },
}
</script>

<style scoped></style>
