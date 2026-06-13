<template>
  <div style="position:fixed;inset:0;overflow-y:auto;overflow-x:hidden;background:var(--bg);background-image:radial-gradient(ellipse 65% 55% at 18% 18%,rgba(210,148,8,.17) 0%,transparent 55%),radial-gradient(ellipse 55% 60% at 82% 82%,rgba(180,96,0,.12) 0%,transparent 55%);">

    <div class="devo-profile-container page" style="position:relative;z-index:1;padding:24px 20px 40px;margin:0 auto;">

      <!-- Top Bar -->
      <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:22px;">
        <div style="display:flex;align-items:center;gap:12px;">
          <img v-if="devotee.photo_url" :src="devotee.photo_url" alt="Profile" style="width:44px;height:44px;border-radius:50%;object-fit:cover;border:2px solid var(--pr1);" />
          <div v-else style="width:44px;height:44px;border-radius:50%;background:var(--pr1);color:#fff;display:flex;align-items:center;justify-content:center;font-weight:700;font-size:18px;">
            {{ devotee.avatar_initials }}
          </div>
          <div>
            <div style="font-size:11px;color:var(--tl);">🙏 Jai Gau Mata</div>
            <h2 style="font-family:'Playfair Display',serif;font-size:20px;">Namaste, <span style="color:var(--pr);">{{ firstName }} Ji</span>!</h2>
          </div>
        </div>
        <div style="display:flex;gap:8px;">
          <button class="btn-ghost" style="padding:8px 10px;color:var(--er);border-color:rgba(239,83,80,.2);display:flex;align-items:center;justify-content:center;" @click="handleLogout" title="Sign Out">
            <LogOut style="width:18px;height:18px;" />
          </button>
        </div>
      </div>

      <!-- Main Layout Grid -->
      <div class="devo-profile-grid">

        <!-- Left Column: Hero, Details, Automations -->
        <div style="display:flex;flex-direction:column;gap:18px;">
          <!-- Hero Card -->
          <div class="sk-leather fu" style="position:relative;padding:22px;text-align:center;border-radius:20px;overflow:hidden;">
            <!-- Transparent Background Watermark inside the card -->
            <img src="/gomata.png" alt="Gomata Watermark" style="position:absolute;top:50%;left:52%;transform:translate(-50%,-50%);width:min(400px, 90%);height:auto;opacity:0.15;mix-blend-mode:multiply;pointer-events:none;z-index:1;filter:grayscale(10%);" />
            
            <div style="position:relative;z-index:2;">
                <div style="display:flex;justify-content:center;margin-bottom:12px;">
                  <picture>
                    <source srcset="/logo.webp" type="image/webp">
                    <img src="/logo1.png" alt="Krishan Balram Gaushala Logo" width="100" height="100" class="logo-img" style="width:100px;height:100px;" loading="eager" decoding="async" />
                  </picture>
                </div>
              <div style="font-family:'Playfair Display',serif;font-size:18px;font-weight:700;">Krishan Balram Gaushala</div>
              <div style="font-size:10px;color:var(--tl);margin:3px 0 12px;line-height:1.4;">Singla Enclave, Village Dullon Khurd, Pakhowal Road, Ludhiana<br>Member since {{ memberSince }}</div>
              <div style="display:inline-flex;align-items:center;gap:7px;background:rgba(46,125,50,.12);border:1px solid rgba(67,160,71,.3);border-radius:20px;padding:5px 16px;">
                <span style="width:8px;height:8px;background:var(--ok1);border-radius:50%;box-shadow:0 0 7px var(--ok1);"/>
                <span style="color:var(--ok1);font-size:12px;font-weight:700;">Active Devotee</span>
              </div>
            </div>
          </div>

          <!-- Details -->
          <div class="nr fu2" style="padding:16px;">
            <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:8px;">
              <div class="slbl" style="margin:0;">My Details</div>
              <button class="btn-ghost" style="padding:5px 12px;font-size:10px;margin-left:auto;display:inline-flex;align-items:center;gap:4px;" @click="editing = !editing">
                <span v-if="editing">Cancel</span>
                <span v-else style="display:inline-flex;align-items:center;gap:4px;"><Pencil style="width:11px;height:11px;" /> Edit</span>
              </button>
            </div>

            <!-- Viewing details -->
            <div v-if="!editing">
              <div class="a-row" v-if="devotee.dob">
                <div style="display:flex;gap:12px;align-items:center;">
                  <div style="width:36px;height:36px;border-radius:50%;background:rgba(184,78,0,0.08);color:var(--pr);display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                    <Cake style="width:18px;height:18px;" />
                  </div>
                  <div>
                    <div style="font-size:10px;color:var(--td);">Birthday</div>
                    <div style="font-size:14px;font-weight:700;">{{ formatDate(devotee.dob) }}</div>
                  </div>
                </div>
                <div style="font-size:11px;color:var(--td);text-align:right;">Yearly blessings<br>from Gaushala</div>
              </div>
              <div class="a-row" v-if="devotee.anniversary">
                <div style="display:flex;gap:12px;align-items:center;">
                  <div style="width:36px;height:36px;border-radius:50%;background:rgba(225,48,108,0.08);color:#e1306c;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                    <Heart style="width:18px;height:18px;" />
                  </div>
                  <div>
                    <div style="font-size:10px;color:var(--td);">Anniversary</div>
                    <div style="font-size:14px;font-weight:700;">{{ formatDate(devotee.anniversary) }}</div>
                  </div>
                </div>
                <div style="font-size:11px;color:var(--td);text-align:right;">Yearly blessings<br>sent on WhatsApp</div>
              </div>
              <div style="display:flex;gap:12px;align-items:center;padding:10px 0;">
                <div style="width:36px;height:36px;border-radius:50%;background:rgba(37,211,102,0.08);color:var(--wa);display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                  <Smartphone style="width:18px;height:18px;" />
                </div>
                <div>
                  <div style="font-size:10px;color:var(--td);">WhatsApp</div>
                  <div style="font-size:14px;font-weight:700;">+91 {{ devotee.whatsapp }}</div>
                </div>
              </div>
            </div>

            <!-- Editing details form -->
            <div v-else style="margin-top:12px;display:flex;flex-direction:column;gap:12px;">
              <div>
                <div style="font-size:11px;font-weight:600;color:var(--tl);margin-bottom:4px;">Full Name</div>
                <input v-model="form.name" class="n-inp" type="text" placeholder="Full Name">
              </div>
              <div style="display:grid;grid-template-columns:1fr 1fr;gap:10px;">
                <div>
                  <div style="font-size:11px;font-weight:600;color:var(--tl);margin-bottom:4px;">Date of Birth</div>
                  <input v-model="form.dob" class="n-inp" type="text" placeholder="DD-MM-YYYY">
                  <div v-if="errors.dob || $page.props.errors.dob" style="font-size:10px;color:var(--er);margin-top:4px;">{{ errors.dob || $page.props.errors.dob }}</div>
                </div>
                <div>
                  <div style="font-size:11px;font-weight:600;color:var(--tl);margin-bottom:4px;">Anniversary Date</div>
                  <input v-model="form.anniversary" class="n-inp" type="text" placeholder="DD-MM-YYYY">
                  <div v-if="errors.anniversary || $page.props.errors.anniversary" style="font-size:10px;color:var(--er);margin-top:4px;">{{ errors.anniversary || $page.props.errors.anniversary }}</div>
                </div>
              </div>
              <div style="margin-top:6px;">
                <div style="font-size:11px;font-weight:600;color:var(--tl);margin-bottom:4px;">Profile Photo</div>
                <input type="file" @change="handlePhotoUpload" accept="image/*" class="n-inp file-upload-input" style="padding:10px;">
                <div v-if="photoError" style="font-size:10px;color:var(--er);margin-top:4px;">{{ photoError }}</div>
                <div v-if="$page.props.errors.photo" style="font-size:10px;color:var(--er);margin-top:4px;">{{ $page.props.errors.photo }}</div>
              </div>
              <div v-if="$page.props.errors.name" style="font-size:10px;color:var(--er);margin-top:4px;">{{ $page.props.errors.name }}</div>
              <div style="margin-top:6px;">
                <button class="btn-saffron" style="width:100%;padding:10px;font-size:13px;" :disabled="saving" @click="saveProfile">
                  <span v-if="saving" class="spin"/>
                  <span v-else>✓ Save Changes</span>
                </button>
              </div>
            </div>
          </div>

          <!-- What you receive -->
          <!-- <div class="nr fu3" style="padding:16px;">
            <div class="slbl">My Automations</div>
            <div class="a-row">
              <div style="width:32px;height:32px;border-radius:50%;background:rgba(37,211,102,0.08);color:var(--wa);display:flex;align-items:center;justify-content:center;flex-shrink:0;margin-right:12px;">
                <MessageCircle style="width:16px;height:16px;" />
              </div>
              <div style="flex:1;">
                <div style="font-size:13px;font-weight:600;">WhatsApp Birthday Wish</div>
                <div style="font-size:11px;color:var(--tl);margin-top:1px;">Personal message from Gaushala on your birthday</div>
              </div>
            </div>
            <div class="a-row" v-if="devotee.anniversary">
              <div style="width:32px;height:32px;border-radius:50%;background:rgba(240,138,48,0.08);color:var(--pr2);display:flex;align-items:center;justify-content:center;flex-shrink:0;margin-right:12px;">
                <Heart style="width:16px;height:16px;" />
              </div>
              <div style="flex:1;">
                <div style="font-size:13px;font-weight:600;">WhatsApp Anniversary Wish</div>
                <div style="font-size:11px;color:var(--tl);margin-top:1px;">Anniversary blessings for you and your family</div>
              </div>
            </div>
            <div style="display:flex;align-items:center;padding:10px 0;">
              <div style="width:32px;height:32px;border-radius:50%;background:rgba(24,119,242,0.08);color:var(--fb);display:flex;align-items:center;justify-content:center;flex-shrink:0;margin-right:12px;">
                <svg viewBox="0 0 24 24" style="width:16px;height:16px;" fill="currentColor">
                  <path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/>
                </svg>
              </div>
              <div style="flex:1;">
                <div style="font-size:13px;font-weight:600;">Facebook Page Blessing</div>
                <div style="font-size:11px;color:var(--tl);margin-top:1px;">Featured in Gaushala's Facebook birthday post</div>
              </div>
            </div>
          </div> -->
        </div>

        <!-- Right Column: Events, Cows Directory -->
        <div style="display:flex;flex-direction:column;gap:18px;">
          <!-- Upcoming Events Card -->
          <div class="nr fu4" style="padding:16px;">
            <div class="slbl" style="margin-bottom:12px;">Upcoming Events & Seva</div>
            <div v-if="events && events.length" class="events-grid-layout">
              <div v-for="ev in events" :key="ev.id" class="ev-card" style="margin-bottom:0;border-left-color:var(--pr1);box-shadow:none;border-radius:12px;background:var(--bg1);">
                <div class="ev-ico">{{ ev.icon }}</div>
                <div style="flex:1;">
                  <h4 style="font-size:13px;font-weight:700;color:var(--tx);margin:0;">{{ ev.title }}</h4>
                  <p style="font-size:11px;color:var(--tl);line-height:1.4;margin-top:2px;">{{ ev.description }}</p>
                  <div style="display:flex;gap:12px;font-size:10px;color:var(--td);margin-top:6px;font-weight:600;align-items:center;">
                    <span style="display:inline-flex;align-items:center;gap:4px;"><Calendar style="width:12px;height:12px;" /> {{ ev.date_label }}</span>
                    <span style="display:inline-flex;align-items:center;gap:4px;"><Clock style="width:12px;height:12px;" /> {{ ev.time_label }}</span>
                  </div>
                </div>
              </div>
            </div>
            <div v-else style="text-align:center;padding:24px 15px;font-size:11px;color:var(--tl);">
              No upcoming events listed for now. Check back later!
            </div>
          </div>

          <!-- Gau Seva Blessing Card -->
          <div class="nr fu3" style="padding:20px; border-radius:20px; background: linear-gradient(135deg, rgba(230, 120, 23, 0.04) 0%, rgba(240, 180, 60, 0.08) 100%) !important; border: 1px solid rgba(180,128,40,0.15) !important; position:relative; overflow:hidden;">
            <!-- Subtle design elements -->
            <div style="display:flex; gap:14px; align-items:flex-start; position:relative; z-index:2;">
              <div style="width:38px;height:38px;border-radius:50%;background:rgba(184,78,0,0.08);color:var(--pr1);display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                <Sparkles style="width:18px;height:18px;" />
              </div>
              <div style="flex:1;">
                <div style="font-family:'Playfair Display',serif; font-size:15px; font-weight:700; color:var(--pr); margin-bottom:6px;">Gau Seva Blessing</div>
                <div style="font-size:13px; font-style:italic; color:var(--tx); line-height:1.6; font-family:'Playfair Display',serif; margin-bottom:6px;">
                  "गावो मे चाग्रतो यान्तु गावो मे सन्तु पृष्ठतः।<br>गावो मे हृदये सन्तु गवां मध्ये वसाम्यहम्॥"
                </div>
                <div style="font-size:11px; color:var(--tl); line-height:1.5;">
                  May Gau Mata be in front of me, behind me, and in my heart. Serving cows brings peace, health, and spiritual prosperity to our home and family.
                </div>
              </div>
            </div>
          </div>
        </div>

      </div>

    </div>
  </div>
