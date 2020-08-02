export default function(obj, err, text) {

    var _message = '';
    var _html = '';

    if(obj.status === 422 && obj.responseJSON && obj.responseJSON.error){
        $.each(obj.responseJSON, function(index, el){
            _message += obj.responseJSON.error;
            _html += '<p>' + obj.responseJSON.error + '</p>';
        });
    }
    else if(obj.status === 422 && obj.responseJSON){
        $.each(obj.responseJSON, function(index, el){
            _message += $.isArray(el) ? el[0]: el;
            _html += '<p>' + $.isArray(el) ? el[0]: el + '</p>';
        });
    }
    else {
        _message += err + ': ' + text;
        _html += '<p>' + err + ': ' + text + '</p>';

        if(obj.responseText){
            _message += obj.responseText;
            _html += obj.responseText;
        }
    }

    return {
        message: _message,
        htmlMessage: _html,
    }
}