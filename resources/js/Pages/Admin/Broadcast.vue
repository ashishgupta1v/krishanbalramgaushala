<template>
  <AdminLayout>
    <div class="page">
      <h2 style="font-family:'Playfair Display',serif;font-size:20px;margin-bottom:3px;">WhatsApp Broadcast</h2>
      <p style="color:var(--tl);font-size:12px;margin-bottom:18px;">Send messages to all or filtered devotees</p>

      <div class="desktop-grid-2" style="align-items:start;">
        
        <!-- Left Pane: Editor & Options -->
        <div style="min-width: 0; width: 100%;">
          <!-- Template chips — horizontal scroll on mobile -->
          <div class="slbl" style="margin-top:0;">Choose Template</div>
          <div class="chip-row" style="margin-bottom:16px;">
            <button v-for="t in templates" :key="t.key" class="chip" :class="{ on: activeTmpl === t.key }" @click="selectTemplate(t)">{{ t.label }}</button>
          </div>

          <!-- Message editor -->
          <div class="slbl">Message Content</div>
          <textarea v-model="messageBody" class="n-ta" rows="6" placeholder="Type your message…" style="min-height:120px;" @input="updatePreview"/>
          <div style="font-size:11px;color:var(--td);margin-top:4px;margin-bottom:16px;">Use <strong>{name}</strong> for personalisation</div>

          <!-- Recipients -->
          <div class="slbl">Recipients</div>
          <div class="chip-row" style="margin-bottom:24px;">
            <button class="chip" :class="{ on: rcptMode === 'all' }" @click="rcptMode = 'all'">All Devotees ({{ totalCount }})</button>
            <button class="chip" :class="{ on: rcptMode === 'active' }" @click="rcptMode = 'active'">Active Only ({{ activeCount }})</button>
          </div>

          <button v-if="!sent" class="btn-green" style="width:100%;padding:15px;font-size:15px;margin-bottom:16px;" :disabled="sending" @click="sendBroadcast">
            <span v-if="sending" class="spin"/>
            <span v-else>📲 Send to {{ rcptCount }} Devotees</span>
          </button>
        </div>

        <!-- Right Pane: WhatsApp Live Preview -->
        <div style="min-width: 0; width: 100%;">
          <!-- WA Preview -->
          <div class="slbl" style="margin-top:0;">WhatsApp Preview</div>
          <div class="wa-bbl" style="margin-bottom:18px;">
            <div style="display:flex;align-items:center;gap:9px;margin-bottom:9px;padding-bottom:9px;border-bottom:1px solid rgba(255,255,255,.1);">
              <div style="width:36px;height:36px;border-radius:50%;background:#128c7e;display:flex;align-items:center;justify-content:center;font-size:18px;flex-shrink:0;">🐄</div>
              <div>
                <div style="color:#fff;font-weight:700;font-size:12px;">Krishan Balram Gaushala</div>
                <div style="color:rgba(255,255,255,.4);font-size:10px;">Official Number</div>
              </div>
            </div>
            <div class="wa-msg">
              <p style="color:#fff;font-size:12px;line-height:1.7;white-space:pre-line;margin:0;word-break:break-word;">{{ preview }}</p>
            </div>
          </div>

          <!-- Success banner -->
          <div v-if="sent" style="background:rgba(46,125,50,.12);border:1px solid rgba(67,160,71,.28);border-radius:14px;padding:18px;text-align:center;">
            <div style="font-size:36px;margin-bottom:6px;">✅</div>
            <div style="color:var(--ok1);font-weight:700;font-size:14px;">Sent to {{ sentCount }} devotees on WhatsApp!</div>
            <button style="background:none;border:none;color:var(--td);font-size:12px;cursor:pointer;margin-top:8px;font-family:'Poppins',sans-serif;text-decoration:underline;" @click="sent = false">Send another</button>
          </div>
        </div>

      </div>
    </div>
  </AdminLayout>
</template>

<script setup>
import { ref, computed } from 'vue';
import { router } from '@inertiajs/vue3';
import AdminLayout from './Layout.vue';

const props = defineProps({
  templates:   { type: Array,  default: () => [] },
  totalCount:  { type: Number, default: 0 },
  activeCount: { type: Number, default: 0 },
  activeTab:   { type: String, default: 'broadcast' },
});

const activeTmpl  = ref(props.templates[0]?.key || '');
const messageBody = ref(props.templates[0]?.body || '');
const rcptMode    = ref('all');
const sending     = ref(false);
const sent        = ref(false);
const sentCount   = ref(0);

const rcptCount = computed(() => rcptMode.value === 'all' ? props.totalCount : props.activeCount);
const preview   = computed(() => (messageBody.value || '(empty)').replace(/\{name\}/g, 'Devotee'));

function selectTemplate(t) {
  activeTmpl.value = t.key;
  messageBody.value = t.body;
}

async function sendBroadcast() {
  if (!messageBody.value.trim()) return;
  sending.value = true;
  try {
    const res = await axios.post(route('admin.broadcast.send'), {
      message: messageBody.value,
      mode: rcptMode.value,
    });
    sentCount.value = res.data.count;
    sent.value = true;
  } catch (e) {
    alert(e.response?.data?.message || 'Failed to send.');
  } finally {
    sending.value = false;
  }
}
</script>
