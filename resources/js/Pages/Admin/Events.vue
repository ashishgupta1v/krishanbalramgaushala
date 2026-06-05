<template>
  <AdminLayout>
    <div class="page">
      <!-- Title Bar -->
      <div style="display:flex;justify-content:space-between;align-items:flex-start;flex-wrap:wrap;gap:12px;margin-bottom:18px;">
        <div style="min-width:260px;flex:1;">
          <h2 style="font-family:'Playfair Display',serif;font-size:20px;line-height:1.25;">Interactive Event & Calendar Manager</h2>
          <p style="color:var(--tl);font-size:12px;margin-top:4px;line-height:1.4;">Manage upcoming festivals, prayers, meetings, and veterinary checks dynamically</p>
        </div>
        <button class="btn-saffron" @click="openCreateModal" style="padding:10px 18px;font-size:12px;white-space:nowrap;flex-shrink:0;height:fit-content;margin-top:2px;">➕ Add Event</button>
      </div>

      <!-- Events Grid / Table -->
      <div class="desktop-grid-2" style="align-items:start;gap:20px;">
        <!-- Left Side: List of current events -->
        <div style="display:flex;flex-direction:column;gap:12px;">
          <div class="slbl" style="margin-top:0;">📋 Scheduled Events ({{ events.length }})</div>
          
          <div v-for="ev in events" :key="ev.id" class="nr-sm sk-leather" style="padding:14px 16px;border-radius:16px;display:flex;gap:14px;align-items:start;">
            <div class="ev-ico" :style="`background:${evColor(ev.type).bg};border:1px solid ${evColor(ev.type).bdr}44;width:44px;height:44px;border-radius:12px;display:flex;align-items:center;justify-content:center;font-size:20px;flex-shrink:0;`">
              <span>{{ ev.icon }}</span>
            </div>
            
            <div style="flex:1;min-width:0;">
              <div style="display:flex;justify-content:space-between;align-items:start;gap:6px;">
                <h4 style="font-size:14px;font-weight:700;color:var(--tx);">{{ ev.title }}</h4>
                <div style="display:flex;gap:4px;">
                  <button @click="openEditModal(ev)" style="background:none;border:none;cursor:pointer;font-size:13px;" title="Edit">✏️</button>
                  <button @click="deleteEvent(ev.id)" style="background:none;border:none;cursor:pointer;font-size:13px;" title="Delete">🗑️</button>
                </div>
              </div>
              <p style="font-size:11px;color:var(--tl);margin-top:2px;line-height:1.4;">{{ ev.description }}</p>
              
              <div style="display:flex;gap:8px;flex-wrap:wrap;margin-top:8px;">
                <span :style="`font-size:10px;background:${evColor(ev.type).bg};color:${evColor(ev.type).dot};border:1px solid ${evColor(ev.type).bdr}44;border-radius:20px;padding:2px 9px;font-weight:700;text-transform:capitalize;`">{{ ev.type }}</span>
                <span style="font-size:10px;background:rgba(180,128,40,.1);color:var(--tl);border-radius:20px;padding:2px 9px;font-weight:600;">⏰ {{ fmtDT(ev.scheduled_at) }}</span>
                <span v-if="ev.time_label" style="font-size:10px;background:rgba(180,128,40,.1);color:var(--tl);border-radius:20px;padding:2px 9px;font-weight:600;">🏷️ {{ ev.time_label }}</span>
                <span v-if="ev.is_recurring" style="font-size:10px;background:rgba(46,125,50,.1);color:var(--ok);border-radius:20px;padding:2px 9px;font-weight:700;">🔄 Recurring</span>
              </div>
            </div>
          </div>

          <div v-if="events.length === 0" class="nr" style="padding:40px;text-align:center;color:var(--td);font-size:13px;">
            No upcoming events have been dynamically scheduled. Click "Add Event" to create one.
          </div>
        </div>

        <!-- Right Side: Fast Quick-Fact & Guidelines panel -->
        <div class="nr sk-leather" style="padding:18px 20px;border-radius:20px;">
          <h3 style="font-family:'Playfair Display',serif;font-size:16px;font-weight:700;margin-bottom:10px;">📅 Gaushala Calendar Rules</h3>
          <p style="font-size:12px;color:var(--tl);line-height:1.6;margin-bottom:12px;">
            Events scheduled here will automatically appear on the Devotee Registry and personal profiles.
          </p>
          <div style="background:rgba(180,128,40,.08);border-radius:12px;padding:12px;margin-bottom:12px;">
            <div style="font-size:11px;font-weight:700;color:var(--tm);margin-bottom:4px;">✨ Active Types</div>
            <ul style="font-size:11px;color:var(--tx);padding-left:14px;display:flex;flex-direction:column;gap:5px;">
              <li><strong>festival:</strong> Grand celebrations like Gopashtami or Janmashtami.</li>
              <li><strong>daily:</strong> Morning or evening aarti bells and daily feeds.</li>
              <li><strong>weekly:</strong> Weekly volunteering, cleaning, or prayers.</li>
              <li><strong>health:</strong> Veterinary checkups and camp schedules.</li>
              <li><strong>meeting:</strong> Board reviews and operational assemblies.</li>
            </ul>
          </div>
          <p style="font-size:11px;color:var(--td);line-height:1.4;">
            * Emojis should match the theme, for instance: 🙏 for festivals, 🪔 for daily prayers, 🩺 for health checks, 🤝 for weekly volunteer jobs.
          </p>
        </div>
      </div>

      <!-- MODAL DIALOG (CREATE & EDIT FORM) -->
      <div v-if="modalOpen" style="position:fixed;inset:0;background:rgba(40,20,5,.5);backdrop-filter:blur(3px);display:flex;align-items:center;justify-content:center;z-index:9999;padding:16px;">
        <div class="nr sk-leather" style="width:100%;max-width:480px;border-radius:20px;padding:24px;box-shadow:0 12px 30px rgba(0,0,0,.25);">
          
          <h3 style="font-family:'Playfair Display',serif;font-size:18px;font-weight:700;margin-bottom:14px;color:var(--tx);">
            {{ isEdit ? '📝 Edit Scheduled Event' : '✨ Add Upcoming Event' }}
          </h3>

          <form @submit.prevent="submitForm" style="display:flex;flex-direction:column;gap:12px;">
            <!-- Title -->
            <div>
              <label style="font-size:11px;font-weight:700;color:var(--tl);display:block;margin-bottom:4px;">Event Title *</label>
              <input v-model="form.title" class="n-inp" type="text" placeholder="e.g. Gopashtami Special Gau Puja" required>
            </div>

            <!-- Description -->
            <div>
              <label style="font-size:11px;font-weight:700;color:var(--tl);display:block;margin-bottom:4px;">Description *</label>
              <textarea v-model="form.description" class="n-ta" rows="3" placeholder="Explain the activities and invite details…" required></textarea>
            </div>

            <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px;">
              <!-- Icon/Emoji -->
              <div>
                <label style="font-size:11px;font-weight:700;color:var(--tl);display:block;margin-bottom:4px;">Icon Emoji *</label>
                <input v-model="form.icon" class="n-inp" type="text" placeholder="e.g. 🙏" required style="font-size:16px;text-align:center;">
              </div>

              <!-- Type Select -->
              <div>
                <label style="font-size:11px;font-weight:700;color:var(--tl);display:block;margin-bottom:4px;">Event Type *</label>
                <select v-model="form.type" style="width:100%;padding:13px 15px;border:none;border-radius:12px;background:var(--bg);box-shadow:inset 3px 3px 7px var(--sd),inset -3px -3px 7px var(--sl);color:var(--tx);font-family:'Poppins',sans-serif;font-size:13px;outline:none;" required>
                  <option value="festival">Festival</option>
                  <option value="daily">Daily Bell</option>
                  <option value="weekly">Weekly Routine</option>
                  <option value="health">Vet / Health</option>
                  <option value="meeting">Meeting</option>
                  <option value="other">Other Event</option>
                </select>
              </div>
            </div>

            <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px;">
              <!-- Scheduled At -->
              <div>
                <label style="font-size:11px;font-weight:700;color:var(--tl);display:block;margin-bottom:4px;">Scheduled At *</label>
                <input v-model="form.scheduled_at" class="n-inp" type="datetime-local" required>
              </div>

              <!-- Time/Period Label -->
              <div>
                <label style="font-size:11px;font-weight:700;color:var(--tl);display:block;margin-bottom:4px;">Display Time Label</label>
                <input v-model="form.time_label" class="n-inp" type="text" placeholder="e.g. 7:00 AM or Every Sunday">
              </div>
            </div>

            <!-- Recurring Checkbox -->
            <label style="display:flex;align-items:center;gap:8px;font-size:12px;font-weight:600;color:var(--tx);margin-top:4px;cursor:pointer;">
              <input v-model="form.is_recurring" type="checkbox" style="width:16px;height:16px;accent-color:var(--pr);">
              <span>This is a recurring event</span>
            </label>

            <!-- Buttons -->
            <div style="display:flex;justify-content:end;gap:10px;margin-top:10px;border-top:1px solid rgba(180,128,40,.15);padding-top:14px;">
              <button type="button" class="btn-ghost" @click="modalOpen = false" style="padding:8px 16px;font-size:12px;">Cancel</button>
              <button type="submit" class="btn-saffron" style="padding:8px 20px;font-size:12px;">Save Event</button>
            </div>

          </form>
        </div>
      </div>

    </div>
  </AdminLayout>