</template>

<script setup>
import { ref, computed } from 'vue';
import { router } from '@inertiajs/vue3';
import { useDevoteeStore } from '@/Stores/devotee';
import { LogOut, Pencil, Cake, Heart, Smartphone, MessageCircle, Calendar, Clock, Sparkles } from '@lucide/vue';
import { compressImage } from '@/Utils/image';

const store = useDevoteeStore();

const props = defineProps({
  devotee: { type: Object, required: true },
  events: { type: Array, default: () => [] },
});

const editing = ref(false);
const saving = ref(false);

function formatForInput(dateStr) {
  if (!dateStr) return '';
  const d = new Date(dateStr);
  if (isNaN(d.getTime())) return dateStr;
  const day = String(d.getDate()).padStart(2, '0');
  const month = String(d.getMonth() + 1).padStart(2, '0');
  const year = d.getFullYear();
  return `${day}-${month}-${year}`;
}

const form = ref({
  name: props.devotee.name || '',
  dob: formatForInput(props.devotee.dob),
  anniversary: formatForInput(props.devotee.anniversary),
  fb_consent: true,
  photo: null,
});

const photoError = ref('');

async function handlePhotoUpload(e) {
  photoError.value = '';
  const file = e.target.files[0];
  if (file) {
    if (file.size > 15 * 1024 * 1024) {
      photoError.value = 'Photo size must be less than 15MB.';
      e.target.value = '';
      form.value.photo = null;
      return;
    }
    try {
      const compressed = await compressImage(file, 400, 0.8);
      form.value.photo = compressed;
    } catch (err) {
      console.error('Image compression failed, using original file', err);
      form.value.photo = file;
    }
  }
}

