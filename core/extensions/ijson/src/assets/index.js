/**
 * IJSon editor
 */
var iJson = /** @class */ (function () {
    function iJson(obj) {
        this.obj = obj;
    }
    /**
     * Create Events for drag and drop
     *
     * @param {object} elem
     */
    iJson.prototype.createEvents = function (elem) {
        elem.addEventListener('dragstart', this.handleDragStart);
        elem.addEventListener('dragenter', this.handleDragEnter);
        elem.addEventListener('dragover', this.handleDragOver);
        elem.addEventListener('dragleave', this.handleDragLeave);
        elem.addEventListener('drop', this.handleDrop);
        elem.addEventListener('dragend', this.handleDragEnd);
    };
    /**
     * Create drag and drop html list
     *
     * @param {object} sel
     * @param {string} key
     * @param {string} val
     * @param {string} type
     */
    iJson.prototype.createList = function (sel, key, val, type) {
        // create elements
        var div = document.createElement('div'), 
        k = document.createElement('input'), 
        s = document.createElement('span'), 
        t = document.createElement('select'), 
        v = document.createElement('input'), 
        b = document.createElement('button');
        // add class and draggable
        div.className = 'key';
        div.draggable = true;
        sel.appendChild(div);
        // drag button
        s.innerHTML = '☰';
        s.className = 'drag';
        div.appendChild(s);
        // init drag events
        this.createEvents(div);
        // create input key
        k.type = 'text';
        k.className = 'input-key';
        k.style.width = '125px';
        k.value = key;
        if (type === 'number')
            v.type = 'number';
        else if (type === 'date')
            v.type = 'date';
        else
            v.type = 'text';
        v.className = 'input-val';
        div.appendChild(k);
        // create select type
        t.className = 'input-select';
        t.style.width = '100px';
        div.appendChild(t);
        var array = ['string', 'number', 'boolean', 'object', 'date'];
        for (var i = 0; i < array.length; i++) {
            var option = document.createElement("option");
            if (array[i] === type)
                option.selected = true;
            option.value = array[i];
            option.text = array[i];
            t.appendChild(option);
        }
        // on change fn
        t.onchange = function () {
            if (t.value === 'number')
                v.type = 'number';
            else if (t.value === 'date')
                v.type = 'date';
            else
                v.type = 'text';
        };
        v.value = val;
        div.appendChild(v);
        // btn to remove key
        b.className = 'btn btn-danger';
        b.innerHTML = '&times;';
        b.onclick = function () {
            this.parentElement.remove();
        };
        div.appendChild(b);
    };
    /**
     * Import json elements and create list
     *
     * @param {objects} file
     */
    iJson.prototype.importList = function (file) {
        var _this = this;
        Array.from(file).map(function (item) {
            var vars = window.vars;
            _this.createList(window.vars, item.key, item.val, item.type);
        });
    };
    /**
     * Convert html list to json data
     */
    iJson.prototype.exportList = function () {
        var selectors = document.querySelectorAll('.key');
        var objs = [];
        Array.from(selectors).map(function (item) {
            if (typeof item.children[1] !== 'undefined') {
                var val1 = item.children[1], val2 = item.children[2], val3 = item.children[3];
                objs.push({
                    key: val1.value,
                    type: val2.value,
                    val: val3.value
                });
            }
        });
        return JSON.stringify(objs, null, 2);
    };
    /**
     * Static method for drag & drop functions
     *
     *
     * @param {object} element
     * @param {objects} obj
     */
    iJson.setValues = function (element, obj) {
        element.children[1].value = obj.key;
        if (obj.type === 'number') {
            element.children[3].type = 'number';
        }
        else if (obj.type === 'date') {
            element.children[3].type = 'date';
        }
        else {
            element.children[3].type = 'text';
        }
        element.children[2].value = obj.type;
        element.children[3].value = obj.val;
    };
    /**
     * Drag start event
     *
     * @param {object} e
     */
    iJson.prototype.handleDragStart = function (e) {
        // set opacity 
        e.target.style.opacity = '0.5';
        // get global obj
        self.obj = this;
        // set array
        var val1 = e.target.children[1], val2 = e.target.children[2], val3 = e.target.children[3];
        var arr = {
            key: val1.value,
            type: val2.value,
            val: val3.value
        };
        // transfer json data 
        e.dataTransfer.setData('arr', JSON.stringify(arr));
    };
    /**
     * Drag & Drop over
     *
     * @param {object} e
     */
    iJson.prototype.handleDragOver = function (e) {
        if (e.preventDefault)
            e.preventDefault();
        // check if is draggable
        if (e.target.getAttribute('draggable')) {
            e.dataTransfer.dropEffect = 'move';
            e.target.className = 'key drag-over';
        }
        return false;
    };
    /**
     * Drag & Drop enter
     *
     * @param {object} e
     */
    iJson.prototype.handleDragEnter = function (e) {
        // check if is draggable
        if (e.target.getAttribute('draggable')) {
            e.target.className = "key drag-drop";
        }
    };
    /**
     * Drag & Drop leave
     *
     * @param {object} e
     */
    iJson.prototype.handleDragLeave = function (e) {
        // check if is draggable
        if (e.target.getAttribute('draggable')) {
            e.target.className = 'key drag-move';
        }
    };
    /**
     * Drag & Drop drop
     *
     * @param {object} e
     */
    iJson.prototype.handleDrop = function (e) {
        var obj = self.obj;
        if (e.stopPropagation)
            e.stopPropagation();
        // check if is draggable
        if (!e.target.getAttribute('draggable'))
            return false;
        // exists global obj ?
        if (obj != this) {
            // set old data
            var oldData = {
                key: e.target.children[1].value,
                type: e.target.children[2].value,
                val: e.target.children[3].value
            };
            var newData = JSON.parse(e.dataTransfer.getData('arr'));
            // set  values in the new place
            iJson.setValues(obj, oldData);
            // set values in the other place	
            iJson.setValues(e.target, newData);
        }
        return false;
    };
    /**
     * Drag & Drop end
     *
     * @param {object} e
     */
    iJson.prototype.handleDragEnd = function (e) {
        // get all key and set opacity to 1 and remove drag classes
        var cols = document.querySelectorAll('[draggable]');
        Array.from(cols).map(function (col) {
            col.style.opacity = '1';
            col.className = "key";
        });
    };
    return iJson;
}());
/**
 *  Search in table
 *
 * @param  {string}
 * @return {array}
 */
var searchPages = function (name) {
    var input, filter, table, tr, td, i;
    input = document.getElementById("myInput");
    filter = input.value;
    if (filter !== '') {
        axios.get(site_url + '/' + name + '/search/' + filter)
            .then(function (r) {
            var html = '';
            Array.from(r.data.content).forEach(function (item) {
                html += "<li class=\"list-group-item\">\n<a href=\"" + site_url + "/" + name + "/edit/" + item.uid + "\" style=\"text-decoration:none;\">\n<span class=\"text-dark\">" + item.name + "</span> - " + item.title + " - " + item.created + "\n</a>\n</li>";
            });
            document.getElementById("mySearch").innerHTML = html;
        });
    }
    else {
        document.getElementById("mySearch").innerHTML = '';
    }
};