</template>

<script setup>
import { ref } from 'vue';
import { router } from '@inertiajs/vue3';
import AdminLayout from './Layout.vue';

const props = defineProps({
  events:    { type: Array,  default: () => [] },
  activeTab: { type: String, default: 'events' },
});

const modalOpen = ref(false);
const isEdit    = ref(false);
const editId    = ref(null);

const form = ref({
  title:        '',
  description:  '',
  icon:         '🙏',
  type:         'festival',
  scheduled_at: '',
  time_label:   '',
  is_recurring: false,
});

// Event category styling configs
const EV_COLS = {
  festival: { bdr: '#e65100', bg: 'rgba(230,81,0,.12)', dot: '#e65100' },
  daily:    { bdr: 'var(--gd1)', bg: 'rgba(200,150,8,.1)', dot: 'var(--gd1)' },
  weekly:   { bdr: 'var(--wa)', bg: 'rgba(37,211,102,.1)', dot: 'var(--wa)' },
  upcoming: { bdr: 'var(--pr1)', bg: 'rgba(184,78,0,.1)', dot: 'var(--pr1)' },
  meeting:  { bdr: 'var(--fb)', bg: 'rgba(24,119,242,.1)', dot: 'var(--fb)' },
  health:   { bdr: '#7b1fa2', bg: 'rgba(123,31,162,.1)', dot: '#7b1fa2' },
};
function evColor(type) { return EV_COLS[type] || EV_COLS.upcoming; }

