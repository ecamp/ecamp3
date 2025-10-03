<template>
  <dialog-form
    v-model="showDialog"
    :loading="loading"
    :error="error"
    icon="mdi-calendar-plus"
    :title="$t('components.campAdmin.dialogCategoryCreate.title')"
    :submit-action="createCategory"
    :submit-label="$t('global.button.create')"
    submit-color="success"
    :cancel-action="close"
  >
    <template #activator="scope">
      <slot name="activator" v-bind="scope" />
    </template>

    <template #moreActions>
      <ClipboardInfoDialog
        translation-context-i18n-key="components.campAdmin.dialogCategoryCreate.clipboardInfoDialog"
        @closed="attemptLoadingEntityFromClipboard"
      >
        <template #activator="{ props }">
          <v-btn v-show="showClipboardPrompt" v-bind="props">
            <v-icon left>mdi-information-outline</v-icon>
            {{ $t('components.campAdmin.dialogCategoryCreate.copyPasteCategory') }}
          </v-btn>
        </template>
      </ClipboardInfoDialog>
    </template>

    <div v-if="hasClipboardEntity">
      <div class="mb-8">
        <div v-if="!clipboardAccessDenied">
          {{ $t('components.campAdmin.dialogCategoryCreate.clipboard') }}
          <div style="float: right">
            <small>
              <a
                href="#"
                style="color: inherit; text-decoration: none"
                @click="clearClipboard"
              >
                {{ $t('components.campAdmin.dialogCategoryCreate.clearClipboard') }}
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
              {{ clipboardEntity.title }}
            </v-list-item-title>
            <v-list-item-subtitle>
              {{ clipboardEntity.camp().title }}
            </v-list-item-subtitle>
          </v-list-item-content>
          <v-list-item-action>
            <e-checkbox
              v-model="copyContent"
              :label="$t('components.campAdmin.dialogCategoryCreate.copyContent')"
            />
          </v-list-item-action>
        </v-list-item>
      </div>
    </div>
    <dialog-category-form :camp="camp" :is-new="true" :category="entityData">
      <template v-if="clipboardAccessDenied" #textFieldTitleAppend>
        <PopoverPrompt
          v-model="showCopyCategoryUrlPopover"
          icon="mdi-content-paste"
          :title="$t('components.campAdmin.dialogCategoryCreate.pasteCategory')"
        >
          <template #activator="{ props }">
            <v-btn
              :title="$t('components.campAdmin.dialogCategoryCreate.pasteCategory')"
              text
              class="v-btn--has-bg"
              height="56"
              v-bind="props"
            >
              <v-progress-circular v-if="clipboardEntityLoading" indeterminate />
              <v-icon v-else>mdi-content-paste</v-icon>
            </v-btn>
          </template>
          {{ $t('components.campAdmin.dialogCategoryCreate.copySourceInfo') }}
          <e-text-field
            v-model="clipboardEntityUrl"
            :label="
              $t('components.campAdmin.dialogCategoryCreate.copyCategoryOrActivity')
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
import router, { categoryRoute } from '@/router.js'
import DialogForm from '@/components/dialog/DialogForm.vue'
import DialogBase from '@/components/dialog/DialogBase.vue'
import DialogCategoryForm from './DialogCategoryForm.vue'
import PopoverPrompt from '../prompt/PopoverPrompt.vue'
import CategoryChip from '../generic/CategoryChip.vue'
import ClipboardInfoDialog from '../generic/ClipboardInfoDialog.vue'
import { useClipboardEntity } from '@/components/generic/useClipboardEntity.js'
import { apiStore as api } from '@/plugins/store/index.js'
import { getCurrentInstance, nextTick, ref } from 'vue'

export default {
  name: 'DialogCategoryCreate',
  components: {
    ClipboardInfoDialog,
    CategoryChip,
    PopoverPrompt,
    DialogCategoryForm,
    DialogForm,
  },
  extends: DialogBase,
  props: {
    camp: { type: Object, required: true },
  },
  setup() {
    const showCopyCategoryUrlPopover = ref(false)

    // Hack: In this case we need access to a method defined in the options API
    // because moving this method to the composition API would force us to move
    // entityData and a whole big mess of inheritance-related code to the
    // composition API as well. On a previous attempt, this completely broke the
    // vee-validate validators, resulting in all validation always failing in all
    // dialogs in the app. So using the undocumented but well-known
    // getCurrentInstance here is the lesser evil right now.
    const currentInstance = getCurrentInstance()

    const clipboard = useClipboardEntity({
      fetchClipboardEntity: async (url) => {
        if (!url.startsWith(window.location.origin)) return null
        url = url.substring(window.location.origin.length)
        const match = router.matcher.match(url)

        let result
        if (match.name === 'camp/activity') {
          result = await api.get().activities({ id: match.params['activityId'] })
        } else if (match.name === 'camp/admin/activity/category') {
          result = await api.get().categories({ id: match.params['categoryId'] })
        }

        if (['camp/activity', 'camp/admin/activity/category'].includes(match.name)) {
          // if Paste-Popover is shown, close it now
          if (showCopyCategoryUrlPopover.value) {
            nextTick(() => {
              showCopyCategoryUrlPopover.value = false
            })
          }
        }

        return result
      },
      onEntityLoaded: function () {
        currentInstance.proxy.setCopyContentCheckbox(true)
      },
      onEntityLoadFailed: function () {
        currentInstance.proxy.setCopyContentCheckbox(false)
      },
    })

    return {
      ...clipboard,
      showCopyCategoryUrlPopover,
    }
  },
  data() {
    return {
      entityProperties: ['camp', 'short', 'name', 'color', 'numberingStyle'],
      entityUri: '',
    }
  },
  computed: {
    copyContent: {
      get() {
        return this.entityData.copyCategorySource != null
      },
      set(val) {
        this.setCopyContentCheckbox(val)
      },
    },
    copyCategorySourceCategory() {
      if (!this.hasClipboardEntity) return null
      return this.clipboardEntity.short
        ? this.clipboardEntity
        : this.clipboardEntity.category?.()
    },
  },
  watch: {
    showDialog: function (showDialog) {
      if (showDialog) {
        this.attemptLoadingEntityFromClipboard()
        this.setEntityData({
          camp: this.camp._meta.self,
          short: '',
          name: '',
          color: '#000000',
          numberingStyle: '1',
        })
      } else {
        // clear form on exit
        this.clipboardEntityUrl = null
        this.clearEntityData()
      }
      this.clipboardEntity = null
    },
  },
  mounted() {
    this.api.href(this.api.get(), 'categories').then((uri) => (this.entityUri = uri))
  },
  methods: {
    async createCategory() {
      const createdCategory = await this.create(this.entityData)
      await this.api.reload(this.camp.categories())
      this.$router.push(categoryRoute(this.camp, createdCategory, { new: true }))
    },
    setCopyContentCheckbox(val) {
      if (val) {
        this.entityData.copyCategorySource = this.clipboardEntity._meta.self
        this.entityData.short = this.copyCategorySourceCategory.short
        this.entityData.name = this.copyCategorySourceCategory.name
        this.entityData.color = this.copyCategorySourceCategory.color
        this.entityData.numberingStyle = this.copyCategorySourceCategory.numberingStyle
      } else {
        this.entityData.copyCategorySource = null
      }
    },
  },
}
</script>

<style scoped></style>
