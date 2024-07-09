<template>
  <dialog-form
    v-model="showDialog"
    :loading="loading"
    :error="error"
    icon="mdi-calendar-plus"
    :title="$tc('components.campAdmin.dialogCategoryCreate.title')"
    :submit-action="createCategory"
    :submit-label="$tc('global.button.create')"
    submit-color="success"
    :cancel-action="close"
  >
    <template #activator="scope">
      <slot name="activator" v-bind="scope" />
    </template>

    <template #moreActions>
      <CopyCategoryInfoDialog @closed="refreshCopyCategorySource">
        <template #activator="{ on }">
          <v-btn v-show="clipboardPermission === 'prompt'" v-on="on">
            <v-icon left>mdi-information-outline</v-icon>
            {{ $tc('components.campAdmin.dialogCategoryCreate.copyPasteCategory') }}
          </v-btn>
        </template>
      </CopyCategoryInfoDialog>
    </template>

    <div v-if="hasCopyCategorySource">
      <div class="mb-8">
        <div v-if="!clipboardAccessDenied">
          {{ $tc('components.campAdmin.dialogCategoryCreate.clipboard') }}
          <div style="float: right">
            <small>
              <a
                href="#"
                style="color: inherit; text-decoration: none"
                @click="clearClipboard"
              >
                {{ $tc('components.campAdmin.dialogCategoryCreate.clearClipboard') }}
              </a>
            </small>
          </div>
        </div>
        <v-list-item
          class="ec-copy-source rounded-xl blue-grey lighten-5 blue-grey--text text--darken-4 mt-1"
        >
          <v-list-item-avatar>
            <v-icon color="blue-grey">mdi-clipboard-check-outline</v-icon>
          </v-list-item-avatar>
          <v-list-item-content>
            <v-list-item-title>
              <CategoryChip :category="copyCategorySourceCategory" class="mx-1" dense />
              {{ copyCategorySource.title }}
            </v-list-item-title>
            <v-list-item-subtitle>
              {{ copyCategorySource.camp().title }}
            </v-list-item-subtitle>
          </v-list-item-content>
          <v-list-item-action>
            <e-checkbox
              v-model="copyContent"
              :label="$tc('components.campAdmin.dialogCategoryCreate.copyContent')"
            />
          </v-list-item-action>
        </v-list-item>
      </div>
    </div>
    <dialog-category-form :camp="camp" :is-new="true" :category="entityData">
      <template v-if="clipboardAccessDenied" #textFieldTitleAppend>
        <PopoverPrompt
          v-model="copyCategorySourceUrlShowPopover"
          icon="mdi-content-paste"
          :title="$tc('components.campAdmin.dialogCategoryCreate.pasteCategory')"
        >
          <template #activator="{ on }">
            <v-btn
              :title="$tc('components.campAdmin.dialogCategoryCreate.pasteCategory')"
              text
              class="v-btn--has-bg"
              height="56"
              v-on="on"
            >
              <v-progress-circular v-if="copyCategorySourceUrlLoading" indeterminate />
              <v-icon v-else>mdi-content-paste</v-icon>
            </v-btn>
          </template>
          {{ $tc('components.campAdmin.dialogCategoryCreate.copySourceInfo') }}
          <e-text-field
            v-model="copyCategorySourceUrl"
            :label="
              $tc('components.campAdmin.dialogCategoryCreate.copyCategoryOrActivity')
            "
            style="margin-bottom: 12px"
            autofocus
          />
        </PopoverPrompt>
      </template>
    </dialog-category-form>
  </dialog-form>
</template>

<script>
import { categoryRoute } from '@/router.js'
import DialogForm from '@/components/dialog/DialogForm.vue'
import DialogBase from '@/components/dialog/DialogBase.vue'
import DialogCategoryForm from './DialogCategoryForm.vue'
import PopoverPrompt from '../prompt/PopoverPrompt.vue'
import router from '@/router.js'
import CategoryChip from '../generic/CategoryChip.vue'
import CopyCategoryInfoDialog from '../category/CopyCategoryInfoDialog.vue'

