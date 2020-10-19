<template>
  <v-menu
    v-model="showEntryInfo"
    :activator="selectedElement"
    :open-on-click="false"
    :close-on-click="false"
    :close-on-content-click="false"
    offset-y>
    <v-card v-if="scheduleEntry">
      <v-form>
        <v-card-text>
          <e-text-field v-model="scheduleEntry.activity().title" no-label
                        class="font-weight-bold" placeholder="Aktivitätsname"
                        hide-details="auto" />
          <v-row no-gutters class="my-4">
            <e-time-picker label="Start"
                           :icon="null" class="flex-full"
                           :value="toTimeString(scheduleEntry.startTime)" />
            <e-time-picker width="100" label="Ende"
                           :icon="null" class="flex-full mt-0"
                           :value="toTimeString(scheduleEntry.endTime)" />
          </v-row>
          <v-row no-gutters class="my-4" style="gap: 12px">
            <e-select v-model="scheduleEntry.activity().activityCategory" label="Aktivitätstyp"
                      :items="activityCategories.items" item-value="id"
                      :return-object="true" required
                      item-text="name">
              <template #item="{item, on, attrs}">
                <v-list-item :key="item.id" v-bind="attrs" v-on="on">
                  <v-list-item-avatar>
                    <v-chip :color="item.color">{{ item.short }}</v-chip>
                  </v-list-item-avatar>
                  <v-list-item-content>
                    {{ item.name }}
                  </v-list-item-content>
                </v-list-item>
              </template>
              <template #selection="{item}">
                <div class="v-select__selection">
                    <span class="black--text">
                      {{ item.name }}
                    </span>
                  <v-chip x-small :color="item.color">{{ item.short }}</v-chip>
                </div>
              </template>
            </e-select>
            <e-text-field v-model="scheduleEntry.activity().location" :label="$tc('entity.activity.fields.location')"
                          hide-details="auto"
                          class="mt-0" />
          </v-row>
          <e-select label="Verantwortliche Leitende" multiple
                    chips deletable-chips
                    :items="[{text:'Leitende1'},{text:'Leitende2'}]" />
        </v-card-text>
        <v-card-actions class="px-4 pb-4 flex-wrap">
          <v-alert
            v-if="createdError"
            dense
            class="w-100"
            border="left"
            outlined
            type="error">
            {{ createdError }}
          </v-alert>
          <v-btn v-if="!scheduleEntry.tmpEvent"
                 color="primary" :to="scheduleEntryRoute(scheduleEntry.activity().camp(), scheduleEntry)">
            {{ $tc('global.button.open') }}
          </v-btn>
          <v-btn text color="secondary"
                 class="ml-auto"
                 @click="cancel">
            {{ $tc('global.button.cancel') }}
          </v-btn>
          <v-btn v-if="scheduleEntry.tmpEvent" color="success" @click="create">
            <v-icon v-if="isPersisting" class="mdi-spin">mdi-loading</v-icon>
            <span v-else>{{ createdError ? $tc('global.button.tryagain') : $tc('global.button.create') }}</span>
          </v-btn>
          <v-btn v-else color="success" @click="save">Speichern</v-btn>
        </v-card-actions>
      </v-form>
    </v-card>
  </v-menu>
</template>
<script>
import ESelect from '@/components/form/base/ESelect'
import ETextField from '@/components/form/base/ETextField'
import ETimePicker from '@/components/form/base/ETimePicker'
import { scheduleEntryRoute } from '@/router'

export default {
  name: 'ScheduleEntryPopup',
  components: {
    ESelect,
    ETextField,
    ETimePicker
  },
  props: {
    scheduleEntry: {
      type: Object,
      required: false,
      default: null
    },
    showEntryInfo: {
      type: Boolean,
      default: false
    },
    selectedElement: {
      type: [Object, Element],
      required: false,
      default: null
    },
    clear: {
      type: Function,
      required: true
    },
    revert: {
      type: Function,
      required: true
    }
  },
  data () {
    return {
      isPersisting: false,
      createdError: ''
    }
  },
  computed: {
    scheduleEntriesUrl () {
      return this.api.get().scheduleEntries()._meta.self
    },
    activityCategories () {
      return this.scheduleEntry.activity().camp().activityCategories()
    }
  },
  methods: {
    create () {
      this.isPersisting = true
      this.api.post(this.scheduleEntriesUrl, this.scheduleEntry).then(() => {
        this.isPersisting = false
        this.createdError = ''
        this.$emit('close')
      }).catch((error) => {
        this.isPersisting = false
        this.createdError = error
      })
    },
    cancel () {
      this.$emit('cancel')
    },
    save () {
      // TODO: api push
      this.$emit('close')
    },
    toTimeString (date) {
      return this.$moment(date).format(this.$tc('global.moment.hourLong'))
    },
    scheduleEntryRoute
  }
}
</script>
