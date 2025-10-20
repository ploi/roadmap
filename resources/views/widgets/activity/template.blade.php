<div id="roadmap-activity-widget-button" class="roadmap-activity-widget-button roadmap-activity-widget-${position}">
    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
        <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 13.5l10.5-11.25L12 10.5h8.25L9.75 21.75 12 13.5H3.75z" />
    </svg>
    <span>${buttonText}</span>
</div>
<div id="roadmap-activity-widget-modal" class="roadmap-activity-widget-modal roadmap-activity-widget-hidden">
    <div class="roadmap-activity-widget-modal-content">
        <div class="roadmap-activity-widget-header">
            <div>
                <h3>${this.config.modal_title || 'Recent activity'}</h3>
                <p class="roadmap-activity-widget-subtitle"><a href="https://github.com/ploi/roadmap" target="_blank" rel="noopener noreferrer">Open-source</a> roadmapping software by <a href="https://ploi.io" target="_blank" rel="noopener noreferrer">ploi.io</a></p>
            </div>
            <button id="roadmap-activity-widget-close" class="roadmap-activity-widget-close">&times;</button>
        </div>
        <div class="roadmap-activity-widget-search-wrapper">
            <input
                type="text"
                id="roadmap-activity-widget-search"
                class="roadmap-activity-widget-search-input"
                placeholder="Search by title..."
            />
        </div>
        <div id="roadmap-activity-widget-content" class="roadmap-activity-widget-content">
            <div class="roadmap-activity-widget-loading">Loading...</div>
        </div>
    </div>
</div>
