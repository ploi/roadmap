<div id="roadmap-widget-button" class="roadmap-widget-button roadmap-widget-${position}">
    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
        <path stroke-linecap="round" stroke-linejoin="round" d="M7.5 8.25h9m-9 3H12m-9.75 1.51c0 1.6 1.123 2.994 2.707 3.227 1.129.166 2.27.293 3.423.379.35.026.67.21.865.501L12 21l2.755-4.133a1.14 1.14 0 01.865-.501 48.172 48.172 0 003.423-.379c1.584-.233 2.707-1.626 2.707-3.228V6.741c0-1.602-1.123-2.995-2.707-3.228A48.394 48.394 0 0012 3c-2.392 0-4.744.175-7.043.513C3.373 3.746 2.25 5.14 2.25 6.741v6.018z" />
    </svg>
    <span>${buttonText}</span>
</div>
<div id="roadmap-widget-modal" class="roadmap-widget-modal roadmap-widget-hidden">
    <div class="roadmap-widget-modal-content">
        <div class="roadmap-widget-header">
            <div>
                <h3>Send Feedback</h3>
                <p class="roadmap-widget-subtitle"><a href="https://github.com/ploi/roadmap" target="_blank" rel="noopener noreferrer">Open-source</a> roadmapping software by <a href="https://ploi.io" target="_blank" rel="noopener noreferrer">ploi.io</a></p>
            </div>
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
