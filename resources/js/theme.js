// This function is responsible for setting the initial theme when the page loads.
function setThemeOnLoad() {
    // Check if the user has set a specific theme preference in localStorage
    if (localStorage.theme === "dark") {
        document.documentElement.classList.add("dark");
    } else if (localStorage.theme === "light") {
        document.documentElement.classList.remove("dark");
    }
    // If no preference is saved in localStorage, default to light theme
    else if (
        !("theme" in localStorage) &&
        window.matchMedia("(prefers-color-scheme: dark)").matches
    ) {
        document.documentElement.classList.add("dark");
    } else {
        document.documentElement.classList.remove("dark");
    }
}

// This function toggles between dark and light mode when called, and also updates the localStorage to remember the user’s choice.
function switchTheme() {
    if (localStorage.theme === "dark") {
        document.documentElement.classList.remove("dark");
        localStorage.theme = "light";
    } else {
        document.documentElement.classList.add("dark");
        localStorage.theme = "dark";
    }
}

export { setThemeOnLoad, switchTheme };