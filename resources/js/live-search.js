/**
 * Live Search Module
 * Handles real-time, debounced search for administrative tables.
 */

const debounce = (func, wait) => {
    let timeout;
    return (...args) => {
        clearTimeout(timeout);
        timeout = setTimeout(() => func.apply(this, args), wait);
    };
};

export const initLiveSearch = () => {
    const searchInputs = document.querySelectorAll('[data-live-search="true"]');

    searchInputs.forEach(input => {
        const form = input.closest('form');
        if (!form) return;

        // Container to update (defaults to #table-container or specified by data-target)
        const targetSelector = input.dataset.target || '#table-container';
        
        const performSearch = async () => {
            const formData = new FormData(form);
            const params = new URLSearchParams(formData);
            
            // For Laravel, ensure we handle tabs if present
            const currentUrl = new URL(form.action || window.location.href);
            params.forEach((value, key) => {
                currentUrl.searchParams.set(key, value);
            });

            try {
                // Show a subtle loading state if needed
                const targetContainer = document.querySelector(targetSelector);
                if (targetContainer) {
                    targetContainer.style.opacity = '0.5';
                }

                const response = await fetch(currentUrl.toString(), {
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                });

                if (!response.ok) throw new Error('Network response was not ok');

                const html = await response.text();
                const parser = new DOMParser();
                const doc = parser.parseFromString(html, 'text/html');
                const newContent = doc.querySelector(targetSelector);

                if (newContent && targetContainer) {
                    targetContainer.innerHTML = newContent.innerHTML;
                    targetContainer.style.opacity = '1';

                    // Update URL without reloading
                    window.history.pushState({}, '', currentUrl.toString());

                    // Re-initialize any components if necessary (e.g., flowbite tooltips/dropdowns in the new content)
                    if (window.FlowbiteInstances) {
                        // This is a generic way to refresh flowbite if needed
                        // Depending on the version, you might need to call initFlowbite()
                    }
                }
            } catch (error) {
                console.error('Live search failed:', error);
                if (targetContainer) targetContainer.style.opacity = '1';
            }
        };

        const debouncedSearch = debounce(performSearch, 300);

        input.addEventListener('input', (e) => {
            if (['Control', 'Shift', 'Alt', 'Meta'].includes(e.key)) return;
            debouncedSearch();
        });
    });

    // Intercept pagination and sort link clicks for any container already on the page or added later
    document.addEventListener('click', async (e) => {
        const link = e.target.closest('a');
        if (!link) return;

        // Check if the link is inside a live-search container
        const targetContainer = link.closest('[id^="container-"], #table-container');
        if (!targetContainer) return;

        // Don't intercept if it's an external link or a reset button/export link
        if (link.hostname !== window.location.hostname) return;
        if (link.classList.contains('no-ajax') || link.getAttribute('href').includes('export') || link.getAttribute('href').includes('pdf')) return;

        e.preventDefault();
        const url = link.href;

        try {
            targetContainer.style.opacity = '0.5';
            const response = await fetch(url, {
                headers: { 'X-Requested-With': 'XMLHttpRequest' }
            });

            if (!response.ok) throw new Error('Network response was not ok');

            const html = await response.text();
            const parser = new DOMParser();
            const doc = parser.parseFromString(html, 'text/html');
            
            // Try to find the same container in the response
            const containerId = targetContainer.id;
            const newContent = doc.getElementById(containerId) || doc.querySelector('#table-container');

            if (newContent) {
                targetContainer.innerHTML = newContent.innerHTML;
                window.history.pushState({}, '', url);
            }
            targetContainer.style.opacity = '1';
        } catch (error) {
            console.error('AJAX navigation failed:', error);
            window.location.href = url; // Fallback to normal navigation
        }
    });
};
