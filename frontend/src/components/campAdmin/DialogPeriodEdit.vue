<template>
  <dialog-form
    v-model="showDialog"
    :loading="loading"
    :error="error"
    icon="mdi-calendar-edit"
    :title="period.description"
    :submit-action="update"
    :cancel-action="close"
  >
    <template #activator="scope">
      <slot name="activator" v-bind="scope" />
    </template>

    <dialog-period-form
      :period="entityData"
      :start-disabled="startDisabled"
      :end-disabled="endDisabled"
    >
      <template #beforeDates>
        <e-select
          v-model="mode"
          :name="$tc('components.campAdmin.dialogPeriodEdit.mode')"
          :items="modeItems"
          class="mt-6"
        />
      </template>
    </dialog-period-form>

    <e-checkbox
      v-model="entityData.moveScheduleEntries"
      :disabled="moveScheduleEntriesDisabled"
      :name="$tc('components.campAdmin.dialogPeriodEdit.moveScheduleEntries')"
    />
    <svg
      viewBox="0 12 400 76"
      width="100%"
      height="80"
      xmlns="http://www.w3.org/2000/svg"
    >
      <rect x="0" y="15" width="14" height="70" class="day-background" />
      <rect x="42" y="15" width="28" height="70" class="day-background" />
      <rect x="98" y="15" width="28" height="70" class="day-background" />
      <rect x="154" y="15" width="28" height="70" class="day-background" />

      <rect x="218" y="15" width="28" height="70" class="day-background" />
      <rect x="274" y="15" width="28" height="70" class="day-background" />
      <rect x="330" y="15" width="28" height="70" class="day-background" />
      <rect x="386" y="15" width="14" height="70" class="day-background" />

      <g class="periodGrp" :class="cls">
        <rect x="70" y="22" width="260" height="56" ry="2" class="period" :class="cls" />

        <rect x="101" y="40" width="22" height="32" class="category c1" :class="cls" />
        <rect x="129" y="34" width="22" height="32" class="category c2" :class="cls" />

        <rect x="249" y="40" width="22" height="32" class="category c1" :class="cls" />
        <rect x="277" y="34" width="22" height="32" class="category c2" :class="cls" />
      </g>
      <path
        style="stroke: rgb(0, 0, 0); fill: rgba(0, 0, 0, 0); stroke-width: 5px"
        d="M 200 10 C 190 20 190 40 200 50 C 210 60 210 80 200 90"
      />

      <path
        style="stroke: rgb(255, 255, 255); fill: rgba(0, 0, 0, 0); stroke-width: 2px"
        d="M 200 10 C 190 20 190 40 200 50 C 210 60 210 80 200 90"
      />
    </svg>
  </dialog-form>
</template>

<script>
import DialogBase from '@/components/dialog/DialogBase.vue'
import DialogForm from '@/components/dialog/DialogForm.vue'
import DialogPeriodForm from './DialogPeriodForm.vue'

const MODE_FREE = 'free'
const MODE_MOVE_PERIOD = 'move-period'
const MODE_MOVE_START = 'move-start'
const MODE_MOVE_END = 'move-end'

