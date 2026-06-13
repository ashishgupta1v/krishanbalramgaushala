<template>
  <div style="position:fixed;inset:0;overflow-y:auto;overflow-x:hidden;background:var(--bg);background-image:radial-gradient(ellipse 65% 55% at 18% 18%,rgba(210,148,8,.17) 0%,transparent 55%),radial-gradient(ellipse 55% 60% at 82% 82%,rgba(180,96,0,.12) 0%,transparent 55%);">
    <div class="devo-card-container page" style="padding:24px 20px 50px;max-width:480px;margin:0 auto;">

      <!-- Header -->
      <div style="text-align:center;margin-bottom:30px;">
        <div style="display:flex;justify-content:center;margin-bottom:12px;">
          <picture>
            <source srcset="/logo.webp" type="image/webp">
            <img src="/logo1.png" alt="Krishan Balram Gaushala Logo" width="80" height="80" class="logo-img" style="width:80px;height:80px;" loading="eager" decoding="async" />
          </picture>
        </div>
        <h1 style="font-family:'Playfair Display',serif;font-size:22px;font-weight:800;">Reset Password</h1>
        <p style="color:var(--tl);font-size:12px;margin-top:3px;">Verify your identity to choose a new password</p>
      </div>

      <!-- Success Reset Screen -->
      <div v-if="resetSuccess" style="text-align:center;padding:20px 10px;">
        <div style="font-size:64px;margin-bottom:16px;">✅</div>
        <h2 style="font-family:'Playfair Display',serif;font-size:22px;font-weight:800;margin-bottom:10px;">Password Reset!</h2>
        <p style="color:var(--tl);font-size:13px;line-height:1.6;margin-bottom:28px;">
          Your password has been updated. You can now sign in using your WhatsApp number and new password.
        </p>
        <button class="btn-saffron" style="padding:14px 44px;font-size:15px;" @click="goLogin">Sign In Now →</button>
      </div>

      <!-- Form (Reset) -->
      <div v-else class="nr" style="padding:24px;border-radius:20px;margin-bottom:20px;">
        <div style="margin-bottom:14px;">
          <div class="slbl" style="margin-top:0;">WhatsApp Number *</div>
          <div style="position:relative;">
            <span style="position:absolute;left:15px;top:50%;transform:translateY(-50%);font-weight:700;font-size:13px;color:var(--tl);pointer-events:none;">+91</span>
            <input v-model="form.whatsapp" class="n-inp" type="tel" placeholder="10-digit number" style="padding-left:48px;" maxlength="10" inputmode="numeric">
          </div>
        </div>

        <div style="margin-bottom:14px;">
          <div class="slbl" style="margin-top:0;">Full Name *</div>
          <input v-model="form.name" class="n-inp" type="text" placeholder="As registered (e.g. Rajesh Kumar Sharma)">
        </div>

        <div style="margin-bottom:16px;">
          <div class="slbl" style="margin-top:0;">Date of Birth *</div>
          <input v-model="form.dobRaw" class="n-inp" type="text" placeholder="DD-MM-YYYY (e.g. 15-08-1995)">
        </div>

        <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px;margin-bottom:22px;">
          <div>
            <div class="slbl" style="margin-top:0;">New Password *</div>
            <input v-model="form.password" class="n-inp" type="password" placeholder="Min. 6 characters">
          </div>
          <div>
            <div class="slbl" style="margin-top:0;">Confirm Password *</div>
            <input v-model="form.password_confirmation" class="n-inp" type="password" placeholder="Confirm Password">
          </div>
        </div>

        <p v-if="errorMsg" style="color:var(--er);font-size:12px;text-align:center;margin-bottom:16px;">{{ errorMsg }}</p>

        <button class="btn-saffron" style="width:100%;padding:15px;font-size:15px;" :disabled="resetting" @click="resetDevoteePassword">
          <span v-if="resetting" class="spin"/>
          <span v-else>Reset Password →</span>
        </button>
      </div>

    </div>
  </div>
</template>

<script setup>
import { ref } from 'vue';
import { router } from '@inertiajs/vue3';

const resetting = ref(false);
const resetSuccess = ref(false);
const errorMsg = ref('');

const form = ref({
  whatsapp: '',
  name: '',
  dobRaw: '',
  password: '',
  password_confirmation: '',
});

function back() { router.visit(route('devotee.login')); }
function goLogin() { router.visit(route('devotee.login')); }

function validateDateStr(val) {
  if (!val) return false;
  const regex = /^(\d{2})-(\d{2})-(\d{4})$/;
  if (!regex.test(val)) return false;
  const [, d, m, y] = val.match(regex);
  const day = parseInt(d, 10);
  const month = parseInt(m, 10);
  const year = parseInt(y, 10);
  if (day < 1 || day > 31 || month < 1 || month > 12) return false;
  const currentYear = new Date().getFullYear();
  if (year < 1900 || year > currentYear + 1) return false;
  const dateObj = new Date(year, month - 1, day);
  return dateObj.getFullYear() === year && dateObj.getMonth() === month - 1 && dateObj.getDate() === day;
}

function parseUIDate(val) {
  if (!val) return null;
  const parts = val.split('-');
  return `${parts[2]}-${parts[1]}-${parts[0]}`; // YYYY-MM-DD
}

async function resetDevoteePassword() {
  errorMsg.value = '';

  if (!/^\d{10}$/.test(form.value.whatsapp)) {
    errorMsg.value = 'Please enter a valid 10-digit WhatsApp number';
    return;
  }
  if (!form.value.name.trim()) {
    errorMsg.value = 'Please enter your registered full name';
    return;
  }

  const dobClean = form.value.dobRaw.trim();
  if (!dobClean) {
    errorMsg.value = 'Please enter your date of birth';
    return;
  }
  if (!validateDateStr(dobClean)) {
    errorMsg.value = 'Enter Date of Birth in DD-MM-YYYY format (e.g. 15-08-1995)';
    return;
  }

  if (!form.value.password || form.value.password.length < 6) {
    errorMsg.value = 'New Password must be at least 6 characters long';
    return;
  }
  if (form.value.password !== form.value.password_confirmation) {
    errorMsg.value = 'New Password confirmation does not match';
    return;
  }

  resetting.value = true;
  try {
    const payload = {
      whatsapp: form.value.whatsapp,
      name: form.value.name,
      dob: parseUIDate(dobClean),
      password: form.value.password,
      password_confirmation: form.value.password_confirmation,
    };

    const res = await axios.post(route('devotee.reset-password.post'), payload);
    if (res.data.success) {
      resetSuccess.value = true;
    } else {
      errorMsg.value = res.data.message || 'Verification failed';
    }
  } catch (e) {
    errorMsg.value = e.response?.data?.message || 'Verification failed. Please check WhatsApp number, name, and DOB.';
  } finally {
    resetting.value = false;
  }
}
</script>
