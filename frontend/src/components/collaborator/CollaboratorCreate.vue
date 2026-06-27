<template>
  <DetailPane
    :model-value="showDialog"
    :loading="loading"
    :error="error"
    icon="mdi-account-plus"
    :title="$t('components.collaborator.collaboratorCreate.title')"
    :submit-action="createCollaboration"
    :submit-label="$t('components.collaborator.collaboratorCreate.invite')"
    submit-icon="mdi-email-fast"
    submit-color="success"
    :cancel-action="close"
  >
    <template #activator="{ props }">
      <ButtonAdd
        color="blue-grey-darken-2"
        variant="text"
        class="my-n2"
        icon="mdi-account-plus"
        data-testid="collaborator-invite-cta"
        v-bind="props"
        @click="showDialog = true"
      >
        {{ $t('components.collaborator.collaboratorCreate.inviteCta') }}
      </ButtonAdd>
    </template>

    <ValidationField
      v-slot="{ errors: veeErrors }"
      :label="$t('components.collaborator.collaboratorCreate.emailLabel')"
      name="inviteEmail"
      vee-rules="required|email"
    >
      <v-combobox
        v-model="entityData.inviteEmail"
        v-model:search="search"
        data-testid="collaborator-invite-search"
        :items="profileItems"
        :loading="searchLoading"
        :no-filter="true"
        :return-object="false"
        :hide-no-data="!search || searchLoading"
        item-title="value"
        item-value="value"
        type="email"
        class="mb-2"
        autocomplete="off"
        :menu-icon="null"
        :error-messages="veeErrors"
        :label="$t('components.collaborator.collaboratorCreate.emailLabel')"
        :hint="$t('components.collaborator.collaboratorCreate.searchHint')"
        persistent-hint
        @update:model-value="onSelect"
      >
        <template #item="{ item, props: itemProps }">
          <v-list-item
            v-bind="itemProps"
            :title="item.raw.displayName"
            :subtitle="item.raw.value"
            data-testid="collaborator-invite-result"
          >
            <template #prepend>
              <v-icon icon="mdi-account-circle" class="mr-2" />
            </template>
          </v-list-item>
        </template>

        <template #no-data>
          <v-list-item
            :title="$t('components.collaborator.collaboratorCreate.noResults')"
          />
        </template>
      </v-combobox>
    </ValidationField>

    <CollaboratorForm :collaboration="entityData" />
  </DetailPane>
</template>

<script>
import { debounce } from 'lodash-es'
import ButtonAdd from '@/components/buttons/ButtonAdd.vue'
import DetailPane from '@/components/generic/DetailPane.vue'
import DialogBase from '@/components/dialog/DialogBase.vue'
import CollaboratorForm from '@/components/collaborator/CollaboratorForm.vue'
import ValidationField from '@/components/form/base/ValidationField.vue'

const DEFAULT_INVITE_ROLE = 'member'

export default {
  name: 'CollaboratorCreate',
  components: { ButtonAdd, DetailPane, CollaboratorForm, ValidationField },
  extends: DialogBase,
  provide() {
    return {
      entityName: 'campCollaboration',
    }
  },
  props: {
    camp: { type: Object, required: true },
  },
  data() {
    return {
      entityProperties: ['camp', 'inviteEmail', 'role'],
      entityUri: '',
      search: '',
      profiles: [],
      searchLoading: false,
    }
  },
  computed: {
    profileItems() {
      return this.profiles.map((profile) => ({
        // The email is the value which is ultimately sent to the API and, after selection,
        // shown in the input (item-title="value").
        value: profile.email,
        // Richer label shown in the dropdown list (see the #item slot).
        displayName: this.profileDisplayName(profile),
      }))
    },
  },
  watch: {
    showDialog: function (showDialog) {
      if (showDialog) {
        this.setEntityData({
          camp: this.camp._meta.self,
          inviteEmail: '',
          role: DEFAULT_INVITE_ROLE,
        })
      } else {
        // clear form on exit
        this.clearEntityData()
        this.search = ''
        this.profiles = []
      }
    },
    search(value) {
      this.debouncedSearchProfiles(value)
    },
  },
  mounted() {
    this.api
      .href(this.api.get(), 'campCollaborations')
      .then((uri) => (this.entityUri = uri))
    this.debouncedSearchProfiles = debounce(this.searchProfiles, 300)
  },
  methods: {
    profileDisplayName(profile) {
      const name = [profile.firstname, profile.surname].filter(Boolean).join(' ')
      if (profile.nickname && name) {
        return `${profile.nickname} (${name})`
      }
      return profile.nickname || name || profile.email
    },
    async searchProfiles(value) {
      const searchTerm = (value || '').trim()
      if (searchTerm.length < 1) {
        this.profiles = []
        return
      }
      this.searchLoading = true
      try {
        const collection = await this.api.get().profiles({ search: searchTerm })._meta
          .load
        // Guard against out-of-order responses overwriting a newer search.
        if (this.search.trim() === searchTerm) {
          this.profiles = collection.items.map((item) => ({
            email: item.email,
            firstname: item.firstname,
            surname: item.surname,
            nickname: item.nickname,
          }))
        }
      } catch {
        this.profiles = []
      } finally {
        this.searchLoading = false
      }
    },
    onSelect() {
      // Once a value is chosen (selected profile or typed email), the result list is no
      // longer relevant and would otherwise keep showing stale suggestions.
      this.profiles = []
    },
    createCollaboration() {
      return this.create().then(() => {
        this.api.reload(this.camp.campCollaborations())
      })
    },
  },
}
</script>

<style scoped></style>
