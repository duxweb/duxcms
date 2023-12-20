import { useTranslate } from '@refinedev/core'
import { CodeEditor, Editor, FormModal, useSelect } from '@duxweb/dux-refine'
import { Button, Form, Input, Select, Textarea } from 'tdesign-react/esm'
import { MinusCircleIcon } from 'tdesign-icons-react'
import { useCallback } from 'react'

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
