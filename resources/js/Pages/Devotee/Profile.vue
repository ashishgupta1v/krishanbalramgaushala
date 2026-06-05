<template>
  <div style="position:fixed;inset:0;overflow-y:auto;overflow-x:hidden;background:var(--bg);background-image:radial-gradient(ellipse 65% 55% at 18% 18%,rgba(210,148,8,.17) 0%,transparent 55%),radial-gradient(ellipse 55% 60% at 82% 82%,rgba(180,96,0,.12) 0%,transparent 55%);">

    <div class="devo-profile-container page" style="position:relative;z-index:1;padding:24px 20px 40px;margin:0 auto;">

      <!-- Top Bar -->
      <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:22px;">
        <div>
          <div style="font-size:11px;color:var(--tl);">🙏 Jai Gau Mata</div>
          <h2 style="font-family:'Playfair Display',serif;font-size:20px;">Namaste, <span style="color:var(--pr);">{{ firstName }} Ji</span>!</h2>
        </div>
        <div style="display:flex;gap:8px;">
          <button class="btn-ghost" style="padding:7px 12px;font-size:11px;color:var(--er);border-color:rgba(239,83,80,.2);" @click="handleLogout">Sign Out 🚪</button>
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
                <div style="width: 100px; height: 100px; border-radius: 50%; display: flex; align-items: center; justify-content: center; background: linear-gradient(145deg, var(--bg1), var(--bg)); box-shadow: 4px 4px 12px var(--sd), -4px -4px 12px var(--sl); padding: 4px; border: 1px solid rgba(255,255,255,0.7);">
                  <img src="/logo.jpg" alt="Krishan Balram Gaushala Logo" style="width: 100%; height: 100%; border-radius: 50%; object-fit: cover;" />
                </div>
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
              <button class="btn-ghost" style="padding:5px 12px;font-size:10px;margin-left:auto;" @click="editing = !editing">
                {{ editing ? 'Cancel' : '📝 Edit' }}
              </button>
            </div>

            <!-- Viewing details -->
            <div v-if="!editing">
              <div class="a-row" v-if="devotee.dob">
                <div style="display:flex;gap:10px;align-items:center;">
                  <span style="font-size:21px;">🎂</span>
                  <div>
                    <div style="font-size:10px;color:var(--td);">Birthday</div>
                    <div style="font-size:14px;font-weight:700;">{{ formatDate(devotee.dob) }}</div>
                  </div>
                </div>
                <div style="font-size:11px;color:var(--td);text-align:right;">Yearly blessings<br>from Gaushala</div>
              </div>
              <div class="a-row" v-if="devotee.anniversary">
                <div style="display:flex;gap:10px;align-items:center;">
                  <span style="font-size:21px;">💐</span>
                  <div>
                    <div style="font-size:10px;color:var(--td);">Anniversary</div>
                    <div style="font-size:14px;font-weight:700;">{{ formatDate(devotee.anniversary) }}</div>
                  </div>
                </div>
                <div style="font-size:11px;color:var(--td);text-align:right;">Yearly blessings<br>sent on WhatsApp</div>
              </div>
              <div style="display:flex;gap:10px;align-items:center;padding:10px 0;">
                <span style="font-size:21px;">📲</span>
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
                  <input v-model="form.dob" class="n-inp" type="date">
                </div>
                <div>
                  <div style="font-size:11px;font-weight:600;color:var(--tl);margin-bottom:4px;">Anniversary Date</div>
                  <input v-model="form.anniversary" class="n-inp" type="date">
                </div>
              </div>
              <div style="margin-top:6px;">
                <button class="btn-saffron" style="width:100%;padding:10px;font-size:13px;" :disabled="saving" @click="saveProfile">
                  <span v-if="saving" class="spin"/>
                  <span v-else>✓ Save Changes</span>
                </button>
              </div>
            </div>
          </div>

          <!-- What you receive -->
          <div class="nr fu3" style="padding:16px;">
            <div class="slbl">My Automations</div>
            <div class="a-row">
              <span style="font-size:20px;">📲</span>
              <div style="flex:1;margin-left:10px;">
                <div style="font-size:13px;font-weight:600;">WhatsApp Birthday Wish</div>
                <div style="font-size:11px;color:var(--tl);margin-top:1px;">Personal message from Gaushala on your birthday</div>
              </div>
            </div>
            <div class="a-row" v-if="devotee.anniversary">
              <span style="font-size:20px;">💐</span>
              <div style="flex:1;margin-left:10px;">
                <div style="font-size:13px;font-weight:600;">WhatsApp Anniversary Wish</div>
                <div style="font-size:11px;color:var(--tl);margin-top:1px;">Anniversary blessings for you and your family</div>
              </div>
            </div>
            <div style="display:flex;align-items:center;gap:10px;padding:10px 0;">
              <span style="font-size:20px;">📘</span>
              <div style="flex:1;margin-left:10px;">
                <div style="font-size:13px;font-weight:600;">Facebook Page Blessing</div>
                <div style="font-size:11px;color:var(--tl);margin-top:1px;">Featured in Gaushala's Facebook birthday post</div>
              </div>
            </div>
          </div>
        </div>

        <!-- Right Column: Events, Cows Directory -->
        <div style="display:flex;flex-direction:column;gap:18px;">
          <!-- Upcoming Events Card -->
          <div class="nr fu4" style="padding:16px;">
            <div class="slbl">Upcoming Events & Seva</div>
            <div v-if="events && events.length" style="display:flex;flex-direction:column;gap:10px;margin-top:5px;">
              <div v-for="ev in events" :key="ev.id" class="ev-card" style="margin-bottom:0;border-left-color:var(--pr1);box-shadow:none;border-radius:12px;background:var(--bg1);">
                <div class="ev-ico">{{ ev.icon }}</div>
                <div style="flex:1;">
                  <h4 style="font-size:13px;font-weight:700;color:var(--tx);margin:0;">{{ ev.title }}</h4>
                  <p style="font-size:11px;color:var(--tl);line-height:1.4;margin-top:2px;">{{ ev.description }}</p>
                  <div style="display:flex;gap:12px;font-size:10px;color:var(--td);margin-top:6px;font-weight:600;">
                    <span>📅 {{ ev.date_label }}</span>
                    <span>⏰ {{ ev.time_label }}</span>
                  </div>
                </div>
              </div>
            </div>
            <div v-else style="text-align:center;padding:15px;font-size:11px;color:var(--tl);">
              No upcoming events listed for now. Check back later!
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

const store = useDevoteeStore();

const props = defineProps({
  devotee: { type: Object, required: true },
  events: { type: Array, default: () => [] },
});

const editing = ref(false);
const saving = ref(false);
const form = ref({
  name: props.devotee.name || '',
  dob: props.devotee.dob ? props.devotee.dob.substring(0, 10) : '',
  anniversary: props.devotee.anniversary ? props.devotee.anniversary.substring(0, 10) : '',
  fb_consent: true,
});

const firstName = computed(() => props.devotee.name?.split(' ')[0] || 'Devotee');
const memberSince = computed(() => {
  if (!props.devotee.joined_at) return 'Recently';
  const d = new Date(props.devotee.joined_at);
  return d.toLocaleDateString('en-IN', { month: 'short', year: 'numeric' });
});

function formatDate(dateStr) {
  if (!dateStr) return '';
  const d = new Date(dateStr);
  return d.toLocaleDateString('en-IN', { day: 'numeric', month: 'long' });
}

function saveProfile() {
  saving.value = true;
  router.put(route('devotee.profile.update'), form.value, {
    onSuccess: () => {
      editing.value = false;
      saving.value = false;
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

