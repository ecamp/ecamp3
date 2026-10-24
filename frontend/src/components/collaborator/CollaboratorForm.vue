<template>
  <e-form name="campCollaboration">
    <e-text-field
      v-if="status"
      class="ec-status-field"
      :model-value="translatedStatus"
      readonly
      path="status"
    >
      <template #append>
        <slot name="statusChange" />
      </template>
    </e-text-field>
    <CollaboratorRoleSelect
      v-if="readonlyRole"
      v-model="localCollaboration.role"
      readonly
      aria-readonly="true"
      aria-describedby="readonly"
      :hint="$t('components.collaborator.collaboratorForm.roleHint')"
    />
    <CollaboratorRoleSelect v-else v-model="localCollaboration.role" />

    <fieldset
      v-if="!!initialCollaboration"
      class="e-form-container e-avatar-field v-card__text rounded-t"
    >
      <legend>
        {{ $t('components.collaborator.collaboratorForm.overrideAvatar') }}
      </legend>

      <div class="d-flex gap-4 align-center">
        <UserAvatar
          :user="initialCollaboration?.user?.()"
          :camp-collaboration="avatarCollaboration"
        />
        <div class="flex-grow-1">
          <e-text-field
            v-model="localCollaboration.abbreviation"
            path="abbreviation"
            variant="underlined"
            vee-rules="oneEmojiOrTwoCharacters"
          />

          <e-color-picker
            v-model="localCollaboration.color"
            variant="underlined"
            path="color"
          />
        </div>
      </div>
    </fieldset>
  </e-form>
</template>

<script>
import UserAvatar from '@/components/user/UserAvatar.vue'
import CollaboratorRoleSelect from '@/components/collaborator/CollaboratorRoleSelect.vue'

export default {
  name: 'SettingsCollaboratorForm',
  components: { UserAvatar, CollaboratorRoleSelect },
  props: {
    collaboration: { type: Object, required: true },
    status: { type: [String, Boolean], required: false, default: false },
    readonlyRole: { type: [String, Boolean], required: false, default: false },
    initialCollaboration: { type: Object, required: false, default: null },
  },
  computed: {
    localCollaboration() {
      return this.collaboration
    },
    avatarCollaboration() {
      return {
        ...this.initialCollaboration,
        ...this.localCollaboration,
      }
    },
    translatedStatus() {
      return this.$t(`entity.campCollaboration.status.${this.status}`)
    },
  },
}
</script>

<style scoped>
/*noinspection CssUnusedSymbol*/
.ec-status-field:deep(.v-input__append-inner) {
  margin-top: 0;
  align-self: center;
  margin-right: -4px;
}
.e-avatar-field {
  display: grid;
  border: none;
  background: #eee;
  padding: 12px;
  border-bottom: 1px solid rgba(0, 0, 0, 0.42) !important;
}
.e-avatar-field legend {
  float: left;
}
</style>