export default {
  name: 'DialogPeriodEdit',
  components: { DialogForm, DialogPeriodForm },
  extends: DialogBase,
  props: {
    period: { type: Object, required: true },
  },
  data() {
    return {
      entityProperties: ['description', 'start', 'end', 'moveScheduleEntries'],
      modes: [MODE_FREE, MODE_MOVE_PERIOD, MODE_MOVE_START, MODE_MOVE_END],
      mode: MODE_MOVE_PERIOD,
      startDisabled: false,
      endDisabled: true,
      moveScheduleEntriesDisabled: true,
    }
  },
  computed: {
    modeItems() {
      return this.modes.map((item) => {
        return {
          text: this.$tc('components.campAdmin.dialogPeriodEdit.modes.' + item),
          value: item,
        }
      })
    },
    cls() {
      if (this.mode == MODE_FREE) {
        return this.mode + (this.entityData.moveScheduleEntries ? '-move' : '-stay')
      }
      return this.mode
    },
  },
  watch: {
    // copy data whenever dialog is opened
    showDialog: function (showDialog) {
      if (showDialog) {
        this.loadEntityData(this.period._meta.self)
        this.mode = MODE_MOVE_PERIOD
        this.startDisabled = false
        this.endDisabled = true
        this.moveScheduleEntriesDisabled = true
      }
    },
    loading: function (isLoading) {
      if (!isLoading) {
        this.$set(this.entityData, 'moveScheduleEntries', true)
      }
    },
    mode: function (mode) {
      this.$set(this.entityData, 'start', this.period.start)
      this.$set(this.entityData, 'end', this.period.end)
      switch (mode) {
        case MODE_FREE:
          this.startDisabled = false
          this.endDisabled = false
          break

        case MODE_MOVE_PERIOD:
          this.$set(this.entityData, 'moveScheduleEntries', true)
          this.startDisabled = false
          this.endDisabled = true
          break

        case MODE_MOVE_START:
          this.$set(this.entityData, 'moveScheduleEntries', false)
          this.startDisabled = false
          this.endDisabled = true
          break

        case MODE_MOVE_END:
          this.$set(this.entityData, 'moveScheduleEntries', false)
          this.startDisabled = true
          this.endDisabled = false
          break
      }
      this.moveScheduleEntriesDisabled = mode != MODE_FREE
    },
    entityData: {
      handler(period) {
        if (this.mode == MODE_MOVE_PERIOD) {
          const newEnd = this.$date.unix(
            this.$date.utc(period.start, 'YYYY-MM-DD').unix() -
              this.$date.utc(this.period.start, 'YYYY-MM-DD').unix() +
              this.$date.utc(this.period.end, 'YYYY-MM-DD').unix()
          )
          this.$set(period, 'end', newEnd.format('YYYY-MM-DD'))
        }
      },
      deep: true,
    },
  },
  mounted() {
    this.mode = MODE_MOVE_PERIOD
    this.startDisabled = false
    this.endDisabled = true
    this.moveScheduleEntriesDisabled = true
  },
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
rect.category.c1 {
  fill: rgb(255, 137, 26);
}
rect.category.c2 {
  fill: rgb(45, 172, 13);
}

/* Animations */
g.periodGrp,
rect.period,
rect.category {
  animation-duration: 5s;
  animation-direction: normal;
  animation-timing-function: linear;
  animation-iteration-count: infinite;
}

g.periodGrp.free-move {
  animation-name: free-grp;
}
rect.period.free-move {
  animation-name: free-move-period;
}

rect.period.free-stay {
  animation-name: free-stay-period;
}

rect.period.move-period,
rect.category.move-period {
  animation-name: move-period;
}

rect.period.move-start {
  animation-name: period-move-start;
}

rect.period.move-end {
  animation-name: period-move-end;
}

@keyframes free-grp {
  0% {
    transform: translate(0px, 0px);
  }
  3% {
    transform: translate(-28px, 0px);
  }
  25% {
    transform: translate(-28px, 0px);
  }
  28% {
    transform: translate(0px, 0px);
  }
}

@keyframes free-move-period {
  50% {
    width: 260px;
  }
  53% {
    width: 288px;
  }
  75% {
    width: 288px;
  }
  78% {
    width: 260px;
  }
}

@keyframes free-stay-period {
  0% {
    transform: translate(0px, 0px);
    width: 260px;
  }
  3% {
    transform: translate(0px, 0px);
    width: 288px;
  }
  25% {
    transform: translate(0px, 0px);
    width: 288px;
  }
  28% {
    transform: translate(0px, 0px);
    width: 260px;
  }
  50% {
    transform: translate(0px, 0px);
    width: 260px;
  }
  53% {
    transform: translate(-28px, 0px);
    width: 288px;
  }
  75% {
    transform: translate(-28px, 0px);
    width: 288px;
  }
  78% {
    transform: translate(0px, 0px);
    width: 260px;
  }
}

@keyframes move-period {
  0% {
    transform: translate(0px, 0px);
  }
  3% {
    transform: translate(28px, 0px);
  }
  25% {
    transform: translate(28px, 0px);
  }
  28% {
    transform: translate(0px, 0px);
  }
  50% {
    transform: translate(0px, 0px);
  }
  53% {
    transform: translate(-28px, 0px);
  }
  75% {
    transform: translate(-28px, 0px);
  }
  78% {
    transform: translate(0px, 0px);
  }
}

@keyframes period-move-start {
  0% {
    transform: translate(0px, 0px);
    width: 260px;
  }
  3% {
    transform: translate(28px, 0px);
    width: 232px;
  }
  25% {
    transform: translate(28px, 0px);
    width: 232px;
  }
  28% {
    transform: translate(0px, 0px);
    width: 260px;
  }
  50% {
    transform: translate(0px, 0px);
    width: 260px;
  }
  53% {
    transform: translate(-28px, 0px);
    width: 288px;
  }
  75% {
    transform: translate(-28px, 0px);
    width: 288px;
  }
  78% {
    transform: translate(0px, 0px);
    width: 260px;
  }
}

@keyframes period-move-end {
  0% {
    width: 260px;
  }
  3% {
    width: 232px;
  }
  25% {
    width: 232px;
  }
  28% {
    width: 260px;
  }
  50% {
    width: 260px;
  }
  53% {
    width: 288px;
  }
  75% {
    width: 288px;
  }
  78% {
    width: 260px;
  }
}
</style>