const firstName = computed(() => props.devotee.name?.split(' ')[0] || 'Devotee');
const memberSince = computed(() => {
  if (!props.devotee.joined_at) return 'Recently';
  const d = new Date(props.devotee.joined_at);
  return d.toLocaleDateString('en-IN', { month: 'short', year: 'numeric' });
});

function formatDate(dateStr) {
  if (!dateStr) return '';
  try {
    const d = new Date(dateStr);
    if (isNaN(d.getTime())) return dateStr;
    return d.toLocaleDateString('en-IN', { day: 'numeric', month: 'long' });
  } catch (e) {
    return dateStr;
  }
}

const errors = ref({});

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

function formatBeforeSubmit(val) {
  if (!val) return val;
  val = String(val).trim().replace(/[\/\.\s]/g, '-');
  // If user entered exactly 8 digits (DDMMYYYY), convert to DD-MM-YYYY
  const match = val.match(/^(\d{2})(\d{2})(\d{4})$/);
  if (match) {
    return `${match[1]}-${match[2]}-${match[3]}`;
  }
  return val;
}

function saveProfile() {
  errors.value = {};
  
  const dobVal = formatBeforeSubmit(form.value.dob);
  const anniversaryVal = formatBeforeSubmit(form.value.anniversary);
  
  if (!dobVal) {
    errors.value.dob = 'Date of birth is required.';
  } else if (!validateDateStr(dobVal)) {
    errors.value.dob = 'Enter Date of Birth in DD-MM-YYYY format (e.g. 15-08-1995).';
  }
  
  if (anniversaryVal && !validateDateStr(anniversaryVal)) {
    errors.value.anniversary = 'Enter Anniversary Date in DD-MM-YYYY format (e.g. 24-11-2018).';
  }
  
  if (Object.keys(errors.value).length > 0) {
    return;
  }
  
  saving.value = true;
  
  const payload = {
    ...form.value,
    dob: parseUIDate(dobVal),
    anniversary: parseUIDate(anniversaryVal),
    _method: 'put' // Required for file uploads via inertia putting
  };

  router.post(route('devotee.profile.update'), payload, {
    onSuccess: () => {
      editing.value = false;
      saving.value = false;
      // Re-initialize form to get freshly updated server formatting
      form.value.name = props.devotee.name || '';
      form.value.dob = formatForInput(props.devotee.dob);
      form.value.anniversary = formatForInput(props.devotee.anniversary);
      form.value.photo = null; // Clear the file input reference
    },
    onError: () => {
      saving.value = false;
    }
  });
}

function back() { router.visit(route('choose')); }

function handleLogout() {
  store.clear();
  router.post(route('devotee.logout'));
}
</script>

<style scoped>
.sk-leather, :deep(.sk-leather) {
  background: linear-gradient(150deg, rgba(255,255,255,0.85), rgba(247,244,234,0.85) 50%, rgba(239,230,208,0.85)) !important;
  backdrop-filter: blur(12px) !important;
  -webkit-backdrop-filter: blur(12px) !important;
  border: 1px solid rgba(255,255,255,0.5) !important;
}
.nr, :deep(.nr) {
  background: rgba(255, 255, 255, 0.8) !important;
  backdrop-filter: blur(12px) !important;
  -webkit-backdrop-filter: blur(12px) !important;
  border: 1px solid rgba(255,255,255,0.5) !important;
}
</style>
