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
  indexName: window?.manage?.indexName || 'system',
  resourcesPrefix: true,
  moduleApp: {
    admin: {
      register: false,
      forgotPassword: false,
    },
  },
  sideType: window?.manage?.sideType || 'level',
  lang: window?.lang,
  baiduMap: window?.manage?.baiduMap || '',
}
export default config
