<!--
Displays a single activity
-->

<template>
  <v-container fluid>
    <content-card toolbar :loaded="!scheduleEntry()._meta.loading && !activity.camp()._meta.loading">
      <template #title>
        <v-toolbar-title class="font-weight-bold">
          {{ scheduleEntry().number }}
          <v-menu v-if="!category._meta.loading" offset-y :disabled="layoutMode">
            <template #activator="{ on, attrs }">
              <v-chip
                :color="category.color"
                dark
                v-bind="attrs"
                v-on="on">
                {{ category.short }}
              </v-chip>
            </template>
            <v-list>
              <v-list-item
                v-for="cat in camp.categories().items"
                :key="cat._meta.self"
                @click="changeCategory(cat)">
                <v-list-item-title>
                  <v-chip :color="cat.color">
                    {{ cat.short }}
                  </v-chip>
                  {{ cat.name }}
                </v-list-item-title>
              </v-list-item>
            </v-list>
          </v-menu>
          <a v-if="!editActivityTitle"
             style="color: inherit"
             @click="editActivityTitle = true">
            {{ activity.title }}
          </a>
        </v-toolbar-title>
        <div v-if="editActivityTitle" class="mx-2" style="flex-grow: 1">
          <api-text-field
            :uri="activity._meta.self"
            fieldname="title"
            :disabled="layoutMode"
            dense
            autofocus
            :auto-save="false"
            @finished="editActivityTitle = false" />
        </div>
      </template>
      <template #title-actions>
        <!-- layout/content switch -->
        <v-btn v-if="!layoutMode"
               color="primary"
               outlined
               @click="layoutMode = true">
          <template v-if="$vuetify.breakpoint.smAndUp">
            <v-icon left>mdi-puzzle-edit-outline</v-icon>
            {{ $tc('views.activity.activity.changeLayout') }}
          </template>
          <template v-else>{{ $tc('views.activity.activity.layout') }}</template>
        </v-btn>
        <v-btn v-else
               color="success"
               outlined
               @click="layoutMode = false">
          <template v-if="$vuetify.breakpoint.smAndUp">
            <v-icon left>mdi-file-document-edit-outline</v-icon>
            {{ $tc('views.activity.activity.backToContents') }}
          </template>
          <template v-else>{{ $tc('views.activity.activity.back') }}</template>
        </v-btn>

        <!-- print preview button -->
        <v-tooltip bottom>
          <template #activator="{ on, attrs }">
            <v-btn
              class="ml-3"
              color="primary"
              outlined
              :to="{ name: 'camp/print/activity', params: { campId: activity.camp().id, scheduleEntryId: scheduleEntry().id } }"
              v-bind="attrs"
              v-on="on">
              <v-icon>mdi-printer</v-icon>
            </v-btn>
          </template>
          <span>{{ $tc('views.activity.printPreview') }}</span>
        </v-tooltip>
      </template>

      <v-card-text class="px-0 py-0">
        <v-skeleton-loader v-if="activity._meta.loading" type="article" />
        <template v-else>
          <!-- Header -->
          <v-row dense class="activity-header">
            <v-col class="col col-sm-6 col-12">
              <v-row v-if="$vuetify.breakpoint.smAndUp" dense>
                <v-col cols="2">
                  {{ $tc('entity.scheduleEntry.fields.nr') }}
                </v-col>
                <v-col cols="6">
                  {{ $tc('entity.scheduleEntry.fields.time') }}
                </v-col>
                <v-col cols="4">
                  {{ $tc('views.activity.activity.options') }}
                </v-col>
              </v-row>
              <v-row
                v-for="scheduleEntryItem in scheduleEntries"
                :key="scheduleEntryItem._meta.self" dense
                class="mt-0">
                <v-col cols="2">
                  ({{ scheduleEntryItem.number }})
                </v-col>
                <v-col cols="6">
                  {{ $date.utc(scheduleEntryItem.startTime).format($tc('global.datetime.dateShort')) }} <b>
                    {{ $date.utc(scheduleEntryItem.startTime).format($tc('global.datetime.hourShort')) }} </b> - {{
                    $date.utc(scheduleEntryItem.startTime).format($tc('global.datetime.dateShort')) == $date.utc(scheduleEntryItem.endTime).format($tc('global.datetime.dateShort'))
                      ? ''
                      : $date.utc(scheduleEntryItem.endTime).format($tc('global.datetime.dateShort'))
                  }} <b> {{ $date.utc(scheduleEntryItem.endTime).format($tc('global.datetime.hourShort')) }} </b>
                </v-col>
                <v-col cols="4">
                  <dialog-schedule-entry-edit :schedule-entry="scheduleEntryItem">
                    <template #activator="{on: dialog}">
                      <v-tooltip bottom>
                        <template #activator="{ on: tooltip }">
                          <v-icon v-on="{ ...tooltip, ...dialog}">mdi-calendar-edit</v-icon>
                        </template>
                        {{ $tc('views.activity.activity.editScheduleEntry') }}
                      </v-tooltip>
                    </template>
                  </dialog-schedule-entry-edit>
                  <v-menu :offset-y="false" rounded :nudge-left="42">
                    <template #activator="{ on: menu }">
                      <v-tooltip bottom>
                        <template #activator="{ on: tooltip }">
                          <v-icon class="mx-2" v-on="{ ...tooltip, ...menu}">
                            mdi-calendar-plus
                          </v-icon>
                        </template>
                        {{ $tc('views.activity.activity.copyScheduleEntry') }}
                      </v-tooltip>
                    </template>
                    <v-card tile>
                      <v-tooltip bottom>
                        <template #activator="{ on, attrs }">
                          <v-btn :disabled="!scheduleEntryItem.copyToDayBefore"
                                 icon
                                 v-bind="attrs"
                                 v-on="on"
                                 @click="duplicateScheduleEntry(scheduleEntryItem, -24*60)">
                            <v-icon>
                              mdi-calendar-arrow-left
                            </v-icon>
                          </v-btn>
                        </template>
                        <span>
                          {{ $tc('views.activity.activity.copyScheduleEntryDayBefore') }}
                        </span>
                      </v-tooltip>
                      <dialog-schedule-entry-create
                        :camp="scheduleEntryItem.period().camp"
                        :activity="scheduleEntryItem.activity"
                        :schedule-entry="scheduleEntryItem">
                        <template #activator="{on: dialog}">
                          <v-tooltip bottom>
                            <template #activator="{ on: tooltip, attrs }">
                              <v-btn icon v-bind="attrs" v-on="{ ...tooltip, ...dialog}">
                                <v-icon>
                                  mdi-calendar-clock
                                </v-icon>
                              </v-btn>
                            </template>
                            <span>
                              {{ $tc('views.activity.activity.copyScheduleEntryAnyTime') }}
                            </span>
                          </v-tooltip>
                        </template>
                      </dialog-schedule-entry-create>
                      <v-tooltip bottom>
                        <template #activator="{ on, attrs }">
                          <v-btn :disabled="!scheduleEntryItem.copyToDayAfter"
                                 icon
                                 v-bind="attrs"
                                 v-on="on"
                                 @click="duplicateScheduleEntry(scheduleEntryItem, 24*60)">
                            <v-icon>
                              mdi-calendar-arrow-right
                            </v-icon>
                          </v-btn>
                        </template>
                        <span>
                          {{ $tc('views.activity.activity.copyScheduleEntryDayAfter') }}
                        </span>
                      </v-tooltip>
                    </v-card>
                  </v-menu>
                  <dialog-entity-delete :entity="scheduleEntryItem">
                    <template #activator="{ on: dialog }">
                      <v-tooltip bottom>
                        <template #activator="{ on: tooltip }">
                          <v-icon v-on="{ ...tooltip, ...dialog}">mdi-calendar-minus</v-icon>
                        </template>
                        {{ $tc('views.activity.activity.deleteScheduleEntry') }}
                      </v-tooltip>
                    </template>
                    {{ $tc('views.activity.activity.deleteScheduleEntryQuestion') }}
                    <ul>
                      <li>
                        ({{ scheduleEntryItem.number }})
                        {{ $date.utc(scheduleEntryItem.startTime).format($tc('global.datetime.dateShort')) }} <b>
                          {{ $date.utc(scheduleEntryItem.startTime).format($tc('global.datetime.hourShort')) }} </b> - {{
                          $date.utc(scheduleEntryItem.startTime).format($tc('global.datetime.dateShort')) == $date.utc(scheduleEntryItem.endTime).format($tc('global.datetime.dateShort'))
                            ? ''
                            : $date.utc(scheduleEntryItem.endTime).format($tc('global.datetime.dateShort'))
                        }} <b> {{ $date.utc(scheduleEntryItem.endTime).format($tc('global.datetime.hourShort')) }} </b>
                      </li>
                    </ul>
                  </dialog-entity-delete>
                </v-col>
              </v-row>
            </v-col>
            <v-col class="col col-sm-6 col-12">
              <v-row dense>
                <v-col>
                  <api-text-field
                    :name="$tc('entity.activity.fields.location')"
                    :uri="activity._meta.self"
                    fieldname="location"
                    :disabled="layoutMode"
                    dense />
                </v-col>
              </v-row>
              <v-row dense>
                <v-col>
                  <api-select
                    :name="$tc('entity.activity.fields.responsible')"
                    dense
                    multiple
                    chips
                    deletable-chips
                    small-chips
                    :uri="activity._meta.self"
                    fieldname="campCollaborations"
                    :disabled="layoutMode"
                    :items="availableCampCollaborations" />
                </v-col>
              </v-row>
            </v-col>
          </v-row>
          <content-node
            v-if="activity.rootContentNode"
            :content-node="activity.rootContentNode()"
            :layout-mode="layoutMode" />
        </template>
      </v-card-text>
    </content-card>
  </v-container>
