<template>
  <div class="sky">
    <div class="hill">
      <div class="d-flex absolute w-100 top-0 localnav">
        <ButtonBack
          v-if="!$vuetify.breakpoint.mdAndUp && hasHistory"
          visible-label
          text
          dark
        />
        <UserMeta v-if="!$vuetify.breakpoint.mdAndUp" avatar-only />
      </div>
      <ShootingStar class="shootingstar" />
      <TentNight class="tent" />
      <div class="relative">
        <p class="white--text text-center px-3 d-flex justify-center mb-n8 relative">
          <i18n path="views.pageNotFound.detail">
            <template #br><br /></template>
          </i18n>
          <br />
          <v-btn text dark :to="{ name: 'home' }" class="absolute bottom-n100">
            <v-icon left>mdi-tent</v-icon>
            {{ $tc('views.pageNotFound.backToHome') }}
          </v-btn>
        </p>
      </div>
    </div>
  </div>
</template>

<script>
import TentNight from '@/assets/tents/TentNight.vue'
import UserMeta from '@/components/navigation/UserMeta.vue'
import ButtonBack from '@/components/buttons/ButtonBack.vue'
import ShootingStar from '@/assets/tents/ShootingStar.vue'

export default {
  name: 'PageNotFound',
  components: { ShootingStar, ButtonBack, UserMeta, TentNight },
  data: () => {
    return {
      hasHistory: false,
    }
  },
  mounted() {
    if (window.history.length && window.history.length >= 1) {
      this.hasHistory = true
    }
  },
}
</script>

<style scoped lang="scss">
.localnav {
  align-items: center;
  justify-content: space-between;
}

.sky {
  height: 100%;
  background-image: radial-gradient(circle at bottom, #607d8b, #0c3c4c, #0e1c22),
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

.shootingstar {
  position: absolute;
  bottom: 0;
  left: 50%;
  width: 200px;
  transform-origin: center;
  animation: schnuppe 1s 2s 1 linear both;
}

@keyframes schnuppe {
  0% {
    display: block;
    opacity: 0;
    transform: translateY(75vh) rotate(20deg) translateY(-150vh);
  }
  50% {
    display: block;
    opacity: 1;
    transform: translateY(75vh) rotate(0) translateY(-150vh);
  }
  100% {
    display: block;
    opacity: 0;
    transform: translateY(75vh) rotate(-20deg) translateY(-150vh);
  }
}
</style>
