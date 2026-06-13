<template>
  <div style="position:fixed;inset:0;display:flex;flex-direction:column;align-items:center;justify-content:center;padding:24px 20px;background:var(--bg);background-image:radial-gradient(ellipse 65% 55% at 18% 18%,rgba(210,148,8,.17) 0%,transparent 55%),radial-gradient(ellipse 55% 60% at 82% 82%,rgba(180,96,0,.12) 0%,transparent 55%);">
    <div class="devo-card-container" style="width:100%;max-width:380px;padding:24px 20px;">
      <!-- Header -->
      <div style="text-align:center;margin-bottom:32px;display:flex;flex-direction:column;align-items:center;">
        <!-- Logo -->
        <div style="margin-bottom:16px;">
          <picture>
            <source srcset="/logo.webp" type="image/webp">
            <img src="/logo1.png" alt="Krishan Balram Gaushala Logo" width="90" height="90" class="logo-img" style="width:90px;height:90px;" loading="eager" decoding="async" />
          </picture>
        </div>
        <h1 class="fu" style="font-family:'Playfair Display',serif;font-size:26px;font-weight:800;">Welcome</h1>
        <p class="fu2" style="color:var(--tl);font-size:12px;margin-top:4px;line-height:1.5;">
          Krishan Balram Gaushala<br>Singla Enclave, Village Dullon Khurd, Pakhowal Road, Ludhiana
        </p>
      </div>

      <!-- Devotee Register -->
      <button class="choice-btn fu3" style="padding:16px 20px;margin-bottom:14px;border-left:4px solid var(--gd1);" @click="goRegister" :data-prefetch="route('register')">
        <div style="display:flex;align-items:center;gap:15px;">
          <div style="width:50px;height:50px;border-radius:12px;display:flex;align-items:center;justify-content:center;box-shadow:3px 3px 8px var(--sd),-2px -2px 6px var(--sl);background:var(--bg);flex-shrink:0;color:var(--gd1);">
            <HeartHandshake style="width:24px;height:24px;" />
          </div>
          <div>
            <div style="font-family:'Playfair Display',serif;font-size:16px;font-weight:800;color:var(--tx);">Join Gaushala Family</div>
            <div style="font-size:11px;color:var(--tl);margin-top:2px;">New Devotee? Register for becoming part of our family</div>
          </div>
        </div>
      </button>

      <!-- Devotee Sign In -->
      <button class="choice-btn fu3" style="padding:16px 20px;margin-bottom:12px;border-left:4px solid var(--pr1);" @click="goLogin" :data-prefetch="route('devotee.login')">
        <div style="display:flex;align-items:center;gap:15px;">
          <div style="width:50px;height:50px;border-radius:12px;display:flex;align-items:center;justify-content:center;box-shadow:3px 3px 8px var(--sd),-2px -2px 6px var(--sl);background:var(--bg);flex-shrink:0;color:var(--pr1);">
            <LogIn style="width:24px;height:24px;" />
          </div>
          <div>
            <div style="font-family:'Playfair Display',serif;font-size:16px;font-weight:800;color:var(--tx);">Devotee Sign In</div>
            <div style="font-size:11px;color:var(--tl);margin-top:2px;">Sign in to view your details & upcoming events</div>
          </div>
        </div>
      </button>

      <!-- Admin button -->
      <button class="choice-btn fu4" style="padding:16px 20px;opacity:.9;margin-bottom:14px;" @click="goAdmin" :data-prefetch="route('admin.login')">
        <div style="display:flex;align-items:center;gap:15px;">
          <div style="width:50px;height:50px;border-radius:12px;display:flex;align-items:center;justify-content:center;box-shadow:3px 3px 8px var(--sd),-2px -2px 6px var(--sl);background:var(--bg);flex-shrink:0;color:var(--tl);">
            <Lock style="width:22px;height:22px;" />
          </div>
          <div>
            <div style="font-size:15px;font-weight:700;color:var(--tx);">Admin Panel</div>
            <div style="font-size:11px;color:var(--tl);margin-top:2px;">Gaushala management dashboard</div>
          </div>
          <span style="margin-left:auto;color:var(--td);font-size:20px;font-weight:300;">›</span>
        </div>
      </button>
    </div>

    <!-- Credits Footer -->
    <div class="fu4" style="position:absolute; bottom:24px; left:0; right:0; text-align:center; font-size:11px; color:var(--tl); z-index:10; font-family:'Poppins',sans-serif; letter-spacing:0.5px; opacity: 0.85;">
      Designed & Developed by <span style="color:var(--gd1); font-weight:700;">Ashish Gupta</span>
    </div>
  </div>
</template>

<script setup>
import { onMounted } from 'vue';
import { router, usePage } from '@inertiajs/vue3';
import { useDevoteeStore } from '@/Stores/devotee';
import { HeartHandshake, LogIn, Lock } from '@lucide/vue';

const store = useDevoteeStore();

onMounted(() => {
  const page = usePage();
  if (page.props.auth?.devotee) {
    router.visit(route('devotee.profile'));
    return;
  }
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
