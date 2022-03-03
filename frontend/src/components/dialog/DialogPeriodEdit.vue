<template>
  <dialog-form
    v-model="showDialog"
    :loading="loading"
    :error="error"
    icon="mdi-calendar-edit"
    :title="period.description"
    max-width="600px"
    :submit-action="update"
    :cancel-action="close">
    <template #activator="scope">
      <slot name="activator" v-bind="scope" />
    </template>
    <dialog-period-form :period="entityData" />

    <e-checkbox v-model="entityData.moveScheduleEntries"
                :name="$tc('components.dialog.dialogPeriodEdit.moveScheduleEntries')" />

    <svg viewBox="0 12 400 76" width="100%"
         height="80"
         xmlns="http://www.w3.org/2000/svg">

      <rect x="0" y="15"
            width="14" height="70"
            class="day-background" />
      <rect x="42" y="15"
            width="28" height="70"
            class="day-background" />
      <rect x="98" y="15"
            width="28" height="70"
            class="day-background" />
      <rect x="154" y="15"
            width="28" height="70"
            class="day-background" />

      <rect x="218" y="15"
            width="28" height="70"
            class="day-background" />
      <rect x="274" y="15"
            width="28" height="70"
            class="day-background" />
      <rect x="330" y="15"
            width="28" height="70"
            class="day-background" />
      <rect x="386" y="15"
            width="14" height="70"
            class="day-background" />

      <g :class="{'annimation-all': entityData.moveScheduleEntries }">
        <rect x="70" y="22"
              width="260" height="56"
              ry="1" class="period"
              :class="{ 'annimation-period': !entityData.moveScheduleEntries, 'period': entityData.moveScheduleEntries }" />

        <rect x="101" y="40"
              width="22" height="32"
              class="category1" />
        <rect x="129" y="34"
              width="22" height="32"
              class="category2" />

        <rect x="249" y="40"
              width="22" height="32"
              class="category1" />
        <rect x="277" y="34"
              width="22" height="32"
              class="category2" />
      </g>
      <path style="stroke: rgb(0, 0, 0); fill: rgba(0, 0, 0, 0); stroke-width: 5px;"
            d="M 200 10 C 190 20 190 40 200 50 C 210 60 210 80 200 90" />

      <path style="stroke: rgb(255, 255, 255); fill: rgba(0, 0, 0, 0); stroke-width: 2px;"
            d="M 200 10 C 190 20 190 40 200 50 C 210 60 210 80 200 90" />
    </svg>
  </dialog-form>
</template>

<script>
import DialogBase from '@/components/dialog/DialogBase.vue'
import DialogForm from '@/components/dialog/DialogForm.vue'
import DialogPeriodForm from './DialogPeriodForm.vue'

export default {
  name: 'DialogPeriodEdit',
  components: { DialogForm, DialogPeriodForm },
  extends: DialogBase,
  props: {
    period: { type: Object, required: true }
  },
  data () {
    return {
      entityProperties: [
        'description',
        'start',
        'end',
        'moveScheduleEntries'
      ]
    }
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
        this.entityData.moveScheduleEntries = true
      }
    }
  }
}
</script>

<style scoped>
  rect.day-background {
    fill: rgb(200, 200, 200);
  }
  rect.period {
    stroke: rgb(0, 0, 0);
    fill: rgba(30, 30, 255, 0.18);
  }
  rect.category1 {
    fill: rgb(255, 137, 26);
  }
  rect.category2 {
    fill: rgb(45, 172, 13);
  }

  /* Move Period + Events */
  g.annimation-all {
      animation-name: move;
      animation-duration: 5s;
      animation-direction: normal;
      animation-timing-function: linear;
      animation-iteration-count: infinite;
  }
  g.annimation-all rect.period {
      animation-name: resize;
      animation-duration: 5s;
      animation-direction: normal;
      animation-timing-function: linear;
      animation-iteration-count: infinite;
  }

  /* Move Period */
  rect.annimation-period {
      animation-name: move-and-resize;
      animation-duration: 5s;
      animation-direction: normal;
      animation-timing-function: linear;
      animation-iteration-count: infinite;
  }

  @keyframes move {
      0% { transform: translate(0px, 0px); }
      3% { transform: translate(28px, 0px); }
      25% { transform: translate(28px, 0px); }
      28% { transform: translate(0px, 0px); }
      50% { transform: translate(0px, 0px); }
      53% { transform: translate(-28px, 0px); }
      75% { transform: translate(-28px, 0px); }
      78% { transform: translate(0px, 0px); }
  }

  @keyframes resize {
      0% { width: 260px; }
      3% { width: 260px; }
      25% { width: 260px; }
      28% { width: 260px; }
      50% { width: 260px; }
      53% { width: 288px; }
      75% { width: 288px; }
      78% { width: 260px; }
  }

  @keyframes move-and-resize {
      0% { transform: translate(0px, 0px); width: 260px; }
      3% { transform: translate(28px, 0px); width: 260px; }
      25% { transform: translate(28px, 0px); width: 260px; }
      28% { transform: translate(0px, 0px); width: 260px; }
      50% { transform: translate(0px, 0px); width: 260px; }
      53% { transform: translate(-28px, 0px); width: 288px; }
      75% { transform: translate(-28px, 0px); width: 288px; }
      78% { transform: translate(0px, 0px); width: 260px; }
  }
</style>
