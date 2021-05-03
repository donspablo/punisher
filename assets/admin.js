var offsetx = 12;
var offsety = 8;

function newelement(a) {
    if (document.createElement) {
        var b = document.createElement('div');
        b.id = a;
        with (b.style) {
            display = 'none';
            position = 'absolute'
        }
        b.innerHTML = '&nbsp;';
        document.body.appendChild(b)
    }
}

var ie5 = (document.getElementById && document.all);
var ns6 = (document.getElementById && !document.all);
var ua = navigator.userAgent.toLowerCase();
var isapple = (ua.indexOf('applewebkit') != -1 ? 1 : 0);

function getmouseposition(e) {
    if (document.getElementById) {
        var a = (document.compatMode && document.compatMode != 'BackCompat') ? document.documentElement : document.body;
        pagex = (isapple == 1 ? 0 : (ie5) ? a.scrollLeft : window.pageXOffset);
        pagey = (isapple == 1 ? 0 : (ie5) ? a.scrollTop : window.pageYOffset);
        mousex = (ie5) ? event.x : (ns6) ? clientX = e.clientX : false;
        mousey = (ie5) ? event.y : (ns6) ? clientY = e.clientY : false;
        var b = document.getElementById('tooltip');
        b.style.left = (mousex + pagex + offsetx) + 'px';
        b.style.top = (mousey + pagey + offsety) + 'px'
    }
}

function tooltip(a) {
    if (!document.getElementById('tooltip')) newelement('tooltip');
    var b = document.getElementById('tooltip');
    b.innerHTML = a;
    b.style.display = 'block';
    document.onmousemove = getmouseposition
}

function exit() {
    document.getElementById('tooltip').style.display = 'none'
}

window.domReadyFuncs = [];
window.addDomReadyFunc = function (a) {
    window.domReadyFuncs.push(a)
};

function init() {
    if (arguments.callee.done) return;
    arguments.callee.done = true;
    if (_timer) clearInterval(_timer);
    for (var i = 0; i < window.domReadyFuncs.length; ++i) {
        try {
            window.domReadyFuncs[i]()
        } catch (ignore) {
        }
    }
}

if (document.addEventListener) {
    document.addEventListener("DOMContentLoaded", init, false)
}
if (/WebKit/i.test(navigator.userAgent)) {
    var _timer = setInterval(function () {
        if (/loaded|complete/.test(document.readyState)) {
            init()
        }
    }, 10)
}
window.onload = init;
if (!window.XMLHttpRequest) {
    window.XMLHttpRequest = function () {
        return new ActiveXObject('Microsoft.XMLHTTP')
    }
}

function runAjax(a, b, c, d) {
    var e = new XMLHttpRequest();
    var f = b ? 'POST' : 'GET';
    e.open(f, a, true);
    e.setRequestHeader("Content-Type", "application/x-javascript;");
    e.onreadystatechange = function () {
        if (e.readyState == 4 && e.status == 200) {
            if (e.responseText) {
                c.call(d, e.responseText)
            }
        }
    };
    e.send(b)
}

window.addEventListener('load', (event) => {
    document.body.classList.add('loaded');
});