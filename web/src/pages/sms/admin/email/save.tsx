import { useTranslate } from '@refinedev/core'
import { CodeEditor, FormModal } from '@duxweb/dux-refine'
import { Form, Input } from 'tdesign-react/esm'

const Page = (props: Record<string, any>) => {
  const translate = useTranslate()

  return (
    <FormModal id={props?.id}>
      <Form.FormItem label={translate('sms.email.fields.name')} name='name'>
        <Input />
      </Form.FormItem>
      <Form.FormItem
        label={translate('sms.email.fields.label')}
        name='label'
        help={translate('sms.email.help.label')}
      >
        <Input />
      </Form.FormItem>

      <Form.FormItem
        label={translate('sms.email.fields.content')}
        name='content'
        help={translate('sms.email.help.content')}
      >
        <CodeEditor type='html' />
      </Form.FormItem>
    </FormModal>
  )
}

export default Page
