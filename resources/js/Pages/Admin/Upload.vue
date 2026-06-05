<template>
  <AdminLayout>
    <div class="page">
      <h2 style="font-family:'Playfair Display',serif;font-size:20px;margin-bottom:4px;">Bulk Upload</h2>
      <p style="color:var(--tl);font-size:12px;margin-bottom:18px;">Import multiple devotees at once via CSV</p>

      <!-- Sample download -->
      <div class="nr-sm" style="padding:14px;margin-bottom:14px;display:flex;justify-content:space-between;align-items:center;">
        <div>
          <div style="font-size:13px;font-weight:700;">📄 Sample CSV Template</div>
          <div style="font-size:11px;color:var(--tl);margin-top:2px;">name, whatsapp, dob, anniversary, city</div>
        </div>
        <button class="btn-brass" style="padding:8px 14px;font-size:11px;" @click="downloadSample">⬇ Download</button>
      </div>

      <!-- Drop zone -->
      <div
        class="dropz"
        :class="{ drag: dragging }"
        style="margin-bottom:14px;"
        @click="$refs.csvInput.click()"
        @dragover.prevent="dragging = true"
        @dragleave="dragging = false"
        @drop.prevent="onDrop"
      >
        <div style="font-size:46px;margin-bottom:12px;">📂</div>
        <div style="font-size:14px;font-weight:700;color:var(--tm);margin-bottom:5px;">Drop CSV file here</div>
        <div style="font-size:12px;color:var(--td);">or tap to browse</div>
        <input ref="csvInput" type="file" accept=".csv" style="display:none;" @change="onFileChosen">
      </div>

      <!-- Validation Errors Display -->
      <div v-if="$page.props.errors.rows" class="nr" style="padding:16px;margin-bottom:18px;background:rgba(198,40,40,.06);border:1px solid rgba(198,40,40,.25);border-radius:16px;color:var(--er);">
        <div style="font-weight:700;font-size:13px;margin-bottom:6px;display:flex;align-items:center;gap:6px;">
          <span>⚠️</span>
          <span>Import Failed — Please correct the following errors:</span>
        </div>
        <ul style="margin:0;padding-left:20px;font-size:11px;line-height:1.65;list-style-type:disc;font-family:'Poppins',sans-serif;">
          <li v-for="(err, idx) in [].concat($page.props.errors.rows)" :key="idx">{{ err }}</li>
        </ul>
      </div>

      <!-- Column guide -->
      <div class="slbl">Column Reference</div>
      <div class="nr" style="padding:14px;margin-bottom:18px;border-radius:16px;">
        <div v-for="(col, i) in cols" :key="col.key" style="display:flex;gap:10px;align-items:center;padding:7px 0;" :style="i < cols.length - 1 ? 'border-bottom:1px solid rgba(180,128,40,.15);' : ''">
          <code style="background:rgba(184,78,0,.12);padding:2px 8px;border-radius:5px;font-size:11px;color:var(--pr1);flex-shrink:0;">{{ col.key }}</code>
          <span style="font-size:10px;font-weight:700;flex-shrink:0;width:54px;" :style="col.required ? 'color:var(--pr);' : 'color:var(--td);'">{{ col.required ? 'Required' : 'Optional' }}</span>
          <span style="font-size:12px;color:var(--tl);">{{ col.desc }}</span>
        </div>
      </div>

      <!-- Preview -->
      <div v-if="rows.length">
        <div class="slbl">Preview — {{ rows.length }} rows ready to import</div>
        <div style="overflow-x:auto;border-radius:12px;margin-bottom:14px;">
          <table style="width:100%;border-collapse:collapse;font-family:'Poppins',sans-serif;">
            <thead>
              <tr style="background:rgba(184,78,0,.15);">
                <th v-for="h in ['Name','WhatsApp','DOB','City']" :key="h" style="padding:8px 10px;text-align:left;font-size:11px;font-weight:700;color:var(--pr1);white-space:nowrap;">{{ h }}</th>
              </tr>
            </thead>
            <tbody>
              <tr v-for="(row, i) in rows.slice(0, 5)" :key="i" :style="i % 2 === 0 ? '' : 'background:rgba(255,246,210,.4);'">
                <td style="padding:7px 10px;font-size:12px;font-weight:600;">{{ row.name || '—' }}</td>
                <td style="padding:7px 10px;font-size:12px;color:var(--tl);">{{ row.whatsapp || '—' }}</td>
                <td style="padding:7px 10px;font-size:12px;color:var(--tl);">{{ row.dob || '—' }}</td>
                <td style="padding:7px 10px;font-size:12px;color:var(--tl);">{{ row.city || '—' }}</td>
              </tr>
            </tbody>
          </table>
          <p v-if="rows.length > 5" style="color:var(--td);font-size:11px;text-align:center;padding:6px 0;">+ {{ rows.length - 5 }} more rows</p>
        </div>
        <div style="display:flex;gap:10px;">
          <button class="btn-ghost" style="flex:1;padding:12px;font-size:13px;" @click="clearUpload">Clear</button>
          <button class="btn-saffron" style="flex:2;padding:12px;font-size:14px;" :disabled="importing" @click="doImport">
            <span v-if="importing" class="spin"/>
            <span v-else>✅ Import {{ rows.length }} Members</span>
          </button>
        </div>
      </div>
    </div>
  </AdminLayout>
</template>

<script setup>
import { ref } from 'vue';
import { router } from '@inertiajs/vue3';
import AdminLayout from './Layout.vue';

defineProps({ activeTab: { type: String, default: 'upload' } });

const dragging  = ref(false);
const rows      = ref([]);
const importing = ref(false);
const csvInput  = ref(null);

const cols = [
  { key: 'name',        required: true,  desc: 'Full devotee name' },
  { key: 'whatsapp',    required: true,  desc: '10-digit (no +91)' },
  { key: 'dob',         required: false, desc: 'YYYY-MM-DD format' },
  { key: 'anniversary', required: false, desc: 'YYYY-MM-DD format' },
  { key: 'city',        required: false, desc: 'City name' },
];

function downloadSample() {
  window.location.href = route('admin.upload.sample');
}

function onDrop(e) {
  dragging.value = false;
  const file = e.dataTransfer.files[0];
  if (file) parseCSV(file);
}

function onFileChosen(e) {
  const file = e.target.files[0];
  if (file) parseCSV(file);
}

function parseCSV(file) {
  if (!file.name.endsWith('.csv')) { alert('Upload a CSV file'); return; }
  const reader = new FileReader();
  reader.onload = (e) => {
    const lines = e.target.result.trim().split('\n');
    const headers = lines[0].split(',').map(h => h.trim());
    rows.value = lines.slice(1).filter(l => l.trim()).map(line => {
      const vals = line.split(',').map(v => v.trim());
      const obj = {};
      headers.forEach((h, i) => obj[h] = vals[i] || '');
      return obj;
    });
  };
  reader.readAsText(file);
}

function clearUpload() { rows.value = []; if (csvInput.value) csvInput.value.value = ''; }

async function doImport() {
  if (!rows.value.length) return;
  importing.value = true;
  router.post(route('admin.members.import'), { rows: rows.value }, {
    onSuccess: () => { rows.value = []; importing.value = false; },
    onError:   () => { importing.value = false; },
    preserveScroll: true,
  });
}
</script>
