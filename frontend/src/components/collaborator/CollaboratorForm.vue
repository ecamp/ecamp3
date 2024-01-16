<template>
  <div>
    <e-text-field
      v-if="status"
      class="ec-status-field"
      :value="translatedStatus"
      readonly
      :name="$tc('entity.campCollaboration.fields.status')"
    >
      <template #append>
        <slot name="statusChange" />
      </template>
    </e-text-field>
    <v-tooltip v-if="readonlyRole" eager location="bottom">
      <span id="readonly">
        {{ $tc('components.collaborator.collaboratorForm.roleHint') }}
      </span>
      <template #activator="{ on }">
        <div tabindex="0" class="mt-3" v-on="on">
          <e-select
            v-model="localCollaboration.role"
            fieldname="role"
            readonly
            aria-readonly="true"
            aria-describedby="readonly"
            :name="$tc('entity.campCollaboration.fields.role')"
            :items="items"
            :hint="$tc('components.collaborator.collaboratorForm.roleHint')"
            persistent-hint
            item-value="key"
            item-text="role"
            vee-rules="required"
          >
            <template #selection="{ item }">
              <span
                >{{ item.role }} &middot;
                <span class="text-grey"
                  ><v-icon v-for="icon in item.icons" :key="icon" size="x-small">{{
                    icon
                  }}</v-icon
                  >&thinsp; ></span
                >
              </span>
            </template>
          </e-select>
        </div>
      </template>
    </v-tooltip>
    <e-select
      v-else
      v-model="localCollaboration.role"
      :name="$tc('entity.campCollaboration.fields.role')"
      fieldname="role"
      :items="items"
      :hint="$tc('components.collaborator.collaboratorForm.roleHint')"
      persistent-hint
      item-value="key"
      item-text="role"
      vee-rules="required"
    >
      <template #item="{ item, on, attrs }">
        <v-list-item v-bind="attrs" lines="two" v-on="on">
          <v-list-item-title>{{ item.role }}</v-list-item-title>
          <span class="text-caption">{{ item.abilities }}</span>

          <v-list-item-action-text class="text-right">
            <span
              ><v-icon v-for="icon in item.icons" :key="icon" size="small">{{
                icon
              }}</v-icon
              >&thinsp;</span
            >
          </v-list-item-action-text>
        </v-list-item>
      </template>
      <template #selection="{ item }">
        <span
          >{{ item.role }} &middot;
          <span class="text-grey"
            ><v-icon v-for="icon in item.icons" :key="icon" size="x-small">{{
              icon
            }}</v-icon
            >&thinsp;</span
          >
        </span>
      </template>
    </e-select>
  </div>
</template>

<script>
export default {
  name: 'SettingsCollaboratorForm',
  props: {
    collaboration: { type: Object, required: true },
    status: { type: [String, Boolean], required: false, default: false },
    readonlyRole: { type: [String, Boolean], required: false, default: false },
  },
  computed: {
    items() {
      return [
        {
          key: 'manager',
          role: this.$tc('entity.camp.collaborators.manager'),
          abilities: this.$tc('global.collaborationAbilities.manager'),
          icons: ['mdi-eye-outline', 'mdi-pencil-outline', 'mdi-cog-outline'],
        },
        {
          key: 'member',
          role: this.$tc('entity.camp.collaborators.member'),
          abilities: this.$tc('global.collaborationAbilities.member'),
          icons: ['mdi-eye-outline', 'mdi-pencil-outline'],
        },
        {
          key: 'guest',
          role: this.$tc('entity.camp.collaborators.guest'),
          abilities: this.$tc('global.collaborationAbilities.guest'),
          icons: ['mdi-eye-outline'],
        },
      ]
    },
    localCollaboration() {
      return this.collaboration
    },
    translatedStatus() {
      return this.$tc(`entity.campCollaboration.status.${this.status}`)
    },
  },
}
</script>

<style scoped>
.ec-status-field::v-deep .v-input__append-inner {
  margin-top: 0;
  align-self: center;
  margin-right: -4px;
}
</style>
