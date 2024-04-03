import React from 'react'
import ReactDOM from 'react-dom/client'
import App from './App';

const fieldName = document.currentScript.dataset.fieldName;
const rootElement = document.getElementById(fieldName);

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
    <App fieldName={fieldName}/>
  </React.StrictMode>,
)
