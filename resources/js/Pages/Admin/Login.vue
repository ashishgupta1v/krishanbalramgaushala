<template>
  <div style="position:fixed;inset:0;display:flex;flex-direction:column;align-items:center;justify-content:center;padding:28px;background:var(--bg);">
    <div style="width:100%;max-width:340px;">
      <div style="text-align:center;margin-bottom:30px;">
        <div style="display:flex;justify-content:center;margin-bottom:14px;">
          <div style="width: 80px; height: 80px; border-radius: 50%; display: flex; align-items: center; justify-content: center; background: linear-gradient(145deg, var(--bg1), var(--bg)); box-shadow: 4px 4px 10px var(--sd), -4px -4px 10px var(--sl); padding: 3px; border: 1px solid rgba(255,255,255,0.75);">
            <picture>
              <source srcset="/logo.webp" type="image/webp">
              <img src="/logo1.png" alt="Krishan Balram Gaushala Logo" width="80" height="80" style="width:100%;height:100%;object-fit:contain;" loading="eager" decoding="async" />
            </picture>
          </div>
        </div>
        <h1 style="font-family:'Playfair Display',serif;font-size:24px;font-weight:800;">Admin Panel</h1>
        <p style="color:var(--tl);font-size:13px;margin-top:4px;">Krishan Balram Gaushala</p>
      </div>
      <div class="nr" style="padding:24px;border-radius:20px;">
        <div class="slbl">Username</div>
        <input v-model="form.username" class="n-inp" type="text" placeholder="admin" style="margin-bottom:16px;" @keydown.enter="login">
        <div class="slbl">Password</div>
        <input v-model="form.password" class="n-inp" type="password" placeholder="••••••••" style="margin-bottom:22px;" @keydown.enter="login">
        <p v-if="error" style="color:var(--er);font-size:12px;text-align:center;margin-bottom:12px;">{{ error }}</p>
        <button class="btn-saffron" style="width:100%;padding:15px;font-size:15px;" :disabled="loading" @click="login">
          <span v-if="loading" class="spin"/>
          <span v-else>Login to Dashboard →</span>
        </button>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref } from 'vue';
import { router, useForm } from '@inertiajs/vue3';

const form = ref({ username: '', password: '' });
const loading = ref(false);
const error = ref('');

function back() { router.visit(route('choose')); }

async function login() {
  error.value = '';
  if (!form.value.username || !form.value.password) { error.value = 'Enter credentials'; return; }
  loading.value = true;
  router.post(route('admin.login.post'), form.value, {
    onError: (e) => { error.value = e.message || 'Invalid credentials'; },
    onFinish: () => { loading.value = false; },
  });
}
</script>
