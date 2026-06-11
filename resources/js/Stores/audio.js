import { defineStore } from 'pinia';
import { ref } from 'vue';

export const useAudioStore = defineStore('audio', () => {
  const isPlaying = ref(false);
  const audio = ref(null);
  const shouldPlay = ref(false);
  const hasInteracted = ref(false);
  
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
    if (audio.value) return;
    
    console.log('[Audio] Initializing HTML5 Audio element...');
    
    const audioObj = new Audio('/audio/bhajan.mp3');
    audioObj.loop = true;
    
    audioObj.addEventListener('error', (e) => {
      const currentSrc = audioObj.src || '';
      if (!currentSrc.includes(fallbackUrl)) {
        console.warn('[Audio] Failed to load primary bhajan /audio/bhajan.mp3. Swapping to fallback serene flute melody.', e);
        audioObj.src = fallbackUrl;
        audioObj.load(); // Force browser to load the new source
        
        if (shouldPlay.value && isEnabled.value) {
          console.log('[Audio] Swapped source. Attempting playback on fallback track...');
          audioObj.play()
            .then(() => {
              isPlaying.value = true;
              console.log('[Audio] Fallback playback started successfully!');
            })
            .catch(err => {
              console.warn('[Audio] Fallback playback was blocked by browser. Queuing for user interaction.', err);
              isPlaying.value = false;
              setupInteractionRecovery();
            });
        }
      } else {
        console.error('[Audio] Both primary and fallback tracks failed to load.');
      }
    });

    audio.value = audioObj;
  }

  function setupInteractionRecovery() {
    if (hasInteracted.value) return;
    
    const startPlay = () => {
      if (!isEnabled.value || isPlaying.value) {
        cleanupListeners();
        return;
      }
      
      console.log('[Audio] User interaction detected. Triggering playback...');
      init();
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
            console.warn('[Audio] Playback failed on interaction. Will try again on next interaction:', e);
          });
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
      console.log('[Audio] Playback ignored because audio is disabled by user preference.');
      return;
    }

    shouldPlay.value = true;
    init();

    if (audio.value) {
      console.log('[Audio] Attempting to start background playback...');
      audio.value.play()
        .then(() => {
          isPlaying.value = true;
          hasInteracted.value = true;
          console.log('[Audio] Background playback started successfully!');
        })
        .catch(err => {
          console.warn('[Audio] Autoplay blocked by browser. Queuing on first interaction.', err);
          isPlaying.value = false;
          setupInteractionRecovery();
        });
    }
  }

  function pause() {
    console.log('[Audio] Pausing playback.');
    shouldPlay.value = false;
    if (audio.value) {
      audio.value.pause();
      isPlaying.value = false;
    }
  }

  function toggleAudio() {
    init();
    isEnabled.value = !isEnabled.value;
    console.log(`[Audio] User toggled audio preference: ${isEnabled.value ? 'ENABLED' : 'DISABLED'}`);
    
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
