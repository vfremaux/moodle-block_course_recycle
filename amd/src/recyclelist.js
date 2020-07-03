
/* eslint-disable no-unused-vars */

define(['jquery', 'core/config', 'core/log', 'block_course_recycle/bootstrap-select'], function($, cfg, log, bootstrapselect) {

    var courserecyclelist = {

        init: function() {
            $(".recycle-course-edit-handle").bind('click', this.load_change_form);
            $("#modal-status-save").bind('click', this.submit_change_form);
            $("#menutopcatid").bind('change', this.reload_category);
            log.debug('AMD Course Recycle List initialized');
        },

        load_change_form: function() {

            var that = $(this);

            var waiter = '<div class="centered"><center><img id="detail-waiter" src="';
            waiter += cfg.wwwroot + '/pix/i/ajaxloader.gif" /></center></div>';
            $('#recyclelist-edit-inner-form').html(waiter);

            var id = that.attr('data-course');

            var url = cfg.wwwroot + '/blocks/course_recycle/ajax/services.php';
            url += '?what=getmodalform';
            url += '&id=' + id;

            $.get(url, function(data) {
                $('#recyclelist-edit-inner-form').html(data);
                $("#modal-status-save").attr('data-course', id);
                $('.selectpicker').selectpicker();
            }, 'html');
        },

        submit_change_form: function() {

            var that = $(this);

            var courseid = that.attr('data-course');
            var url = cfg.wwwroot + '/blocks/course_recycle/ajax/services.php';
            url += '?what=changerecycle';
            url += '&id=' + courseid;
            var radioname = 'recycleaction';
            var statusvalue = $('[name='+ radioname +']:checked').val();
            url += '&status=' + statusvalue;
            url += '&postactions=' + courserecyclelist.get_postactions_code(statusvalue);

            $.get(url, function(data) {
                if (data.result === 'success') {
                    var oldclassname = 'status-' + data.oldstate;
                    $('.recycle-list-status-' + courseid).removeClass(oldclassname);
                    var newclassname = 'status-' + data.newstate;
                    $('.recycle-list-status-' + courseid).addClass(newclassname);
                    $('#recycle-status-' + courseid).html(data.newlabel);
                    $('#recyclelist-edit-form').modal('hide'); // Close the modal dialog.
                }
            }, 'json');
        },

        get_status_code: function (statusix) {
            var statuscodes = [
                'RequestForArchive',
                'Stay',
                'Reset',
                'Clone',
                'Delete',
                'Archive',
                'CloneAndReset',
                'ArchiveAndDelete',
                'ArchiveAndReset',
                'ArchiveAndRetire',
                'CloneArchiveAndReset'
            ];

            return statuscodes[statusix];
        },

        get_postactions_code: function (statusix) {
            var postactionscodes = {
                'RequestForArchive': '',
                'Stay': '',
                'Reset': '',
                'Clone': '',
                'Delete': '',
                'Archive': '',
                'CloneAndReset': '',
                'ArchiveAndDelete': 'Delete',
                'ArchiveAndReset': 'Reset',
                'ArchiveAndRetire': 'Retire',
                'CloneArchiveAndReset': 'CloneAndReset'
            };

            return postactionscodes[statusix];
        },

        reload_category: function() {
            $('#recycle-confirm-category-filter-form').submit();
        }

    };

    return courserecyclelist;
});