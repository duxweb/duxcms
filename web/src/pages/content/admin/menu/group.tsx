import { useTranslate } from '@refinedev/core'
import { FormModal } from '@duxweb/dux-refine'
import { Form, Input } from 'tdesign-react/esm'

const Page = (props: Record<string, any>) => {
  const translate = useTranslate()

  return (
    <FormModal id={props?.id} resource='content.menu'>
      <Form.FormItem label={translate('content.menu.fields.groupName')} name='name'>
        <Input />
      </Form.FormItem>
      <Form.FormItem label={translate('content.menu.fields.groupTitle')} name='title'>
        <Input />
      </Form.FormItem>
    </FormModal>
  )
}

export default Page
