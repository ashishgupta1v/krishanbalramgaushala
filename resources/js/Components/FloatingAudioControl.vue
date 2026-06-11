<template>
  <button
    v-if="isVisible"
    class="audio-floater"
    :class="{ playing: store.isPlaying && store.isEnabled }"
    @click="store.toggleAudio"
    :title="store.isEnabled ? 'Mute/Turn Off Background Music' : 'Unmute/Turn On Background Music'"
  >
    <VolumeX v-if="!store.isEnabled || !store.isPlaying" style="width:16px;height:16px;" />
    <Volume2 v-else style="width:16px;height:16px;" />
  </button>
</template>

<script setup>
import { computed } from 'vue';
import { usePage } from '@inertiajs/vue3';
import { useAudioStore } from '@/Stores/audio';
import { Volume2, VolumeX } from '@lucide/vue';

const store = useAudioStore();
const page = usePage();

// Hide the audio controls when on admin pages
const isVisible = computed(() => {
  const url = page.url || '';
  return !url.startsWith('/admin');
});
</script>

<style scoped>
.audio-floater {
  position: fixed;
  bottom: 12px;
  left: 16px;
  width: 32px;
  height: 32px;
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
  cursor: pointer;
  z-index: 999;
  border: 1px solid rgba(180, 128, 40, 0.15);
  background: rgba(255, 255, 255, 0.85);
  backdrop-filter: blur(8px);
  -webkit-backdrop-filter: blur(8px);
  color: var(--td);
  opacity: 0.8;
  transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
  box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
  padding: 0;
}

.audio-floater:hover {
  transform: scale(1.1);
  opacity: 1;
  color: var(--pr1);
  background: #fff;
  border-color: rgba(180, 128, 40, 0.3);
  box-shadow: 0 6px 16px rgba(180, 128, 40, 0.15);
}

.audio-floater:active {
  transform: scale(0.95);
}

/* Pulse animation when music is playing and active */
@keyframes audio-pulse {
  0% {
    box-shadow: 0 0 0 0 rgba(212, 102, 0, 0.25);
  }
  70% {
    box-shadow: 0 0 0 8px rgba(212, 102, 0, 0);
  }
  100% {
    box-shadow: 0 0 0 0 rgba(212, 102, 0, 0);
  }
}

.audio-floater.playing {
  animation: audio-pulse 2s infinite;
  border-color: rgba(212, 102, 0, 0.25);
}
</style>
