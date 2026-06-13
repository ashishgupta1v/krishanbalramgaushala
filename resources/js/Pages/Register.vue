<template>
  <div style="position:fixed;inset:0;overflow-y:auto;overflow-x:hidden;background:var(--bg);background-image:radial-gradient(ellipse 65% 55% at 18% 18%,rgba(210,148,8,.17) 0%,transparent 55%),radial-gradient(ellipse 55% 60% at 82% 82%,rgba(180,96,0,.12) 0%,transparent 55%);">
    <div class="devo-card-container page" style="padding:24px 20px 50px;max-width:480px;margin:0 auto;">

      <!-- Header -->
      <div style="text-align:center;margin-bottom:24px;">
        <div style="display:flex;justify-content:center;margin-bottom:12px;">
          <picture>
            <source srcset="/logo.webp" type="image/webp">
            <img src="/logo1.png" alt="Krishan Balram Gaushala Logo" width="80" height="80" class="logo-img" style="width:80px;height:80px;" loading="eager" decoding="async" />
          </picture>
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
        <div style="margin-bottom:14px;">
          <div class="slbl" style="margin-top:0;">Profile Photo</div>
          <input type="file" @change="handlePhotoUpload" accept="image/*" class="n-inp file-upload-input" style="padding:10px;">
          <div v-if="photoError" style="font-size:10px;color:var(--er);margin-top:4px;">{{ photoError }}</div>
        </div>

        <!-- Married toggle -->
        <div style="margin-bottom:14px;display:flex;align-items:center;gap:8px;padding:4px 2px;">
          <input v-model="isMarried" type="checkbox" class="n-chk" id="married_chk">
          <label for="married_chk" style="font-size:13px;font-weight:600;color:var(--tx);cursor:pointer;user-select:none;display:flex;align-items:center;gap:6px;flex-wrap:wrap;">
            <Heart style="width:14px;height:14px;color:var(--pr1);fill:var(--pr1);" />
            <span>Married Devotee</span>
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
      <div v-else style="text-align:center;display:flex;flex-direction:column;align-items:center;">
        <div style="width:72px;height:72px;border-radius:50%;background:rgba(46,125,50,0.1);color:var(--ok1);display:flex;align-items:center;justify-content:center;margin-bottom:16px;animation:pulse 2s ease infinite;">
          <Check style="width:36px;height:36px;stroke-width:3;" />
        </div>
        <h2 style="font-family:'Playfair Display',serif;font-size:24px;font-weight:800;margin-bottom:6px;">Registered!</h2>
        <p style="color:var(--tl);font-size:14px;line-height:1.8;margin-bottom:26px;">
          Welcome, <strong>{{ form.name }} Ji</strong>!<br>
          You're now part of our Gau Seva family. 🙏
        </p>
        <button class="btn-saffron" style="padding:14px 44px;font-size:15px;" @click="goProfile">View My Profile →</button>
      </div>

    </div>
  </div>
</template>

<script setup>
import { ref, computed } from 'vue';
import { router, Link } from '@inertiajs/vue3';
import { useDevoteeStore } from '@/Stores/devotee';
import { Heart, Check } from '@lucide/vue';

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
  photo: null,
});

const photoError = ref('');

function handlePhotoUpload(e) {
  photoError.value = '';
  const file = e.target.files[0];
  if (file) {
    if (file.size > 5 * 1024 * 1024) {
      photoError.value = 'Photo size must be less than 5MB.';
      e.target.value = '';
      form.value.photo = null;
      return;
    }
    form.value.photo = file;
  }
}

const welcomeMsg = computed(() =>
  `🙏 Jai Gau Mata!\n\nDear ${form.value.name} Ji,\n\nWelcome to our divine Gau Seva family! 🐄\n\n May Gau Mata bless your bond with eternal love and togetherness. \n\n— Krishan Balram Gaushala, Singla Enclave, Village Dullon Khurd, Pakhowal Road, Ludhiana`
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
  
  const dobClean = form.value.dobRaw.trim().replace(/[\/\.\s]/g, '-');
  if (!dobClean) { errorMsg.value = 'Please enter your date of birth'; return; }
  let dClean = dobClean;
  const dMatch = dClean.match(/^(\d{2})(\d{2})(\d{4})$/);
  if (dMatch) dClean = `${dMatch[1]}-${dMatch[2]}-${dMatch[3]}`;
  
  if (!validateDateStr(dClean)) { errorMsg.value = 'Enter Date of Birth in DD-MM-YYYY format (e.g. 15-08-1995)'; return; }

  if (isMarried.value) {
    let aClean = form.value.anniversaryRaw.trim().replace(/[\/\.\s]/g, '-');
    if (!aClean) { errorMsg.value = 'Please enter your anniversary date'; return; }
    const aMatch = aClean.match(/^(\d{2})(\d{2})(\d{4})$/);
    if (aMatch) aClean = `${aMatch[1]}-${aMatch[2]}-${aMatch[3]}`;
    
    if (!validateDateStr(aClean)) { errorMsg.value = 'Enter Anniversary Date in DD-MM-YYYY format (e.g. 24-11-2018)'; return; }
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
    const formData = new FormData();
    formData.append('name', form.value.name);
    formData.append('whatsapp', form.value.whatsapp);
    
    // Support DDMMYYYY formatted dynamically to DD-MM-YYYY
    let dClean = dobClean;
    const dMatch = dClean.match(/^(\d{2})(\d{2})(\d{4})$/);
    if (dMatch) dClean = `${dMatch[1]}-${dMatch[2]}-${dMatch[3]}`;
    formData.append('dob', parseUIDate(dClean));
    
    if (isMarried.value) {
      let aClean = form.value.anniversaryRaw.trim().replace(/[\/\.\s]/g, '-');
      const aMatch = aClean.match(/^(\d{2})(\d{2})(\d{4})$/);
      if (aMatch) aClean = `${aMatch[1]}-${aMatch[2]}-${aMatch[3]}`;
      formData.append('anniversary', parseUIDate(aClean));
    }
    
    formData.append('fb_consent', form.value.fb_consent ? '1' : '0');
    formData.append('password', form.value.password);
    formData.append('password_confirmation', form.value.password_confirmation);
    
    if (form.value.photo) {
      formData.append('photo', form.value.photo);
    }
    
    const response = await axios.post(route('register.store'), formData, {
      headers: { 'Content-Type': 'multipart/form-data' }
    });
    
    // Verify the response indicates success
    if (response.data && response.data.success) {
      store.setDevotee({ name: form.value.name, whatsapp: form.value.whatsapp });
      registered.value = true;
    } else {
      errorMsg.value = response.data?.message || 'Registration failed. Please try again.';
    }
  } catch (e) {
    // Extract specific field validation errors from Laravel
    const errors = e.response?.data?.errors;
    if (errors) {
      const firstError = Object.values(errors).flat()[0];
      errorMsg.value = firstError || e.response?.data?.message || 'Registration failed.';
    } else {
      errorMsg.value = e.response?.data?.message || 'Registration failed. This phone number may already be registered.';
    }
  } finally {
    registering.value = false;
  }
}

function goProfile() { router.visit(route('devotee.profile')); }
</script>
