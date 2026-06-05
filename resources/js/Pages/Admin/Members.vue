<template>
  <AdminLayout>
    <div class="page">
      <!-- Header -->
      <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:14px;">
        <div>
          <h2 style="font-family:'Playfair Display',serif;font-size:20px;">Members</h2>
          <p style="color:var(--tl);font-size:12px;margin-top:1px;">{{ filteredMembers.length }} of {{ members.length }} devotees</p>
        </div>
        <a :href="route('admin.members.export', { milestone: selectedMilestone, period: selectedPeriod })" class="btn-ghost" style="padding:7px 14px;font-size:11px;text-decoration:none;font-weight:600;display:flex;align-items:center;gap:6px;">📥 Export CSV</a>
      </div>

      <!-- Search & Milestone Filters -->
      <div style="display:flex;flex-wrap:wrap;gap:12px;margin-bottom:16px;">
        <div style="position:relative;flex:1;min-width:240px;">
          <span style="position:absolute;left:14px;top:50%;transform:translateY(-50%);font-size:14px;pointer-events:none;">🔍</span>
          <input v-model="search" class="n-inp" type="text" placeholder="Search name or number…" style="padding-left:40px;">
        </div>
        
        <!-- Milestone Type Selector -->
        <div style="display:flex;align-items:center;gap:6px;">
          <span style="font-size:11px;font-weight:700;color:var(--tl);white-space:nowrap;">Milestone:</span>
          <select v-model="selectedMilestone" @change="applyFilters" style="padding:10px 14px;border:none;border-radius:12px;background:var(--bg);box-shadow:inset 2px 2px 5px var(--sd),inset -2px -2px 5px var(--sl);color:var(--tx);font-family:'Poppins',sans-serif;font-size:12px;outline:none;cursor:pointer;">
            <option value="all">🎉 All Devotees</option>
            <option value="birthday">🎂 Birthdays</option>
            <option value="anniversary">💐 Anniversaries</option>
            <option value="any">💝 Any Milestone</option>
          </select>
        </div>

        <!-- Period Selector -->
        <div style="display:flex;align-items:center;gap:6px;">
          <span style="font-size:11px;font-weight:700;color:var(--tl);white-space:nowrap;">Period:</span>
          <select v-model="selectedPeriod" @change="applyFilters" style="padding:10px 14px;border:none;border-radius:12px;background:var(--bg);box-shadow:inset 2px 2px 5px var(--sd),inset -2px -2px 5px var(--sl);color:var(--tx);font-family:'Poppins',sans-serif;font-size:12px;outline:none;cursor:pointer;">
            <option value="all">📅 Any Time</option>
            <option value="today">☀️ Celebrating Today</option>
            <option value="next_7_days">🔮 Next 7 Days</option>
            <option value="next_week">🗓️ Next Calendar Week</option>
          </select>
        </div>
      </div>

      <!-- City chips -->
      <div style="display:flex;gap:8px;overflow-x:auto;padding-bottom:4px;margin-bottom:16px;">
        <button
          v-for="city in cities"
          :key="city"
          class="chip"
          :class="{ on: filterCity === city }"
          @click="filterCity = city"
        >{{ city === 'all' ? 'All Cities' : city }}</button>
      </div>

      <!-- Member list -->
      <div class="desktop-grid-2">
        <div
          v-for="m in filteredMembers"
          :key="m.id"
          style="border-radius:14px;padding:13px 14px;cursor:pointer;transition:box-shadow .18s;"
          :style="expandedId === m.id
            ? 'box-shadow:inset 4px 4px 10px var(--sd),inset -4px -4px 10px var(--sl);background:var(--bg1);'
            : 'box-shadow:4px 4px 10px var(--sd),-4px -4px 10px var(--sl);background:var(--bg);'"
          @click="toggleMember(m.id)"
        >
          <!-- Summary row -->
          <div style="display:flex;gap:11px;align-items:center;">
            <div class="av" :style="`width:44px;height:44px;background:${avColor(m.name)};font-size:13px;`">{{ initials(m.name) }}</div>
            <div style="flex:1;min-width:0;">
              <div style="font-size:14px;font-weight:700;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">{{ m.name }}</div>
              <div style="font-size:11px;color:var(--tl);">+91 {{ m.whatsapp }} &nbsp;·&nbsp; {{ m.city }}</div>
            </div>
            <div style="text-align:right;flex-shrink:0;">
              <div v-if="m.dob"  style="font-size:10px;color:var(--td);">🎂 {{ fmtMD(m.dob) }}</div>
              <div v-if="m.anniversary" style="font-size:10px;color:var(--td);">💐 {{ fmtMD(m.anniversary) }}</div>
              <span style="font-size:9px;padding:2px 7px;border-radius:8px;font-weight:700;" :style="m.status==='active'?'background:rgba(46,125,50,.14);color:var(--ok1);':'background:rgba(180,128,40,.1);color:var(--td);'">{{ m.status === 'active' ? 'Active' : 'Inactive' }}</span>
            </div>
          </div>

          <!-- Expanded details -->
          <div v-if="expandedId === m.id" style="border-top:1px solid rgba(180,128,40,.18);margin-top:12px;padding-top:12px;">
            <div style="display:grid;grid-template-columns:1fr 1fr 1fr;gap:8px;margin-bottom:10px;">
              <div style="background:rgba(180,128,40,.1);border-radius:9px;padding:8px 10px;">
                <div style="font-size:9px;color:var(--td);margin-bottom:2px;">JOINED</div>
                <div style="font-size:11px;font-weight:700;">{{ fmtD(m.joined_at) }}</div>
              </div>
              <div style="background:rgba(180,128,40,.1);border-radius:9px;padding:8px 10px;">
                <div style="font-size:9px;color:var(--td);margin-bottom:2px;">FB CONSENT</div>
                <div style="font-size:11px;font-weight:700;" :style="m.fb_consent?'color:var(--ok1);':'color:var(--td);'">{{ m.fb_consent ? '✓ Yes' : '✗ No' }}</div>
              </div>
              <div style="background:rgba(180,128,40,.1);border-radius:9px;padding:8px 10px;">
                <div style="font-size:9px;color:var(--td);margin-bottom:2px;">STATUS</div>
                <div style="font-size:11px;font-weight:700;" :style="m.status==='active'?'color:var(--ok1);':'color:var(--td);'">{{ m.status === 'active' ? 'Active' : 'Inactive' }}</div>
              </div>
            </div>
            <button @click.stop="removeMember(m.id)" style="background:rgba(198,40,40,.12);border:1px solid rgba(198,40,40,.28);border-radius:9px;padding:7px 14px;color:#ef5350;font-size:12px;cursor:pointer;font-family:'Poppins',sans-serif;font-weight:600;">🗑 Remove Member</button>
          </div>
        </div>
        <div v-if="filteredMembers.length === 0" style="text-align:center;color:var(--td);padding:40px 20px;font-size:14px;">No members found</div>
      </div>
    </div>
  </AdminLayout>
