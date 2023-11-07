import { useTranslate } from '@refinedev/core'
import { FormModal, UploadImage } from '@duxweb/dux-refine'
import { Form, Input } from 'tdesign-react/esm'

const Page = (props: Record<string, any>) => {
  const translate = useTranslate()

  return (
    <FormModal id={props?.id}>
      <Form.FormItem label={translate('content.source.fields.name')} name='name' requiredMark>
        <Input />
      </Form.FormItem>
      <Form.FormItem label={translate('content.source.fields.avatar')} name='avatar'>
        <UploadImage />
      </Form.FormItem>
    </FormModal>
  )
}

export default Page
