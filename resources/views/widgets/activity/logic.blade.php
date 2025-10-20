const API_BASE = '{{ url('/') }}';

// Global configuration object for activity widget
window.$roadmapActivity = {
    open: function() {
        const widget = document.querySelector('roadmap-activity-widget');
        if (widget) widget.openModal();
    }
};

class RoadmapActivityWidgetElement extends HTMLElement {
    constructor() {
        super();
        this.config = null;
        this.isOpen = false;
        this.darkMode = false;
        this.activities = [];
        this.loading = false;
        this.currentPage = 0;
        this.hasMore = true;
        this.searchQuery = '';
        this.searchTimeout = null;

        // Attach shadow DOM
        this.attachShadow({ mode: 'open' });
    }

    async connectedCallback() {
        await this.init();
        this.setupDarkModeObserver();
    }

    setupDarkModeObserver() {
        // Check initial dark mode state
        this.updateDarkMode();

        // Watch for changes to the HTML class
        const observer = new MutationObserver(() => {
            this.updateDarkMode();
        });

        observer.observe(document.documentElement, {
            attributes: true,
            attributeFilter: ['class']
        });
    }

    updateDarkMode() {
        const isDark = document.documentElement.classList.contains('dark');
        if (this.darkMode !== isDark) {
            this.darkMode = isDark;
            // Re-render if already initialized
            if (this.config) {
                this.render();
            }
        }
    }

    async init() {
        try {
            // Fetch widget configuration
            const response = await fetch(`${API_BASE}/api/activity-widget/config`);
            const config = await response.json();

            if (!config.enabled) {
                return;
            }

            this.config = config;
            this.render();
        } catch (error) {
            console.error('Failed to initialize Activity Widget:', error);
        }
    }

    render() {
        // Render into shadow DOM
        this.shadowRoot.innerHTML = `
            ${this.getStyles()}
            <div class="roadmap-activity-widget-root ${this.darkMode ? 'dark' : ''}">
                ${this.getTemplate()}
            </div>
        `;

        // Hide button if configured
        if (this.config.hide_button) {
            const button = this.shadowRoot.getElementById('roadmap-activity-widget-button');
            if (button) {
                button.style.display = 'none';
            }
        }

        // Attach event listeners
        this.attachEventListeners();
    }

    getStyles() {
        const primaryColor = this.config.primary_color || '#2563EB';
        return `
@include('widgets.activity.styles')
        `;
    }

    getTemplate() {
        const position = this.config.position || 'bottom-left';
        const buttonText = this.config.button_text || 'Recent Activity';

        return `
@include('widgets.activity.template')
        `;
    }

    attachEventListeners() {
        const button = this.shadowRoot.getElementById('roadmap-activity-widget-button');
        const modal = this.shadowRoot.getElementById('roadmap-activity-widget-modal');
        const closeBtn = this.shadowRoot.getElementById('roadmap-activity-widget-close');
        const searchInput = this.shadowRoot.getElementById('roadmap-activity-widget-search');

        button.addEventListener('click', () => this.openModal());
        closeBtn.addEventListener('click', () => this.closeModal());
        modal.addEventListener('click', (e) => {
            if (e.target === modal) {
                this.closeModal();
            }
        });

        // Search input with debouncing
        if (searchInput) {
            searchInput.addEventListener('input', (e) => this.handleSearch(e.target.value));
        }
    }

    async openModal() {
        const modal = this.shadowRoot.getElementById('roadmap-activity-widget-modal');
        modal.classList.remove('roadmap-activity-widget-hidden');
        modal.classList.add('roadmap-activity-widget-opening');
        this.isOpen = true;

        // Remove opening class after animation
        setTimeout(() => {
            modal.classList.remove('roadmap-activity-widget-opening');
        }, 200);

        // Reset for fresh load
        this.activities = [];
        this.currentPage = 0;
        this.hasMore = true;

        // Fetch initial activities
        await this.fetchActivities();

        // Setup scroll listener for infinite scroll
        this.setupScrollListener();
    }

    closeModal() {
        const modal = this.shadowRoot.getElementById('roadmap-activity-widget-modal');
        modal.classList.add('roadmap-activity-widget-hidden');
        this.isOpen = false;
    }

    handleSearch(query) {
        // Clear previous timeout
        if (this.searchTimeout) {
            clearTimeout(this.searchTimeout);
        }

        // Debounce search by 300ms
        this.searchTimeout = setTimeout(() => {
            this.searchQuery = query;
            this.activities = [];
            this.currentPage = 0;
            this.hasMore = true;
            this.fetchActivities();
        }, 300);
    }