function openCreateModal() {
  isEdit.value    = false;
  editId.value    = null;
  form.value = {
    title:        '',
    description:  '',
    icon:         '🙏',
    type:         'festival',
    scheduled_at: '',
    time_label:   '',
    is_recurring: false,
  };
  modalOpen.value = true;
}

function openEditModal(event) {
  isEdit.value    = true;
  editId.value    = event.id;
  form.value = {
    title:        event.title,
    description:  event.description,
    icon:         event.icon,
    type:         event.type,
    scheduled_at: event.scheduled_at,
    time_label:   event.time_label || '',
    is_recurring: !!event.is_recurring,
  };
  modalOpen.value = true;
}

function submitForm() {
  if (isEdit.value) {
    router.put(route('admin.events.update', editId.value), form.value, {
      onSuccess: () => {
        modalOpen.value = false;
      }
    });
  } else {
    router.post(route('admin.events.store'), form.value, {
      onSuccess: () => {
        modalOpen.value = false;
      }
    });
  }
}

function deleteEvent(id) {
  if (!confirm('Are you sure you want to delete this event?')) return;
  router.delete(route('admin.events.destroy', id));
}

function fmtDT(d) {
  if (!d) return '';
  const dt = new Date(d);
  return dt.toLocaleString('en-IN', { day: 'numeric', month: 'short', hour: '2-digit', minute: '2-digit' });
}
</script>
