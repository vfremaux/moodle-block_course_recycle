
function ajax_recycle_change_action(wwwroot, courseid, userid, action) {

    var url = wwwroot+'/block/course_recycle/ajax/services.php?id='+courseid+'&userid='+userid+'&what=change&action='+action;

    $.get(url, function(data) {
        $('#block-recycle-state').html(data);
    },
    'html');
}