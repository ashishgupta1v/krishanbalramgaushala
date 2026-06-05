<template>
  <div style="position:fixed;inset:0;overflow-y:auto;overflow-x:hidden;background:var(--bg);background-image:radial-gradient(ellipse 65% 55% at 18% 18%,rgba(210,148,8,.17) 0%,transparent 55%),radial-gradient(ellipse 55% 60% at 82% 82%,rgba(180,96,0,.12) 0%,transparent 55%);">
    <div class="devo-card-container page" style="padding:24px 20px 50px;max-width:480px;margin:0 auto;">

      <!-- Back -->
      <div style="margin-bottom:20px;">
        <button class="btn-ghost" style="padding:7px 16px;font-size:12px;" @click="back">← Back</button>
      </div>

      <!-- Header -->
      <div style="text-align:center;margin-bottom:30px;">
        <div style="display:flex;justify-content:center;margin-bottom:12px;">
          <div style="width: 80px; height: 80px; border-radius: 50%; display: flex; align-items: center; justify-content: center; background: linear-gradient(145deg, var(--bg1), var(--bg)); box-shadow: 4px 4px 10px var(--sd), -4px -4px 10px var(--sl); padding: 3px; border: 1px solid rgba(255,255,255,0.7);">
            <img src="/logo.jpg" alt="Krishan Balram Gaushala Logo" style="width: 100%; height: 100%; border-radius: 50%; object-fit: cover;" />
          </div>
        </div>
        <h1 style="font-family:'Playfair Display',serif;font-size:22px;font-weight:800;">Devotee Sign In</h1>
        <p style="color:var(--tl);font-size:12px;margin-top:3px;">Krishan Balram Gaushala, Ludhiana</p>
      </div>

      <!-- Form (Login) -->
      <div class="nr" style="padding:24px;border-radius:20px;margin-bottom:20px;">
        <div style="margin-bottom:16px;">
          <div class="slbl" style="margin-top:0;">WhatsApp Number *</div>
          <div style="position:relative;">
            <span style="position:absolute;left:15px;top:50%;transform:translateY(-50%);font-weight:700;font-size:13px;color:var(--tl);pointer-events:none;">+91</span>
            <input v-model="form.whatsapp" class="n-inp" type="tel" placeholder="10-digit number" style="padding-left:48px;" maxlength="10" inputmode="numeric" @keydown.enter="loginDevotee">
          </div>
        </div>

        <div style="margin-bottom:22px;">
          <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:6px;">
            <div class="slbl" style="margin:0;">Password *</div>
            <Link :href="route('devotee.reset-password')" style="font-size:11px;color:var(--pr1);font-weight:700;text-decoration:none;margin-left:auto;">Forgot Password?</Link>
          </div>
          <input v-model="form.password" class="n-inp" type="password" placeholder="••••••••" @keydown.enter="loginDevotee">
        </div>

        <p v-if="errorMsg" style="color:var(--er);font-size:12px;text-align:center;margin-bottom:16px;">{{ errorMsg }}</p>

        <button class="btn-saffron" style="width:100%;padding:15px;font-size:15px;" :disabled="loggingIn" @click="loginDevotee">
          <span v-if="loggingIn" class="spin"/>
          <span v-else>Sign In →</span>
        </button>
      </div>

      <div style="text-align:center;margin-top:20px;">
        <span style="font-size:12px;color:var(--tl);">New Devotee? </span>
        <Link :href="route('register')" style="font-size:12px;color:var(--pr1);font-weight:700;text-decoration:none;">Join Gaushala Family here</Link>
      </div>

    </div>
  </div>
</template>

<script setup>
import { ref } from 'vue';
import { router, Link } from '@inertiajs/vue3';
import { useDevoteeStore } from '@/Stores/devotee';

const store = useDevoteeStore();
const loggingIn = ref(false);
const errorMsg = ref('');

const form = ref({
  whatsapp: '',
  password: '',
});

function back() { router.visit(route('choose')); }

async function loginDevotee() {
  errorMsg.value = '';
  if (!/^\d{10}$/.test(form.value.whatsapp)) {
    errorMsg.value = 'Please enter a valid 10-digit WhatsApp number';
    return;
  }
  if (!form.value.password) {
    errorMsg.value = 'Please enter your password';
    return;
  }

  loggingIn.value = true;
  try {
    const res = await axios.post(route('devotee.login.post'), form.value);
    if (res.data.success) {
      store.setDevotee(res.data.devotee);
      router.visit(route('devotee.profile'));
    } else {
      errorMsg.value = 'Authentication failed';
    }
  } catch (e) {
    errorMsg.value = e.response?.data?.message || 'Invalid WhatsApp number or password';
  } finally {
    loggingIn.value = false;
  }
}
</script>
