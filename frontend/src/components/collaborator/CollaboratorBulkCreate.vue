<template>
  <DetailPane
    :model-value="showDialog"
    :loading="loading"
    :error="error"
    icon="mdi-account-multiple-plus"
    :title="$t('components.collaborator.collaboratorBulkCreate.title')"
    :submit-action="bulkInvite"
    :submit-label="$t('components.collaborator.collaboratorBulkCreate.invite')"
    :submit-enabled="parsedEmails.length > 0"
    submit-icon="mdi-email-fast"
    submit-color="success"
    :cancel-action="close"
  >
    <template #activator="{ props }">
      <ButtonAdd
        color="blue-grey-darken-2"
        variant="text"
        class="my-n2"
        icon="mdi-account-multiple-plus"
        v-bind="props"
        @click="showDialog = true"
      >
        {{ $t('components.collaborator.collaboratorBulkCreate.inviteCta') }}
      </ButtonAdd>
    </template>

    <e-form name="campCollaboration">
      <v-textarea
        v-model="emailsText"
        :label="$t('components.collaborator.collaboratorBulkCreate.emailsLabel')"
        :hint="$t('components.collaborator.collaboratorBulkCreate.emailsHint')"
        :rows="5"
        class="mb-2"
      />

      <CollaboratorRoleSelect v-model="role" />
    </e-form>

    <v-alert
      v-if="result && result.createdCampCollaborations.length > 0"
      type="success"
      class="mt-2"
      variant="tonal"
    >
      <div v-if="result.createdCampCollaborations.length > 0">
        {{
          $t('components.collaborator.collaboratorBulkCreate.successCount', {
            count: result.createdCampCollaborations.length,
          })
        }}
      </div>
    </v-alert>

    <v-alert
      v-if="result && result.validationFailed.length > 0"
      type="info"
      class="mt-2"
      variant="outlined"
    >
      <p>{{ $t('components.collaborator.collaboratorBulkCreate.validationFailed') }}</p>
      <p>{{ result.validationFailed.join(', ') }}</p>
    </v-alert>

    <v-alert
      v-if="result && result.failed.length > 0"
      type="error"
      class="mt-2"
      variant="tonal"
    >
      {{
        $t('components.collaborator.collaboratorBulkCreate.failedEmails', {
          emails: result.failed.join(', '),
        })
      }}
    </v-alert>
  </DetailPane>
</template>

<script>
import ButtonAdd from '@/components/buttons/ButtonAdd.vue'
import DetailPane from '@/components/generic/DetailPane.vue'
import CollaboratorRoleSelect from '@/components/collaborator/CollaboratorRoleSelect.vue'

const DEFAULT_BULK_INVITE_ROLE = 'member'

export default {
  name: 'CollaboratorBulkCreate',
  components: { ButtonAdd, DetailPane, CollaboratorRoleSelect },
  props: {
    camp: { type: Object, required: true },
  },
  data() {
    return {
      showDialog: false,
      loading: false,
      error: null,
      emailsText: '',
      role: DEFAULT_BULK_INVITE_ROLE,
      result: null,
    }
  },
  computed: {
    parsedEmails() {
      return this.emailsText
        .split(/[\s,;]+/)
        .map((e) => e.trim())
        .filter((e) => e.length > 0)
    },
  },
  methods: {
    close() {
      this.showDialog = false
      this.reset()
    },
    reset() {
      this.emailsText = ''
      this.role = DEFAULT_BULK_INVITE_ROLE
      this.result = null
      this.error = null
    },
    async bulkInvite() {
      this.loading = true
      this.error = null
      this.result = null

      try {
        const postUrl = await this.api.href(this.api.get(), 'campCollaborations')

        const settled = await Promise.allSettled(
          this.parsedEmails.map((email) =>
            this.api.post(postUrl, {
              camp: this.camp._meta.self,
              inviteEmail: email,
              role: this.role,
            })
          )
        )

        const createdCampCollaborations = []
        const validationFailed = []
        const failed = []

        settled.forEach((r, i) => {
          const email = this.parsedEmails[i]
          if (r.status === 'fulfilled') {
            createdCampCollaborations.push(email)
          } else if (r.reason?.response?.status === 422) {
            validationFailed.push(email)
          } else {
            failed.push(email)
            if (!this.error) {
              this.error = r.reason
            }
          }
        })

        this.result = {
          createdCampCollaborations,
          validationFailed,
          failed,
        }

        if (failed.length === 0 && validationFailed.length === 0) {
          this.emailsText = ''
        }

        if (createdCampCollaborations.length > 0) {
          await this.api.reload(this.camp.campCollaborations())
        }
      } catch (err) {
        this.error = err
      } finally {
        this.loading = false
      }
    },
  },
}
</script>
