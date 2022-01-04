<!--
Displays a single activity
-->

<template>
  <v-container fluid>
    <content-card toolbar :loaded="!scheduleEntry()._meta.loading && !activity.camp()._meta.loading">
      <template #title>
        <v-toolbar-title class="font-weight-bold">
          {{ scheduleEntry().number }}
          <v-menu v-if="!category._meta.loading" offset-y :disabled="layoutMode || !isContributor">
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
             @click="makeTitleEditable();">
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
               :disabled="!isContributor"
               @click="layoutMode = true">
          <template v-if="$vuetify.breakpoint.smAndUp">
            <v-icon left>mdi-puzzle-edit-outline</v-icon>
            {{ $tc('views.activity.activity.changeLayout') }}
          </template>
          <template v-else>{{ $tc('views.activity.activity.layout') }}</template>
        </v-btn>
        <v-btn v-else-if="isContributor"
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
        <v-skeleton-loader v-if="loading" type="article" />
        <template v-else>
          <!-- Header -->
          <v-row dense class="activity-header">
            <v-col class="col col-sm-6 col-12">
              <v-row v-if="$vuetify.breakpoint.smAndUp" dense>
                <v-col cols="2">
                  {{ $tc('entity.scheduleEntry.fields.nr') }}
                </v-col>
                <v-col cols="10">
                  {{ $tc('entity.scheduleEntry.fields.time') }}
                </v-col>
              </v-row>
              <v-row
                v-for="scheduleEntryItem in scheduleEntries"
                :key="scheduleEntryItem._meta.self" dense>
                <v-col cols="2">
                  ({{ scheduleEntryItem.number }})
                </v-col>
                <v-col cols="10">
                  {{ $date.utc(scheduleEntryItem.startTime).format($tc('global.datetime.dateShort')) }} <b>
                    {{ $date.utc(scheduleEntryItem.startTime).format($tc('global.datetime.hourShort')) }} </b> - {{
                    $date.utc(scheduleEntryItem.startTime).format($tc('global.datetime.dateShort')) == $date.utc(scheduleEntryItem.endTime).format($tc('global.datetime.dateShort'))
                      ? ''
                      : $date.utc(scheduleEntryItem.endTime).format($tc('global.datetime.dateShort'))
                  }} <b> {{ $date.utc(scheduleEntryItem.endTime).format($tc('global.datetime.hourShort')) }} </b>
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
                    :disabled="layoutMode || !isContributor"
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
                    :disabled="layoutMode || !isContributor"
                    :items="availableCampCollaborations" />
                </v-col>
              </v-row>
            </v-col>
          </v-row>

          <v-skeleton-loader v-if="loading" type="article" />
          <content-node
            v-else
            :content-node="activity.rootContentNode()"
            :layout-mode="layoutMode"
            :disabled="isContributor === false" />
        </template>
      </v-card-text>
    </content-card>
  </v-container>
</template>

<script>
import ContentCard from '@/components/layout/ContentCard.vue'
import ApiTextField from '@/components/form/api/ApiTextField.vue'
import ApiSelect from '@/components/form/api/ApiSelect.vue'
import ContentNode from '@/components/activity/ContentNode.vue'
import { defineHelpers } from '@/common/helpers/scheduleEntry/dateHelperUTC.js'
import { campRoleMixin } from '@/mixins/campRoleMixin'

export default {
  name: 'Activity',
  components: {
    ContentCard,
    ApiTextField,
    ApiSelect,
    ContentNode
  },
  mixins: [campRoleMixin],
  props: {
    scheduleEntry: {
      type: Function,
      required: true
    }
  },
  data () {
    return {
      layoutMode: false,
      editActivityTitle: false,
      loading: true
    }
  },
  computed: {
    availableCampCollaborations () {
      const currentCampCollaborationIRIs = this.activity.campCollaborations().items.map(cc => cc._meta.self)
      return this.campCollaborations.filter(cc => {
        return (cc.status === 'established') || (currentCampCollaborationIRIs.includes(cc._meta.self))
      }).map(value => {
        const inactive = value.status === 'inactive'
        const text = value.user().displayName + (inactive ? (' (' + this.$tc('entity.campCollaboration.inactive')) + ')' : '')

        // following structure is defined by vuetify v-select items property
        return {
          value: value._meta.self,
          text
        }
      })
    },
    currentCampCollaborationIRIs () {
      return this.activity.campCollaborations().items.map(cc => cc._meta.self)
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
      return this.activity.scheduleEntries().items.map((entry) => defineHelpers(entry))
    },
    contentNodes () {
      return this.activity.contentNodes()
    }
  },

  // reload data every time user navigates to Activity view
  async mounted () {
    this.loading = true
    await this.scheduleEntry().activity()._meta.load // wait if activity is being loaded as part of a collection
    await this.scheduleEntry().activity().$reload() // reload as single entity to ensure all embedded entities are included in a single network request
    this.loading = false
  },

  methods: {
    changeCategory (category) {
      this.activity.$patch({
        category: category._meta.self
      })
    },
    countContentNodes (contentType) {
      return this.contentNodes.items.filter(cn => {
        return cn.contentType().id === contentType.id
      }).length
    },
    makeTitleEditable () {
      if (this.isContributor) {
        this.editActivityTitle = true
      }
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
