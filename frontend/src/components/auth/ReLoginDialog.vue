<template>
  <v-dialog v-model="isAuthRequired" persistent max-width="500">
    <v-card style="overflow: hidden">
      <v-card-title>{{ $t('global.reLoginDialog.title') }}</v-card-title>
      <v-card-text>
        <p class="mb-0">{{ $t('global.reLoginDialog.description') }}</p>
      </v-card-text>
      <v-card-actions>
        <v-btn variant="outlined" @click="openLoginTab">
          {{ $t('global.reLoginDialog.loginAgain') }}
        </v-btn>
        <v-spacer />
        <v-btn variant="text" @click="logOut">{{ $t('global.button.logout') }}</v-btn>
      </v-card-actions>
    </v-card>
  </v-dialog>
</template>

<script>
import { resolveReAuth, rejectReAuth, getJWTExpirationTimestamp } from '@/plugins/auth.js'
import { mapGetters } from 'vuex'

export default {
  name: 'ReLoginDialog',
  data() {
    return {
      pollInterval: null,
    }
  },
  computed: {
    ...mapGetters(['isAuthRequired']),
  },
  watch: {
    isAuthRequired(val) {
      if (!val) {
        this.stopPolling()
      }
    },
  },
  beforeUnmount() {
    this.stopPolling()
  },
  methods: {
    openLoginTab() {
      window.open(this.$router.resolve({ name: 'login' }).href, '_blank')
      this.startPolling()
    },
    startPolling() {
      if (this.pollInterval) return
      this.pollInterval = setInterval(() => {
        if (Date.now() < getJWTExpirationTimestamp()) {
          this.stopPolling()
          resolveReAuth()
        }
      }, 1000)
    },
    stopPolling() {
      if (this.pollInterval) {
        clearInterval(this.pollInterval)
        this.pollInterval = null
      }
    },
    async logOut() {
      this.stopPolling()
      rejectReAuth()
      await this.$auth.logout()
    },
  },
}
</script>
