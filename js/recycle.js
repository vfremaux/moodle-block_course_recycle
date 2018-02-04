/**
 *
 */
// jshint undef:false, unused:false

function ajax_recycle_change_action(courseid, blockid, userid, action) {

    var url = M.cfg.wwwroot + '/blocks/course_recycle/ajax/services.php?';
    url += 'course=' + courseid;
    url += '&id=' + blockid;
    url += '&userid=' + userid;
    url += '&what=change&action=' + action;

    $.get(url, function(data) {
        $('#block-recycle-state').html(data);
    },
    'html');
}