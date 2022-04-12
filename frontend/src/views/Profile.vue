<template>
  <v-container fluid>
    <content-card max-width="800" :title="$tc('views.profile.profile') + ': ' + user.displayName" toolbar>
      <v-col>
        <ProfileForm />
      </v-col>
    </content-card>
  </v-container>
</template>

<script>
import ContentCard from '@/components/layout/ContentCard.vue'
import VueI18n from '@/plugins/i18n'
import ProfileForm from '@/components/profile/ProfileForm.vue'

export default {
  name: 'Home',
  components: {
    ProfileForm,
    ContentCard
  },
  computed: {
    user () {
      return this.$auth.user()
    }
  },
  watch: {
    user () {
      if (VueI18n.availableLocales.includes(this.profile.language)) {
        this.$store.commit('setLanguage', this.profile.language)
      }
    }
  },
  mounted () {
    this.api.reload(this.user)
      .then(user => this.api.reload(user.profile()))
  }
}
</script>

<style lang="scss" scoped>
</style>
