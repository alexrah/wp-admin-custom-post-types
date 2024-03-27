type tEnv = {
  LOG_LEVEL: 0|1|2|3|4|5|6
}


declare global {
  interface Window {
    env?: tEnv
  }
  const APP_MODE: 'production'|'development';
}
