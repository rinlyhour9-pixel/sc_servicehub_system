// Small enhancements for the sidebar UI
// - Keyboard shortcut: Ctrl+B toggles sidebar
// - Add small accessibility helpers

document.addEventListener("DOMContentLoaded", function () {
    const toggle = document.getElementById("sidebarToggle");
    if (!toggle) return;

    document.addEventListener("keydown", function (e) {
        if ((e.ctrlKey || e.metaKey) && e.key.toLowerCase() === "b") {
            e.preventDefault();
            toggle.click();
        }
    });

    // Ensure tooltip titles are accessible on hover for compact mode
    const compactObserver = new MutationObserver(() => {
        // nothing fancy yet; placeholder for future features
    });
    compactObserver.observe(document.documentElement, {
        attributes: true,
        subtree: true,
    });
});
