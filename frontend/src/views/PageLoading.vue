<template>
  <div class="sky">
    <div class="hill">
      <TentNight class="tent" />
      <div class="relative">
        <p class="text-white text-center px-3 d-flex justify-center mb-n8 relative">
          {{ $t('views.pageLoading.loading') }}
        </p>
      </div>
    </div>
  </div>
</template>

<script>
import TentNight from '@/assets/tents/TentNight.vue'
import { isLoggedIn } from '@/plugins/auth.js'
import { mapGetters } from 'vuex'

export default {
  name: 'PageLoading',
  components: { TentNight },
  computed: {
    ...mapGetters(['isAuthInitializing']),
  },
  watch: {
    isAuthInitializing(initializing) {
      if (!initializing) this.navigateAfterAuth()
    },
  },
  mounted() {
    if (!this.$store.state.auth.authInitializing) {
      this.navigateAfterAuth()
    }
  },
  methods: {
    navigateAfterAuth() {
      if (this.$route.name !== 'loading') return
      const redirect = this.$route.query.redirect
      if (isLoggedIn()) {
        this.$router.replace(redirect || { name: 'home' }).catch(() => {})
      } else {
        this.$router
          .replace({ name: 'login', query: redirect ? { redirect } : {} })
          .catch(() => {})
      }
    },
  },
}
</script>

<style scoped lang="scss">
.sky {
  height: 100%;
  background-image:
    radial-gradient(circle at bottom, #607d8b, #0c3c4c, #0e1c22),
    url('../assets/tents/stars.svg');
  background-blend-mode: screen;
  background-size: contain, 1470px;
  background-repeat: no-repeat, repeat-x;
  background-position:
    bottom,
    center top;
  animation: 350s linear infinite sky-translate;
}

@keyframes sky-translate {
  0% {
    background-position:
      bottom,
      calc(50% + 120px) top;
  }
  100% {
    background-position:
      top,
      calc(50% + 120px - 1780px) top;
  }
}

.hill {
  height: 100%;
  background-image: radial-gradient(
    150vmax 70% at bottom,
    #0f252e,
    #0d171d 70.1%,
    transparent 50%
  );
  display: flex;
  flex-direction: column;
  justify-content: center;
  background-position: bottom center;
  overflow: hidden;
  position: relative;
}

.tent {
  height: 300px;
  max-height: 80vw;
  width: auto;
  margin: 0 auto;
  z-index: 2;
}
</style>
