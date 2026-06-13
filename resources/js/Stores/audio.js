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

  const fallbackUrl = 'https://archive.org/download/PanditHariprasadChaurasiaAtRamakrishnaMissionDelhi20140119/PanditHariprasadChaurasiaAtRamakrishnaMissionDelhi2014_01_19.mp3';

  function init() {
    if (audio.value) return;
    
    console.log('[Audio] Initializing HTML5 Audio element...');
    
    // Start with custom bhajan.mp3, optimize preload to reduce server strain and use cache-busting
    const audioObj = new Audio('/audio/bhajan.mp3?v=3');
    audioObj.loop = true;
    audioObj.preload = 'metadata';
    
    audioObj.addEventListener('error', (e) => {
      const err = audioObj.error;
      const errCode = err ? err.code : null;
      const currentSrc = audioObj.src || '';
      
      console.warn(`[Audio] Error event triggered on source. Code: ${errCode}`, e);
      
      // Swap to fallback ONLY on decoding error (3) or format not supported (4)
      if (errCode === 3 || errCode === 4) {
        if (!currentSrc.includes('PanditHariprasadChaurasia')) {
          console.warn('[Audio] Failed to decode primary bhajan /audio/bhajan.mp3. Swapping to fallback serene flute melody.');
          audioObj.src = fallbackUrl;
          audioObj.load();
          
          if (shouldPlay.value && isEnabled.value) {
            console.log('[Audio] Swapped source. Attempting playback on fallback track...');
            audioObj.play()
              .then(() => {
                isPlaying.value = true;
                console.log('[Audio] Fallback playback started successfully!');
              })
              .catch(err => {
                console.warn('[Audio] Fallback playback blocked by browser in error event. Registering interaction recovery.', err);
                isPlaying.value = false;
                setupInteractionRecovery();
              });
          }
        } else {
          console.error('[Audio] Both primary and fallback tracks failed to load.');
        }
      } else {
        console.log('[Audio] Non-fatal audio loading error (e.g. abort or network fluctuation). Ignored source swap.');
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
          .catch(err => {
            console.warn('[Audio] Playback failed on interaction:', err.name);
            
            // If primary failed due to load/decode issues, swap to fallback immediately inside click context
            if (err.name !== 'NotAllowedError') {
              const mediaErr = audio.value.error;
              const errCode = mediaErr ? mediaErr.code : null;
              
              // Only swap to fallback on real, fatal media errors (code 3 = decode, 4 = unsupported)
              if (errCode === 3 || errCode === 4) {
                const currentSrc = audio.value.src || '';
                if (!currentSrc.includes('PanditHariprasadChaurasia')) {
                  console.warn('[Audio] Swapping to fallback serene flute melody on user interaction...');
                  audio.value.src = fallbackUrl;
                  audio.value.load();
                  
                  audio.value.play()
                    .then(() => {
                      isPlaying.value = true;
                      shouldPlay.value = true;
                      hasInteracted.value = true;
                      console.log('[Audio] Fallback playback started successfully on user interaction!');
                      cleanupListeners();
                    })
                    .catch(fallbackErr => {
                      console.warn('[Audio] Fallback playback also failed on interaction:', fallbackErr.name);
                    });
                }
              }
            }
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
          console.warn('[Audio] Playback attempt failed:', err.name, err.message);
          
          if (err.name === 'NotAllowedError') {
            console.warn('[Audio] Autoplay blocked by browser. Queuing on first interaction.');
            isPlaying.value = false;
            setupInteractionRecovery();
          } else {
            // Loading/decoding error (e.g. corrupt bhajan.mp3)
            const mediaErr = audio.value.error;
            const errCode = mediaErr ? mediaErr.code : null;
            
            // Only swap to fallback on real, fatal media errors (code 3 = decode, 4 = unsupported)
            if (errCode === 3 || errCode === 4) {
              const currentSrc = audio.value.src || '';
              if (!currentSrc.includes('PanditHariprasadChaurasia')) {
                console.warn('[Audio] Primary source failed to play. Swapping to fallback serene flute melody...');
                audio.value.src = fallbackUrl;
                audio.value.load();
                
                if (shouldPlay.value && isEnabled.value) {
                  audio.value.play()
                    .then(() => {
                      isPlaying.value = true;
                      hasInteracted.value = true;
                      console.log('[Audio] Fallback playback started successfully!');
                    })
                    .catch(fallbackErr => {
                      console.warn('[Audio] Fallback playback also failed:', fallbackErr.name);
                      isPlaying.value = false;
                      setupInteractionRecovery();
                    });
                }
              } else {
                isPlaying.value = false;
              }
            } else {
              console.log('[Audio] Non-fatal play failure (e.g. network stall). Retrying on interaction.');
              isPlaying.value = false;
              setupInteractionRecovery();
            }
          }
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
