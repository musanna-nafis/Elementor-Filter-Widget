jQuery(function ($) {
    'use strict';

    function fetchEvents(pageNumber = 1) {
        const widgetWrapper = $('.af-elementor-widget-container'); // Find the main widget container
        const form = widgetWrapper.find('#af-main-filter-form');
        const resultsContainer = widgetWrapper.find('#af-event-results-container');
        const paginationContainer = widgetWrapper.find('#af-event-pagination-container');
        const loader = widgetWrapper.find('#af-event-results-loader');
        const countContainer = widgetWrapper.find('#event-count');
        
        // ============== পরিবর্তন শুরু ==============
        // const statusFilter = widgetWrapper.find('#event-status-filter'); // এই লাইনের আর প্রয়োজন নেই
        
        // Elementor-এর সেটিং থেকে per_page সংখ্যাটি নেওয়া
        const postsPerPage = form.data('per-page') || 9;
        // প্যানেল থেকে সেট করা ডিফল্ট স্ট্যাটাস form-এর data-attribute থেকে নেওয়া হচ্ছে
        const defaultStatus = form.data('default-status') || 'past'; 

        let formData = form.serializeArray();
        // formData.push({ name: 'event_status', value: statusFilter.val() }); // আগের লাইন
        formData.push({ name: 'event_status', value: defaultStatus }); // নতুন লাইন
        formData.push({ name: 'action', value: 'complex_filter_events' });
        formData.push({ name: 'security', value: af_ajax_params.nonce });
        formData.push({ name: 'paged', value: pageNumber });
        formData.push({ name: 'posts_per_page', value: postsPerPage });
        // ============== পরিবর্তন শেষ ==============

        $.ajax({
            url: af_ajax_params.ajax_url, type: 'POST', data: $.param(formData),
            beforeSend: function () {
                loader.show();
                resultsContainer.html('');
                paginationContainer.html('');
            },
            success: function (response) {
                loader.hide();
                if (response.success) {
                    resultsContainer.html(response.data.html);
                    countContainer.text(response.data.count);
                    paginationContainer.html(response.data.pagination);
                } else {
                    resultsContainer.html('<p>Something went wrong. Please try again.</p>');
                }
            },
            error: function () {
                loader.hide();
                resultsContainer.html('<p>Error: Could not connect to the server.</p>');
            }
        });
    }

    // Use event delegation for dynamically placed widgets
    $(document).on('submit', '#af-main-filter-form', function (e) {
        e.preventDefault();
        fetchEvents(1);
    });

    // ============== পরিবর্তন শুরু: অপ্রয়োজনীয় change ইভেন্ট মুছে ফেলা হয়েছে ==============
    // $(document).on('change', '#event-status-filter', function () {
    //     fetchEvents(1);
    // });
    
    $(document).on('click', '#af-event-pagination-container .page-numbers', function(e) {
        e.preventDefault();
        let url = $(this).attr('href');
        let pageNumber = 1;
        let pagedMatch = url.match(/paged=(\d+)/) || url.match(/\/page\/(\d+)/);
        if (pagedMatch) { pageNumber = pagedMatch[1]; }
        fetchEvents(parseInt(pageNumber));
    });

    // Initial fetch if the widget exists on the page
    if ($('#af-event-filter-container').length) {
        fetchEvents(1);
    }
});