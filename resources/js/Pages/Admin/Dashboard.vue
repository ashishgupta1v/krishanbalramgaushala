<template>
  <AdminLayout>
    <div class="page">
      <!-- Title row -->
      <div style="display:flex;justify-content:space-between;align-items:flex-start;margin-bottom:18px;">
        <div>
          <h2 style="font-family:'Playfair Display',serif;font-size:20px;">Dashboard</h2>
          <p style="color:var(--tl);font-size:11px;margin-top:2px;">{{ today }}</p>
        </div>
        <div class="sk-seal" style="width:38px;height:38px;display:flex;align-items:center;justify-content:center;font-size:18px;flex-shrink:0;">🙏</div>
      </div>

      <!-- Stat cards -->
      <div class="desktop-grid-4" style="margin-bottom:22px;">
        <div class="nr stat-card">
          <div class="stat-v" style="color:var(--pr1);">{{ stats.total }}</div>
          <div class="stat-l">Total Members</div>
          <div class="stat-s">{{ stats.active }} active</div>
        </div>
        <div class="nr stat-card">
          <div class="stat-v" style="color:var(--gd);">{{ stats.birthdays_today }}</div>
          <div class="stat-l">Birthdays Today</div>
          <div class="stat-s">WA wishes sent ✓</div>
        </div>
        <div class="nr stat-card">
          <div class="stat-v" style="color:#9c27b0;">{{ stats.anniversaries_today }}</div>
          <div class="stat-l">Anniversaries Today</div>
          <div class="stat-s">WA wishes sent ✓</div>
        </div>
        <div class="nr stat-card">
          <div class="stat-v" style="color:var(--wa);">{{ stats.wa_sent_today }}</div>
          <div class="stat-l">WA Sent Today</div>
          <div class="stat-s">All delivered ✓</div>
        </div>
      </div>

      <!-- Dynamic Main Dashboard Grid -->
      <div class="desktop-grid-2" style="margin-bottom:18px;align-items:start;">
        
        <!-- Left column: Upcoming Events & Activities -->
        <div>
          <div class="slbl" style="margin-top:0;">📅 Upcoming Events & Activities</div>
          <div style="display:flex;flex-direction:column;gap:10px;">
            <div v-for="ev in events" :key="ev.id">
              <div class="ev-card" style="margin-bottom:0;" :style="`border-left-color:${evColor(ev.type).bdr};`">
                <div class="ev-ico" :style="`background:${evColor(ev.type).bg};border:1px solid ${evColor(ev.type).bdr}44;`">
                  <span>{{ ev.icon }}</span>
                </div>
                <div style="flex:1;min-width:0;">
                  <div style="font-size:13px;font-weight:700;margin-bottom:2px;">{{ ev.title }}</div>
                  <div style="font-size:11px;color:var(--tl);margin-bottom:6px;line-height:1.4;">{{ ev.description }}</div>
                  <div style="display:flex;gap:7px;flex-wrap:wrap;">
                    <span :style="`font-size:10px;background:${evColor(ev.type).bg};color:${evColor(ev.type).dot};border:1px solid ${evColor(ev.type).bdr}44;border-radius:20px;padding:2px 9px;font-weight:700;`">{{ ev.date_label }}</span>
                    <span style="font-size:10px;background:rgba(180,128,40,.1);color:var(--tl);border-radius:20px;padding:2px 9px;font-weight:600;">⏰ {{ ev.time_label }}</span>
                  </div>
                </div>
              </div>
            </div>
            <div v-if="!events || !events.length" class="nr" style="padding:20px;text-align:center;font-size:12px;color:var(--tl);">
              No upcoming activities scheduled.
            </div>
          </div>
        </div>

        <!-- Right column: Quick Actions & Automation Status stacked vertically -->
        <div style="display:flex;flex-direction:column;gap:20px;">
          
          <!-- Quick Actions -->
          <div>
            <div class="slbl" style="margin-top:0;">⚡ Quick Actions</div>
            <div style="display:grid;grid-template-columns:repeat(3,1fr);gap:10px;">
              <button v-for="qa in quickActions" :key="qa.tab" class="nr-sm" style="padding:15px 8px;text-align:center;border:none;cursor:pointer;font-family:'Poppins',sans-serif;border-radius:14px;background:var(--bg1);" @click="goTab(qa.tab)" @mousedown="e=>e.target.style.boxShadow='inset 3px 3px 7px var(--sd),inset -3px -3px 7px var(--sl)'" @mouseup="e=>e.target.style.boxShadow=''" @mouseleave="e=>e.target.style.boxShadow=''">
                <div style="font-size:24px;margin-bottom:4px;">{{ qa.icon }}</div>
                <div style="font-size:10px;font-weight:700;color:var(--tl);">{{ qa.label }}</div>
              </button>
            </div>
          </div>

          <!-- Automation Status -->
          <div>
            <div class="slbl" style="margin-top:0;">🤖 Automation Status</div>
            <div class="nr" style="padding:14px 16px;border-radius:18px;">
              <div v-for="(item, i) in automationStatus" :key="i" class="a-row" :style="i===automationStatus.length-1?'border-bottom:none;':''">
                <div style="display:flex;align-items:center;">
                  <span class="a-dot" :style="`background:${item.color};box-shadow:0 0 6px ${item.color};${item.pulse?'animation:pulse 2s ease infinite;':''}`"/>
                  <span style="font-size:13px;">{{ item.label }}</span>
                </div>
                <span class="bdg" :style="`background:${item.badgeBg};color:${item.badgeColor};border:1px solid ${item.badgeBorder};`">{{ item.status }}</span>
              </div>
            </div>
          </div>

        </div>

      </div>
    </div>
  </AdminLayout>
