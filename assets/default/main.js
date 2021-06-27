
window.addEventListener('load', (event) => {
    if(document.getElementById('options')) document.getElementById('options').style.display = 'none';
    if(document.getElementById('input')) document.getElementById('input').focus();
    document.body.classList.add('loaded');
});
if(typeof disableOverride !== "undefined") disableOverride();