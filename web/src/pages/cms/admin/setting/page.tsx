import { useTranslate } from '@refinedev/core'
import { FormPage, FormPageItem } from '@duxweb/dux-refine'
import { Input, Textarea, Tabs, Radio } from 'tdesign-react/esm'
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
      className='app-card-tabs'
    >
      <Tabs placement={'top'} defaultValue={0}>
        <Tabs.TabPanel value={0} label={translate('cms.setting.tabs.base')} destroyOnHide={false}>
          <FormPageItem
            label={translate('cms.setting.fields.title')}
            name={['site', 'title']}
            help={translate('cms.setting.fields.titleHelp')}
          >
            <Input />
          </FormPageItem>

          <FormPageItem
            label={translate('cms.setting.fields.keyword')}
            name={['site', 'keyword']}
            help={translate('cms.setting.fields.keywordHelp')}
          >
            <Input />
          </FormPageItem>

          <FormPageItem
            label={translate('cms.setting.fields.desc')}
            name={['site', 'description']}
            help={translate('cms.setting.fields.descHelp')}
          >
            <Input />
          </FormPageItem>

          <FormPageItem
            label={translate('cms.setting.fields.copyright')}
            name={['site', 'copyright']}
            help={translate('cms.setting.fields.copyrightHelp')}
          >
            <Textarea />
          </FormPageItem>
        </Tabs.TabPanel>
        <Tabs.TabPanel value={1} label={translate('cms.setting.tabs.fun')} destroyOnHide={false}>
          <FormPageItem
            label={translate('cms.setting.fields.editor')}
            name={['cms', 'editor']}
            help={translate('cms.setting.fields.editorHelp')}
            initialData={'default'}
          >
            <Radio.Group>
              <Radio value='default'>TinyMCE</Radio>
              <Radio value='markdown'>Markdown</Radio>
            </Radio.Group>
          </FormPageItem>
        </Tabs.TabPanel>
      </Tabs>
    </FormPage>
  )
}

export default Index
