import vueApp from './assets/vue_init.js'


// https://github.com/xy2z/dopenote/issues/138
// Check if an existing page/tab is already open
// and deny it, since we have not setup sockets/sync
// between tabs, so it would result in errors if the
// same note is being edited across different tabs.
localStorage.openpages = Date.now()

function onLocalStorageEvent(e) {
  if (e.key == "openpages") {
  	console.log('openpages')
    // Emit that you're already available.
    localStorage.page_available = Date.now()
  }

  if (e.key == 'page_available') {
    // Another page/tab is already open.
    vueApp.$destroy()
    document.title = 'Another tab is already open'
    document.getElementById('app').innerHTML = '<div class="already_open">' +
    '<h1>Another tab is already open</h1>' +
    'Currently Dopenote only supports 1 tab open at a time, in order to keep everything synced.<br>' +
    'This will be fixed in the upcoming releases.' +
    '</div>'
  }
};

window.addEventListener('storage', onLocalStorageEvent, false)