</template>

<script setup>
import { ref, computed } from 'vue';
import { router } from '@inertiajs/vue3';
import AdminLayout from './Layout.vue';

const props = defineProps({
  members:          { type: Array,  default: () => [] },
  activeTab:        { type: String, default: 'members' },
  currentMilestone: { type: String, default: 'all' },
  currentPeriod:    { type: String, default: 'all' },
});

const search            = ref('');
const filterCity        = ref('all');
const expandedId        = ref(null);
const selectedMilestone = ref(props.currentMilestone);
const selectedPeriod    = ref(props.currentPeriod);

function applyFilters() {
  router.get(route('admin.members'), {
    milestone: selectedMilestone.value,
    period:    selectedPeriod.value,
  }, {
    preserveState: true,
    preserveScroll: true,
    replace: true,
  });
}

const cities = computed(() => ['all', ...new Set(props.members.map(m => m.city))]);

const filteredMembers = computed(() => {
  const q = search.value.toLowerCase();
  return props.members.filter(m => {
    const matchSearch = !q || m.name.toLowerCase().includes(q) || m.whatsapp.includes(q);
    const matchCity   = filterCity.value === 'all' || m.city === filterCity.value;
    return matchSearch && matchCity;
  });
});

function toggleMember(id) { expandedId.value = expandedId.value === id ? null : id; }

function removeMember(id) {
  if (!confirm('Remove this member?')) return;
  router.delete(route('admin.members.destroy', id), { preserveScroll: true });
}

// Avatar helpers
const AV_COLORS = ['#c04800','#7b1fa2','#1565c0','#2e7d32','#e65100','#00695c','#6a1b9a'];
function avColor(name) { return AV_COLORS[name.charCodeAt(0) % AV_COLORS.length]; }
function initials(name) {
  const p = name.trim().split(' ');
  return p.length >= 2 ? (p[0][0] + p[1][0]).toUpperCase() : name.slice(0, 2).toUpperCase();
}
function fmtMD(d) { if (!d) return ''; const dt = new Date(d); return dt.toLocaleDateString('en-IN', { day: 'numeric', month: 'short' }); }
function fmtD(d)  { if (!d) return ''; const dt = new Date(d); return dt.toLocaleDateString('en-IN', { month: 'short', year: 'numeric' }); }
</script>
