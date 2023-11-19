import { useTranslate, useResource } from '@refinedev/core'
import { FormPage, Editor, UploadImageManage, FormPageItem } from '@duxweb/dux-refine'
import { Form, Input, Radio } from 'tdesign-react/esm'

const Page = () => {
  const translate = useTranslate()
  const { id } = useResource()

  return (
    <FormPage
      formProps={{
        labelAlign: 'top',
      }}
      back
      id={id}
      settingRender={
        <>
          <Form.FormItem label={translate('content.page.fields.name')} name='name'>
            <Input />
          </Form.FormItem>
          <Form.FormItem label={translate('content.page.fields.subtitle')} name='subtitle'>
            <Input />
          </Form.FormItem>
          <Form.FormItem label={translate('content.page.fields.image')} name='image'>
            <UploadImageManage accept='image/*' />
          </Form.FormItem>
          <Form.FormItem
            label={translate('content.page.fields.status')}
            name='status'
            initialData={true}
          >
            <Radio.Group>
              <Radio value={true}>{translate('content.page.tab.published')}</Radio>
              <Radio value={false}>{translate('content.page.tab.unpublished')}</Radio>
            </Radio.Group>
          </Form.FormItem>
        </>
      }
    >
      <FormPageItem name='title'>
        <Input placeholder={translate('content.page.validate.title')} />
      </FormPageItem>

      <FormPageItem name='content'>
        <Editor />
      </FormPageItem>
    </FormPage>
  )
}

export default Page
