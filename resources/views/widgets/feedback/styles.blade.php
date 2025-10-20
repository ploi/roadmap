<style>
    * {
        box-sizing: border-box;
        margin: 0;
        padding: 0;
    }
    .roadmap-widget-root {
        display: contents;
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
        align-items: flex-start;
        padding: 20px;
        border-bottom: 1px solid #e5e7eb;
    }
    .roadmap-widget-header h3 {
        font-size: 20px;
        font-weight: 600;
        color: #111827;
        margin-bottom: 2px;
    }
    .roadmap-widget-subtitle {
        font-size: 12px;
        color: #6b7280;
        font-weight: 400;
    }
    .roadmap-widget-subtitle a {
        color: #6b7280;
        text-decoration: none;
        border-bottom: 1px solid #d1d5db;
        transition: all 0.2s;
    }
    .roadmap-widget-subtitle a:hover {
        color: ${primaryColor};
        border-bottom-color: ${primaryColor};
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
    .roadmap-widget-message a {
        color: inherit;
        font-weight: 600;
        text-decoration: underline;
    }
    .roadmap-widget-message a:hover {
        opacity: 0.8;
    }

    /* Dark mode styles */
    .dark .roadmap-widget-modal {
        background-color: rgba(0, 0, 0, 0.7);
    }
    .dark .roadmap-widget-modal-content {
        background-color: #1f2937;
        color: #f3f4f6;
    }
    .dark .roadmap-widget-header {
        border-bottom-color: #374151;
    }
    .dark .roadmap-widget-header h3 {
        color: #f9fafb;
    }
    .dark .roadmap-widget-subtitle {
        color: #9ca3af;
    }
    .dark .roadmap-widget-subtitle a {
        color: #9ca3af;
        border-bottom-color: #4b5563;
    }
    .dark .roadmap-widget-subtitle a:hover {
        color: ${primaryColor};
        border-bottom-color: ${primaryColor};
    }
    .dark .roadmap-widget-close {
        color: #9ca3af;
    }
    .dark .roadmap-widget-close:hover {
        background-color: #374151;
        color: #f3f4f6;
    }
    .dark .roadmap-widget-form-group label {
        color: #e5e7eb;
    }
    .dark .roadmap-widget-form-group input,
    .dark .roadmap-widget-form-group textarea {
        background-color: #374151;
        border-color: #4b5563;
        color: #f3f4f6;
    }
    .dark .roadmap-widget-form-group input::placeholder,
    .dark .roadmap-widget-form-group textarea::placeholder {
        color: #6b7280;
    }
    .dark .roadmap-widget-form-group input:focus,
    .dark .roadmap-widget-form-group textarea:focus {
        border-color: ${primaryColor};
        background-color: #374151;
    }
    .dark .roadmap-widget-btn-secondary {
        background-color: #374151;
        color: #e5e7eb;
    }
    .dark .roadmap-widget-btn-secondary:hover {
        background-color: #4b5563;
    }
    .dark .roadmap-widget-message.success {
        background-color: #064e3b;
        color: #d1fae5;
    }
    .dark .roadmap-widget-message.error {
        background-color: #7f1d1d;
        color: #fecaca;
    }
</style>
