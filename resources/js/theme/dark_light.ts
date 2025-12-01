import { ref, onMounted } from "vue";

const isDark = ref(false);

export function useTheme() {
  // load state saat mount
  onMounted(() => {
    isDark.value = localStorage.getItem("theme") === "dark"
    applyTheme()
  })

  function toggleTheme() {
    isDark.value = !isDark.value
    localStorage.setItem("theme", isDark.value ? "dark" : "light")
    applyTheme()
  }

  function applyDarkTheme(){
    isDark.value = true;
    localStorage.setItem("theme", isDark.value ? "dark" : "light")
    applyTheme();
  }

  function applyLightTheme(){
    isDark.value = false;
    localStorage.setItem("theme", isDark.value ? "dark" : "light")
    applyTheme();
  }

  function applyTheme() {
    if (isDark.value) {
      document.documentElement.classList.add("dark")
    } else {
      document.documentElement.classList.remove("dark")
    }
  }

  return { isDark, toggleTheme, applyDarkTheme, applyLightTheme }
}
