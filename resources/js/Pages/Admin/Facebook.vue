<template>
  <AdminLayout>
    <div class="page">
      <!-- Header -->
      <div style="display:flex;justify-content:space-between;align-items:flex-start;margin-bottom:14px;">
        <div>
          <h2 style="font-family:'Playfair Display',serif;font-size:20px;margin-bottom:2px;">Facebook Posts</h2>
          <p style="color:var(--tl);font-size:12px;">Auto-post blessings on Gaushala Page</p>
        </div>
        <button
          @click="toggleAuto"
          style="padding:6px 14px;border-radius:20px;font-size:11px;font-weight:700;cursor:pointer;font-family:'Poppins',sans-serif;transition:all .2s;flex-shrink:0;display:inline-flex;align-items:center;gap:6px;"
          :style="autoOn ? 'background:rgba(24,119,242,.14);color:var(--fb);border:1px solid rgba(24,119,242,.35);' : 'background:rgba(180,128,40,.1);color:var(--tl);border:1px solid rgba(180,128,40,.25);'"
        >
          <span :style="`display:inline-block;width:7px;height:7px;border-radius:50%;background:${autoOn ? '#2e7d32' : '#c62828'};`" />
          <span>Auto: {{ autoOn ? 'ON' : 'OFF' }}</span>
        </button>
      </div>

      <div v-if="autoOn" style="background:rgba(24,119,242,.1);border:1px solid rgba(24,119,242,.28);border-radius:12px;padding:12px 14px;margin-bottom:14px;display:flex;gap:12px;align-items:flex-start;">
        <div style="width:32px;height:32px;border-radius:50%;background:rgba(24,119,242,.12);color:var(--fb);display:flex;align-items:center;justify-content:center;flex-shrink:0;">
          <svg viewBox="0 0 24 24" style="width:16px;height:16px;" fill="currentColor">
            <path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/>
          </svg>
        </div>
        <p style="font-size:12px;color:#42a5f5;line-height:1.65;margin:0;flex:1;">Birthday & anniversary blessings are automatically posted at <strong>7:00 AM IST</strong> daily. Only consented devotees are named.</p>
      </div>

      <!-- Compose toggle -->
      <button class="nr-sm" style="width:100%;padding:12px;margin-bottom:14px;border:none;cursor:pointer;font-size:13px;font-weight:700;color:var(--pr);border-radius:12px;font-family:'Poppins',sans-serif;text-align:center;" @click="composeOpen = !composeOpen">
        {{ composeOpen ? '✕ Cancel' : '+ Create Manual Post' }}
      </button>

      <!-- Compose box -->
      <div v-if="composeOpen">
        <div class="nr" style="padding:16px;margin-bottom:14px;border-radius:18px;">
          <div class="slbl">Post Content</div>
          <textarea v-model="newPost" class="n-ta" rows="5" placeholder="Write your post…&#10;&#10;Tip: #JaiGauMata #GauSeva #Ludhiana"/>
          <div style="display:grid;grid-template-columns:1fr 1fr;gap:10px;margin:12px 0;">
            <div><div class="slbl" style="margin-top:0;">Date</div><input v-model="postDate" type="date" class="n-inp"></div>
            <div><div class="slbl" style="margin-top:0;">Time</div><input v-model="postTime" type="time" class="n-inp" value="07:00"></div>
          </div>
          <button class="btn-brass" style="width:100%;padding:12px;font-size:14px;display:inline-flex;align-items:center;justify-content:center;gap:6px;" @click="schedulePost">
            <Calendar style="width:16px;height:16px;" />
            <span>Schedule Post</span>
          </button>
        </div>
      </div>

      <!-- Post history -->
      <div class="slbl">Schedule & History</div>
      <div v-for="p in posts" :key="p.id" class="fb-card">
        <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:8px;">
          <div style="display:flex;gap:7px;align-items:center;">
            <span class="bdg" :style="`background:${typeColor(p.type)}1a;color:${typeColor(p.type)};border:1px solid ${typeColor(p.type)}44;`">{{ p.type }}</span>
            <span style="font-size:11px;color:var(--tl);">{{ p.when }}</span>
          </div>
          <span class="bdg" style="display:inline-flex;align-items:center;gap:4px;" :style="p.status==='sent'?'background:rgba(46,125,50,.14);color:var(--ok1);border:1px solid rgba(67,160,71,.3);':'background:rgba(249,168,37,.14);color:#f9a825;border:1px solid rgba(249,168,37,.3);'">
            <Check v-if="p.status==='sent'" style="width:11px;height:11px;" />
            <Clock v-else style="width:11px;height:11px;" />
            <span>{{ p.status === 'sent' ? 'Sent' : 'Scheduled' }}</span>
          </span>
        </div>
        <p style="color:var(--tm);font-size:12px;line-height:1.5;display:-webkit-box;-webkit-line-clamp:2;-webkit-box-orient:vertical;overflow:hidden;">{{ p.content }}</p>
        <div v-if="p.devotee_count" style="margin-top:5px;font-size:11px;color:var(--td);display:inline-flex;align-items:center;gap:5px;">
          <Cake style="width:12px;height:12px;color:var(--pr1);" />
          <span>{{ p.devotee_count }} devotees covered</span>
        </div>
      </div>
    </div>
  </AdminLayout>
</template>

<script setup>
import { ref } from 'vue';
import { router } from '@inertiajs/vue3';
import AdminLayout from './Layout.vue';
import { Calendar, Check, Clock, Cake } from '@lucide/vue';

const props = defineProps({
  posts:     { type: Array,   default: () => [] },
  autoOn:    { type: Boolean, default: true },
  activeTab: { type: String,  default: 'facebook' },
});

const composeOpen = ref(false);
const newPost     = ref('');
const postDate    = ref('');
const postTime    = ref('07:00');
const autoOn      = ref(props.autoOn);

const TYPE_COLORS = { birthday: 'var(--pr1)', anniversary: '#9c27b0', event: 'var(--fb)', manual: 'var(--ok)', scheduled: '#f9a825' };
function typeColor(type) { return TYPE_COLORS[type] || TYPE_COLORS.birthday; }

function toggleAuto() {
  router.patch(route('admin.facebook.auto'), { enabled: !autoOn.value }, {
    onSuccess: () => { autoOn.value = !autoOn.value; },
    preserveScroll: true,
  });
}

function schedulePost() {
  if (!newPost.value.trim()) return;
  router.post(route('admin.facebook.schedule'), {
    content: newPost.value,
    date: postDate.value,
    time: postTime.value,
  }, {
    onSuccess: () => { newPost.value = ''; composeOpen.value = false; },
    preserveScroll: true,
  });
}
</script>