</template>

<script>
import ContentCard from '@/components/layout/ContentCard.vue'
import ApiTextField from '@/components/form/api/ApiTextField.vue'
import ApiSelect from '@/components/form/api/ApiSelect.vue'
import DialogEntityDelete from '@/components/dialog/DialogEntityDelete.vue'
import DialogScheduleEntryEdit from '@/components/dialog/DialogScheduleEntryEdit.vue'
import DialogScheduleEntryCreate from '@/components/dialog/DialogScheduleEntryCreate.vue'
import ContentNode from '@/components/activity/ContentNode.vue'
import { defineHelpers } from '@/common/helpers/scheduleEntry/dateHelperUTC.js'

export default {
  name: 'Activity',
  components: {
    ContentCard,
    ApiTextField,
    ApiSelect,
    DialogEntityDelete,
    DialogScheduleEntryEdit,
    DialogScheduleEntryCreate,
    ContentNode
  },
  props: {
    scheduleEntry: {
      type: Function,
      required: true
    }
  },
  data () {
    return {
      layoutMode: false,
      editActivityTitle: false
    }
  },
  computed: {
    availableCampCollaborations () {
      const currentCampCollaborationIds = this.activity.campCollaborations().items.map(cc => cc.id)
      return this.campCollaborations.filter(cc => {
        return (cc.status === 'established') || (currentCampCollaborationIds.includes(cc.id))
      }).map(value => {
        const inactive = value.status === 'inactive'
        const text = value.user().displayName + (inactive ? (' (' + this.$tc('entity.campCollaboration.inactive')) + ')' : '')
        return {
          value,
          text
        }
      })
    },
    campCollaborations () {
      return this.activity.camp().campCollaborations().items
    },
    activity () {
      return this.scheduleEntry().activity()
    },
    camp () {
      return this.activity.camp()
    },
    category () {
      return this.activity.category()
    },
    scheduleEntries () {
      return this.activity.scheduleEntries().items.map((entry) => this.scheduleEntryAddProperties(entry))
    },
    contentNodes () {
      return this.activity.contentNodes()
    }
  },
  methods: {
    changeCategory (category) {
      this.activity.$patch({
        categoryId: category.id
      })
    },
    countContentNodes (contentType) {
      return this.contentNodes.items.filter(cn => {
        return cn.contentType().id === contentType.id
      }).length
    },
    scheduleEntryAddProperties (scheduleEntry) {
      const scheduleEntryItem = defineHelpers(scheduleEntry)

      if (!Object.prototype.hasOwnProperty.call(scheduleEntryItem, 'copyToDayBefore')) {
        Object.defineProperty(scheduleEntry, 'copyToDayBefore', {
          get () {
            return this.periodOffset >= 24 * 60
          }
        })
      }
      if (!Object.prototype.hasOwnProperty.call(scheduleEntryItem, 'copyToDayAfter')) {
        Object.defineProperty(scheduleEntry, 'copyToDayAfter', {
          get () {
            const scheduleEntryEnd = this.periodOffset + this.length
            const periodEnd = (this.period().days().items.length) * 24 * 60
            return scheduleEntryEnd + 24 * 60 <= periodEnd
          }
        })
      }
      return scheduleEntryItem
    },
    duplicateScheduleEntry (scheduleEntry, offset) {
      this.activity.scheduleEntries().$post({
        activityId: this.activity.id,
        periodId: scheduleEntry.period().id,
        periodOffset: scheduleEntry.periodOffset + offset,
        length: scheduleEntry.length
      })
    }
  }
}
</script>

<style scoped lang="scss">
.activity-header {
  margin-bottom: 0;
  border-bottom: 1px solid rgba(0, 0, 0, 0.12);
  padding: 1.5rem 16px;

  @media #{map-get($display-breakpoints, 'sm-and-down')} {
    border-bottom: none;
  }
}
</style>
