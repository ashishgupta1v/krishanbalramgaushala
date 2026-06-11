<template>
  <div class="admin-layout-container">
    
    <!-- LEFT SIDEBAR (Tablet & Desktop only) -->
    <aside class="admin-sidebar">
      <div style="padding:24px 18px 18px;border-bottom:1px solid rgba(255,255,255,.15);text-align:center;display:flex;flex-direction:column;align-items:center;">
        <!-- Logo — no circular clip, show full logo with drop-shadow -->
        <div style="width:90px;height:90px;display:flex;align-items:center;justify-content:center;margin-bottom:12px;">
          <picture>
            <source srcset="/logo.webp" type="image/webp">
            <img src="/logo1.png" alt="Logo" width="90" height="90" class="logo-img" style="width:90px;height:90px;" loading="eager" decoding="async" />
          </picture>
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
          :data-prefetch="tabRoute(tab.id)"
        >
          <component :is="tab.icon" style="width:18px;height:18px;flex-shrink:0;" />
          <span>{{ tab.label }}</span>
        </button>
      </div>

      <!-- Logout (Sidebar bottom) -->
      <div style="padding:16px;border-top:1px solid rgba(255,255,255,.1);display:flex;justify-content:center;">
        <button class="admin-sidebar-btn" @click="logout" style="color:var(--gd2);justify-content:center;padding:12px;" title="Sign Out">
          <LogOut style="width:20px;height:20px;" />
        </button>
      </div>
    </aside>

    <!-- RIGHT MAIN PANELS -->
    <main class="admin-main-content">
      
      <!-- MOBILE HEADER (visible only on mobile) -->
      <div class="a-hdr">
        <div style="display:flex;align-items:center;gap:11px;">
          <!-- Logo in header — clean, no circular clip -->
          <div style="width:38px;height:38px;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
            <picture>
              <source srcset="/logo.webp" type="image/webp">
              <img src="/logo1.png" alt="Logo" width="38" height="38" class="logo-img" style="width:38px;height:38px;" loading="eager" decoding="async" />
            </picture>
          </div>
          <div>
            <div style="font-family:'Playfair Display',serif;font-size:14px;color:var(--gd3);font-weight:700;line-height:1.2;">Krishan Balram Gaushala</div>
            <div style="font-size:10px;color:rgba(240,210,140,.65);letter-spacing:.3px;">Admin Dashboard &nbsp;•&nbsp; Pakhowal Road, Ludhiana</div>
          </div>
        </div>
        <button @click="logout" style="background:rgba(255,255,255,.1);border:1px solid rgba(255,255,255,.18);border-radius:8px;padding:8px 10px;color:rgba(240,218,158,.8);display:flex;align-items:center;justify-content:center;cursor:pointer;" title="Sign Out">
          <LogOut style="width:18px;height:18px;" />
        </button>
      </div>

      <!-- Scrollable Content Area -->
      <div class="admin-scroll-area">
        <slot />
      </div>

      <!-- MOBILE BOTTOM NAVIGATION -->
      <nav class="a-nav" style="position:relative;z-index:10;">
        <button
          v-for="tab in tabs" :key="tab.id"
          class="nav-i"
          :class="{ on: currentTab === tab.id }"
          @click="goTab(tab.id)"
          :data-prefetch="tabRoute(tab.id)"
        >
          <component :is="tab.icon" class="nav-ic" style="width:18px;height:18px;flex-shrink:0;" />
          <span class="nav-lb">{{ tab.label }}</span>
        </button>
      </nav>

    </main>

  </div>
</template>

<script setup>
import { computed, markRaw, h } from 'vue';
import { router, usePage } from '@inertiajs/vue3';
import { 
  LayoutDashboard, 
  Users, 
  Megaphone, 
  Calendar, 
  Heart, 
  Upload, 
  LogOut 
} from '@lucide/vue';

const FacebookIcon = {
  render() {
    return h('svg', { viewBox: '0 0 24 24', fill: 'currentColor' }, [
      h('path', { d: 'M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z' })
    ]);
  }
};

const page = usePage();
const currentTab = computed(() => page.props.activeTab || 'dashboard');

const tabs = [
  { id: 'dashboard', icon: markRaw(LayoutDashboard), label: 'Home' },
  { id: 'members',   icon: markRaw(Users), label: 'Members' },
  { id: 'broadcast', icon: markRaw(Megaphone), label: 'Broadcast' },
  { id: 'facebook',  icon: markRaw(FacebookIcon), label: 'Facebook' },
  { id: 'events',    icon: markRaw(Calendar), label: 'Events' },
  { id: 'wishes',    icon: markRaw(Heart), label: 'Wishes' },
  { id: 'upload',    icon: markRaw(Upload), label: 'Upload' },
];

const routeMap = {
  dashboard: 'admin.dashboard',
  members:   'admin.members',
  broadcast: 'admin.broadcast',
  facebook:  'admin.facebook',
  events:    'admin.events',
  wishes:    'admin.wishes',
  upload:    'admin.upload',
};

function tabRoute(id) {
  try { return route(routeMap[id]); } catch { return ''; }
}

function goTab(tabId) {
  if (currentTab.value === tabId) return;
  router.visit(route(routeMap[tabId]), {
    preserveScroll: false,
    preserveState: false,
  });
}

function logout() {
  router.post(route('admin.logout'));
}
</script>

<style scoped>
.page-fade-enter-active {
  animation: pageSlideIn 0.22s ease both;
}
.page-fade-leave-active {
  animation: pageSlideOut 0.15s ease both;
}
@keyframes pageSlideIn {
  from { opacity: 0; transform: translateY(10px); }
  to   { opacity: 1; transform: translateY(0); }
}
@keyframes pageSlideOut {
  from { opacity: 1; transform: translateY(0); }
  to   { opacity: 0; transform: translateY(-6px); }
}
</style>
