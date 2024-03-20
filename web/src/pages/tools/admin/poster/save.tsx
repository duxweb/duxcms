import { useTranslate } from '@refinedev/core'
import { FormModal } from '@duxweb/dux-refine'
import { Form, Input } from 'tdesign-react/esm'

const Page = (props: Record<string, any>) => {
  const translate = useTranslate()

  return (
    <FormModal id={props?.id}>
      <Form.FormItem label={translate('tools.poster.fields.title')} name='title'>
        <Input />
      </Form.FormItem>
    </FormModal>
  )
}

export default Page
