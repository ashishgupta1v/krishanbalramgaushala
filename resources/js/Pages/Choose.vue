<template>
  <div style="position:fixed;inset:0;display:flex;flex-direction:column;align-items:center;justify-content:center;padding:24px 20px;background:var(--bg);background-image:radial-gradient(ellipse 65% 55% at 18% 18%,rgba(210,148,8,.17) 0%,transparent 55%),radial-gradient(ellipse 55% 60% at 82% 82%,rgba(180,96,0,.12) 0%,transparent 55%);">
    <div class="devo-card-container" style="width:100%;max-width:380px;padding:24px 20px;">
      <!-- Header -->
      <div style="text-align:center;margin-bottom:32px;display:flex;flex-direction:column;align-items:center;">
        <div style="width: 90px; height: 90px; border-radius: 50%; display: flex; align-items: center; justify-content: center; margin-bottom: 16px; background: linear-gradient(145deg, var(--bg1), var(--bg)); box-shadow: 6px 6px 14px var(--sd), -6px -6px 14px var(--sl); padding: 4px; border: 1px solid rgba(255,255,255,0.75);">
          <img src="/logo.jpg" alt="Krishan Balram Gaushala Logo" style="width: 100%; height: 100%; border-radius: 50%; object-fit: cover;" />
        </div>
        <h1 class="fu" style="font-family:'Playfair Display',serif;font-size:26px;font-weight:800;">Welcome</h1>
        <p class="fu2" style="color:var(--tl);font-size:12px;margin-top:4px;line-height:1.5;">
          Krishan Balram Gaushala<br>Singla Enclave, Village Dullon Khurd, Pakhowal Road, Ludhiana
        </p>
      </div>

      <!-- Devotee Sign In -->
      <button class="choice-btn fu3" style="padding:16px 20px;margin-bottom:12px;border-left:4px solid var(--pr1);" @click="goLogin" :data-prefetch="route('devotee.login')">
        <div style="display:flex;align-items:center;gap:15px;">
          <div style="width:50px;height:50px;border-radius:12px;display:flex;align-items:center;justify-content:center;font-size:24px;box-shadow:3px 3px 8px var(--sd),-2px -2px 6px var(--sl);background:var(--bg);flex-shrink:0;">🔑</div>
          <div>
            <div style="font-family:'Playfair Display',serif;font-size:16px;font-weight:800;color:var(--tx);">Devotee Sign In</div>
            <div style="font-size:11px;color:var(--tl);margin-top:2px;">Sign in to view your details & upcoming events</div>
          </div>
        </div>
      </button>

      <!-- Devotee Register -->
      <button class="choice-btn fu3" style="padding:16px 20px;margin-bottom:14px;border-left:4px solid var(--gd1);" @click="goRegister" :data-prefetch="route('register')">
        <div style="display:flex;align-items:center;gap:15px;">
          <div style="width:50px;height:50px;border-radius:12px;display:flex;align-items:center;justify-content:center;font-size:24px;box-shadow:3px 3px 8px var(--sd),-2px -2px 6px var(--sl);background:var(--bg);flex-shrink:0;">🐄</div>
          <div>
            <div style="font-family:'Playfair Display',serif;font-size:16px;font-weight:800;color:var(--tx);">Join Gaushala Family</div>
            <div style="font-size:11px;color:var(--tl);margin-top:2px;">New Devotee? Register for becoming part of our family</div>
          </div>
        </div>
      </button>

      <!-- Admin button -->
      <button class="choice-btn fu4" style="padding:16px 20px;opacity:.9;margin-bottom:14px;" @click="goAdmin" :data-prefetch="route('admin.login')">
        <div style="display:flex;align-items:center;gap:14px;">
          <span style="font-size:28px;">🔐</span>
          <div>
            <div style="font-size:15px;font-weight:700;color:var(--tx);">Admin Panel</div>
            <div style="font-size:11px;color:var(--tl);margin-top:2px;">Gaushala management dashboard</div>
          </div>
          <span style="margin-left:auto;color:var(--td);font-size:20px;font-weight:300;">›</span>
        </div>
      </button>
    </div>
  </div>
</template>

<script setup>
import { onMounted } from 'vue';
import { router } from '@inertiajs/vue3';
import { useDevoteeStore } from '@/Stores/devotee';

const store = useDevoteeStore();

onMounted(() => {
  store.loadFromStorage();
});

function goLogin() {
  router.visit(route('devotee.login'));
}

function goRegister() {
  router.visit(route('register'));
}

function goAdmin() { router.visit(route('admin.login')); }
</script>
