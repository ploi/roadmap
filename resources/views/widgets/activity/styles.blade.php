<style>
    * {
        box-sizing: border-box;
        margin: 0;
        padding: 0;
    }
    .roadmap-activity-widget-root {
        display: contents;
    }
    .roadmap-activity-widget-button {
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
    .roadmap-activity-widget-button:hover {
        transform: scale(1.05);
        box-shadow: 0 6px 16px rgba(0, 0, 0, 0.2);
    }
    .roadmap-activity-widget-button svg {
        width: 20px;
        height: 20px;
    }
    .roadmap-activity-widget-bottom-right {
        bottom: 20px;
        right: 20px;
    }
    .roadmap-activity-widget-bottom-left {
        bottom: 20px;
        left: 20px;
    }
    .roadmap-activity-widget-top-right {
        top: 20px;
        right: 20px;
    }
    .roadmap-activity-widget-top-left {
        top: 20px;
        left: 20px;
    }
    .roadmap-activity-widget-modal {
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
    .roadmap-activity-widget-modal.roadmap-activity-widget-opening {
        animation: roadmap-activity-fade-in 0.15s ease-out;
    }
    .roadmap-activity-widget-modal-content {
        background-color: white;
        border-radius: 12px;
        max-width: 600px;
        width: 90%;
        max-height: 90vh;
        overflow: hidden;
        box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
        display: flex;
        flex-direction: column;
    }
    .roadmap-activity-widget-opening .roadmap-activity-widget-modal-content {
        animation: roadmap-activity-scale-in 0.2s ease-out;
    }
    @keyframes roadmap-activity-fade-in {
        from {
            opacity: 0;
        }
        to {
            opacity: 1;
        }
    }
    @keyframes roadmap-activity-scale-in {
        from {
            opacity: 0;
            transform: scale(0.95);
        }
        to {
            opacity: 1;
            transform: scale(1);
        }
    }
    .roadmap-activity-widget-header {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        padding: 20px;
        border-bottom: 1px solid #e5e7eb;
        flex-shrink: 0;
    }
    .roadmap-activity-widget-header h3 {
        font-size: 20px;
        font-weight: 600;
        color: #111827;
        margin-bottom: 2px;
    }
    .roadmap-activity-widget-subtitle {
        font-size: 12px;
        color: #6b7280;
        font-weight: 400;
    }
    .roadmap-activity-widget-subtitle a {
        color: #6b7280;
        text-decoration: none;
        border-bottom: 1px solid #d1d5db;
        transition: all 0.2s;
    }
    .roadmap-activity-widget-subtitle a:hover {
        color: ${primaryColor};
        border-bottom-color: ${primaryColor};
    }
    .roadmap-activity-widget-close {
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
    .roadmap-activity-widget-close:hover {
        background-color: #f3f4f6;
    }
    .roadmap-activity-widget-search-wrapper {
        padding: 16px 20px;
        border-bottom: 1px solid #e5e7eb;
        flex-shrink: 0;
    }
    .roadmap-activity-widget-search-input {
        width: 100%;
        padding: 10px 12px;
        border: 1px solid #d1d5db;
        border-radius: 6px;
        font-size: 14px;
        font-family: inherit;
        transition: border-color 0.2s;
        background-color: white;
        color: #1f2937;
    }
    .roadmap-activity-widget-search-input:focus {
        outline: none;
        border-color: ${primaryColor};
        box-shadow: 0 0 0 3px ${primaryColor}20;
    }
    .roadmap-activity-widget-search-input::placeholder {
        color: #9ca3af;
    }
    .roadmap-activity-widget-content {
        padding: 20px;
        overflow-y: auto;
        flex: 1;
    }
    .roadmap-activity-widget-loading,
    .roadmap-activity-widget-error,
    .roadmap-activity-widget-empty {
        text-align: center;
        padding: 40px 20px;
        color: #6b7280;
        font-size: 14px;
    }
    .roadmap-activity-widget-error {
        color: #dc2626;
    }
    .roadmap-activity-widget-loading-more {
        text-align: center;
        padding: 16px;
        color: #9ca3af;
        font-size: 13px;
    }
    .roadmap-activity-widget-list {
        display: flex;
        flex-direction: column;
        gap: 12px;
    }
    .roadmap-activity-widget-item {
        display: block;
        padding: 16px;
        border: 1px solid #e5e7eb;
        border-radius: 8px;
        transition: all 0.2s;
        text-decoration: none;
        color: inherit;
    }
    .roadmap-activity-widget-item:hover {
        border-color: ${primaryColor};
        background-color: #f9fafb;
        transform: translateY(-1px);
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
    }
    .roadmap-activity-widget-item-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 8px;
    }
    .roadmap-activity-widget-item-user {
        font-size: 13px;
        font-weight: 600;
        color: #374151;
    }
    .roadmap-activity-widget-item-time {
        font-size: 12px;
        color: #9ca3af;
    }
    .roadmap-activity-widget-item-description {
        font-size: 14px;
        color: #1f2937;
        line-height: 1.5;
        margin-bottom: 8px;
    }
    .roadmap-activity-widget-item-stats {
        display: flex;
        gap: 16px;
        align-items: center;
    }
    .roadmap-activity-widget-stat {
        display: flex;
        align-items: center;
        gap: 4px;
        font-size: 13px;
        color: #6b7280;
    }
    .roadmap-activity-widget-icon {
        width: 16px;
        height: 16px;
    }
    .roadmap-activity-widget-hidden {
        display: none !important;
    }

    /* Dark mode styles */
    .dark .roadmap-activity-widget-modal {
        background-color: rgba(0, 0, 0, 0.7);
    }
    .dark .roadmap-activity-widget-modal-content {
        background-color: #1f2937;
        color: #f3f4f6;
    }
    .dark .roadmap-activity-widget-header {
        border-bottom-color: #374151;
    }
    .dark .roadmap-activity-widget-header h3 {
        color: #f9fafb;
    }
    .dark .roadmap-activity-widget-subtitle {
        color: #9ca3af;
    }
    .dark .roadmap-activity-widget-subtitle a {
        color: #9ca3af;
        border-bottom-color: #4b5563;
    }
    .dark .roadmap-activity-widget-subtitle a:hover {
        color: ${primaryColor};
        border-bottom-color: ${primaryColor};
    }
    .dark .roadmap-activity-widget-close {
        color: #9ca3af;
    }
    .dark .roadmap-activity-widget-close:hover {
        background-color: #374151;
        color: #f3f4f6;
    }
    .dark .roadmap-activity-widget-search-wrapper {
        border-bottom-color: #374151;
    }
    .dark .roadmap-activity-widget-search-input {
        background-color: #374151;
        border-color: #4b5563;
        color: #f3f4f6;
    }
    .dark .roadmap-activity-widget-search-input::placeholder {
        color: #6b7280;
    }
    .dark .roadmap-activity-widget-search-input:focus {
        border-color: ${primaryColor};
        background-color: #374151;
    }
    .dark .roadmap-activity-widget-loading,
    .dark .roadmap-activity-widget-empty {
        color: #9ca3af;
    }
    .dark .roadmap-activity-widget-error {
        color: #f87171;
    }
    .dark .roadmap-activity-widget-loading-more {
        color: #6b7280;
    }
    .dark .roadmap-activity-widget-item {
        border-color: #374151;
        background-color: #1f2937;
    }
    .dark .roadmap-activity-widget-item:hover {
        border-color: ${primaryColor};
        background-color: #374151;
    }
    .dark .roadmap-activity-widget-item-user {
        color: #e5e7eb;
    }
    .dark .roadmap-activity-widget-item-description {
        color: #f3f4f6;
    }
    .dark .roadmap-activity-widget-stat {
        color: #9ca3af;
    }
</style>
