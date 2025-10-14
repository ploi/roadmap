const API_BASE = '{{ url('/') }}';

// Global configuration object
window.$roadmap = {
    _name: '',
    _email: '',
    setName: function(name) {
        this._name = name;
        const widget = document.querySelector('roadmap-widget');
        if (widget) widget.updateName(name);
    },
    setEmail: function(email) {
        this._email = email;
        const widget = document.querySelector('roadmap-widget');
        if (widget) widget.updateEmail(email);
    },
    open: function() {
        const widget = document.querySelector('roadmap-widget');
        if (widget) widget.openModal();
    }
};

class RoadmapWidgetElement extends HTMLElement {
    constructor() {
        super();
        this.config = null;
        this.isOpen = false;
        this.darkMode = false;

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
            const response = await fetch(`${API_BASE}/api/widget/config`);
            const config = await response.json();

            if (!config.enabled) {
                return;
            }

            this.config = config;
            this.render();
        } catch (error) {
            console.error('Failed to initialize Roadmap Widget:', error);
        }
    }

    render() {
        // Render into shadow DOM
        this.shadowRoot.innerHTML = `
            ${this.getStyles()}
            <div class="roadmap-widget-root ${this.darkMode ? 'dark' : ''}">
                ${this.getTemplate()}
            </div>
        `;

        // Hide button if configured
        if (this.config.hide_button) {
            const button = this.shadowRoot.getElementById('roadmap-widget-button');
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
@include('widget.styles')
        `;
    }

    getTemplate() {
        const position = this.config.position || 'bottom-right';
        const buttonText = this.config.button_text || 'Feedback';

        return `
@include('widget.template')
        `;
    }

    attachEventListeners() {
        const button = this.shadowRoot.getElementById('roadmap-widget-button');
        const modal = this.shadowRoot.getElementById('roadmap-widget-modal');
        const closeBtn = this.shadowRoot.getElementById('roadmap-widget-close');
        const cancelBtn = this.shadowRoot.getElementById('roadmap-widget-cancel');
        const form = this.shadowRoot.getElementById('roadmap-widget-form');

        button.addEventListener('click', () => this.openModal());
        closeBtn.addEventListener('click', () => this.closeModal());
        cancelBtn.addEventListener('click', () => this.closeModal());
        modal.addEventListener('click', (e) => {
            if (e.target === modal) {
                this.closeModal();
            }
        });
        form.addEventListener('submit', (e) => this.handleSubmit(e));
    }

    openModal() {
        const modal = this.shadowRoot.getElementById('roadmap-widget-modal');
        modal.classList.remove('roadmap-widget-hidden');
        modal.classList.add('roadmap-widget-opening');
        this.isOpen = true;

        // Remove opening class after animation
        setTimeout(() => {
            modal.classList.remove('roadmap-widget-opening');
        }, 200);

        // Pre-fill name and email if set
        if (window.$roadmap._name) {
            this.shadowRoot.getElementById('roadmap-widget-name').value = window.$roadmap._name;
        }
        if (window.$roadmap._email) {
            this.shadowRoot.getElementById('roadmap-widget-email').value = window.$roadmap._email;
        }
    }

    updateName(name) {
        const nameInput = this.shadowRoot.getElementById('roadmap-widget-name');
        if (nameInput) {
            nameInput.value = name;
        }
    }

    updateEmail(email) {
        const emailInput = this.shadowRoot.getElementById('roadmap-widget-email');
        if (emailInput) {
            emailInput.value = email;
        }
    }

    closeModal() {
        const modal = this.shadowRoot.getElementById('roadmap-widget-modal');
        modal.classList.add('roadmap-widget-hidden');
        this.isOpen = false;
        this.resetForm();
    }

    resetForm() {
        const form = this.shadowRoot.getElementById('roadmap-widget-form');
        form.reset();
        this.hideMessage();
    }

    showMessage(message, type) {
        const messageEl = this.shadowRoot.getElementById('roadmap-widget-message');
        messageEl.innerHTML = message;
        messageEl.className = `roadmap-widget-message ${type}`;
        messageEl.classList.remove('roadmap-widget-hidden');
    }

    hideMessage() {
        const messageEl = this.shadowRoot.getElementById('roadmap-widget-message');
        messageEl.classList.add('roadmap-widget-hidden');
    }

    async handleSubmit(e) {
        e.preventDefault();

        const form = e.target;
        const submitBtn = form.querySelector('button[type="submit"]');
        const formData = new FormData(form);

        submitBtn.disabled = true;
        submitBtn.textContent = 'Submitting...';
        this.hideMessage();

        // Append submission URL to content
        const content = formData.get('content');
        const submissionUrl = window.location.href;
        const contentWithUrl = content + '\n\n---\nSubmitted from: ' + submissionUrl;

        try {
            const response = await fetch(`${API_BASE}/api/widget/submit`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                },
                body: JSON.stringify({
                    name: formData.get('name'),
                    email: formData.get('email'),
                    title: formData.get('title'),
                    content: contentWithUrl,
                }),
            });

            const data = await response.json();

            if (response.ok) {
                let message = 'Thank you! Your feedback has been submitted successfully.';
                if (data.item_url) {
                    message += ` <a href="${data.item_url}" target="_blank" rel="noopener noreferrer">View your feedback</a>`;
                }
                this.showMessage(message, 'success');
                // Clear title and description
                this.shadowRoot.getElementById('roadmap-widget-title').value = '';
                this.shadowRoot.getElementById('roadmap-widget-content').value = '';
                setTimeout(() => this.closeModal(), 2000);
            } else {
                this.showMessage(data.error || 'Failed to submit feedback. Please try again.', 'error');
            }
        } catch (error) {
            this.showMessage('Failed to submit feedback. Please try again.', 'error');
        } finally {
            submitBtn.disabled = false;
            submitBtn.textContent = 'Submit';
        }
    }
}

// Define custom element
customElements.define('roadmap-widget', RoadmapWidgetElement);

// Auto-initialize by inserting the custom element
const widget = document.createElement('roadmap-widget');
document.body.appendChild(widget);
