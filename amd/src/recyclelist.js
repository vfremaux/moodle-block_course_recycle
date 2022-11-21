// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

// jshint unused: true, undef:true

/* eslint-disable no-unused-vars */

define(['jquery', 'core/config', 'core/log', 'block_course_recycle/bootstrap-select'], function($, cfg, log, bootstrapselect) {

    var courserecyclelist = {

        init: function() {
            $(".recycle-course-edit-handle").bind('click', this.load_change_form);
            $("#modal-status-save").bind('click', this.submit_change_form);
            $("#menutopcatid").bind('change', this.reload_category);
            $("#confirm-selectall-handle").bind('click', this.select_all);
            $("#confirm-unselectall-handle").bind('click', this.unselect_all);
            $("#menuchangestatus").bind('change', this.group_change);
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
                'Ask',
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
                'ArchiveCloneAndReset'
            ];

            return statuscodes[statusix];
        },

        get_postactions_code: function (statusix) {
            var postactionscodes = {
                'Ask': '',
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
                'ArchiveCloneAndReset': 'CloneAndReset'
            };

            return postactionscodes[statusix];
        },

        reload_category: function() {
            $('#recycle-confirm-category-filter-form').submit();
        },

        select_all: function() {
            $('.confirm-select').prop('checked', true);
        },

        unselect_all: function() {
            $('.confirm-select').prop('checked', false);
        },

        group_change: function() {
            $('#id-groupcommands').submit();
        }

    };

    return courserecyclelist;
});