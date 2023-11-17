import { useTranslate } from '@refinedev/core'
import { FormModal } from '@duxweb/dux-refine'
import { Form, Input } from 'tdesign-react/esm'

const Page = (props: Record<string, any>) => {
  const translate = useTranslate()

  return (
    <FormModal id={props?.id} resource='tools.fileDir'>
      <Form.FormItem label={translate('tools.file.fields.dirName')} name='name'>
        <Input />
      </Form.FormItem>
    </FormModal>
  )
}

export default Page