export default {
  name: 'DialogCategoryCreate',
  components: {
    CopyCategoryInfoDialog,
    CategoryChip,
    PopoverPrompt,
    DialogCategoryForm,
    DialogForm,
  },
  extends: DialogBase,
  props: {
    camp: { type: Object, required: true },
  },
  data() {
    return {
      entityProperties: ['camp', 'short', 'name', 'color', 'numberingStyle'],
      embeddedCollections: ['preferredContentTypes'],
      entityUri: '',
      clipboardPermission: 'unknown',
      copyCategorySource: null,
      copyCategorySourceUrl: null,
      copyCategorySourceUrlLoading: false,
      copyCategorySourceUrlShowPopover: false,
    }
  },
  computed: {
    clipboardAccessDenied() {
      return (
        this.clipboardPermission === 'unaccessable' ||
        this.clipboardPermission === 'denied'
      )
    },
    hasCopyCategorySource() {
      return this.copyCategorySource != null && this.copyCategorySource._meta.self != null
    },
    copyContent: {
      get() {
        return this.entityData.copyCategorySource != null
      },
      set(val) {
        if (val) {
          this.entityData.copyCategorySource = this.copyCategorySource._meta.self
          this.entityData.short = this.copyCategorySourceCategory.short
          this.entityData.name = this.copyCategorySourceCategory.name
          this.entityData.color = this.copyCategorySourceCategory.color
          this.entityData.numberingStyle = this.copyCategorySourceCategory.numberingStyle
        } else {
          this.entityData.copyCategorySource = null
        }
      },
    },
    copyCategorySourceCategory() {
      if (!this.hasCopyCategorySource) return null
      return this.copyCategorySource.short
        ? this.copyCategorySource
        : this.copyCategorySource.category()
    },
  },
  watch: {
    showDialog: function (showDialog) {
      if (showDialog) {
        this.refreshCopyCategorySource()
        this.setEntityData({
          camp: this.camp._meta.self,
          short: '',
          name: '',
          color: '#000000',
          numberingStyle: '1',
          copyCategorySource: null,
        })
      } else {
        // clear form on exit
        this.clearEntityData()
        this.copyCategorySource = null
        this.copyCategorySourceUrl = null
      }
    },
    copyCategorySourceUrl: function (url) {
      this.copyCategorySourceUrlLoading = true

      this.getCopyCategorySource(url).then(
        (categoryOrActivityProxy) => {
          if (categoryOrActivityProxy != null) {
            categoryOrActivityProxy._meta.load.then(
              async (categoryOrActivity) => {
                if (!categoryOrActivity.short) {
                  await categoryOrActivity.category()._meta.load
                }
                this.copyCategorySource = categoryOrActivity
                this.copyContent = true
                this.copyCategorySourceUrlLoading = false
              },
              () => {
                this.copyCategorySourceUrlLoading = false
              }
            )
          } else {
            this.copyCategorySource = null
            this.copyContent = false
            this.copyCategorySourceUrlLoading = false
          }

          // if Paste-Popover is shown, close it now
          if (this.copyCategorySourceUrlShowPopover) {
            this.$nextTick(() => {
              this.copyCategorySourceUrlShowPopover = false
            })
          }
        },
        () => {
          this.copyCategorySourceUrlLoading = false
        }
      )
    },
  },
  mounted() {
    this.api.href(this.api.get(), 'categories').then((uri) => (this.entityUri = uri))
  },
  methods: {
    async createCategory() {
      const createdCategory = await this.create()
      await this.api.reload(this.camp.categories())
      this.$router.push(categoryRoute(this.camp, createdCategory, { new: true }))
    },
    refreshCopyCategorySource() {
      navigator.permissions.query({ name: 'clipboard-read' }).then(
        (p) => {
          this.clipboardPermission = p.state
          this.copyCategorySource = null

          if (p.state === 'granted') {
            navigator.clipboard
              .readText()
              .then(async (url) => {
                const copyCategorySource = await this.getCopyCategorySource(url)
                this.copyCategorySource = await copyCategorySource?._meta.load
              })
              .catch(() => {
                this.clipboardPermission = 'unaccessable'
                console.warn('clipboard permission not requestable')
              })
          }
        },
        () => {
          this.clipboardPermission = 'unaccessable'
          console.warn('clipboard permission not requestable')
        }
      )
    },
    async getCopyCategorySource(url) {
      if (url?.startsWith(window.location.origin)) {
        url = url.substring(window.location.origin.length)
        const match = router.matcher.match(url)

        if (match.name === 'activity') {
          const scheduleEntry = await this.api
            .get()
            .scheduleEntries({ id: match.params['scheduleEntryId'] })
          return await scheduleEntry.activity()
        } else if (match.name === 'admin/activity/category') {
          return await this.api.get().categories({ id: match.params['categoryId'] })
        }
      }
      return null
    },
    async clearClipboard() {
      await navigator.clipboard.writeText('')
      this.refreshCopyCategorySource()
    },
  },
}
</script>

<style scoped></style>
