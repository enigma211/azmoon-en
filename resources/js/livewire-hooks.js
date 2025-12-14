// Livewire SPA Loading Hooks
document.addEventListener('DOMContentLoaded', function () {
    const loadingOverlay = document.getElementById('global-loading');
    let loadingTimer = null;

    // Listen for Livewire navigation events
    document.addEventListener('livewire:navigating', function () {
        // Show loading overlay after 2 seconds
        loadingTimer = setTimeout(() => {
            if (loadingOverlay) {
                loadingOverlay.classList.remove('hidden');
            }
        }, 2000);
    });

    // Hide loading overlay when navigation is complete
    document.addEventListener('livewire:navigated', function () {
        if (loadingTimer) {
            clearTimeout(loadingTimer);
            loadingTimer = null;
        }
        if (loadingOverlay) {
            loadingOverlay.classList.add('hidden');
        }
    });

    // Also hide on any page load error
    document.addEventListener('livewire:navigation-failed', function () {
        if (loadingTimer) {
            clearTimeout(loadingTimer);
            loadingTimer = null;
        }
        if (loadingOverlay) {
            loadingOverlay.classList.add('hidden');
        }
    });

    // Hide loading on initial page load
    window.addEventListener('load', function () {
        if (loadingOverlay) {
            loadingOverlay.classList.add('hidden');
        }
    });
});
