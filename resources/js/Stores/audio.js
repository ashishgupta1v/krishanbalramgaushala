import { defineStore } from 'pinia';
import { ref } from 'vue';

export const useAudioStore = defineStore('audio', () => {
  const isPlaying = ref(false);
  const audio = ref(null);
  const shouldPlay = ref(false);
  const hasInteracted = ref(false);
  const isInitializing = ref(false);
  
  // Persistent preference: default to true (enabled)
  const isEnabled = ref(true);
  try {
    const saved = localStorage.getItem('gaushala_audio_enabled');
    if (saved !== null) {
      isEnabled.value = saved === 'true';
    }
  } catch {}

  const fallbackUrl = 'https://assets.mixkit.co/music/preview/mixkit-serene-view-1002.mp3';

  function init() {
    if (audio.value || isInitializing.value) return;
    isInitializing.value = true;
    
    console.log('[Audio] Checking custom audio track availability...');
    const sourceUrl = '/audio/bhajan.mp3';
    
    // Asynchronous HEAD request check to prevent blocking load loops
    fetch(sourceUrl, { method: 'HEAD' })
      .then(res => {
        let finalUrl = sourceUrl;
        if (!res.ok) {
          console.warn(`[Audio] Custom bhajan /audio/bhajan.mp3 returned status ${res.status}. Falling back to default.`);
          finalUrl = fallbackUrl;
        } else {
          console.log('[Audio] Custom bhajan /audio/bhajan.mp3 is available on the server.');
        }
        createAudioElement(finalUrl);
      })
      .catch(err => {
        console.warn('[Audio] Custom bhajan not found or failed check. Using default serene flute melody.', err);
        createAudioElement(fallbackUrl);
      });
  }

  function createAudioElement(url) {
    console.log('[Audio] Creating HTML5 Audio element with URL:', url);
    const audioObj = new Audio(url);
    audioObj.loop = true;
    
    audio.value = audioObj;
    isInitializing.value = false;

    // If play was queued during the background fetch check, trigger play now
    if (shouldPlay.value && isEnabled.value) {
      triggerPlayback();
    }
  }

  function triggerPlayback() {
    if (!audio.value) return;
    
    audio.value.play()
      .then(() => {
        isPlaying.value = true;
        hasInteracted.value = true;
        console.log('[Audio] Background audio started playing successfully.');
      })
      .catch(err => {
        console.warn('[Audio] Playback blocked by browser policy. Registering interaction listeners.', err);
        isPlaying.value = false;
        setupInteractionRecovery();
      });
  }

  function setupInteractionRecovery() {
    if (hasInteracted.value) return;
    
    const startPlay = () => {
      if (!isEnabled.value || isPlaying.value) {
        cleanupListeners();
        return;
      }
      
      console.log('[Audio] User interacted with document.');
      if (audio.value) {
        audio.value.play()
          .then(() => {
            isPlaying.value = true;
            shouldPlay.value = true;
            hasInteracted.value = true;
            console.log('[Audio] Playback started successfully on user interaction!');
            cleanupListeners();
          })
          .catch(e => {
            console.warn('[Audio] Playback failed on interaction. Will try again on next tap:', e);
          });
      } else {
        console.log('[Audio] Audio element not ready yet. Queuing play intent for completion.');
        shouldPlay.value = true;
        cleanupListeners();
      }
    };

    const cleanupListeners = () => {
      window.removeEventListener('click', startPlay);
      window.removeEventListener('touchstart', startPlay);
    };

    window.addEventListener('click', startPlay);
    window.addEventListener('touchstart', startPlay);
  }

  function play() {
    if (!isEnabled.value) {
      console.log('[Audio] Playback skipped. Audio is disabled by user.');
      return;
    }

    shouldPlay.value = true;
    init();

    if (audio.value) {
      triggerPlayback();
    }
  }

  function pause() {
    console.log('[Audio] Pausing background audio.');
    shouldPlay.value = false;
    if (audio.value) {
      audio.value.pause();
      isPlaying.value = false;
    }
  }

  function toggleAudio() {
    isEnabled.value = !isEnabled.value;
    console.log(`[Audio] Toggled audio preference: ${isEnabled.value ? 'ENABLED' : 'DISABLED'}`);
    
    try {
      localStorage.setItem('gaushala_audio_enabled', String(isEnabled.value));
    } catch {}

    if (isEnabled.value) {
      play();
    } else {
      pause();
    }
  }

  return { isPlaying, isEnabled, play, pause, toggleAudio };
});
