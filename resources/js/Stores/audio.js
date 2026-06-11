import { defineStore } from 'pinia';
import { ref } from 'vue';

export const useAudioStore = defineStore('audio', () => {
  const isPlaying = ref(false);
  const audio = ref(null);
  const hasInteracted = ref(false);
  
  // Persistent preference: default to true (enabled)
  const isEnabled = ref(true);
  try {
    const saved = localStorage.getItem('gaushala_audio_enabled');
    if (saved !== null) {
      isEnabled.value = saved === 'true';
    }
  } catch {}

  function init() {
    if (audio.value) return;
    
    const fallbackUrl = 'https://assets.mixkit.co/music/preview/mixkit-serene-view-1002.mp3';
    
    // Try custom bhajan path, fallback to CDN if not found
    const audioObj = new Audio('/audio/bhajan.mp3');
    audioObj.loop = true;
    
    audioObj.addEventListener('error', (e) => {
      if (audioObj.src !== fallbackUrl && !audioObj.src.endsWith(fallbackUrl)) {
        console.warn('Custom bhajan file /audio/bhajan.mp3 not found. Falling back to serene flute melody.');
        audioObj.src = fallbackUrl;
        if (isPlaying.value && isEnabled.value) {
          audioObj.play().catch(err => console.log('Audio playback blocked:', err));
        }
      }
    });

    audio.value = audioObj;
  }

  function play() {
    // Respect devotee preference
    if (!isEnabled.value) return;

    init();
    if (audio.value) {
      audio.value.play()
        .then(() => {
          isPlaying.value = true;
          hasInteracted.value = true;
        })
        .catch(err => {
          console.warn('Playback blocked by browser. Queuing on first interaction:', err);
          isPlaying.value = false;
          
          const startPlay = () => {
            if (audio.value && !isPlaying.value && isEnabled.value) {
              audio.value.play()
                .then(() => {
                  isPlaying.value = true;
                  hasInteracted.value = true;
                })
                .catch(e => console.log('Interactive play failed:', e));
            }
          };
          
          if (!hasInteracted.value) {
            window.addEventListener('click', startPlay, { once: true });
            window.addEventListener('touchstart', startPlay, { once: true });
          }
        });
    }
  }

  function pause() {
    if (audio.value) {
      audio.value.pause();
      isPlaying.value = false;
    }
  }

  function toggleAudio() {
    init();
    isEnabled.value = !isEnabled.value;
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
