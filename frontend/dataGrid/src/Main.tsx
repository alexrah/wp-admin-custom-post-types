import React from 'react'
import ReactDOM from 'react-dom/client'
import App from './App';

const rootElement = document.getElementById(document.currentScript.dataset.rootId);

// @ts-ignore
window.env = window.env || {};

if(APP_MODE === 'development'){

  window.env.LOG_LEVEL = 6;
  const eScript = document.createElement('script');
  eScript.src = 'http://localhost:35729/livereload.js';
  document.head.append(eScript);

} else {
  window.env.LOG_LEVEL = 0;
}

ReactDOM.createRoot(rootElement!).render(
  <React.StrictMode>
    <App/>
  </React.StrictMode>,
)
