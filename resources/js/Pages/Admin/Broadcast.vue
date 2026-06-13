<template>
  <AdminLayout>
    <div class="page" style="max-width:600px; margin:0 auto;">
      <h2 style="font-family:'Playfair Display',serif;font-size:20px;margin-bottom:3px;">WhatsApp Broadcast</h2>
      <p style="color:var(--tl);font-size:12px;margin-bottom:20px;">Send messages to all or filtered devotees</p>

      <div v-if="!sent" style="display:flex; flex-direction:column; gap:16px;">
        <!-- Template chips -->
        <div>
          <div class="slbl" style="margin-top:0; margin-bottom:8px;">Choose Template</div>
          <div class="chip-row" style="margin-bottom:4px;">
            <button v-for="t in templates" :key="t.key" class="chip" :class="{ on: activeTmpl === t.key }" @click="selectTemplate(t)">{{ t.label }}</button>
          </div>
        </div>

        <!-- Message editor -->
        <div>
          <div class="slbl" style="margin-bottom:6px;">Message Content</div>
          <textarea v-model="messageBody" class="n-ta" rows="6" placeholder="Type your message…" style="min-height:120px;"/>
          <div style="font-size:11px;color:var(--td);margin-top:6px;">Use <strong>{name}</strong> for personalisation</div>
        </div>

        <!-- Recipients -->
        <div>
          <div class="slbl" style="margin-bottom:8px;">Recipients</div>
          <div class="chip-row" style="margin-bottom:12px;">
            <button class="chip" :class="{ on: rcptMode === 'all' }" @click="rcptMode = 'all'">All Devotees ({{ totalCount }})</button>
            <button class="chip" :class="{ on: rcptMode === 'active' }" @click="rcptMode = 'active'">Active Only ({{ activeCount }})</button>
          </div>
        </div>

        <button class="btn-green" style="width:100%;padding:15px;font-size:15px;display:inline-flex;align-items:center;justify-content:center;gap:6px;" :disabled="sending" @click="sendBroadcast">
          <span v-if="sending" class="spin"/>
          <span v-else style="display:inline-flex;align-items:center;gap:6px;"><SendHorizontal style="width:16px;height:16px;" /> Send to {{ rcptCount }} Devotees</span>
        </button>
      </div>

      <!-- Success banner -->
      <div v-else style="background:rgba(46,125,50,.12);border:1px solid rgba(67,160,71,.28);border-radius:16px;padding:24px;text-align:center;display:flex;flex-direction:column;align-items:center;margin-top:16px;">
        <div style="width:48px;height:48px;border-radius:50%;background:rgba(46,125,50,0.1);color:var(--ok1);display:flex;align-items:center;justify-content:center;margin-bottom:12px;">
          <Check style="width:24px;height:24px;stroke-width:3;" />
        </div>
        <div style="color:var(--ok1);font-weight:700;font-size:15px;">Sent to {{ sentCount }} devotees on WhatsApp!</div>
        <button style="background:none;border:none;color:var(--td);font-size:13px;cursor:pointer;margin-top:12px;font-family:'Poppins',sans-serif;text-decoration:underline;" @click="sent = false">Send another</button>
      </div>
    </div>
  </AdminLayout>
</template>

<script setup>
import { ref, computed } from 'vue';
import { router } from '@inertiajs/vue3';
import AdminLayout from './Layout.vue';
import { SendHorizontal, Check } from '@lucide/vue';

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
      template_key: activeTmpl.value,
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
