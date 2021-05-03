window.addDomReadyFunc(function () {
    document.getElementById('options').style.display = 'none';
    document.getElementById('input').focus();
});
disableOverride();


window.addEventListener('load', (event) => {
    document.body.classList.add('loaded');
});