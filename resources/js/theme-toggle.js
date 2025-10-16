const themeToggle = () => ({
  theme: 'auto',

  init() {
    window.matchMedia('(prefers-color-scheme: dark)').addEventListener('change', () => {
      updateTheme();
    });

    const currentTheme = localStorage.getItem('theme') || 'auto';
    this.setTheme(currentTheme);
  },

  setTheme(theme) {
    console.log('Setting theme to:', theme);
    this.theme = theme

    if (theme === 'dark' || theme === 'light') {
      localStorage.setItem('theme', theme)
    } else {
      // Auto mode - remove from localStorage
      localStorage.removeItem('theme');
    }

    updateTheme();
  },
})

export { themeToggle }
