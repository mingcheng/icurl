// vim: set et sw=4 ts=4 sts=4 fdm=marker ff=unix fenc=utf8 nobomb:
/**
 * iCurl Javascript
 *
 * @author mingcheng<i.feelinglucky@gmail.com>
 * @date   2009-09-22
 * @link   http://www.gracecode.com/
 */

window.addEvent('domready', function() {
    var escapeHTML = function(str) {
		var div = document.createElement('div');
		var text = document.createTextNode(str);
		div.appendChild(text);
		return div.innerHTML;
    };
    var result = $('result').empty().addClass('hidden'), params = $('params').empty();

    /*
    result.addEvent('click', function(e){
        this.select();
        e.stop();
    });
    */

    $('ag').value = navigator.userAgent;

    $('show_extra').addEvent('click', function(e){
        $('extra')[$('extra').hasClass('hidden') ? 'removeClass' : 'addClass']('hidden');
        e.stop();
    });

    $('add_param').addEvent('click', function(e){
        e.stop();
        var p = document.createElement('li');
        p.innerHTML = '<input type="text" name="n[]" value="" />: <input type="text" name="v[]" value="" />' + 
                            '<button class="del" title="删除">X</button>';
        params.appendChild(p);
    });

    params.addEvent('click', function(e){
        var target = e.target;
        if ('button' == target.tagName.toLowerCase() && 'del' == target.className) {
            e.stop();
            params.removeChild(target.parentNode);
        }
    });

    $('a').addEvent('click', function(e) {
        $('auth')[$('auth').hasClass('hidden') ? 'removeClass' : 'addClass']('hidden');
    });

    $('icurl').addEvent('submit', function(e){
        var isURL = /^http:\/\/[^\/\.]+?\..+\w*$/i;
        if (!isURL.test($('q').value)) {
            e.stop();
            $('q').addClass('error').focus();
            return;
        }
        $('q').removeClass('error');

        if ($('b').checked) {
            result.empty().addClass('hidden');
        } else {
            result.empty().removeClass('hidden').addClass('ajax-loading');
        }

        /*
        this.set('send', {
            onComplete: function(response) {
                result.removeClass('ajax-loading').set('html', escapeHTML(response));
            }
        });
        this.send();
        */
    });
});
