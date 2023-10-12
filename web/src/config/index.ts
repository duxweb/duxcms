import { Config } from '@duxweb/dux-refine'

const config: Config = {
  projectId: '',
  apiUrl: '',
  apiPath: {
    login: 'login',
    check: 'check',
    register: 'register',
    forgotPassword: 'forgot-password',
    updatePassword: 'update-password',
    upload: 'upload',
    menu: 'menu',
  },
  defaultApp: 'admin',
  resourcesPrefix: true,
  moduleApp: {
    admin: {
      register: false,
      forgotPassword: false,
    },
  },
  sideType: 'app',
  lang: window?.lang,
}
export default config
