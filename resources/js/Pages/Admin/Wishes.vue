<template>
  <AdminLayout>
    <div class="page">
      <!-- Title -->
      <div style="margin-bottom: 18px;">
        <h2 style="font-family:'Playfair Display',serif;font-size:20px;">Wish Log & Delivery Analytics</h2>
        <p style="color:var(--tl);font-size:12px;margin-top:1px;">Monitor automated birthday/anniversary messages and manage pre-approved WhatsApp templates</p>
      </div>

      <!-- Split Layout -->
      <div class="desktop-grid-2" style="align-items:start;gap:20px;">
        
        <!-- LEFT PANEL: Wish Logs & Live Analytics -->
        <div style="display:flex;flex-direction:column;gap:16px;">
          
          <!-- Quick Analytics Header -->
          <div style="display:grid;grid-template-columns:repeat(3, 1fr);gap:10px;">
            <div class="nr-sm" style="padding:12px;background:#ffffff;text-align:center;">
              <div style="font-size:20px;font-weight:700;color:var(--wa);">{{ stats.sent }}</div>
              <div style="font-size:9px;font-weight:700;color:var(--td);text-transform:uppercase;">Sent</div>
            </div>
            <div class="nr-sm" style="padding:12px;background:#ffffff;text-align:center;">
              <div style="font-size:20px;font-weight:700;color:var(--pr1);">{{ stats.pending }}</div>
              <div style="font-size:9px;font-weight:700;color:var(--td);text-transform:uppercase;">Pending</div>
            </div>
            <div class="nr-sm" style="padding:12px;background:#ffffff;text-align:center;">
              <div style="font-size:20px;font-weight:700;color:var(--er);">{{ stats.failed }}</div>
              <div style="font-size:9px;font-weight:700;color:var(--td);text-transform:uppercase;">Failed</div>
            </div>
          </div>

          <!-- Logs Feed Card -->
          <div class="nr sk-leather" style="padding:18px;border-radius:18px;display:flex;flex-direction:column;gap:12px;">
            <div style="display:flex;justify-content:space-between;align-items:center;">
              <span class="slbl" style="margin:0;">🔔 Message Delivery Logs</span>
              
              <!-- Filter Dropdown -->
              <select v-model="filterStatus" style="padding:6px 10px;border:none;border-radius:8px;background:var(--bg);box-shadow:inset 1px 1px 3px var(--sd),inset -1px -1px 3px var(--sl);color:var(--tx);font-family:'Poppins',sans-serif;font-size:10px;outline:none;cursor:pointer;">
                <option value="all">All Logs</option>
                <option value="sent">Sent Successfully</option>
                <option value="pending">Pending Sends</option>
                <option value="failed">Failed Sends</option>
              </select>
            </div>

            <!-- Log Entries -->
            <div style="display:flex;flex-direction:column;gap:10px;">
              <div v-for="l in filteredLogs" :key="l.id" class="nr-sm" style="padding:10px 12px;background:var(--bg1);border-radius:12px;display:flex;align-items:center;gap:10px;">
                <div class="av" :style="`width:34px;height:34px;background:${avColor(l.devotee?.name || 'Devotee')};font-size:10px;flex-shrink:0;`">
                  {{ initials(l.devotee?.name || 'Devotee') }}
                </div>
                
                <div style="flex:1;min-width:0;">
                  <div style="font-size:12px;font-weight:700;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">{{ l.devotee?.name || 'Unknown Devotee' }}</div>
                  <div style="font-size:9px;color:var(--tl);">
                    +91 {{ l.devotee?.whatsapp || 'N/A' }} &nbsp;•&nbsp; 
                    <span style="text-transform:capitalize;">{{ l.wish_type }}</span>
                  </div>
                </div>

                <div style="text-align:right;flex-shrink:0;">
                  <span :class="['bdg', 'log-badge-' + l.status]" style="font-size:9px;padding:2px 7px;border-radius:6px;font-weight:700;display:inline-block;text-transform:capitalize;">
                    {{ l.status }}
                  </span>
                  <div style="font-size:8px;color:var(--td);margin-top:2px;">{{ fmtTime(l.created_at) }}</div>
                </div>
              </div>

              <!-- Expanded Error display if any failures -->
              <div v-if="failedLogsWithErrors.length > 0" style="background:rgba(198,40,40,.06);border:1px solid rgba(198,40,40,.18);border-radius:12px;padding:12px;margin-top:10px;">
                <div style="font-size:11px;font-weight:700;color:#ef5350;margin-bottom:4px;">⚠️ Error Analytics Details</div>
                <div v-for="fl in failedLogsWithErrors.slice(0, 3)" :key="fl.id" style="font-size:10px;color:var(--tx);margin-bottom:6px;line-height:1.4;">
                  <strong>{{ fl.devotee?.name }}:</strong> {{ fl.error_message || 'Unknown network error' }}
                </div>
              </div>

              <div v-if="filteredLogs.length === 0" style="text-align:center;padding:40px;color:var(--td);font-size:12px;">
                No wish logs matched the selected filter.
              </div>
            </div>
          </div>
        </div>

        <!-- RIGHT PANEL: Meta Custom Template Editor & Activations -->
        <div style="display:flex;flex-direction:column;gap:16px;">
          
          <!-- Active Selector / Approved Templates List -->
          <div class="nr sk-leather" style="padding:18px;border-radius:18px;">
            <span class="slbl" style="margin-top:0;margin-bottom:12px;">📁 Approved Meta Templates</span>
            
            <div style="display:flex;flex-direction:column;gap:10px;">
              <div v-for="t in templates" :key="t.id" class="nr-sm" style="padding:12px;background:var(--bg1);border-radius:12px;border:1px solid rgba(180,128,40,.12);">
                <div style="display:flex;justify-content:space-between;align-items:start;margin-bottom:6px;">
                  <div>
                    <h4 style="font-size:12px;font-weight:700;color:var(--tx);display:flex;align-items:center;gap:6px;">
                      {{ t.label }}
                      <span v-if="t.is_active_for" style="font-size:8px;background:rgba(46,125,50,.12);color:var(--ok);border:1px solid rgba(46,125,50,.2);border-radius:4px;padding:1px 4px;font-weight:800;text-transform:uppercase;">
                        Active {{ t.is_active_for }}
                      </span>
                    </h4>
                    <span style="font-size:9px;color:var(--td);font-family:monospace;">{{ t.meta_name || 'N/A' }}</span>
                  </div>

                  <div style="display:flex;gap:4px;align-items:center;">
                    <span v-if="t.status === 'pending'" style="font-size:8px;background:rgba(180,128,40,.1);color:var(--td);border-radius:4px;padding:2px 5px;font-weight:700;">Pending</span>
                    <span v-else style="font-size:8px;background:rgba(46,125,50,.1);color:var(--ok1);border-radius:4px;padding:2px 5px;font-weight:700;">Approved</span>

                    <button v-if="t.status === 'pending'" @click="approveTemplate(t.id)" class="btn-ghost" style="padding:2px 5px;font-size:8px;font-weight:700;border-radius:4px;box-shadow:none;border:1px solid rgba(180,128,40,.3);" title="Simulate approval">Simulate Approve</button>
                  </div>
                </div>

                <!-- Template Preview text -->
                <p style="font-size:10px;color:var(--tl);background:var(--bg);border-radius:8px;padding:8px;font-family:monospace;white-space:pre-wrap;line-height:1.45;">{{ t.body }}</p>

                <!-- Active Toggle Actions -->
                <div v-if="t.status === 'approved'" style="display:flex;justify-content:end;gap:6px;margin-top:8px;">
                  <button @click="setActive(t.id, 'birthday')" :disabled="t.is_active_for === 'birthday'" class="btn-ghost" style="padding:4px 8px;font-size:9px;box-shadow:none;border:1px solid rgba(180,128,40,.3);border-radius:6px;font-weight:600;">
                    Set Active Birthday
                  </button>
                  <button @click="setActive(t.id, 'anniversary')" :disabled="t.is_active_for === 'anniversary'" class="btn-ghost" style="padding:4px 8px;font-size:9px;box-shadow:none;border:1px solid rgba(180,128,40,.3);border-radius:6px;font-weight:600;">
                    Set Active Anniversary
                  </button>
                </div>
              </div>
            </div>
          </div>

          <!-- Create / Submit Template Form -->
          <div class="nr sk-leather" style="padding:18px;border-radius:18px;">
            <span class="slbl" style="margin-top:0;margin-bottom:12px;">📝 Submit New Meta Template</span>
            
            <form @submit.prevent="submitTemplate" style="display:flex;flex-direction:column;gap:12px;">
              <div>
                <label style="font-size:10px;font-weight:700;color:var(--tl);display:block;margin-bottom:4px;">Template Label *</label>
                <input v-model="form.label" class="n-inp" type="text" placeholder="e.g. Gopashtami Festival Wish" required style="font-size:12px;padding:10px 12px;">
              </div>

              <div style="display:grid;grid-template-columns:1fr;gap:10px;">
                <div>
                  <label style="font-size:10px;font-weight:700;color:var(--tl);display:block;margin-bottom:4px;">Meta Developer Name *</label>
                  <input v-model="form.meta_name" class="n-inp" type="text" placeholder="e.g. gaushala_festival_wish" required style="font-size:12px;padding:10px 12px;font-family:monospace;">
                </div>
                <div>
                  <label style="font-size:10px;font-weight:700;color:var(--tl);display:block;margin-bottom:4px;">Category *</label>
                  <select v-model="form.category" style="width:100%;padding:10px 12px;border:none;border-radius:12px;background:var(--bg);box-shadow:inset 3px 3px 7px var(--sd),inset -3px -3px 7px var(--sl);color:var(--tx);font-family:'Poppins',sans-serif;font-size:12px;outline:none;" required>
                    <option value="utility">Utility</option>
                    <option value="marketing">Marketing</option>
                  </select>
                </div>
              </div>

              <div>
                <label style="font-size:10px;font-weight:700;color:var(--tl);display:block;margin-bottom:4px;">Message Body (Supports {name} placeholder) *</label>
                <textarea v-model="form.body" class="n-ta" rows="3" placeholder="🙏 Jai Gau Mata!\nDear {name} Ji,\n..." required style="font-size:11px;"></textarea>
              </div>

              <div style="display:flex;justify-content:end;margin-top:4px;">
                <button type="submit" class="btn-saffron" style="padding:8px 18px;font-size:11px;">🚀 Submit to Meta</button>
              </div>
            </form>
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
  logs:      { type: Array,  default: () => [] },
  templates: { type: Array,  default: () => [] },
  activeTab: { type: String, default: 'wishes' },
});

