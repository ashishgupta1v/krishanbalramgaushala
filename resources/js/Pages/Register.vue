<template>
  <div style="position:fixed;inset:0;overflow-y:auto;overflow-x:hidden;background:var(--bg);background-image:radial-gradient(ellipse 65% 55% at 18% 18%,rgba(210,148,8,.17) 0%,transparent 55%),radial-gradient(ellipse 55% 60% at 82% 82%,rgba(180,96,0,.12) 0%,transparent 55%);">
    <div class="devo-card-container page" style="padding:24px 20px 50px;max-width:480px;margin:0 auto;">

      <!-- Header -->
      <div style="text-align:center;margin-bottom:24px;">
        <div style="display:flex;justify-content:center;margin-bottom:12px;">
          <div style="width: 80px; height: 80px; border-radius: 50%; display: flex; align-items: center; justify-content: center; background: linear-gradient(145deg, var(--bg1), var(--bg)); box-shadow: 4px 4px 10px var(--sd), -4px -4px 10px var(--sl); padding: 3px; border: 1px solid rgba(255,255,255,0.75);">
            <img src="/logo.jpg" alt="Krishan Balram Gaushala Logo" style="width: 100%; height: 100%; border-radius: 50%; object-fit: cover;" />
          </div>
        </div>
        <h1 style="font-family:'Playfair Display',serif;font-size:22px;font-weight:800;">
          {{ registered ? 'Welcome! 🙏' : 'Join Gau Seva Family' }}
        </h1>
        <p style="color:var(--tl);font-size:12px;margin-top:3px;">Krishan Balram Gaushala, Ludhiana</p>
      </div>

      <!-- Form (Registration) -->
      <div v-if="!registered">
        <div style="margin-bottom:14px;">
          <div class="slbl" style="margin-top:0;">Full Name *</div>
          <input v-model="form.name" class="n-inp" type="text" placeholder="e.g. Rajesh Kumar Sharma">
        </div>
        <div style="margin-bottom:14px;">
          <div class="slbl" style="margin-top:0;">WhatsApp Number *</div>
          <div style="position:relative;">
            <span style="position:absolute;left:15px;top:50%;transform:translateY(-50%);font-weight:700;font-size:13px;color:var(--tl);pointer-events:none;">+91</span>
            <input v-model="form.whatsapp" class="n-inp" type="tel" placeholder="10-digit number" style="padding-left:48px;" maxlength="10" inputmode="numeric">
          </div>
        </div>

        <!-- Married toggle -->
        <div style="margin-bottom:14px;display:flex;align-items:center;gap:8px;padding:4px 2px;">
          <input v-model="isMarried" type="checkbox" class="n-chk" id="married_chk">
          <label for="married_chk" style="font-size:13px;font-weight:600;color:var(--tx);cursor:pointer;user-select:none;display:flex;align-items:center;gap:6px;flex-wrap:wrap;">
            <span>💍 Married Devotee</span>
            <span style="font-size:11px;color:var(--tl);font-weight:400;">(Receive anniversary blessings)</span>
          </label>
        </div>

        <!-- Date inputs (manual text entry) -->
        <div :style="isMarried ? 'display:grid;grid-template-columns:1fr 1fr;gap:12px;margin-bottom:14px;' : 'margin-bottom:14px;'">
          <div>
            <div class="slbl" style="margin-top:0;">Date of Birth *</div>
            <input v-model="form.dobRaw" class="n-inp" type="text" placeholder="DD-MM-YYYY (e.g. 15-08-1995)">
          </div>
          <div v-if="isMarried">
            <div class="slbl" style="margin-top:0;">Anniversary Date *</div>
            <input v-model="form.anniversaryRaw" class="n-inp" type="text" placeholder="DD-MM-YYYY (e.g. 24-11-2018)">
          </div>
        </div>

        <!-- Passwords -->
        <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px;margin-bottom:14px;">
          <div>
            <div class="slbl" style="margin-top:0;">Choose Password *</div>
            <input v-model="form.password" class="n-inp" type="password" placeholder="Min. 6 characters">
          </div>
          <div>
            <div class="slbl" style="margin-top:0;">Confirm Password *</div>
            <input v-model="form.password_confirmation" class="n-inp" type="password" placeholder="Confirm Password">
          </div>
        </div>

        <button class="btn-saffron" style="width:100%;padding:15px;font-size:15px;margin-top:10px;" :disabled="registering" @click="registerDevotee">
          <span v-if="registering" class="spin"/>
          <span v-else>✓ Register Now →</span>
        </button>
        <p v-if="errorMsg" style="color:var(--er);font-size:12px;text-align:center;margin-top:10px;">{{ errorMsg }}</p>

        <div style="text-align:center;margin-top:20px;">
          <span style="font-size:12px;color:var(--tl);">Already have an account? </span>
          <Link :href="route('devotee.login')" style="font-size:12px;color:var(--pr1);font-weight:700;text-decoration:none;">Sign In here</Link>
        </div>
      </div>

      <!-- Success Screen -->
      <div v-else style="text-align:center;">
        <div style="font-size:72px;margin-bottom:16px;animation:pulse 2s ease infinite;">✅</div>
        <h2 style="font-family:'Playfair Display',serif;font-size:24px;font-weight:800;margin-bottom:6px;">Registered!</h2>
        <p style="color:var(--tl);font-size:14px;line-height:1.8;margin-bottom:26px;">
          Welcome, <strong>{{ form.name }} Ji</strong>!<br>
          You're now part of our Gau Seva family. 🙏
        </p>
        <!-- WA Message Preview -->
        <div class="wa-bbl" style="max-width:320px;margin:0 auto 28px;text-align:left;">
          <div style="display:flex;align-items:center;gap:10px;margin-bottom:9px;padding-bottom:9px;border-bottom:1px solid rgba(255,255,255,.1);">
            <div style="width:38px;height:38px;border-radius:50%;display:flex;align-items:center;justify-content:center;flex-shrink:0;padding:1px;background:var(--bg);border:1px solid rgba(255,255,255,0.5);">
              <img src="/logo.jpg" alt="logo" style="width:100%;height:100%;border-radius:50%;object-fit:cover;" />
            </div>
            <div>
              <div style="color:#fff;font-weight:700;font-size:12px;">Krishan Balram Gaushala</div>
              <div style="color:rgba(255,255,255,.45);font-size:10px;">Official • Just now</div>
            </div>
          </div>
          <div class="wa-msg">
            <p style="color:#fff;font-size:12px;line-height:1.7;white-space:pre-line;margin:0;">{{ welcomeMsg }}</p>
            <div style="text-align:right;color:rgba(255,255,255,.4);font-size:10px;margin-top:5px;">✓✓ Delivered</div>
          </div>
        </div>
        <button class="btn-saffron" style="padding:14px 44px;font-size:15px;" @click="goProfile">View My Profile →</button>
      </div>

    </div>
  </div>
