<template>
  <div id="s-splash" style="position:fixed;inset:0;display:flex;flex-direction:column;align-items:center;justify-content:center;text-align:center;padding:32px 28px;overflow:hidden;background:var(--bg);background-image:radial-gradient(ellipse 65% 55% at 18% 18%,rgba(210,148,8,.17) 0%,transparent 55%),radial-gradient(ellipse 55% 60% at 82% 82%,rgba(180,96,0,.12) 0%,transparent 55%);">
    <!-- Concentric rings -->
    <div v-for="size in [540,390,270,170]" :key="size" class="s-ring"
         :style="`width:${size}px;height:${size}px;border:1px solid rgba(200,140,0,${size===540?.05:size===390?.08:size===270?.12:.17})`"/>

    <!-- Content -->
    <div style="position:relative;z-index:1;display:flex;flex-direction:column;align-items:center;">
      <!-- Logo -->
      <div style="width: 130px; height: 130px; border-radius: 50%; display: flex; align-items: center; justify-content: center; margin-bottom: 20px; background: linear-gradient(145deg, var(--bg1), var(--bg)); box-shadow: 8px 8px 20px var(--sd), -8px -8px 20px var(--sl); padding: 5px; border: 1px solid rgba(255,255,255,0.75); animation: pulse 2.5s ease infinite;">
        <img src="/logo.jpg" alt="Krishan Balram Gaushala Logo" style="width: 100%; height: 100%; border-radius: 50%; object-fit: cover; box-shadow: inset 2px 2px 5px rgba(0,0,0,0.15);" />
      </div>
      <div class="fu" style="font-family:'Playfair Display',serif;font-size:11px;font-weight:700;letter-spacing:3.5px;color:var(--gd1);text-transform:uppercase;margin-bottom:4px;">GauSeva Connect</div>
      <h1 class="fu2" style="font-family:'Playfair Display',serif;font-size:28px;font-weight:800;color:var(--tx);line-height:1.18;text-align:center;">Krishan Balram</h1>
      <h2 class="fu3" style="font-family:'Playfair Display',serif;font-size:22px;font-weight:700;color:var(--pr);margin-bottom:5px;">Gaushala</h2>
      <div class="fu4" style="font-size:11px;color:var(--tl);font-weight:500;line-height:1.4;">Singla Enclave, Village Dullon Khurd,<br>Pakhowal Road, Ludhiana</div>
      <div class="fu4" style="font-size:11px;color:var(--td);margin-bottom:52px;">Est. 1992 &nbsp;•&nbsp; Registered &nbsp;🙏</div>

      <!-- Loader -->
      <div v-if="loading" style="width:36px;height:36px;border:3px solid rgba(180,100,0,.18);border-top-color:var(--pr1);border-radius:50%;animation:spin .9s linear infinite;"/>

      <!-- Enter button -->
      <button v-else class="btn-saffron fu si" style="font-size:16px;padding:16px 50px;" @click="enter" :data-prefetch="route('choose')">
        🙏 &nbsp; Jai Gau Mata — Enter
      </button>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import { router } from '@inertiajs/vue3';

const loading = ref(true);

onMounted(() => {
  setTimeout(() => { loading.value = false; }, 1800);
});

function enter() {
  router.visit(route('choose'));
}
</script>

<style scoped>
.s-ring {
  position: absolute;
  border-radius: 50%;
  pointer-events: none;
  top: 50%;
  left: 50%;
  transform: translate(-50%, -50%);
}
</style>
