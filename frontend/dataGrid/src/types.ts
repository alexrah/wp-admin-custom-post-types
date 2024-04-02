type tEnv = {
  LOG_LEVEL: 0|1|2|3|4|5|6
}


declare global {
  interface Window {
    env?: tEnv
    wpAdminCPT: {}
  }
  const APP_MODE: 'production'|'development';

  namespace NodeJS {
    interface ProcessEnv {
      DEV_TEST_URL: string,
      DEV_TEST_SERVER_IP: string,
      DEV_TEST_SERVER_PORT: string,
      DEV_TEST_SERVER_USERNAME: string,
      DEV_TEST_SERVER_SSH_KEY_PATH: string,
      DEV_TEST_SERVER_UPLOAD_PATH: string
    }
  }

}

export type tCouncilor = {
  nome: string,
  cognome: string
}