import { useTranslate } from '@refinedev/core'
import { FormModal, UploadImageManage } from '@duxweb/dux-refine'
import { Form, Input } from 'tdesign-react/esm'

const Page = (props: Record<string, any>) => {
  const translate = useTranslate()

  return (
    <FormModal id={props?.id}>
      <Form.FormItem label={translate('content.source.fields.name')} name='name' requiredMark>
        <Input />
      </Form.FormItem>
      <Form.FormItem label={translate('content.source.fields.avatar')} name='avatar'>
        <UploadImageManage accept='image/*' />
      </Form.FormItem>
    </FormModal>
  )
}

export default Page
