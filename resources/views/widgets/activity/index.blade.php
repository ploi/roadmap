(function() {
    'use strict';

    // Prevent multiple initializations
    if (customElements.get('roadmap-activity-widget')) {
        return;
    }

@include('widgets.activity.logic')
})();
