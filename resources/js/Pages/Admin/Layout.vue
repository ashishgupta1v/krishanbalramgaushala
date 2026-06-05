<template>
  <div class="admin-layout-container">
    
    <!-- LEFT SIDEBAR (Tablet & Desktop only) -->
    <aside class="admin-sidebar">
      <div style="padding:24px 18px 18px;border-bottom:1px solid rgba(255,255,255,.15);text-align:center;display:flex;flex-direction:column;align-items:center;">
        <div style="width: 90px; height: 90px; border-radius: 50%; display: flex; align-items: center; justify-content: center; margin-bottom: 12px; background: linear-gradient(145deg, var(--bg1), var(--bg)); box-shadow: inset 2px 2px 6px rgba(0,0,0,0.1), 4px 4px 10px rgba(0,0,0,0.15); padding: 3px; border: 1px solid rgba(255,255,255,0.2);">
          <img src="/logo.jpg" alt="Logo" style="width: 100%; height: 100%; border-radius: 50%; object-fit: cover;" />
        </div>
        <h2 style="font-family:'Playfair Display',serif;font-size:16px;color:#fff;font-weight:800;line-height:1.2;">Krishan Balram</h2>
        <h3 style="font-family:'Playfair Display',serif;font-size:14px;color:var(--gd2);font-weight:700;margin-top:2px;">Gaushala</h3>
        <p style="font-size:9px;color:rgba(255,255,255,.5);margin-top:5px;letter-spacing:.3px;">Pakhowal Road, Ludhiana</p>
      </div>

      <!-- Navigation Tabs -->
      <div class="admin-sidebar-nav" style="flex:1;margin-top:16px;">
        <button
          v-for="tab in tabs" :key="tab.id"
          class="admin-sidebar-btn"
          :class="{ active: currentTab === tab.id }"
          @click="goTab(tab.id)"
        >
          <span style="font-size:18px;">{{ tab.icon }}</span>
          <span>{{ tab.label }}</span>
        </button>
      </div>

      <!-- Logout (Sidebar bottom) -->
      <div style="padding:16px;border-top:1px solid rgba(255,255,255,.1);">
        <button class="admin-sidebar-btn" @click="logout" style="color:var(--gd2);">
          <span>🔐</span>
          <span>Sign Out</span>
        </button>
      </div>
    </aside>

    <!-- RIGHT MAIN PANELS (Responsive Main Area) -->
    <main class="admin-main-content">
      
      <!-- MOBILE HEADER (visible only on mobile) -->
      <div class="a-hdr">
        <div style="display:flex;align-items:center;gap:11px;">
          <div style="width: 38px; height: 38px; border-radius: 50%; display: flex; align-items: center; justify-content: center; background: linear-gradient(145deg, var(--bg1), var(--bg)); box-shadow: 2px 2px 6px rgba(0,0,0,0.15); padding: 2px; border: 1px solid rgba(255,255,255,0.6); flex-shrink:0;">
            <img src="/logo.jpg" alt="Logo" style="width: 100%; height: 100%; border-radius: 50%; object-fit: cover;" />
          </div>
          <div>
            <div style="font-family:'Playfair Display',serif;font-size:14px;color:var(--gd3);font-weight:700;line-height:1.2;">Krishan Balram Gaushala</div>
            <div style="font-size:10px;color:rgba(240,210,140,.65);letter-spacing:.3px;">Admin Dashboard &nbsp;•&nbsp; Pakhowal Road, Ludhiana</div>
          </div>
        </div>
        <button @click="logout" style="background:rgba(255,255,255,.1);border:1px solid rgba(255,255,255,.18);border-radius:8px;padding:6px 12px;color:rgba(240,218,158,.8);font-size:11px;cursor:pointer;font-family:'Poppins',sans-serif;">Logout</button>
      </div>

      <!-- Main Content Slots -->
      <div style="flex:1;overflow-y:auto;overflow-x:hidden;min-height:0;">
        <slot />
      </div>

      <!-- MOBILE BOTTOM NAVIGATION (visible only on mobile) -->
      <nav class="a-nav" style="position:relative;z-index:10;">
        <button
          v-for="tab in tabs" :key="tab.id"
          class="nav-i"
          :class="{ on: currentTab === tab.id }"
          @click="goTab(tab.id)"
        >
          <span class="nav-ic">{{ tab.icon }}</span>
          <span class="nav-lb">{{ tab.label }}</span>
        </button>
      </nav>

    </main>

  </div>
</template>

<script setup>
import { computed } from 'vue';
import { router, usePage } from '@inertiajs/vue3';

const page = usePage();
const currentTab = computed(() => page.props.activeTab || 'dashboard');

const tabs = [
  { id: 'dashboard', icon: '🏠', label: 'Home' },
  { id: 'members',   icon: '👥', label: 'Members' },
  { id: 'broadcast', icon: '📣', label: 'Broadcast' },
  { id: 'facebook',  icon: '📘', label: 'Facebook' },
  { id: 'events',    icon: '📅', label: 'Events' },
  { id: 'wishes',    icon: '💝', label: 'Wishes' },
  { id: 'upload',    icon: '📤', label: 'Upload' },
];

function goTab(tabId) {
  const routeMap = {
    dashboard: 'admin.dashboard',
    members:   'admin.members',
    broadcast: 'admin.broadcast',
    facebook:  'admin.facebook',
    events:    'admin.events',
    wishes:    'admin.wishes',
    upload:    'admin.upload',
  };
  router.visit(route(routeMap[tabId]), { preserveScroll: false });
}

function logout() {
  router.post(route('admin.logout'));
}
</script>