</template>

<script setup>
import { ref, computed } from 'vue';
import { router, Link } from '@inertiajs/vue3';
import { useDevoteeStore } from '@/Stores/devotee';

const store = useDevoteeStore();
const registered = ref(false);
const registering = ref(false);
const errorMsg = ref('');
const isMarried = ref(false);

const form = ref({
  name: '',
  whatsapp: '',
  dobRaw: '',
  anniversaryRaw: '',
  password: '',
  password_confirmation: '',
  fb_consent: true,
});

const welcomeMsg = computed(() =>
  `🙏 Jai Gau Mata!\n\nDear ${form.value.name} Ji,\n\nWelcome to our divine Gau Seva family! 🐄\n\nYou will receive special blessings on your Birthday and Anniversary from Krishan Balram Gaushala, Singla Enclave, Village Dullon Khurd, Pakhowal Road, Ludhiana.\n\n— Anand Tayal Ji`
);

function back() { router.visit(route('choose')); }

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

async function registerDevotee() {
  errorMsg.value = '';
  if (!form.value.name.trim()) { errorMsg.value = 'Please enter your full name'; return; }
  if (!/^\d{10}$/.test(form.value.whatsapp)) { errorMsg.value = 'Enter valid 10-digit WhatsApp number'; return; }
  
  const dobClean = form.value.dobRaw.trim();
  if (!dobClean) { errorMsg.value = 'Please enter your date of birth'; return; }
  if (!validateDateStr(dobClean)) { errorMsg.value = 'Enter Date of Birth in DD-MM-YYYY format (e.g. 15-08-1995)'; return; }

  if (isMarried.value) {
    const annClean = form.value.anniversaryRaw.trim();
    if (!annClean) { errorMsg.value = 'Please enter your anniversary date'; return; }
    if (!validateDateStr(annClean)) { errorMsg.value = 'Enter Anniversary Date in DD-MM-YYYY format (e.g. 24-11-2018)'; return; }
  }

  if (!form.value.password || form.value.password.length < 6) {
    errorMsg.value = 'Password must be at least 6 characters long';
    return;
  }
  if (form.value.password !== form.value.password_confirmation) {
    errorMsg.value = 'Password confirmation does not match';
    return;
  }

  registering.value = true;
  try {
    const payload = {
      name: form.value.name,
      whatsapp: form.value.whatsapp,
      dob: parseUIDate(dobClean),
      anniversary: isMarried.value ? parseUIDate(form.value.anniversaryRaw.trim()) : null,
      fb_consent: form.value.fb_consent,
      password: form.value.password,
      password_confirmation: form.value.password_confirmation,
    };
    
    await axios.post(route('register.store'), payload);
    store.setDevotee({ name: form.value.name, whatsapp: form.value.whatsapp });
    registered.value = true;
  } catch (e) {
    errorMsg.value = e.response?.data?.message || 'Registration failed. This phone number may already be registered.';
  } finally {
    registering.value = false;
  }
}

function goProfile() { router.visit(route('devotee.profile')); }
</script>
