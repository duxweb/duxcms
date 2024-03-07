import { useTranslate, useResource } from '@refinedev/core'
import { FormPage, FormPageItem } from '@duxweb/dux-refine'
import { Form, Input } from 'tdesign-react/esm'
import { Poster } from './poster'

const Page = () => {
  const translate = useTranslate()
  const { id } = useResource()
  const [form] = Form.useForm()

  return (
    <FormPage
      formProps={{
        labelAlign: 'top',
      }}
      back
      form={form}
      id={id}
    >
      <FormPageItem name='title'>
        <Input placeholder={translate('tools.poster.validate.title')} />
      </FormPageItem>

      <FormPageItem name='data'>
        <Poster />
      </FormPageItem>
    </FormPage>
  )
}

export default Page