</template>

<script setup>
import { computed } from 'vue';
import { router } from '@inertiajs/vue3';
import AdminLayout from './Layout.vue';

const props = defineProps({
  stats:    { type: Object, default: () => ({}) },
  timeline: { type: Array,  default: () => [] },
  events:   { type: Array,  default: () => [] },
  activeTab: { type: String, default: 'dashboard' },
});

const today = computed(() => new Date().toLocaleDateString('en-IN', { weekday: 'long', day: 'numeric', month: 'long', year: 'numeric' }));

const EV_COLS = {
  festival: { bdr: '#e65100', bg: 'rgba(230,81,0,.12)', dot: '#e65100' },
  daily:    { bdr: 'var(--gd1)', bg: 'rgba(200,150,8,.1)', dot: 'var(--gd1)' },
  weekly:   { bdr: 'var(--wa)', bg: 'rgba(37,211,102,.1)', dot: 'var(--wa)' },
  upcoming: { bdr: 'var(--pr1)', bg: 'rgba(184,78,0,.1)', dot: 'var(--pr1)' },
  meeting:  { bdr: 'var(--fb)', bg: 'rgba(24,119,242,.1)', dot: 'var(--fb)' },
  health:   { bdr: '#7b1fa2', bg: 'rgba(123,31,162,.1)', dot: '#7b1fa2' },
};
function evColor(type) { return EV_COLS[type] || EV_COLS.upcoming; }

const quickActions = [
  { tab: 'broadcast', icon: '📣', label: 'Broadcast' },
  { tab: 'facebook',  icon: '📘', label: 'FB Post' },
  { tab: 'upload',    icon: '📤', label: 'Upload CSV' },
];

const automationStatus = [
  { label: 'WhatsApp Birthday Wishes',    color: 'var(--wa)',  status: 'Active',   badgeBg: 'rgba(37,211,102,.14)', badgeColor: 'var(--wa)',  badgeBorder: 'rgba(37,211,102,.3)' },
  { label: 'WhatsApp Anniversary Wishes', color: 'var(--wa)',  status: 'Active',   badgeBg: 'rgba(37,211,102,.14)', badgeColor: 'var(--wa)',  badgeBorder: 'rgba(37,211,102,.3)' },
  { label: 'Facebook Auto-Posts',         color: 'var(--fb)',  status: 'Active',   badgeBg: 'rgba(24,119,242,.14)', badgeColor: 'var(--fb)',  badgeBorder: 'rgba(24,119,242,.3)' },
  { label: 'Daily Cron — 7:00 AM IST',   color: 'var(--ok1)', status: 'Running',  badgeBg: 'rgba(67,160,71,.14)',  badgeColor: 'var(--ok1)', badgeBorder: 'rgba(67,160,71,.3)', pulse: true },
];

function goTab(id) {
  const routeMap = { broadcast: 'admin.broadcast', facebook: 'admin.facebook', upload: 'admin.upload' };
  router.visit(route(routeMap[id]));
}
</script>
