(function() {
    'use strict';

    // Prevent multiple initializations
    if (customElements.get('roadmap-widget')) {
        return;
    }

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

            // Attach shadow DOM
            this.attachShadow({ mode: 'open' });
        }

        async connectedCallback() {
            await this.init();
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
                ${this.getTemplate()}
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
            <style>
                * {
                    box-sizing: border-box;
                    margin: 0;
                    padding: 0;
                }
                .roadmap-widget-button {
                    position: fixed;
                    z-index: 999999;
                    background-color: ${primaryColor};
                    color: white;
                    border: none;
                    border-radius: 50px;
                    padding: 12px 20px;
                    cursor: pointer;
                    font-size: 14px;
                    font-weight: 500;
                    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
                    display: flex;
                    align-items: center;
                    gap: 8px;
                    transition: all 0.2s;
                    font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif;
                }
                .roadmap-widget-button:hover {
                    transform: scale(1.05);
                    box-shadow: 0 6px 16px rgba(0, 0, 0, 0.2);
                }
                .roadmap-widget-button svg {
                    width: 20px;
                    height: 20px;
                }
                .roadmap-widget-bottom-right {
                    bottom: 20px;
                    right: 20px;
                }
                .roadmap-widget-bottom-left {
                    bottom: 20px;
                    left: 20px;
                }
                .roadmap-widget-top-right {
                    top: 20px;
                    right: 20px;
                }
                .roadmap-widget-top-left {
                    top: 20px;
                    left: 20px;
                }
                .roadmap-widget-modal {
                    position: fixed;
                    z-index: 1000000;
                    left: 0;
                    top: 0;
                    width: 100%;
                    height: 100%;
                    background-color: rgba(0, 0, 0, 0.5);
                    display: flex;
                    align-items: center;
                    justify-content: center;
                    font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif;
                }
                .roadmap-widget-modal.roadmap-widget-opening {
                    animation: roadmap-fade-in 0.15s ease-out;
                }
                .roadmap-widget-modal.roadmap-widget-closing {
                    animation: roadmap-fade-out 0.15s ease-out;
                }
                .roadmap-widget-modal-content {
                    background-color: white;
                    border-radius: 12px;
                    max-width: 500px;
                    width: 90%;
                    max-height: 90vh;
                    overflow-y: auto;
                    box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
                }
                .roadmap-widget-opening .roadmap-widget-modal-content {
                    animation: roadmap-scale-in 0.2s ease-out;
                }
                .roadmap-widget-closing .roadmap-widget-modal-content {
                    animation: roadmap-scale-out 0.2s ease-out;
                }
                @keyframes roadmap-fade-in {
                    from {
                        opacity: 0;
                    }
                    to {
                        opacity: 1;
                    }
                }
                @keyframes roadmap-fade-out {
                    from {
                        opacity: 1;
                    }
                    to {
                        opacity: 0;
                    }
                }
                @keyframes roadmap-scale-in {
                    from {
                        opacity: 0;
                        transform: scale(0.95);
                    }
                    to {
                        opacity: 1;
                        transform: scale(1);
                    }
                }
                @keyframes roadmap-scale-out {
                    from {
                        opacity: 1;
                        transform: scale(1);
                    }
                    to {
                        opacity: 0;
                        transform: scale(0.95);
                    }
                }
                .roadmap-widget-header {
                    display: flex;
                    justify-content: space-between;
                    align-items: center;
                    padding: 20px;
                    border-bottom: 1px solid #e5e7eb;
                }
                .roadmap-widget-header h3 {
                    font-size: 20px;
                    font-weight: 600;
                    color: #111827;
                }
                .roadmap-widget-close {
                    background: none;
                    border: none;
                    font-size: 28px;
                    cursor: pointer;
                    color: #6b7280;
                    width: 32px;
                    height: 32px;
                    display: flex;
                    align-items: center;
                    justify-content: center;
                    border-radius: 6px;
                    transition: background-color 0.2s;
                }
                .roadmap-widget-close:hover {
                    background-color: #f3f4f6;
                }
                #roadmap-widget-form {
                    padding: 20px;
                }
                .roadmap-widget-form-group {
                    margin-bottom: 16px;
                }
                .roadmap-widget-form-group label {
                    display: block;
                    margin-bottom: 6px;
                    font-size: 14px;
                    font-weight: 500;
                    color: #374151;
                }
                .roadmap-widget-form-group input,
                .roadmap-widget-form-group textarea {
                    width: 100%;
                    padding: 10px 12px;
                    border: 1px solid #d1d5db;
                    border-radius: 6px;
                    font-size: 14px;
                    font-family: inherit;
                    transition: border-color 0.2s;
                }
                .roadmap-widget-form-group input:focus,
                .roadmap-widget-form-group textarea:focus {
                    outline: none;
                    border-color: ${primaryColor};
                    box-shadow: 0 0 0 3px ${primaryColor}20;
                }
                .roadmap-widget-form-actions {
                    display: flex;
                    gap: 10px;
                    justify-content: flex-end;
                    margin-top: 20px;
                }
                .roadmap-widget-btn {
                    padding: 10px 20px;
                    border-radius: 6px;
                    font-size: 14px;
                    font-weight: 500;
                    cursor: pointer;
                    border: none;
                    transition: all 0.2s;
                    font-family: inherit;
                }
                .roadmap-widget-btn-primary {
                    background-color: ${primaryColor};
                    color: white;
                }
                .roadmap-widget-btn-primary:hover {
                    opacity: 0.9;
                }
                .roadmap-widget-btn-primary:disabled {
                    opacity: 0.5;
                    cursor: not-allowed;
                }
                .roadmap-widget-btn-secondary {
                    background-color: #f3f4f6;
                    color: #374151;
                }
                .roadmap-widget-btn-secondary:hover {
                    background-color: #e5e7eb;
                }
                .roadmap-widget-hidden {
                    display: none !important;
                }
                .roadmap-widget-message {
                    margin-top: 16px;
                    padding: 12px;
                    border-radius: 6px;
                    font-size: 14px;
                }
                .roadmap-widget-message.success {
                    background-color: #d1fae5;
                    color: #065f46;
                }
                .roadmap-widget-message.error {
                    background-color: #fee2e2;
                    color: #991b1b;
                }
            </style>
            `;
        }

        getTemplate() {
            const position = this.config.position || 'bottom-right';
            const buttonText = this.config.button_text || 'Feedback';

            return `
                <div id="roadmap-widget-button" class="roadmap-widget-button roadmap-widget-${position}">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M7.5 8.25h9m-9 3H12m-9.75 1.51c0 1.6 1.123 2.994 2.707 3.227 1.129.166 2.27.293 3.423.379.35.026.67.21.865.501L12 21l2.755-4.133a1.14 1.14 0 01.865-.501 48.172 48.172 0 003.423-.379c1.584-.233 2.707-1.626 2.707-3.228V6.741c0-1.602-1.123-2.995-2.707-3.228A48.394 48.394 0 0012 3c-2.392 0-4.744.175-7.043.513C3.373 3.746 2.25 5.14 2.25 6.741v6.018z" />
                    </svg>
                    <span>${buttonText}</span>
                </div>
                <div id="roadmap-widget-modal" class="roadmap-widget-modal roadmap-widget-hidden">
                    <div class="roadmap-widget-modal-content">
                        <div class="roadmap-widget-header">
                            <h3>Send Feedback</h3>
                            <button id="roadmap-widget-close" class="roadmap-widget-close">&times;</button>
                        </div>
                        <form id="roadmap-widget-form">
                            <div class="roadmap-widget-form-group">
                                <label for="roadmap-widget-name">Name (optional)</label>
                                <input type="text" id="roadmap-widget-name" name="name" placeholder="Your name">
                            </div>
                            <div class="roadmap-widget-form-group">
                                <label for="roadmap-widget-email">Email (optional)</label>
                                <input type="email" id="roadmap-widget-email" name="email" placeholder="your@email.com">
                            </div>
                            <div class="roadmap-widget-form-group">
                                <label for="roadmap-widget-title">Title <span style="color: #dc2626;">*</span></label>
                                <input type="text" id="roadmap-widget-title" name="title" placeholder="Brief summary" required>
                            </div>
                            <div class="roadmap-widget-form-group">
                                <label for="roadmap-widget-content">Description <span style="color: #dc2626;">*</span></label>
                                <textarea id="roadmap-widget-content" name="content" rows="4" placeholder="Tell us more..." required></textarea>
                            </div>
                            <div class="roadmap-widget-form-actions">
                                <button type="button" id="roadmap-widget-cancel" class="roadmap-widget-btn roadmap-widget-btn-secondary">Cancel</button>
                                <button type="submit" class="roadmap-widget-btn roadmap-widget-btn-primary">Submit</button>
                            </div>
                            <div id="roadmap-widget-message" class="roadmap-widget-message roadmap-widget-hidden"></div>
                        </form>
                    </div>
                </div>
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
            messageEl.textContent = message;
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
                    this.showMessage('Thank you! Your feedback has been submitted successfully.', 'success');
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
})();
