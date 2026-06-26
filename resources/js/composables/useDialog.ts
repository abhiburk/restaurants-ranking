import { ref } from 'vue';

export const useDialog = () => {
  const isOpen = ref(false);

  function closeDialog() {
    isOpen.value = false;
  }

  return [isOpen, closeDialog];
};