const filterStatus = ref('all');

const form = ref({
  label:     '',
  meta_name: '',
  body:      '',
  category:  'utility',
});

// Logs filtering logic
const filteredLogs = computed(() => {
  if (filterStatus.value === 'all') return props.logs;
  return props.logs.filter(l => l.status === filterStatus.value);
});

const failedLogsWithErrors = computed(() => {
  return props.logs.filter(l => l.status === 'failed' && l.error_message);
});

// Quick count analytics based on state
const stats = computed(() => {
  return {
    sent:    props.logs.filter(l => l.status === 'sent').length,
    pending: props.logs.filter(l => l.status === 'pending').length,
    failed:  props.logs.filter(l => l.status === 'failed').length,
  };
});

// Actions
function submitTemplate() {
  router.post(route('admin.wishes.templates.submit'), form.value, {
    onSuccess: () => {
      form.value = { label: '', meta_name: '', body: '', category: 'utility' };
    }
  });
}

function approveTemplate(id) {
  router.post(route('admin.wishes.templates.approve', id));
}

function setActive(id, type) {
  router.post(route('admin.wishes.templates.active', id), { type });
}

// Avatar & Formatting helpers
const AV_COLORS = ['#c04800','#7b1fa2','#1565c0','#2e7d32','#e65100','#00695c','#6a1b9a'];
function avColor(name) { return AV_COLORS[name.charCodeAt(0) % AV_COLORS.length]; }
function initials(name) {
  const p = name.trim().split(' ');
  return p.length >= 2 ? (p[0][0] + p[1][0]).toUpperCase() : name.slice(0, 2).toUpperCase();
}
function fmtTime(d) {
  if (!d) return '';
  const dt = new Date(d);
  return dt.toLocaleTimeString('en-IN', { hour: 'numeric', minute: '2-digit' });
}
</script>

<style>
.log-badge-sent { background: rgba(46,125,50,.12); color: var(--ok); border: 1px solid rgba(46,125,50,.25); }
.log-badge-pending { background: rgba(180,128,40,.08); color: var(--tl); border: 1px solid rgba(180,128,40,.2); }
.log-badge-failed { background: rgba(198,40,40,.12); color: #ef5350; border: 1px solid rgba(198,40,40,.25); }
</style>
