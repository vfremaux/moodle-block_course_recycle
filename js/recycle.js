/**
 *
 */
// jshint unused:false, undef:false

function ajax_recycle_change_action(courseid, userid, action) {

    var params = 'id=' + courseid + '&userid=' + userid + '&what=change&action=' + action;
    var url = M.cfg.wwwroot + '/block/course_recycle/ajax/services.php?' + params;

    $.get(url, function(data) {
        $('#block-recycle-state').html(data);
    },
    'html');
}