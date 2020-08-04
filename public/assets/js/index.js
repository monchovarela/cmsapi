
const mobileBreakpoint = window.matchMedia("(max-width: 991px )");

/**
 *  Short selector
 *
 * @param  {string}
 * @return {selector}
 */
var _ = function (e) { return document.querySelector(e); };
/**
 *  Get message
 *
 * @param  {string}
 * @return {func}
 */
var message = function (txt) {
    var div = document.createElement('div');
    div.id = 'message-info';
    div.className = 'info-msg';
    div.innerHTML = '<div>'+txt+'</div>';
    document.body.appendChild(div);
    var w = setTimeout(function () {
        document.body.removeChild(div);
        clearTimeout(w);
    }, 3000);
};
/**
 *  Dropdown btn
 *
 * @param  {string}
 * @return {function}
 */
var dropdown = function (evt) {
    var x = evt.nextElementSibling;
    x.classList.toggle('show');
};
/**
 *  Init js
 *
 * @return {func}
 */
var init = function () {
    if (_('#saveNote')) {
        var storage_1 = window.localStorage, notes = storage_1.getItem('note');
        if (notes)
            _('#notes').value = notes;
        _('#saveNote').addEventListener('click', function (evt) {
            evt.preventDefault();
            storage_1.setItem('note', _('#notes').value);
            _('#saveNote').textContent = 'Success';
            var w = setTimeout(function () {
                _('#saveNote').textContent = 'Save Note';
                clearTimeout(w);
            }, 2000);
        });
    }
};
window.addEventListener('load', init);



