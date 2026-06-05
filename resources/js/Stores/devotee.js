import { defineStore } from 'pinia';
import { ref } from 'vue';

export const useDevoteeStore = defineStore('devotee', () => {
  const devotee = ref(null);

  function setDevotee(data) {
    devotee.value = data;
    try { localStorage.setItem('gaushala_devotee', JSON.stringify(data)); } catch {}
  }

  function loadFromStorage() {
    try {
      const raw = localStorage.getItem('gaushala_devotee');
      if (raw) devotee.value = JSON.parse(raw);
    } catch {}
  }

  function clear() {
    devotee.value = null;
    try { localStorage.removeItem('gaushala_devotee'); } catch {}
  }

  return { devotee, setDevotee, loadFromStorage, clear };
});