    async fetchActivities() {
        if (this.loading || !this.hasMore) return;

        this.loading = true;
        this.currentPage++;

        const contentEl = this.shadowRoot.getElementById('roadmap-activity-widget-content');

        // Show initial loading state
        if (this.currentPage === 1) {
            contentEl.innerHTML = '<div class="roadmap-activity-widget-loading">Loading activities...</div>';
        } else {
            // Show loading at bottom for infinite scroll
            this.appendLoadingIndicator();
        }

        try {
            const url = new URL(`${API_BASE}/api/activity-widget/activities`);
            url.searchParams.set('page', this.currentPage);
            if (this.searchQuery) {
                url.searchParams.set('search', this.searchQuery);
            }

            const response = await fetch(url);
            const data = await response.json();

            if (response.ok && data.activities) {
                this.activities.push(...data.activities);
                this.hasMore = data.has_more;
                this.renderActivities();
            } else {
                if (this.currentPage === 1) {
                    contentEl.innerHTML = '<div class="roadmap-activity-widget-error">Failed to load activities</div>';
                }
            }
        } catch (error) {
            if (this.currentPage === 1) {
                contentEl.innerHTML = '<div class="roadmap-activity-widget-error">Failed to load activities</div>';
            }
        } finally {
            this.loading = false;
        }
    }

    setupScrollListener() {
        const contentEl = this.shadowRoot.getElementById('roadmap-activity-widget-content');

        contentEl.addEventListener('scroll', () => {
            // Check if near bottom (within 100px)
            if (contentEl.scrollHeight - contentEl.scrollTop - contentEl.clientHeight < 100) {
                this.fetchActivities();
            }
        });
    }

    appendLoadingIndicator() {
        const contentEl = this.shadowRoot.getElementById('roadmap-activity-widget-content');
        const list = contentEl.querySelector('.roadmap-activity-widget-list');

        if (list && !list.querySelector('.roadmap-activity-widget-loading-more')) {
            const loader = document.createElement('div');
            loader.className = 'roadmap-activity-widget-loading-more';
            loader.textContent = 'Loading more...';
            list.appendChild(loader);
        }
    }

    renderActivities() {
        const contentEl = this.shadowRoot.getElementById('roadmap-activity-widget-content');

        if (!this.activities || this.activities.length === 0) {
            contentEl.innerHTML = '<div class="roadmap-activity-widget-empty">No recent activity</div>';
            return;
        }

        const activitiesHtml = this.activities.map(activity => {
            const timeAgo = this.timeAgo(activity.created_at);
            const votesHtml = activity.votes ? `
                <div class="roadmap-activity-widget-stat">
                    <svg class="roadmap-activity-widget-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7"></path>
                    </svg>
                    ${activity.votes}
                </div>
            ` : '';
            const commentsHtml = activity.comments !== undefined ? `
                <div class="roadmap-activity-widget-stat">
                    <svg class="roadmap-activity-widget-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path>
                    </svg>
                    ${activity.comments}
                </div>
            ` : '';

            return `
                <a href="${activity.url}" target="_blank" rel="noopener noreferrer" class="roadmap-activity-widget-item">
                    <div class="roadmap-activity-widget-item-header">
                        <div class="roadmap-activity-widget-item-user">${this.escapeHtml(activity.user)}</div>
                        <div class="roadmap-activity-widget-item-time">${timeAgo}</div>
                    </div>
                    <div class="roadmap-activity-widget-item-description">${this.escapeHtml(activity.description)}</div>
                    ${votesHtml || commentsHtml ? `
                        <div class="roadmap-activity-widget-item-stats">
                            ${votesHtml}
                            ${commentsHtml}
                        </div>
                    ` : ''}
                </a>
            `;
        }).join('');

        contentEl.innerHTML = `<div class="roadmap-activity-widget-list">${activitiesHtml}</div>`;
    }

    escapeHtml(text) {
        const div = document.createElement('div');
        div.textContent = text;
        return div.innerHTML;
    }

    timeAgo(dateString) {
        const date = new Date(dateString);
        const now = new Date();
        const seconds = Math.floor((now - date) / 1000);

        const intervals = {
            year: 31536000,
            month: 2592000,
            week: 604800,
            day: 86400,
            hour: 3600,
            minute: 60
        };

        for (const [unit, secondsInUnit] of Object.entries(intervals)) {
            const interval = Math.floor(seconds / secondsInUnit);
            if (interval >= 1) {
                return interval === 1 ? `1 ${unit} ago` : `${interval} ${unit}s ago`;
            }
        }

        return 'just now';
    }
}

// Define custom element
customElements.define('roadmap-activity-widget', RoadmapActivityWidgetElement);

// Auto-initialize by inserting the custom element
const widget = document.createElement('roadmap-activity-widget');
document.body.appendChild(widget);
