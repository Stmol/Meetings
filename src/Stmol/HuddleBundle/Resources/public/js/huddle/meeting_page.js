'use strict';

(function($, global) {
    $().ready(function() {
        $('#member-go').on('click', openMemberForm);
    });

    function openMemberForm() {
        $("#member-form").slideToggle(300);
    }

})(jQuery, window);
