import { useTranslate } from '@refinedev/core'
import {
  FormPage,
  FormPageItem,
  useUpload,
  formatUploadSingle,
  getUploadSingle,
} from '@duxweb/dux-refine'
import { Input, Upload, Switch } from 'tdesign-react/esm'
const Index = () => {
  const translate = useTranslate()
  const uploadParams = useUpload()

  return (
    <FormPage
      title='Setting'
      rest
      action='edit'
      id={0}
      useFormProps={{
        meta: {
          mode: 'page',
        },
      }}
      initFormat={(data) => {
        data.logo = formatUploadSingle(data.logo)
        data.favicon = formatUploadSingle(data.favicon)
        return data
      }}
      saveFormat={(data) => {
        data.logo = getUploadSingle(data.logo)
        return data
      }}
    >
      <FormPageItem
        label={translate('setting.fields.title')}
        name='title'
        help={translate('setting.fields.titleHelp')}
        requiredMark
      >
        <Input />
      </FormPageItem>

      <FormPageItem
        label={translate('setting.fields.logo')}
        name='logo'
        help={translate('setting.fields.titleHelp')}
      >
        <Upload {...uploadParams} theme='image' accept='image/*' />
      </FormPageItem>

      <FormPageItem
        label={translate('setting.fields.favicon')}
        name='favicon'
        help={translate('setting.fields.titleHelp')}
      >
        <Upload {...uploadParams} theme='file' />
      </FormPageItem>

      <FormPageItem
        label={translate('setting.fields.footer')}
        name='footer'
        help={translate('setting.fields.footerHelp')}
      >
        <Input />
      </FormPageItem>
      <FormPageItem
        label={translate('setting.fields.fixedHeader')}
        name='fixedHeader'
        help={translate('setting.fields.fixedHeaderHelp')}
      >
        <Switch />
      </FormPageItem>
      <FormPageItem
        label={translate('setting.fields.fixedSideBar')}
        name='fixedSideBar'
        help={translate('setting.fields.fixedSideBarHelp')}
      >
        <Switch />
      </FormPageItem>
    </FormPage>
  )
}

export default Index
