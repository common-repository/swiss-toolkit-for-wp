(function ($) {
    'use strict';
    $(document).ready(function ($) {

        /**
         * This script handles the deletion of post revisions via an AJAX request when the 
         * '#swiss_toolkit_post_revisions_btn' button is clicked. It shows a loading spinner 
         * during the AJAX call and hides it based on the response. On success, it optionally 
         * refreshes the page or updates the UI.
         */
        $('#swiss_toolkit_post_revisions_btn').on('click', function() {

            const $this = $(this);
            $this.parent()
                .find('.loading-spinner')
                .show();

            $.ajax({
                url: swiss_toolkit_delete_post_revisions.ajax_url,
                type: 'POST',
                data: {
                    action: 'swiss_toolkit_delete_post_revisions',
                    nonce: swiss_toolkit_delete_post_revisions.nonce
                },
                success: function(response) {
                    if (response.success) {
                        $('.loading-spinner').hide();
                        // Optionally, refresh the page or update the UI
                        setTimeout(function() {
                            location.reload();
                        }, 2000); // Refresh after 2 seconds
                    } else {
                        $('.loading-spinner').hide();
                    }
                },
                error: function() {
                    $('.loading-spinner').hide();
                }
            });
        });

        /**
         * This script handles the deletion of post drafts via an AJAX request when the 
         * '#swiss_toolkit_auto_drafts_btn' button is clicked. It shows a loading spinner 
         * during the AJAX call and hides it based on the response. On success, it optionally 
         * refreshes the page or updates the UI.
         */
        $('#swiss_toolkit_auto_drafts_btn').on('click', function() {

            const $this = $(this);
            $this.parent()
                .find('.loading-spinner')
                .show();

            $.ajax({
                url: swiss_toolkit_delete_post_draft.ajax_url,
                type: 'POST',
                data: {
                    action: 'swiss_toolkit_delete_post_draft',
                    nonce: swiss_toolkit_delete_post_draft.nonce
                },
                success: function(response) {
                    if (response.success) {
                        $('.loading-spinner').hide();
                        // Optionally, refresh the page or update the UI
                        setTimeout(function() {
                            location.reload();
                        }, 2000); // Refresh after 2 seconds
                    } else {
                        $('.loading-spinner').hide();
                    }
                },
                error: function() {
                    $('.loading-spinner').hide();
                }
            });
        });

        /**
         * This script handles the deletion of post trashed via an AJAX request when the 
         * '#swiss_toolkit_trashed_posts_btn' button is clicked. It shows a loading spinner 
         * during the AJAX call and hides it based on the response. On success, it optionally 
         * refreshes the page or updates the UI.
         */
        $('#swiss_toolkit_trashed_posts_btn').on('click', function() {

            const $this = $(this);
            $this.parent()
                .find('.loading-spinner')
                .show();

            $.ajax({
                url: swiss_toolkit_delete_post_trash.ajax_url,
                type: 'POST',
                data: {
                    action: 'swiss_toolkit_delete_post_trash',
                    nonce: swiss_toolkit_delete_post_trash.nonce
                },
                success: function(response) {
                    if (response.success) {
                        $('.loading-spinner').hide();
                        // Optionally, refresh the page or update the UI
                        setTimeout(function() {
                            location.reload();
                        }, 2000); // Refresh after 2 seconds
                    } else {
                        $('.loading-spinner').hide();
                    }
                },
                error: function() {
                    $('.loading-spinner').hide();
                }
            });
        });

        /**
         * This script handles the deletion of spam comments via an AJAX request when the 
         * '#swiss_toolkit_spam_comments_btn' button is clicked. It shows a loading spinner 
         * during the AJAX call and hides it based on the response. On success, it optionally 
         * refreshes the page or updates the UI.
         */
        $('#swiss_toolkit_spam_comments_btn').on('click', function() {
            const $this = $(this);
            $this.parent().find('.loading-spinner').show();
        
            $.ajax({
                url: swiss_toolkit_delete_spam_comments.ajax_url,
                type: 'POST',
                data: {
                    action: 'swiss_toolkit_delete_spam_comments',
                    nonce: swiss_toolkit_delete_spam_comments.nonce
                },
                success: function(response) {
                    $('.loading-spinner').hide();
                    if (response.success) {
                        setTimeout(function() {
                            location.reload();
                        }, 2000); // Refresh after 2 seconds
                    }
                },
                error: function() {
                    $('.loading-spinner').hide();
                }
            });
        });

        /**
         * This script handles the deletion of trashed comments via an AJAX request when the 
         * '#swiss_toolkit_trashed_comments_btn' button is clicked. It shows a loading spinner 
         * during the AJAX call and hides it based on the response. On success, it optionally 
         * refreshes the page or updates the UI.
         */
        $('#swiss_toolkit_trashed_comments_btn').on('click', function() {
            const $this = $(this);
            $this.parent().find('.loading-spinner').show();
        
            $.ajax({
                url: swiss_toolkit_delete_trashed_comments.ajax_url,
                type: 'POST',
                data: {
                    action: 'swiss_toolkit_delete_trashed_comments',
                    nonce: swiss_toolkit_delete_trashed_comments.nonce
                },
                success: function(response) {
                    $('.loading-spinner').hide();
                    if (response.success) {
                        setTimeout(function() {
                            location.reload();
                        }, 2000); // Refresh after 2 seconds
                    }
                },
                error: function() {
                    $('.loading-spinner').hide();
                }
            });
        });

        /**
         * This script handles the deletion of unapproved comments via an AJAX request when the 
         * '#swiss_toolkit_unapproved_comments_btn' button is clicked. It shows a loading spinner 
         * during the AJAX call and hides it based on the response. On success, it optionally 
         * refreshes the page or updates the UI.
         */
        $('#swiss_toolkit_unapproved_comments_btn').on('click', function() {
            const $this = $(this);
            $this.parent().find('.loading-spinner').show();
        
            $.ajax({
                url: swiss_toolkit_delete_unapproved_comments.ajax_url,
                type: 'POST',
                data: {
                    action: 'swiss_toolkit_delete_unapproved_comments',
                    nonce: swiss_toolkit_delete_unapproved_comments.nonce
                },
                success: function(response) {
                    $('.loading-spinner').hide();
                    if (response.success) {
                        setTimeout(function() {
                            location.reload();
                        }, 2000); // Refresh after 2 seconds
                    }
                },
                error: function() {
                    $('.loading-spinner').hide();
                }
            });
        }); 
        
        /**
         * This script handles the deletion of orphaned postmeta via an AJAX request when the 
         * '#swiss_toolkit_orphaned_postmeta_btn' button is clicked. It shows a loading spinner 
         * during the AJAX call and hides it based on the response. On success, it optionally 
         * refreshes the page or updates the UI.
         */
        $('#swiss_toolkit_orphaned_postmeta_btn').on('click', function() {
            const $this = $(this);
            $this.parent().find('.loading-spinner').show();

            $.ajax({
                url: swiss_toolkit_delete_orphaned_postmeta.ajax_url,
                type: 'POST',
                data: {
                    action: 'swiss_toolkit_delete_orphaned_postmeta',
                    nonce: swiss_toolkit_delete_orphaned_postmeta.nonce
                },
                success: function(response) {
                    $('.loading-spinner').hide();
                    if (response.success) {
                        setTimeout(function() {
                            location.reload();
                        }, 2000); // Refresh after 2 seconds
                    }
                },
                error: function() {
                    $('.loading-spinner').hide();
                }
            });
        });

        /**
         * This script handles the deletion of orphaned user meta via an AJAX request when the 
         * '#swiss_toolkit_orphaned_user_meta_btn' button is clicked. It shows a loading spinner 
         * during the AJAX call and hides it based on the response. On success, it optionally 
         * refreshes the page or updates the UI.
         */
        $('#swiss_toolkit_orphaned_user_meta_btn').on('click', function() {
            const $this = $(this);
            $this.parent().find('.loading-spinner').show();

            $.ajax({
                url: swiss_toolkit_delete_orphaned_user_meta.ajax_url,
                type: 'POST',
                data: {
                    action: 'swiss_toolkit_delete_orphaned_user_meta',
                    nonce: swiss_toolkit_delete_orphaned_user_meta.nonce
                },
                success: function(response) {
                    $('.loading-spinner').hide();
                    if (response.success) {
                        setTimeout(function() {
                            location.reload();
                        }, 2000); // Refresh after 2 seconds
                    }
                },
                error: function() {
                    $('.loading-spinner').hide();
                }
            });
        });

        /**
         * This script handles the deletion of orphaned comment meta via an AJAX request when the 
         * '#swiss_toolkit_orphaned_comment_meta_btn' button is clicked. It shows a loading spinner 
         * during the AJAX call and hides it based on the response. On success, it optionally 
         * refreshes the page or updates the UI.
         */
        $('#swiss_toolkit_orphaned_comment_meta_btn').on('click', function() {
            const $this = $(this);
            $this.parent().find('.loading-spinner').show();

            $.ajax({
                url: swiss_toolkit_delete_orphaned_comment_meta.ajax_url,
                type: 'POST',
                data: {
                    action: 'swiss_toolkit_delete_orphaned_comment_meta',
                    nonce: swiss_toolkit_delete_orphaned_comment_meta.nonce
                },
                success: function(response) {
                    $('.loading-spinner').hide();
                    if (response.success) {
                        setTimeout(function() {
                            location.reload();
                        }, 2000); // Refresh after 2 seconds
                    }
                },
                error: function() {
                    $('.loading-spinner').hide();
                }
            });
        });

        /**
         * This script handles the deletion of orphaned relationship data via an AJAX request when the 
         * '#swiss_toolkit_orphaned_relationship_data_btn' button is clicked. It shows a loading spinner 
         * during the AJAX call and hides it based on the response. On success, it optionally 
         * refreshes the page or updates the UI.
         */
        $('#swiss_toolkit_orphaned_relationship_data_btn').on('click', function() {
            const $this = $(this);
            $this.parent().find('.loading-spinner').show();

            $.ajax({
                url: swiss_toolkit_delete_orphaned_relationship_data.ajax_url,
                type: 'POST',
                data: {
                    action: 'swiss_toolkit_delete_orphaned_relationship_data',
                    nonce: swiss_toolkit_delete_orphaned_relationship_data.nonce
                },
                success: function(response) {
                    $('.loading-spinner').hide();
                    if (response.success) {
                        setTimeout(function() {
                            location.reload();
                        }, 2000); // Refresh after 2 seconds
                    }
                },
                error: function() {
                    $('.loading-spinner').hide();
                }
            });
        });

        /**
         * This script handles the deletion of pingbacks via an AJAX request when the 
         * '#swiss_toolkit_delete_pingbacks_btn' button is clicked. It shows a loading spinner 
         * during the AJAX call and hides it based on the response. On success, it optionally 
         * refreshes the page or updates the UI.
         */
        $('#swiss_toolkit_delete_pingbacks_btn').on('click', function() {
            const $this = $(this);
            $this.parent().find('.loading-spinner').show();

            $.ajax({
                url: swiss_toolkit_delete_pingbacks.ajax_url,
                type: 'POST',
                data: {
                    action: 'swiss_toolkit_delete_pingbacks',
                    nonce: swiss_toolkit_delete_pingbacks.nonce
                },
                success: function(response) {
                    $('.loading-spinner').hide();
                    if (response.success) {
                        setTimeout(function() {
                            location.reload();
                        }, 2000); // Refresh after 2 seconds
                    }
                },
                error: function() {
                    $('.loading-spinner').hide();
                }
            });
        });

        /**
         * This script handles the deletion of trackbacks via an AJAX request when the 
         * '#swiss_toolkit_delete_trackbacks_btn' button is clicked. It shows a loading spinner 
         * during the AJAX call and hides it based on the response. On success, it optionally 
         * refreshes the page or updates the UI.
         */
        $('#swiss_toolkit_delete_trackbacks_btn').on('click', function() {
            const $this = $(this);
            $this.parent().find('.loading-spinner').show();

            $.ajax({
                url: swiss_toolkit_delete_trackbacks.ajax_url,
                type: 'POST',
                data: {
                    action: 'swiss_toolkit_delete_trackbacks',
                    nonce: swiss_toolkit_delete_trackbacks.nonce
                },
                success: function(response) {
                    $('.loading-spinner').hide();
                    if (response.success) {
                        setTimeout(function() {
                            location.reload();
                        }, 2000); // Refresh after 2 seconds
                    }
                },
                error: function() {
                    $('.loading-spinner').hide();
                }
            });
        });

    });

})(jQuery);