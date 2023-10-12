import { useTranslate } from '@refinedev/core'
import { FormPage, FormPageItem } from '@duxweb/dux-refine'
import { Input, Textarea } from 'tdesign-react/esm'
const Index = () => {
  const translate = useTranslate()

  return (
    <FormPage
      rest
      action='edit'
      id={0}
      useFormProps={{
        meta: {
          mode: 'page',
        },
      }}
    >
      <FormPageItem
        label={translate('cms.setting.fields.title')}
        name='title'
        help={translate('cms.setting.fields.titleHelp')}
      >
        <Input />
      </FormPageItem>

      <FormPageItem
        label={translate('cms.setting.fields.desc')}
        name='desc'
        help={translate('cms.setting.fields.descHelp')}
      >
        <Input />
      </FormPageItem>

      <FormPageItem
        label={translate('cms.setting.fields.copyright')}
        name='copyright'
        help={translate('cms.setting.fields.copyrightHelp')}
      >
        <Textarea />
      </FormPageItem>
    </FormPage>
  )
}

export default Index
