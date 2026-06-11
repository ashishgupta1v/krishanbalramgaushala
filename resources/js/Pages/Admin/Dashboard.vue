<template>
  <AdminLayout>
    <div class="page">
      <!-- Title row -->
      <div style="display:flex;justify-content:space-between;align-items:flex-start;margin-bottom:18px;">
        <div>
          <h2 style="font-family:'Playfair Display',serif;font-size:20px;">Dashboard</h2>
          <p style="color:var(--tl);font-size:11px;margin-top:2px;">{{ today }}</p>
        </div>
        <div class="sk-seal" style="width:38px;height:38px;display:flex;align-items:center;justify-content:center;color:var(--gd1);flex-shrink:0;">
          <Sparkles style="width:18px;height:18px;" />
        </div>
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
          <div class="slbl" style="margin-top:0;display:inline-flex;align-items:center;gap:6px;">
            <Calendar style="width:14px;height:14px;" />
            <span>Upcoming Events & Activities</span>
          </div>
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
                    <span style="font-size:10px;background:rgba(180,128,40,.1);color:var(--tl);border-radius:20px;padding:2px 9px;font-weight:600;display:inline-flex;align-items:center;gap:4px;">
                      <Clock style="width:10px;height:10px;" /> {{ ev.time_label }}
                    </span>
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
            <div class="slbl" style="margin-top:0;display:inline-flex;align-items:center;gap:6px;">
              <Zap style="width:14px;height:14px;" />
              <span>Quick Actions</span>
            </div>
            <div style="display:grid;grid-template-columns:repeat(3,1fr);gap:10px;">
              <button v-for="qa in quickActions" :key="qa.tab" class="nr-sm" style="padding:15px 8px;text-align:center;border:none;cursor:pointer;font-family:'Poppins',sans-serif;border-radius:14px;background:var(--bg1);display:flex;flex-direction:column;align-items:center;justify-content:center;gap:6px;" @click="goTab(qa.tab)" @mousedown="e=>e.target.style.boxShadow='inset 3px 3px 7px var(--sd),inset -3px -3px 7px var(--sl)'" @mouseup="e=>e.target.style.boxShadow=''" @mouseleave="e=>e.target.style.boxShadow=''">
                <component :is="qa.icon" style="width:20px;height:20px;color:var(--pr1);" />
                <div style="font-size:10px;font-weight:700;color:var(--tl);">{{ qa.label }}</div>
              </button>
            </div>
          </div>

          <!-- Automation Status -->
          <div>
            <div class="slbl" style="margin-top:0;display:inline-flex;align-items:center;gap:6px;">
              <Cpu style="width:14px;height:14px;" />
              <span>Automation Status</span>
            </div>
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
import { computed, markRaw, h } from 'vue';
import { router } from '@inertiajs/vue3';
import AdminLayout from './Layout.vue';
import { Sparkles, Calendar, Clock, Zap, Cpu, Megaphone, Upload } from '@lucide/vue';

const FacebookIcon = {
  render() {
    return h('svg', { viewBox: '0 0 24 24', fill: 'currentColor' }, [
      h('path', { d: 'M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z' })
    ]);
  }
};

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
  { tab: 'broadcast', icon: markRaw(Megaphone), label: 'Broadcast' },
  { tab: 'facebook',  icon: markRaw(FacebookIcon), label: 'FB Post' },
  { tab: 'upload',    icon: markRaw(Upload), label: 'Upload CSV' },
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
